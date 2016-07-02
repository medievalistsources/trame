<?php

class conn {
    
    protected $observers = array();
    protected $_error = "";
    protected $conn;
    protected $loginstring;
    protected $user;
    protected $pass;
    protected $dbname;
    protected $ipclient;


    public function __construct($loginstring, $user, $pass, $dbname){
        $this->loginstring = $loginstring;
        $this->user = $user;
        $this->pass = $pass;
        $this->dbname = $dbname;
        $this->ipclient = $_SERVER['REMOTE_ADDR'];
                
    }
    
    public function getconn(){
        $this->conn = new SQL($this->loginstring, $this->user, $this->pass);
        $this->conn->selectDB($this->dbname); 
        if ($this->conn == false){
           // notifica in caso di errore
           $this->_error .= "Errore in connessione al DBMS";
           $this->conn = null;
           $this->notify();
           return $this->conn;
        } else {
            return $this->conn;
        }
    }
    
    public function getconnerror(){
        return $this->_error;
    }
    
    public function dbclose(){
        $this->conn->disconnect(); 
    }
    
    public function getclient(){
        return $this->ipclient;
    }
    
    public function attach($obs) {
        $this->observers[] = $obs;
    }
    
    public function detach($obs) {
        $this->observers = array_diff($this->observers, array($obs));
    }
    
    public function notify() {
        foreach($this->observers as $observer) {
            $observer->updateErrorConn($this);
        }
    }
    
}

?>
