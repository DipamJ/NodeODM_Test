<?php
// ERRORS
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// CONNECT TO DB
$servername = "localhost";
$username = "hub_admin";
$password = "UasHtp_Rocks^^7";
$dbname = "uas_projects";


// DB Connection
function SetDBConnection()
{
    return mysqli_connect("localhost", "hub_admin", "UasHtp_Rocks^^7", "uas_projects");
}
?>