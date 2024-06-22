<?php
// File containing System Variables
define("LOCAL_PATH_ROOT", $_SERVER["DOCUMENT_ROOT"]);
require LOCAL_PATH_ROOT . '/uas_tools/system_management/centralized_management.php';

//// Log Document
//function _log($str)
//{
//    // log to the output
//    $log_str = date('d.m.Y') . ": {$str}\r\n";
//    echo $log_str;
//
//    // log to file
//    if (($fp = fopen('upload_log.txt', 'a+')) !== false) {
//        fputs($fp, $log_str);
//        fclose($fp);
//    }
//}
//
//_log("header_location: " .$header_location);

//function SetFolderHTMLPath()
//{
//    //return 'http://basfhub.gdslab.org/uas_data/uploads/raw/';
//    //return 'https://uashub.tamucc.edu/uas_data/uploads/raw/';
//    //return 'http://basfhub.gdslab.org/wordpress/uas_data/uploads/raw/';
//    //return 'http://bhub.gdslab.org/wordpress/uas_data/uploads/raw/';
//    //return 'http://bhub.gdslab.org/uas_data/uploads/raw/';
//    return $header_location . '/uas_data/uploads/raw/';
//}

		
	function SetTempFolderLocalPath()
	{
		return '/var/www/html/temp/';
	}
	
	function SetFolderLocalPath()
	{
		return '/var/www/html/uas_data/uploads/pointclouds/';
	}
	
//	function SetTrashFolderPath()
//	{
//		return '/var/www/html/temp/Trash/';
//	}
	
	function SetFolderHTMLPath()
	{
		//return 'http://uashub.tamucc.edu/uas_data/uploads/pointclouds/';
        //$header_location = http://bhub.gdslab.org
        return $header_location . '/uas_data/uploads/pointclouds/';
	}
	
	function SetDisplayHTMLPath()
	{
		//return 'http://uashub.tamucc.edu/uas_data/uploads/pointclouds/';
        return $header_location . '/uas_data/uploads/pointclouds/';
	}
?>