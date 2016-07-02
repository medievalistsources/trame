<?php
/*

SQL Buddy - Web based MySQL administration
http://www.sqlbuddy.com/

sql.php
- sql class

MIT license

2008 Calvin Lough <http://calv.in>

*/

class SQL {
	
	var $adapter = "";
	var $method = "";
	var $version = "";
	var $conn = "";
	var $options = "";
	var $errorMessage = "";
	var $db = "";
	
	function SQL($connString, $user = "", $pass = "") {                     //Crea DataBase SQL
		list($this->adapter, $options) = explode(":", $connString, 2);
		
		if ($this->adapter != "sqlite") {
			$this->adapter = "mysql";
		}
		
		$optionsList = explode(";", $options);
		
		foreach ($optionsList as $option) {
			list($a, $b) = explode("=", $option);
			$opt[$a] = $b;
		}
		
		$this->options = $opt;
		$database = (array_key_exists("database", $opt)) ? $opt['database'] : "";
		
		if ($this->adapter == "sqlite" && substr(sqlite_libversion(), 0, 1) == "3" && class_exists("PDO") && in_array("sqlite", PDO::getAvailableDrivers())) {
			$this->method = "pdo";
			
			try
			{
				$this->conn = new PDO("sqlite:" . $database, null, null, array(PDO::ATTR_PERSISTENT => true));
			}
			catch (PDOException $error) {
				$this->conn = false;
            	$this->errorMessage = $error->getMessage();
          	}
		} else if ($this->adapter == "sqlite" && substr(sqlite_libversion(), 0, 1) == "2" && class_exists("PDO") && in_array("sqlite2", PDO::getAvailableDrivers())) {
			$this->method = "pdo";
			
			try
			{
				$this->conn = new PDO("sqlite2:" . $database, null, null, array(PDO::ATTR_PERSISTENT => true));
			}
			catch (PDOException $error) {
				$this->conn = false;
            	$this->errorMessage = $error->getMessage();
          	}
		} else if ($this->adapter == "sqlite") {
			$this->method = "sqlite";
			$this->conn = sqlite_open($database, 0666, $sqliteError);
		} else {
			$this->method = "mysql";
			$host = (array_key_exists("host", $opt)) ? $opt['host'] : "";
			$this->conn = new mysqli($host, $user, $pass);
		}
		
		if ($this->conn && $this->method == "pdo") {
			$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
		}
		
		if ($this->conn && $this->adapter == "mysql") {
			$this->query("SET NAMES 'utf8'");
		}
		
		if ($this->conn && $database) {
			$this->db = $database;
		}
	}
	
	function isConnected() {                    //Verificare connessione al DB
		return ($this->conn !== false);
	}
	
	function disconnect() {                     //Disconnessione DataBase
		if ($this->conn) {
			if ($this->method == "pdo") {
				$this->conn = null;
			} else if ($this->method == "mysql") {
				mysqli_close($this->conn);
				$this->conn = null;
			} else if ($this->method == "sqlite") {
				sqlite_close($this->conn);
				$this->conn = null;
			}
		}
	}
	
	function getAdapter() {             //Ritorna adapter settato nella funzione SQL();
		return $this->adapter;
	}
	
	function getMethod() {          //Ritorna il tipo di method usato per il DB
		return $this->method;
	}
	
	function getOptionValue($optKey) {             //Ritorna options     
		if (array_key_exists($optKey, $this->options)) {
			return $this->options[$optKey];
		} else {
			return false;
		}
	}
	
	function selectDB($db) {                //Seleziono il DataBase MySQL
		if ($this->conn) {
			if ($this->method == "mysql") {
				$this->db = $db;
				return ($this->conn->select_db($db));
			} else {
				return true;
			}
		} else {
			return false;
		}
	}

	function query($queryText) {            //Query al DB, ritorna il risultato
		if ($this->conn) {
			if ($this->method == "pdo") {
				$queryResult = $this->conn->prepare($queryText);
				
				if ($queryResult)
					$queryResult->execute();

				if (!$queryResult) {
					$errorInfo = $this->conn->errorInfo();
					$this->errorMessage = $errorInfo[2];
				}

				return $queryResult;
			} else if ($this->method == "mysql") {
				$queryResult = mysqli_query($this->conn, $queryText);

				if (!$queryResult) {
					$this->errorMessage = mysqli_error($this->conn);
				}

				return $queryResult;
			} else if ($this->method == "sqlite") {
				$queryResult = sqlite_query($this->conn, $queryText);

				if (!$queryResult) {
					$this->errorMessage = sqlite_error_string(sqlite_last_error($this->conn));
				}

				return $queryResult;
			}
		} else {
			return false;
		}
	}
	
	// Be careful using this function - when used with pdo, the pointer is moved
	// to the end of the result set and the query needs to be rerun. Unless you 
	// actually need a count of the rows, use the isResultSet() function instead
	
        function rowCount($resultSet) {         //Numero di row
		if (!$resultSet)
			return false;
		
		if ($this->conn) {
			if ($this->method == "pdo") {
				return count($resultSet->fetchAll());
			} else if ($this->method == "mysql") {
				return $resultSet->num_rows;
			} else if ($this->method == "sqlite") {
				return @sqlite_num_rows($resultSet);
			}
		}
	}
	
	function isResultSet($resultSet) {      //Verifica se esiste risultato in base alle righe
		if ($this->conn) {
			if ($this->method == "pdo") {
				return ($resultSet == true);
			} else {
				return ($this->rowCount($resultSet) > 0);
			}
		}
	}

	function fetchArray($resultSet) {       //Restituisce un array che corrisponde ad una riga caricata 
                                                //oppure FALSE se non ci sono più righe.
		if (!$resultSet)
			return false;
		
		if ($this->conn) {
			if ($this->method == "pdo") {
				return $resultSet->fetch(PDO::FETCH_NUM);
			} else if ($this->method == "mysql") {
				return mysqli_fetch_row($resultSet);
			} else if ($this->method == "sqlite") {
				return sqlite_fetch_array($resultSet, SQLITE_NUM);
			}
		}
	}

	function fetchAssoc($resultSet) {           //Inserisce risultato query in un array associativo
		if (!$resultSet)
			return false;
		
		if ($this->conn) {
			if ($this->method == "pdo") {
				return $resultSet->fetch(PDO::FETCH_ASSOC);
			} else if ($this->method == "mysql") {
				return mysqli_fetch_assoc($resultSet);
			} else if ($this->method == "sqlite") {
				return sqlite_fetch_array($resultSet, SQLITE_ASSOC);
			}
		}
	}

	function affectedRows($resultSet) {     //restituisce il numero di righe coinvolte nell'ultima 
                                                //query INSERT, UPDATE o DELETE associata a identificativo_connessione.
		if (!$resultSet)
			return false;
		
		if ($this->conn) {
			if ($this->method == "pdo") {
				return $resultSet->rowCount();
			} else if ($this->method == "mysql") {
				return @mysqli_affected_rows($resultSet);
			} else if ($this->method == "sqlite") {
				return sqlite_changes($resultSet);
			}
		}
	}
	
	function result($resultSet, $targetRow, $targetColumn = "") {           //Restituisce i contenuti di una cella da un risultato
		if (!$resultSet)
			return false;
		
		if ($this->conn) {
			if ($this->method == "pdo") {
				if ($targetColumn) {
					$resultRow = $resultSet->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_ABS, $targetRow);
					return $resultRow[$targetColumn];
				} else {
					$resultRow = $resultSet->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_ABS, $targetRow);
					return $resultRow[0];
				}
			} else if ($this->method == "mysql") {
				return $this->mysqli_result($resultSet, $targetRow, $targetColumn);
			} else if ($this->method == "sqlite") {
				return sqlite_column($resultSet, $targetColumn);
			}
		}
	}
        
        function mysqli_result($res, $row, $field=0) { 
            $res->data_seek($row); 
            $datarow = $res->fetch_array(); 
            return $datarow[$field]; 
        }
	
	function listDatabases() {          //Lista dei Data Base
		if ($this->conn) {
			if ($this->adapter == "mysql") {
				return $this->query("SHOW DATABASES");
			} else if ($this->adapter == "sqlite") {
				return $this->db;
			}
		}
	}
	
	function listTables() {         //Lista delle Tabelle
		if ($this->conn) {
			if ($this->adapter == "mysql") {
				return $this->query("SHOW TABLES");
			} else if ($this->adapter == "sqlite") {
				return $this->query("SELECT name FROM sqlite_master WHERE type = 'table' ORDER BY name");
			}
		}
	}
	
	function hasCharsetSupport(){
		if ($this->conn) {
			if ($this->adapter == "mysql" && version_compare($this->getVersion(), "4.1", ">")) {
				return true;
			} else  {
				return false;
			}
		}
	}
	
	function listCharset() {            //Lista dei set di caratteri
		if ($this->conn) {
			if ($this->adapter == "mysql") {	
				return $this->query("SHOW CHARACTER SET");
			} else if ($this->adapter == "sqlite") {
				return "";
			}
		}
	}
	
	function listCollation() {          //Lista delle collation
		if ($this->conn) {
			if ($this->adapter == "mysql") {
				return $this->query("SHOW COLLATION");
			} else if ($this->adapter == "sqlite") {
				return "";
			}
		}
	}
	
	function insertId() {           //Restituisce l'identificativo generato per una colonna AUTO_INCREMENT 
                                        //dal precedente query INSERT usando lo specifico identificativo_connessione.
		if ($this->conn) {
			if ($this->method == "pdo") {
				return $this->conn->lastInsertId();
			} else if ($this->method == "mysql") {
				return mysqli_insert_id($this->conn);
			} else if ($this->method == "sqlite") {
				return sqlite_last_insert_rowid($this-conn);
			}
		}
	}

	function escapeString($toEscape) {          //Aggiunge le sequenza di escape ai caratteri speciali
		if ($this->conn) {
			if ($this->method == "pdo") {
				$toEscape = $this->conn->quote($toEscape);
				$toEscape = substr($toEscape, 1, -1);
				return $toEscape;
			} else if ($this->adapter == "mysql") {
				return mysqli_real_escape_string($this->conn,$toEscape);
			} else if ($this->adapter == "sqlite") {
				return sqlite_escape_string($toEscape);
			}
		}
	}
	
	function getVersion() {                 //Versione DB
		if ($this->conn) {
			// cache
			if ($this->version) {
				return $this->version;
			}
			
			if ($this->adapter == "mysql") {
				$verSql = mysqli_get_server_info();
				$version = explode("-", $verSql);
				$this->version = $version[0];
				return $this->version;
			} else if ($this->adapter == "sqlite") {
				$this->version = sqlite_libversion();
				return $this->version;
			}
		}

	}	
	
	function tableRowCount($table) {                //Ritorna il numero di righe nella tabella
		if ($this->conn) {
			if ($this->adapter == "mysql") {
				$countSql = $this->query("SELECT COUNT(*) AS `RowCount` FROM `" . $table . "`");
				$count = (int)($this->result($countSql, 0, "RowCount"));
				return $count;
			} else if ($this->adapter == "sqlite") {
				$countSql = $this->query("SELECT COUNT(*) AS 'RowCount' FROM '" . $table . "'");
				$count = (int)($this->result($countSql, 0, "RowCount"));
				return $count;
			}
		}
	}	
	
	function describeTable($table) {            //Informazioni sulle colonne di una tabella
		if ($this->conn) {
			if ($this->adapter == "mysql") {
				return $this->query("DESCRIBE `" . $table . "`");
			} else if ($this->adapter == "sqlite") {
				$columnSql = $this->query("SELECT sql FROM sqlite_master where tbl_name = '" . $table . "'");
				$columnInfo = $this->result($columnSql, 0, "sql");
				$columnStart = strpos($columnInfo, '(');
				$columns = substr($columnInfo, $columnStart+1, -1);
				$columns = split(',[^0-9]', $columns);
				
				$columnList = array();
				
				foreach ($columns as $column) {
					$column = trim($column);
					$columnSplit = explode(" ", $column, 2);
					$columnName = $columnSplit[0];
					$columnType = (sizeof($columnSplit) > 1) ? $columnSplit[1] : "";
					$columnList[] = array($columnName, $columnType);
				}
				
				return $columnList;
			}
		}
	}
	
	/*
		Return names, row counts etc for every database, table and view in a JSON string
	*/
	function getMetadata() {
		$output = '';
		if ($this->conn) {
			if ($this->adapter == "mysql" && version_compare($this->getVersion(), "5.0.0", ">=")) {
				$this->selectDB("information_schema");
				$schemaSql = $this->query("SELECT `SCHEMA_NAME` FROM `SCHEMATA` ORDER BY `SCHEMA_NAME`");
				if ($this->rowCount($schemaSql)) {
					while ($schema = $this->fetchAssoc($schemaSql)) {
						$output .= '{"name": "' . $schema['SCHEMA_NAME'] . '"';
						// other interesting columns: TABLE_TYPE, ENGINE, TABLE_COLUMN and many more
						$tableSql = $this->query("SELECT `TABLE_NAME`, `TABLE_ROWS` FROM `TABLES` WHERE `TABLE_SCHEMA`='" . $schema['SCHEMA_NAME'] . "' ORDER BY `TABLE_NAME`");
						if ($this->rowCount($tableSql)) {
							$output .= ',"items": [';
							while ($table = $this->fetchAssoc($tableSql)) {
								
								if ($schema['SCHEMA_NAME'] == "information_schema") {
									$countSql = $this->query("SELECT COUNT(*) AS `RowCount` FROM `" . $table['TABLE_NAME'] . "`");
									$rowCount = (int)($this->result($countSql, 0, "RowCount"));
								} else {
									$rowCount = (int)($table['TABLE_ROWS']);
								}
								
								$output .= '{"name":"' . $table['TABLE_NAME'] . '","rowcount":' . $rowCount . '},';
							}
							
							if (substr($output, -1) == ",")
								$output = substr($output, 0, -1);
							
							$output .= ']';
						}
						$output .= '},';
					}
					$output = substr($output, 0, -1);
				}
			} else if ($this->adapter == "mysql") {
				$schemaSql = $this->listDatabases();
				
				if ($this->rowCount($schemaSql)) {
					while ($schema = $this->fetchArray($schemaSql)) {
						$output .= '{"name": "' . $schema[0] . '"';
						
						$this->selectDB($schema[0]);
						$tableSql = $this->listTables();
						
						if ($this->rowCount($tableSql)) {
							$output .= ',"items": [';
							while ($table = $this->fetchArray($tableSql)) {
								$countSql = $this->query("SELECT COUNT(*) AS `RowCount` FROM `" . $table[0] . "`");
								$rowCount = (int)($this->result($countSql, 0, "RowCount"));
								$output .= '{"name":"' . $table[0] . '","rowcount":' . $rowCount . '},';
							}
							
							if (substr($output, -1) == ",")
								$output = substr($output, 0, -1);
							
							$output .= ']';
						}
						$output .= '},';
					}
					$output = substr($output, 0, -1);
				}
			} else if ($this->adapter == "sqlite") {
				$output .= '{"name": "' . $this->db . '"';
				
				$tableSql = $this->listTables();
				
				if ($tableSql) {
					$output .= ',"items": [';
					while ($tableRow = $this->fetchArray($tableSql)) {
						$countSql = $this->query("SELECT COUNT(*) AS 'RowCount' FROM '" . $tableRow[0] . "'");
						$rowCount = (int)($this->result($countSql, 0, "RowCount"));
						$output .= '{"name":"' . $tableRow[0] . '","rowcount":' . $rowCount . '},';
					}
					
					if (substr($output, -1) == ",")
						$output = substr($output, 0, -1);
					
					$output .= ']';
				}
				$output .= '}';
			}
		}
		return $output;
	}

	function error() {
		return $this->errorMessage;
	}

}