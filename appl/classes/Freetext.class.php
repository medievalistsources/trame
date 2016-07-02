<?php



class Freetext extends Search{
    
    //private $f0;                                    //Campo Freetext
    
    public function __construct($conn, $par) {
        parent::__construct($conn, $par);
        //$this->f0 = $this->par['Field0']; 
    }

    public function visit(\Site $site) { 
        $this->typesearch = 0; 
        $site->setTypeSearch($this->typesearch);
        $site->startsearch();
    }

    public function accept(\Site $site, $search) { 
        $site->acceptsite($search);
    }

    
}

?>
