<?php

abstract class CachedSite extends Site{
    protected $ff = "";
    
   public function __construct($conn, $par) {
        parent::__construct($conn, $par);
        $inj = new sqlinjection();
        $par = $inj->controlall($this->conn, $par);
        $this->par['Field6'] = "";
        if (strlen($this->par['Field6_loc'])>0){
        $this->par['Field6'] .= $this->par['Field6_loc'] . " ";
        }
        if (strlen($this->par['Field6_lib'])>0){
        $this->par['Field6'] .= $this->par['Field6_lib'] . " ";
        }
        if (strlen($this->par['Field6_hold'])>0){
        $this->par['Field6'] .= $this->par['Field6_hold'] . " ";
        }
        if (strlen($this->par['Field6_shelf'])>0){
        $this->par['Field6'] .= $this->par['Field6_shelf'] . " ";
        }
        $this->par['Field6'] = trim($this->par['Field6'], " ");
        
        $this->ff = $this->par['Field1']." ".$this->par['Field2']." ".$this->par['Field3']." ".$this->par['Field4']." ".$this->par['Field5']." ".$this->par['Field41']." ".$this->par['Field42']." ".$this->par['Field5']." ".$this->par['Field6']." ".$this->par['Field7'];
    }
   
    protected function processResponse() {
        $r = (strlen($this->par['Field0'])>0 ? 'citta,biblioteca,segnatura,fondo,autori,titoli,search_all' : '');
        $w = (strlen($this->par['Field0'])>0 ? $this->build_and_match('citta,biblioteca,segnatura,fondo,autori,titoli,search_all', $this->par['Field0']) : "");
           
           if (strlen($this->par['Field1'])>0) {
                $r .= ($r ? "," : "") . 'titoli';
                $w .= ($w ? " AND " : "") . $this->build_and_match('titoli', $this->par['Field1']);
            }
            if (strlen($this->par['Field2'])>0) {
                $r .= ($r ? "," : "") . 'autori';
                $w .= ($w ? " AND " : "") . $this->build_and_match('autori', $this->par['Field2']);
            }
            if (strlen($this->par['Field3'])>0) {
                $r .= ($r ? "," : "") . 'incipit';
                $w .= ($w ? " AND " : "") . $this->build_and_match('incipit', $this->par['Field3']);
            }
            if (strlen($this->par['Field41'])>0) {
                $w .= ($w ? " AND " : "") . " date_from >= " . $this->par['Field41'];
            }
            if (strlen($this->par['Field42'])>0) {
                $w .= ($w ? " AND " : "") . " date_to <= " . $this->par['Field42'];
            }
            if (strlen($this->par['Field6'])>0) {
                $w .= ($w ? " AND " : "") . $this->build_and_match('citta,biblioteca,segnatura,fondo', $this->par['Field6']);
                $r .= ($r ? "," : "") . 'citta,biblioteca,segnatura,fondo';
            }
            if (strlen($this->par['Field5'])>0) {
                $r .= ($r ? "," : "") . 'localizzazione';
                $w .= ($w ? " AND " : "") . $this->build_and_match('localizzazione', $this->par['Field5']);
            }
            if (strlen($this->par['Field7'])>0) {
                $w .= ($w ? " AND " : "") . $this->build_and_match('copista', $this->par['Field7']);
                $r .= ($r ? "," : "") . 'copista';
            }
            if ($w) {
                $sql = "select *," . $this->buildRelevant($r, $this->ff) . " as relevant, citta, biblioteca, segnatura, fondo, mssurl, titoli from " . $this->row['cached_dbname'] . " where $w";
                $manSql = $this->conn->query($sql);

                if(mysql_error() == ""){                                        //Errori mysql
                    if ($this->conn->isResultSet($manSql)) {
                    $i = 0;
                    while ($i <= $this->maxnum && $manRow = $this->conn->fetchAssoc($manSql)) {
                        $citta = $manRow['citta'];
                        $biblioteca = $manRow['biblioteca'];
                        $fondo = $manRow['fondo'];
                        $segnatura = $manRow['segnatura'];
                        $titoli = $manRow['titoli'];
                        $manoscritto = $citta;
                        if ($citta) {
                            $manoscritto .= ($manoscritto ? ", " : "") . $citta;
                        }
                        if ($biblioteca) {
                            $manoscritto .= ($manoscritto ? ", " : "") . $biblioteca;
                        }
                        if ($fondo) {
                            $manoscritto .= ($manoscritto ? ", " : "") . $fondo;
                        }
                        if (!$manoscritto && $titoli) {
                            $manoscritto = $titoli;
                        }
                        if ($segnatura) {
                            $manoscritto .= ($manoscritto ? ", " : "") . $segnatura;
                        }
                        $mssurl = $manRow['mssurl'];
                        if (strncmp("http://", $mssurl, 7)) {
                            $mssurl = $this->realUrl . $mssurl;
                        }
                        $this->links[$i]['titolo'] = $manoscritto;
                        $this->links[$i]['url'] = $mssurl;
                        $i ++;
                    }
                    $this->elementsFound = $i;
                    } else {
                        $this->message .= "Nessun elemento individuato";
                        $this->stato = 1;
                    } 
                 } else {
                    $this->message .= "Problemi di connessione al database sql";
                    $this->stato = 2;
                 } 
            } else {
                $this->message .= "Query errato. Controllare i parametri di ricerca";
                $this->stato = 2;
            }
    }
    
    private function build_and_match($f, $v) {
        $v = preg_replace("/'/", " ", $v);
        $v = preg_replace("/\s+/", " +", $v);
        $r = "match($f) against ('+$v' IN BOOLEAN MODE)";
        return $r;
    }
    
    private function build_or_match($f, $v) {
        $v = preg_replace("/'/", " ", $v);
        $v = trim(preg_replace("/\s+/", " ", $v));
        $r = "match($f) against ('$v' IN BOOLEAN MODE)";
        return $r;
    }

    private function buildRelevant($f, $v) {
        return ($f ? $this->build_or_match($f, $v) : 1);
    }
    
}

?>
