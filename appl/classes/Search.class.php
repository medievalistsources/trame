<?php

abstract class Search{
    
    protected $observers;
    protected $par;                                             //Parametri da passare
    protected $conn;                                            //Dati Connessione Data Base
    protected $dbs;                                             //DataBase Siti
    protected $typesearch;                                      //Tipo di ricerca da effettuare sul sito
    protected $stato;                                           //Array associativo del risultato/errore dei siti
    protected $error_message;                                   //Array associativo dell'errore dei siti
    protected $infosite;                                        //Array informazioni dei siti
    protected $elementsfound;                                   //Array numero elementi di ogni sito
    protected $otherResultSite;                                 //Array link per altri risultati dei siti
    protected $links;                                           //Array dei links dei risultati dei siti
    protected $visitID;                                         //Trattamento particolare visitor
    protected $idsite;                                          //Id attuale del sito in ricerca
    
    
    public function __construct($conn,$par) {
        
        $this->par = $par;
        $this->conn = $conn;    
        $this->dbs = explode("|",$par['dbs']);     
    }   
    
    public function StartSearch(){     
        foreach ($this->dbs as $id){
            $query = "select site_class from sites where site_id = " . $id;
            $result = $this->conn->query($query);
            $nameSite = $this->conn->fetchAssoc($result);
            $Site = new $nameSite['site_class']($this->conn, $this->par);
            $this->visitID = "visit" . $id;
            $this->accept($Site, $this);
            $this->stato[$id] = $Site->getstatosite();
            $this->error_message[$id] = $Site->getmessite();
            $this->infosite[$id] = $Site->getInfoSite();
            $this->elementsfound[$id] = $Site->getElementsFound();
            $this->links[$id] = $Site->getlinksite();
            $this->otherResultSite[$id] = $Site->getOtherResultSite();
            $this->idsite = $id;
            $this->notify();
        }   
    }
    
    public abstract function accept(\Site $site, $search);
    public abstract function visit(\Site $site);
    
    public function attach($obs) {
        $this->observers[] = $obs;
    }
    
    public function detach($obs) {
        foreach ($this->observers as $osserv) {
            if($osserv === $obs)
                unset($osserv);
        }
       // $this->observers = \array_diff($this->observers, array($obs));
    }
    
    public function notify() {
        foreach($this->observers as $observer) {
            $observer->updateSearch($this);
        }
    }
    
    public function getStato(){
        return $this->stato;
    }
    
    public function getLinks(){
        return $this->links;
    }
    
    public function getErrorMessage(){
        return $this->error_message;
    }
    
    public function getInfosite(){
        return $this->infosite;
    }
    
    public function getElementsFound(){
        return $this->elementsfound;
    }
    
    public function getIdSite(){
        return $this->idsite;
    }
    
    public function getOtherResult(){
        return $this->otherResultSite;
    }
    
    public function getdbs(){
        return $this->dbs;
    }
}

?>
