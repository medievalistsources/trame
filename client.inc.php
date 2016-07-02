<?php

define("APPLDIR", "/home/fabrizio/PHPProject/Sismel/trameDist/client");
define("CLASSDIR", APPLDIR . "/classes/");
define("INCLUDEDIR", APPLDIR . "/includes/");

define("SERVER","http://localhost/Rest.php");

function __autoload($class_name) {
    include CLASSDIR . $class_name . '.class.php';
}
?>
