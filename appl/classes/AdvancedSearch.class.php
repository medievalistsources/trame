<?php

class AdvancedSearch extends Search{
    
    
    //private $f1;                    //Campo Title 
    //private $f2;                    //Campo Author 
    //private $f3;                    //Campo Incipit 
    //private $f4;                    //Campo Dates 
    //private $f41;                   //Campo 'from:' 
    //private $f42;                   //Campo 'to:' 
    //private $f5;                    //Campo Place 
    //private $f7;                    //Campo Copyst 
    
    
    public function __construct($conn, $par) {
        parent::__construct($conn, $par);
            //$this->f1 = $this->$par['Field1'];
            //$this->f2 = $this->$par['Field2'];
            //$this->f3 = $this->$par['Field3'];
            //$this->f4 = $this->$par['Field4'];
            //$this->f41 = $this->$par['Field41'];
            //$this->f42 = $this->$par['Field42'];
            //$this->f5 = $this->$par['Field5'];
            //$this->f7 = $this->$par['Field7'];
            
    }

    
    public function visit(\Site $site) {  
        $this->typesearch = 2;
        $site->setTypeSearch($this->typesearch);
        $site->startsearch();
    }

    public function accept(\Site $site, $search) { 
        $site->acceptsite($search);
    }
    
    
}

?>
