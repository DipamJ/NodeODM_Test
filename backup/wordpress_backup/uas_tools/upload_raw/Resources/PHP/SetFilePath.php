<?php

function SetTempFolderLocalPath()// TEMPORARY DIRECTORY
{
    //	return '/var/www/html/temp/';
    //return '/var/www/html/wordpress/uas_data/temp/';
    //return '/var/www/html/wordpress/data/temp/';
    return '/var/www/html/wordpress/uas_data/temp/';
}

function SetFolderLocalPath()// PROJECT FOLDER
{
    //return '/var/www/html/wordpress/uas_data/uploads/raw/';
    // AT THIS LOCATION, FOLDERS ARE CREATED STARTING FROM
    // PROJECT NAME, PLATFORM, SENSOR, DATA. FINNALY FILES
    // ARE UPLOADED THERE
    //return '/var/www/html/wordpress/data/uploads/raw/';
    return '/var/www/html/wordpress/uas_data/uploads/raw/';
}

function SetFolderHTMLPath()
{
    //return 'http://basfhub.gdslab.org/uas_data/uploads/raw/';
    //return 'https://uashub.tamucc.edu/uas_data/uploads/raw/';
    //return 'http://basfhub.gdslab.org/wordpress/uas_data/uploads/raw/';
    return 'http://bhub.gdslab.org/wordpress/uas_data/uploads/raw/';
}

function SetDisplayHTMLPath()
{
    //return 'http://basfhub.gdslab.org/uas_data/uploads/raw/';
    //return 'https://uashub.tamucc.edu/uas_data/uploads/raw/';
    //return 'http://basfhub.gdslab.org/wordpress/uas_data/uploads/raw/';
    return 'http://bhub.gdslab.org/wordpress/uas_data/uploads/raw/';
}

?>