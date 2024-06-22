<?php
// File containing System Variables
define("LOCAL_PATH_ROOT", $_SERVER["DOCUMENT_ROOT"]);
require LOCAL_PATH_ROOT . '/uas_tools/system_management/centralized_management.php';

require_once("SetFilePath.php");

function FormatFileName($rawName)
{
    $formattedName = str_replace(' ', '_', $rawName);
    $formattedName = preg_replace('/[^A-Za-z0-9\_]/', '', $formattedName);
    return preg_replace('/_+/', '_', $formattedName);
}

/*
$project = $_GET['project'];
$platform = $_GET['platform'];
$sensor = $_GET['sensor'];
$date = $_GET['date'];
$date = str_replace('/',"-",$date);
$flight = $_GET['flight'];
$type = $_GET['type'];
*/

$project = FormatFileName($_GET['project']);
$platform = FormatFileName($_GET['platform']);
$sensor = FormatFileName($_GET['sensor']);
$date = $_GET['date'];
$date = str_replace('/', "-", $date);
$flight = FormatFileName($_GET['flight']);
$type = $_GET['type'];


$localPath = SetFolderLocalPath() . $project . "/" . $platform . "/" . $sensor . "/" . $date . "/" . $flight . "/" . $type . "/" . $flight . '_' . $type . '.zip';
if (file_exists($localPath)) {
    //echo SetFolderHTMLPath().$project."/".$platform."/".$sensor."/".$date."/".$flight."/".$type."/".$flight.'_'.$type.'.zip';
    //echo str_replace("/var/www/html/", "http://uashub.tamucc.edu/", $localPath);
    // If session hasn't been started, start it
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
//    $header_location = $_SESSION['header_location'];
//    $root_path = $_SESSION['root_path'];
    echo str_replace($root_path, $header_location, $localPath);
    //$row["DownloadPath"] = str_replace("/var/www/html/wordpress/", "http://basfhub.gdslab.org/", $row["UploadFolder"]."/".$row["FileName"]);
} else {
    echo "";
}
