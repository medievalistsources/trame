<?php

class Shelfmark extends Search{
    
    //private $f6_loc;                //Campo City
    //private $f6_lib;                //Campo Library
    //private $f6_hold;               //Campo Holding
    //private $f6_shelf;              //Campo Shelfmark
    
    public function __construct($conn, $par) {
        parent::__construct($conn, $par);
            //$this->f6_loc = $this->par['Field6_loc'];
            //$this->f6_lib = $this->par['Field6_lib'];
            //$this->f6_hold = $this->par['Field6_hold'];
            //$this->f6_shelf = $this->par['Field6_shelf'];
         
            //Gestione campo Field6
    }

    public function visit(\Site $site) {
        $this->typesearch = 1;
        $site->setTypeSearch($this->typesearch);
        $site->startsearch();
    }

    public function accept(\Site $site, $search) { 
        $site->acceptsite($search);
    }
    
    
}

?>
