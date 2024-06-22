<?php
// File containing System Variables
define("LOCAL_PATH_ROOT", $_SERVER["DOCUMENT_ROOT"]);
require LOCAL_PATH_ROOT . '/uas_tools/system_management/centralized_management.php';

function SetTempFolderLocalPath()
{
    return '/var/www/html/temp/';
}

function SetFolderLocalPath()
{
    return '/var/www/html/uas_data/uploads/products/';
}

function SetFolderHTMLPath()
{
    //return 'https://uashub.tamucc.edu/uas_data/uploads/products/';
    //$header_location = http://bhub.gdslab.org
    return $header_location . 'uas_data/uploads/products/';
}

function SetDisplayHTMLPath()
{
    //return 'https://uashub.tamucc.edu/uas_data/uploads/products/';
    return $header_location . 'uas_data/uploads/products/';
}

?>