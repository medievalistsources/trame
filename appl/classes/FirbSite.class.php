<?php

abstract class FirbSite extends GetSite{
    
    private function strip_cdata($c){
	if(preg_match("/<\!\[CDATA/", $c)){                         //Prende il contenuto fra CDATA
		$t = preg_split("/(<\!\[CDATA\[|\]\])/", $c);
		$c = $t[1];                                         //preg_split restituisce una matrice di valori
	}
	return $c;
}

    protected function processResponse(){   
        $response = $this->getResponse($this->realUrl);
	if(!empty($response)){
		$i = 0;
                $tsi = preg_split("/(<titleStmt>|<\/titleStmt>)/", $response, -1);
                $item = preg_split("/(<title>|<\/title>)/", $tsi[1], -1);
		if(preg_match("/<msContents>/", $response) || preg_match("/<msContents\/>/", $response)){
			$ts =  preg_split("/(<msContents>|<\/msContents>)/", $response, -1);
			$items = preg_split("/(<msItem n=\"1\">|<\/msItem>)/", $ts[1], -1);
			foreach ($items as $key => $value){
				$t = preg_split("/(<title>|<\/title>)/", $value, -1);
				$tot = sizeof($t);
				if ($tot == 3){
					if ($i < $this->maxnum){  
						list($segnatura, $localizzazione, $url) = $t;
                                                $segnatura = $this->strip_cdata ($segnatura);
                                               	$localizzazione = $this->strip_cdata ($localizzazione);
						$url = $this->strip_cdata ($url);
                                                $segnatura = strip_tags ($segnatura);
						$localizzazione = strip_tags ($localizzazione);
						$url = strip_tags ($url);
                                                $segnatura = $this->setshelf($segnatura);
                                                $this->links[$i]['titolo'] = $localizzazione.$segnatura;
                                                $this->links[$i]['url'] = $url;
					} else {
						break;
					}
					$i++;
				}
			}
			if ($i==0){
                            $this->message = "Nessun elemento trovato";
                            $this->stato = 1;
			}
		} else {
                    $this->message = "Il sito non risponde con il protocollo corretto";
                    $this->stato = 2;
		}
	} else {
            $this->message = "Il sito non risponde";
            $this->stato = 2;
	}
        list($this->elementsFound, $rest) = preg_split("/ /", $item[1], 2);
    }
    
    protected function setshelf($segnatura){
        return '';
    }
}

?>
