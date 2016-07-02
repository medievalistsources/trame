<?php

class Ontology {
    
    private $_error;
    private $elementsFound;
    private $state = 0;
    
    public function __construct() {
    }
    
    private function getResponse($url) {     
        $ch = curl_init($url);                                
        curl_setopt($ch, CURLOPT_TIMEOUT , 30);
        curl_setopt($ch, CURLOPT_HEADER, 0);                        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);             
	echo $url . "<br/>" ;
        $response = curl_exec($ch);                                
        if(curl_error($ch) != ""){                                 
            $this->_error = curl_error($ch);                     
        }    
        curl_close($ch);                                            
        return $response;
    }
    
    public function getError(){
        return $this->_error;
    }
    
    public function getState(){
        return $this->state;
    }
    
    public function getClassLink(){
        $links = array();
        $response = $this->getResponse(LISTCLASSES);
	if(!empty($response)){
		if(preg_match_all("/\"label\":\"(.*?)\"/", $response, $labels)){
                    preg_match_all("/\"uri\":\"(.*?)\"/", $response, $uri);
                    $this->elementsFound = count($labels[1]);
                    for($i = 0; $i < $this->elementsFound; $i++) {
                        $links[$i]['label'] = $labels[1][$i];
                        $links[$i]['uri'] = $uri[1][$i];
                    }        
		} else {
			$this->message = "Nessun elemento trovato";
			$this->state = 1;
		}
	} else {
		$this->message = "Il sito non responde.";
		$this->state = 2;
	}
        return $links;
    }
    
    public function getInputForm($uri){
        $links = array();
	$uri = str_replace('#','%23',$uri);
        $url = PROPERTIESCLASS . $uri;
        $response = $this->getResponse($url);
	if(!empty($response)){
            $i = 0;
            $obj = preg_split("/({|})/", $response, -1);
            foreach ($obj as $el) {
                if(preg_match("/\"uri\"(.*?)\"type\":\"owl:DatatypeProperty\"/", $el)){
                   $this->elementsFound++; 
                   preg_match_all("/\"uri\":\"(.*?)\"/", $el, $uri);
                   $links[$i]['uri'] = $uri[1][0];
                   preg_match_all("/\"label\":\"(.*?)\"/", $el, $labels);
                   $links[$i]['label'] = $labels[1][0];
                   $i++;
                   } 
            }
            if ($this->elementsFound == 0){
                    $this->message = "Nessun elemento trovato";
                    $this->state = 1;
            }
	} else {
		$this->message = "Il sito non responde.";
		$this->state = 2;
	}
        return $links;
    }
    
    public function getLabel($uri){
        $response = $this->getResponse(LISTCLASSES);
	if(!empty($response)){
            $pattern = "/\"uri\":\"".$uri."\"/";
            $obj = preg_split("/({|})/", $response, -1);
            foreach ($obj as $el) {
                if(preg_match($pattern, $el)){           
                   preg_match_all("/\"label\":\"(.*?)\"/", $el, $labels);
                   $label = $labels[1][0];
                   } 
            }      
        } else {
                $this->message = "Il sito non responde.";
                $this->state = 2;
        }
        return $label;     
    }
    
    public function getContieneLink($uri){
        $links = array();
	$uri = str_replace('#','%23',$uri);
        $url = PROPERTIESCLASS . $uri;
        $response = $this->getResponse($url);
	if(!empty($response)){
            $i = 0;
            $obj = preg_split("/({|})/", $response, -1);
            foreach ($obj as $el) {
                if(preg_match("/\"type\":\"owl:ObjectProperty\"/", $el)){
                   $this->elementsFound++; 
                   preg_match_all("/\"uri\":\"(.*?)\"/", $el, $uri);
                   $links[$i]['uri'] = $uri[1][0];
                   preg_match_all("/\"label\":\"(.*?)\"/", $el, $labels);
                   $links[$i]['label'] = $labels[1][0];
                   preg_match_all("/\"range\":\"(.*?)\"/", $el, $ranges);
                   $links[$i]['labelrange'] = str_replace("sismel:", "", $ranges[1][0]);
                   $links[$i]['range'] = $ranges[1][0];
                   $i++;
                   } 
            }
            if ($this->elementsFound == 0){
                    $this->message = "Nessun elemento trovato";
                    $this->state = 1;
            }
	} else {
		$this->message = "Il sito non responde.";
		$this->state = 2;
	}
        return $links;
    }
   
}
?>
