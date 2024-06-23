<?php
// File containing System Variables
define("LOCAL_PATH_ROOT", $_SERVER["DOCUMENT_ROOT"]);
require LOCAL_PATH_ROOT . '/uas_tools/system_management/centralized_management.php';

function SetTempFolderLocalPath()// TEMPORARY DIRECTORY
{
    //	return '/var/www/html/temp/';
    //return '/var/www/html/wordpress/uas_data/temp/';
    //return '/var/www/html/wordpress/data/temp/';
    //return '/var/www/html/wordpress/uas_data/temp/';
    // return '/var/www/wtxcotton.uashubs.com/uashub/web/uas_data/temp/';
    return '/var/www/html/uas_data/temp/';
}

function SetFolderLocalPath()// PROJECT FOLDER
{
    //return '/var/www/html/wordpress/uas_data/uploads/raw/';
    // AT THIS LOCATION, FOLDERS ARE CREATED STARTING FROM
    // PROJECT NAME, PLATFORM, SENSOR, DATA. FINNALY FILES
    // ARE UPLOADED THERE
    //return '/var/www/html/wordpress/data/uploads/raw/';
    //return '/var/www/html/wordpress/uas_data/uploads/raw/';
    return '/var/www/html/uas_data/uploads/raw/';
}

function SetFolderHTMLPath()
{
    //return 'http://basfhub.gdslab.org/uas_data/uploads/raw/';
    //return 'https://uashub.tamucc.edu/uas_data/uploads/raw/';
    //return 'http://basfhub.gdslab.org/wordpress/uas_data/uploads/raw/';
    //return 'http://bhub.gdslab.org/wordpress/uas_data/uploads/raw/';
    //return 'http://bhub.gdslab.org/uas_data/uploads/raw/';
    return $header_location . '/uas_data/uploads/raw/';
}

function SetDisplayHTMLPath()
{
    //return 'http://basfhub.gdslab.org/uas_data/uploads/raw/';
    //return 'https://uashub.tamucc.edu/uas_data/uploads/raw/';
    //return 'http://basfhub.gdslab.org/wordpress/uas_data/uploads/raw/';
    //return 'http://bhub.gdslab.org/wordpress/uas_data/uploads/raw/';
    //return 'http://bhub.gdslab.org/uas_data/uploads/raw/';
    return $header_location . '/uas_data/uploads/raw/';
}

?>
