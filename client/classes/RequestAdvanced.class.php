<?php

class RequestAdvanced {

    public function __construct($dbs, $author, $title) {
        $this->dbs = $dbs;
        $this->author = $author;
        $this->title = $title;
        $this->qtype = "advanced";
    }

}

?>
