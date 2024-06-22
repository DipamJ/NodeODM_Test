<?php
// here read a variable

define("LOCAL_PATH_ROOT", $_SERVER["DOCUMENT_ROOT"]);
echo LOCAL_PATH_ROOT;
//define('ROOT', dirname(__FILE__));
require LOCAL_PATH_ROOT . '/testing/new2.php';

echo "<br>";
echo($var1);
echo "<br>";
echo($var2);


define("LOCAL_PATH_BOOTSTRAP", __DIR__);
echo(LOCAL_PATH_BOOTSTRAP);

?>