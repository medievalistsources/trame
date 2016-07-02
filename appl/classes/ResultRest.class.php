<?php

class ResultRest {
    private $links;
    public function updateSearch(\Search $s) {
        $this->links = $s->getLinks();
    }
    
    public function getLinks() {
        return $this->links;
    }
}

?>
