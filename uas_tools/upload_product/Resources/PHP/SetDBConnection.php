<?php
// ERRORS
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function SetDBConnection()
{
    return mysqli_connect("localhost", "hub_admin", "", "uas_projects");
}
