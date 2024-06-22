<?php
// File containing System Variables
define("LOCAL_PATH_ROOT", $_SERVER["DOCUMENT_ROOT"]);
require LOCAL_PATH_ROOT . '/uas_tools/system_management/centralized_management.php';

function SetTempFolderLocalPath()
{
    //return '/var/www/html/temp/CropData/';
    return $header_location . '/temp/CropData/';
}

?>