<?php

class RequestSimple{
    
    public function __construct($dbs,$freeText) {
       $this->dbs = $dbs;
        $this->freeText = $freeText;
        $this->qtype= "freetext";
    }  
}

?>
