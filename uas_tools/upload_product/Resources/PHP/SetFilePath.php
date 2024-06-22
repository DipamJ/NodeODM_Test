<?php
// File containing System Variables
//define("LOCAL_PATH_ROOT", $_SERVER["DOCUMENT_ROOT"]);
if (!defined("LOCAL_PATH_ROOT")) define("LOCAL_PATH_ROOT", $_SERVER["DOCUMENT_ROOT"]);
require LOCAL_PATH_ROOT . '/uas_tools/system_management/centralized_management.php';

function SetTempFolderLocalPath()// TEMPORARY DIRECTORY
{
    // return '/var/www/wtxcotton.uashubs.com/uashub/web/uas_data/temp/';
    return '/var/www/html/uas_data/temp/';
}

function SetFolderLocalPath()// PROJECT FOLDER
{
    // return '/var/www/wtxcotton.uashubs.com/uashub/web/uas_data/uploads/products/';
    return '/var/www/html/uas_data/uploads/products/';
}

function SetFolderHTMLPath()
{
    return $header_location . '/uas_data/uploads/products/';
}

function SetDisplayHTMLPath()
{
    return $header_location . '/uas_data/uploads/products/';
}

?>
