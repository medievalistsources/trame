<?php

abstract class PostSite extends Site{
    private $queryString;
    
    public function getResponse($url) { 

        list($urlbase, $urlquery) = preg_split('/\?/', $url);               //Recupero urlbase e i parametri      
        $query = explode("&", $urlquery);                                   //Costruisco array dei campi
        $ch = curl_init();                                                  //Crea una nuova risorsa curl passandogli l'url
        $this->queryString = "";
        foreach ($query as $key => $value) {                            
            $this->queryString .=  $value . '&';
        }
        $this->queryString = rtrim($this->queryString, '&');                //Preparo $queryString
   
        curl_setopt($ch, CURLOPT_TIMEOUT , 80);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);                    //FALSE per fermare CURL dal verificare i certificati dei peer 
        curl_setopt($ch, CURLOPT_URL, $urlbase);                            // URL da raggiungere
        curl_setopt($ch, CURLOPT_HEADER, 0); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, count($query));                      //TRUE per compiere un regolare HTTP POST, in base al risultato di count()
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->queryString);           //I dati completi da mandare in post in un'operazione HTTP POST
        $response = curl_exec($ch);
        if(curl_error($ch) != ""){                                          //log
            $this->message_curl = curl_error($ch);                     
        }    
        curl_close($ch);
        return $response;
    }
}

?>
