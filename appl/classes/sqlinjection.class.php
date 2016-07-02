<?php
class sqlinjection{
   
    public function control($conn, $string){
        $stringescaped = $conn->escapeString($string);
        return $stringescaped;
    }
    
    public function controlall($conn, $arraystring){
        $arrayescaped = array();
        foreach ($arraystring as $ad=>$valore) {
            $arrayescaped[$ad] = $conn->escapeString($valore);
        }
        return $arrayescaped;
    }
    
}
?>
