<?php

// File containing System Variables
define("LOCAL_PATH_ROOT", $_SERVER["DOCUMENT_ROOT"]);
require LOCAL_PATH_ROOT . '/uas_tools/system_management/centralized_management.php';

//_log("header_location: " . $header_location);

//Set path for input directory
function SetGeoJsonFolderPath()
{
    //return "/var/www/html/wordpress/temp/";
    return "/var/www/html/temp/";
}

function SetImageFolderLocalPath()
{
    //return "/var/www/html/temp/";
    return "/var/www/html/temp/PlotBoundary";
}

function SetImageFolderHTMLPath()
{
    //return "http://uashub.tamucc.edu/temp/";
    //return "http://bhub.gdslab.org/temp/PlotBoundary";
    return $header_location . "/temp/PlotBoundary";
}

?>