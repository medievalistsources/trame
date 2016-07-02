<?php

class Result {

    protected $bar;
    protected $dbs;
    protected $i = 0;
    protected $totalsites;
    protected $percentbar;

    public function __construct($bar, $dbs) {
        $this->bar = $bar;
        $this->dbs = $dbs;
        $this->totalsites = count($this->dbs);
        $this->percentbar = 100 / $this->totalsites;
    }

    private function getTipo($cosa) {
        if ($cosa == "D")
            return " (Digital Object)";
        else {
            return "";
        }
    }

    public function updateSearch(\Search $subject) {
        $message = "";
        $id = $subject->getIdSite();
        $stato = $subject->getStato();
        $error_message = $subject->getErrorMessage();
        $links = $subject->getLinks();
        $info = $subject->getInfosite();
        $otherResult = $subject->getOtherResult();
        $this->i++;
        $percent = $this->i * ($this->percentbar);
        $this->bar->setProgressBarProgress($percent);

        if ($stato[$id] == 0) {
            $results = "";
            $site_info = "<h3 class=\"repo\"><a class=\"drop-down\" id=\"#repo" . $info[$id]['site_short_name'] . "" . $info[$id]['site_id']."\" onclick=\"javascript: $('#" . $info[$id]['site_short_name'] . "" . $info[$id]['site_id'] . "').toggle(), 
                rotatePlusMinus(img_" . $info[$id]['site_short_name'] . "" . $info[$id]['site_id'] . ");\"><img name=\"img_" . $info[$id]['site_short_name'] . "" . $info[$id]['site_id'] . "\" 
                src=\"images/meno.png\" value=\"piu\" alt=\"\" class=\"drop-image\" /></a> <a target=\"" . $info[$id]['site_id'] . "\" href=\"" . $info[$id]['site_url'] . "\">" . $info[$id]['site_name'] . "</a></h3>";
            foreach ($links[$id] as $link) {
                if(!isset($link['tipo']))
                    $link['tipo'] = "";
                // $message .= "<li><a target=\"".$id."\" href=\"".$link['url']."\">".$link['titolo']."</a></li>";
                $message .= "<li><a class=\"source\" target=\"" . $id . "\" href=\"" . $link['url'] ."\"  onclick=\"click_rec(this.target,this.innerHTML,this.href);\" \">" . $link['titolo'] .
                        $this->getTipo($link['tipo']) . "</a></li>";
            }
            $results .= $site_info;
            $results .= "<div id=\"" . $info[$id]['site_short_name'] . "" . $info[$id]['site_id'] . "\">";
            $results .= "<ul>$message</ul>";
            if ($otherResult[$id] != "") {
                $results .= "<br/><a href=\"" . $otherResult[$id] . "\" target=\"_blank\">Other results on the remote site</a><br/>";
            }
            $results .= "</div><br/><br/>";
            if(count($links[$id]) > 0)
                print $results;
        } else if ($stato[$id] == 1) {
            $results = "";
//            $site_info = "<h3><a class=\"drop-down\" onclick=\"javascript: $('#". $info[$id]['site_short_name'] ."".$info[$id]['site_id'] . "').toggle(), 
//                rotatePlusMinus(img_".$info[$id]['site_short_name']."".$info[$id]['site_id'].");\"><img name=\"img_". $info[$id]['site_short_name']."".$info[$id]['site_id']."\" 
//                src=\"images/meno.png\" value=\"piu\" alt=\"\" class=\"drop-image\" /></a> <a target=\"".$info[$id]['site_id']."\" href=\"".$info[$id]['site_url']."\">".$info[$id]['site_name']."</a></h3>";
//            $results .= $site_info;
//            $results .= "<div id=\"".$info[$id]['site_short_name']."".$info[$id]['site_id']."\">";  
//            $results .= "Non ci sono risultati per questo sito";         
//            $results .= "</div><br/><br/>";   
//            print $results;
        } else if ($stato[$id] == 2) {
            $results = "";
//            $site_info = "<h3><a class=\"drop-down\" onclick=\"javascript: $('#". $info[$id]['site_short_name'] ."".$info[$id]['site_id'] . "').toggle(), 
//                rotatePlusMinus(img_".$info[$id]['site_short_name']."".$info[$id]['site_id'].");\"><img name=\"img_". $info[$id]['site_short_name']."".$info[$id]['site_id']."\" 
//                src=\"images/meno.png\" value=\"piu\" alt=\"\" class=\"drop-image\" /></a> <a target=\"".$info[$id]['site_id']."\" href=\"".$info[$id]['site_url']."\">".$info[$id]['site_name']."</a></h3>";
//            $results .= $site_info;
//            $results .= "<div id=\"".$info[$id]['site_short_name']."".$info[$id]['site_id']."\">";  
//            $results .= "Il sito non risponde";         
//            $results .= "</div><br/><br/>";   
//            print $results;
        }
    }

}

?>
