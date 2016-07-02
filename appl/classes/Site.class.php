<?php

abstract class Site {
    
    protected $id;                                  //ID specifico del sito
    protected $par;                                 //Parametri passati al sito
    protected $conn;                                //Dati Connessione Data Base
    protected $typesearch;                          //Tipo di ricerca da effettuare
    protected $searchurl;                           //Tipo di ricerca url
    protected $searchuser;                          //Tipo di ricerca user
    protected $maxnum;                              //Numero di righe massime
    protected $row;                                 //Tutte le info sul sito
    protected $realUrl;                             //Url pronta
    protected $elementsFound = 0;                   //Numero di elementi trovati
    protected $results;                             //Risultati del sito
    protected $message;                             //Messaggio di errore dal sito
    protected $message_curl;                        //Messaggio errore del curl
    protected $stato = 0;                           //Stato sulle risposte o richieste al sito
    protected $otherResultSite = "";                //Link per ulteriori risultati
    protected $links;                               //Array di titolo e url di ogni risultato dal sito
    
    /* *****************************
     * function __construct (by .......)
     * 
     * 9/14 Modificata da Alfredo Cosco
     * 
     * Si agiunge uno switch per la retrocompatibilità,
     * le configurazioni dei siti non necessitano più dell'id
     * ma con lìif non è necessario modificare tutti i vecchi files
     * Vedi dettagli alla funzione getSiteIdByClassname()
     * *****************************/       
    public function __construct($conn, $par){                                 
        $this->maxnum = $par['maxnum'];
        $this->par = $par;
        $this->conn = $conn;
        
        //if aggiunto per retrocompatibilità
        if(!isset($this->id)){
			$this->id=$this->getSiteIdByClassname();
			}
			
        $this->row = $this->getSiteData($this->id);
    }
    
    /* *****************************
     * function getSiteIdByClassname()
     * 
     * 9/14 Aggiunta da Alfredo Cosco
     * 
     * Se si rispetta l'identità tra nome della classe e 
     * la colonna site_short_name nel DB non è necessario indicare 
     * l'id staticamente nel parser del sito.
     * Si stabilisce una convenzione in luogo di una configurazione. 
     * *****************************/  
    protected function getSiteIdByClassname(){
	$this->conn->selectDB("tramenew");
	$query=$this->conn->query("SELECT `site_id` FROM  `sites` WHERE  `site_short_name` =  '".get_class($this)."'");
	if ($this->conn->isResultSet($query)) {
		while ($sid = $this->conn->fetchAssoc($query)) {
			return $sid['site_id'];	
			}
		}
	}
    
    public function setTypeSearch($typesearch){
        $this->typesearch = $typesearch;
        switch ($typesearch) {
            case 0:
                $this->searchurl = 'url_search_simple';
                $this->searchuser = 'url_user_simple';
                break;
            case 1:
                $this->searchurl = 'url_search_shelf';
                $this->searchuser = 'url_user_shelf';
                break;
            case 2:
                $this->searchurl = 'url_search_advanced';
                $this->searchuser = 'url_user_advanced';
                break;
        }
    }

    public function acceptsite(\Search $search) {                               //Visitor 
        $visitID = @func_get_arg(1) or false;
        if (!$visitID){
            $search->visit($this);
        } else {
            $search->$visitID($this);
        }
    }
    
    protected abstract function processResponse(); 
    
    protected function getdate($iddata){
        $d=array(
            '1'=>array(
                'from'=>501,
                'to'=>600
            ),
            '2'=>array(
                'from'=>601,
                'to'=>700
            ),
            '3'=>array(
                'from'=>701,
                'to'=>800
            ),
            '4'=>array(
                'from'=>801,
                'to'=>900
            ),
            '5'=>array(
                'from'=>901,
                'to'=>1000
            ),
            '6'=>array(
                'from'=>1001,
                'to'=>1100
            ),
            '7'=>array(
                'from'=>1101,
                'to'=>1200
            ),
            '8'=>array(
                'from'=>1201,
                'to'=>1300
            ),
            '9'=>array(
                'from'=>1301,
                'to'=>1400
            ), 
            '10'=>array(
                'from'=>1401,
                'to'=>1500
            ), 
            '11'=>array(
                'from'=>1501,
                'to'=>1600
            ) 
        );   
        $from_to = $d[$iddata];  
        return $from_to;
    }
    
    protected function setdate(){
        $ft = $this->getdate($_REQUEST['Field4']);
        $this->par['Field41'] = $ft['from'];
        $this->par['Field42'] = $ft['to'];
    }


    public function startSearch(){                                              //Inizia ricerca   
        if(isset($this->par['Field4']) && $this->par['Field4'] != ''){
            $this->setdate();
        }
        $this->realUrl = $this->createUrl($this->row[$this->searchurl]); 
        $this->processResponse();
        /*
         * Edidted by AC 9/14: se i campi url_user_* NON contengono un link
         * l'url per il rendirizzamento a più risultati è preso dai relativi campi url_search*
         */
        $url= (strstr($this->createUrl($this->row[$this->searchuser]),"http")) ? $this->createUrl($this->row[$this->searchuser]) : $this->realUrl;         
        $this->otherResultSite = ($this->elementsFound > $this->maxnum ? $url : "");        
    }
    
    protected function getSiteData($id) {                                       //Seleziona tutte le info del site_id = $id
        $query = "select * from sites where site_id = " . $id;
        $result = $this->conn->query($query);                                   //Risultato della query
        $rows = $this->conn->fetchAssoc($result);                               //Ritorna array associativo del risultato della query
        return $rows;
    }

    protected function getCampi($url) {                                         //Estrae i campi dall'url
        $pattern = "/%Field(.*?)%/s";
        preg_match_all($pattern, $url, $campi);
        return $campi[0];                                                       //Array di array i campi sono all'indirizzo 0
    }

    protected function createUrl($url) {                                        //Crea l'url vera verso il sito da interrogare
        $arrayCampi = $this->getCampi($url);                                    //Array campi da settare 
        foreach ($arrayCampi as $campo) {
            $nomeCampo = str_replace("%", "", $campo);
            $this->par[$nomeCampo] = urlencode($this->par[$nomeCampo]);         //Urlencode dei campi
            $url = str_replace($campo, $this->par[$nomeCampo], $url);       
        }
        $url = preg_replace('/\%maxnum\%/', $this->maxnum, $url);               //Se campo maxnum non c'è restituisce url inalterato
        return $url;
    }
    
    public function getID() {                                                   //ID specifico del sito
        return $this->id;
    }
   
    public function getstatosite(){
        return $this->stato;
    }
    
    public function getmessite(){
        return $this->message;
    }
    
    public function getInfoSite(){
        return $this->row;
    }
    
    public function getElementsFound(){
        return $this->elementsFound;
    }
    
    public function getOtherResultSite(){
         return $this->otherResultSite;
    }
    
    public function getlinksite(){
        return $this->links;
    }
}

?>
