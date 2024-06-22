<?php
// File containing System Variables
define("LOCAL_PATH_ROOT", $_SERVER["DOCUMENT_ROOT"]);
require LOCAL_PATH_ROOT . '/uas_tools/system_management/centralized_management.php';

function SetTempFolderLocalPath()
{
    //return '/var/www/html/temp/';
    //return '/var/www/html/wordpress/uas_data/temp/';
    return '/var/www/html/uas_data/temp/';
}

function SetFolderLocalPath()
{
    //return '/var/www/html/uas_data/uploads/';
    //return '/var/www/html/wordpress/uas_data/uploads/';
    return '/var/www/html/uas_data/uploads/';
}

function SetFolderHTMLPath()
{
    //return 'http://basfhub.gdslab.org/wordpress/uas_data/uploads/';
    //return 'http://bhub.gdslab.org/wordpress/uas_data/uploads/';
    //return 'http://bhub.gdslab.org/uas_data/uploads/';
    return $header_location . '/uas_data/uploads/';
}

?>
