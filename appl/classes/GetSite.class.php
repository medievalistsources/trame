<?php

abstract class GetSite extends Site {

    public function getResponse($url) {

 //       $userAgent = "Mozilla/5.0 (compatible; P-FERB - Jama Musse Jama) ver. 1.0 2011";
        $userAgent = "Mozilla/5.0 (compatible) ver. 1.0 2011";
        $ch = curl_init($url);                                      //crea una nuova risorsa curl passandogli l'url

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION , true);            //TRUE per eseguire le redirect eventuali
        curl_setopt($ch, CURLOPT_TIMEOUT , 30);
        curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);            //FALSE per fermare CURL dal verificare i certificati dei peer
        curl_setopt($ch, CURLOPT_HEADER, 0);                        //TRUE per includere l'intestazione in output [No]
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);             //TRUE per restituire il trasferimento come una stringa del valore restituito di curl_exec()
                                                                    //al posto di metterlo in output direttamente.
        $response = curl_exec($ch);                                 //scarica l'URL e lo passa a $response
        if(curl_error($ch) != ""){                                  //log
            $this->message_curl = curl_error($ch);
        }
        curl_close($ch);                                            //chiude la risorsa curl e rilascia le risorse di sistema
        return $response;
    }
}

?>
