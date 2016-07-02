<?php

class Client {

    private $response;
    private $url;
    private $body;
    private $status;
    private $message;

    public function __construct($url) {
        $this->url = $url;
    }
    
    public function setBody($body) {
        $this->body = $body;
    }
    
    public function connect(){
        $this->status = 1;
        $queryString = "q=" . $this->body;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT , 80);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);                    //FALSE per fermare CURL dal verificare i certificati dei peer 
        curl_setopt($ch, CURLOPT_URL, $this->url);                            // URL da raggiungere
        curl_setopt($ch, CURLOPT_HEADER, 0); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);                      //TRUE per compiere un regolare HTTP POST, in base al risultato di count()
        curl_setopt($ch, CURLOPT_POSTFIELDS, $queryString);           //I dati completi da mandare in post in un'operazione HTTP POST
        $this->response = curl_exec($ch);
        if(curl_error($ch) != ""){                                          //log
            $this->message = curl_error($ch); 
            $this->status = 0;
        }    
        curl_close($ch);
    }
    
    public function getStatus() {
        return $this->status;
    }
    
    public function getResponse() {
        return $this->response;
    }
    public function getMessage() {
        return $this->message;
    }
}

?>
