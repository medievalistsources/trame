<?php

define('LOG', '/home/emi/AdminTrame/log/log.txt');

class LogError{

    public function updateSearch(\Search $subject) {     
       $id = $subject->getIdSite();
       $stato = $subject->getStato();
       $error_message = $subject->getErrorMessage();
       $info = $subject->getInfosite(); 
       switch ($stato[$id]) {
           case 2:
               $type = "Site Error";
               $real_url = $info[$id]['site_url'];
               $error = $error_message[$id];
               $ip = "localhost";
               $this->logMC($type, $ip, $error, $real_url);
               break;
       }
  }
  
    public function updateErrorConn(\conn $conn) { 
       $type = "Connection Error";
       $error = $conn->getconnerror();
       $ip = $conn->getclient();
       $this->logMC($type, $ip, $error);
    }
    
    public function logMC($type, $ip, $error){
        $real_url= @func_get_arg(3) or false;
        $timestamp = date("d F, Y -- G:i");
        $_err = "[".$type."]"."[".$timestamp."]"."[".$ip."]";
        if($real_url){
            $_err .= "[".$real_url."]";
        }
        $_err .= ": ".$error."\n";
        file_put_contents(LOG, $_err, FILE_APPEND | LOCK_EX);
    }  
    
    public function __toString() {
        return "LogError";
    }
}
?>
