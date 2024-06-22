<?php
// File containing System Variables
define("LOCAL_PATH_ROOT", $_SERVER["DOCUMENT_ROOT"]);
require LOCAL_PATH_ROOT . '/uas_tools/system_management/centralized_management.php';

function SetTempFolderLocalPath()// TEMPORARY DIRECTORY
{
    //return '/var/www/html/temp/';
    //return '/var/www/html/wordpress/uas_data/temp/';
    return '/var/www/html/uas_data/temp/';
}

function SetFolderLocalPath()// PROJECT FOLDER
{
    //return '/var/www/html/uas_data/uploads/products/';
    //return '/var/www/html/wordpress/uas_data/uploads/products/';
    return '/var/www/html/uas_data/uploads/products/';
}

function SetFolderHTMLPath()
{
    //return 'https://uashub.tamucc.edu/uas_data/uploads/products/';
    //return 'http://basfhub.gdslab.org/uas_data/uploads/products/';
    //return 'http://basfhub.gdslab.org/wordpress/uas_data/uploads/products/';
    //return 'http://basfhub.gdslab.org/uas_data/uploads/products/';
    //return 'http://bhub.gdslab.org/uas_data/uploads/products/';
    return $header_location . '/uas_data/uploads/products/';
}

function SetDisplayHTMLPath()
{
    //return 'https://uashub.tamucc.edu/uas_data/uploads/products/';
    //return 'http://basfhub.gdslab.org/uas_data/uploads/products/';
    //return 'http://basfhub.gdslab.org/wordpress/uas_data/uploads/products/';
    //return 'http://basfhub.gdslab.org/uas_data/uploads/products/';
    //return 'http://bhub.gdslab.org/uas_data/uploads/products/';
    return $header_location . '/uas_data/uploads/products/';
}

?>