<?php

define("APPLDIR", "/home/fabrizio/PHPProject/Sismel/trameDist/appl");
define("CLASSDIR", APPLDIR . "/classes/");
define("INCLUDEDIR", APPLDIR . "/includes/");
define('DB_NAME', 'tramenew');
define('DB_USER', 'trame');
define('DB_PASSWORD', 'pw.trame.2011');

define('LISTCLASSES', 'http://git-trame.fefonlus.it/OntoService/GetService?verb=ListClasses&format=json');
define('PROPERTIESCLASS', 'http://git-trame.fefonlus.it/OntoService/GetService?verb=ListClassProperties&format=json&class=');

define('DB_HOST', 'localhost');
define('LOGIN_STRING', 'mysql:host=localhost;database=firb;method=mysql');

function __autoload($class_name) {
    include CLASSDIR . $class_name . '.class.php';
}
function _getMessage() {
    $mess = file_get_contents("notice.dat");
    return $mess;
}
?>
