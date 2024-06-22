<?php
// ERRORS
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define("HOST", "localhost");
define("DB_USER", "hub_admin");
define("DB_PASS", "UasHtp_Rocks^^7");
define("DB_NAME", "uas_projects");

/*
$conn = mysqli_connect(HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
        die(mysqli_error());
}	*/

function SetDBConnection()
{
    //return mysqli_connect("127.0.0.1","hub_admin","UasHtp_Rocks^^7","uas_projects");
    //return mysqli_connect("localhost","hub_admin","UasHtp_Rocks^^7","uas_projects");
    return mysqli_connect(HOST, DB_USER, DB_PASS, DB_NAME);

}

?>
