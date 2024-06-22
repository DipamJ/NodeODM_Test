<?php
// File containing System Variables
define("LOCAL_PATH_ROOT", $_SERVER["DOCUMENT_ROOT"]);
require LOCAL_PATH_ROOT . '/uas_tools/system_management/centralized_management.php';

require_once("CommonFunctions.php");
require_once("SetDBConnection.php");

$con = SetDBConnection();

if (mysqli_connect_errno()) {
    echo "Failed to connect to database server: " . mysqli_connect_error();
} else {
    $id = $_GET["id"];
    $zipPath = $_GET["zip"];

    $sql = "select * from raw_data where id = $id";
    //_log('select raw_data : ' . $sql);
    $result = mysqli_query($con, $sql);
    $data = mysqli_fetch_assoc($result);
    //echo $sql;

    //print_r($result);
    //print_r($data);
    //$downloadPath = $date["DownloadPath"];
    //$localPath = str_replace("http://uashub.tamucc.edu/","/var/www/html/",$downloadPath);
    //$htmlPath = $date["HtmlPath"];
    //$thumbPath = $date["ThumbPath"];
    //$display = str_replace("http://uashub.tamucc.edu/","/var/www/html/",$downloadPath);


    $sql = "delete from raw_data where id = $id";
    //_log('delete raw_data : ' . $sql);
    $result = mysqli_query($con, $sql);

    if (mysqli_query($con, $sql)) {
        //echo "1";
        //_log('need to make changes in raw.php : ' . $sql);
        $old = umask(0);

//			chmod(str_replace("http://uashub.tamucc.edu/","/var/www/html/",$data["DownloadPath"]), 0777);
//			$command = "rm '".str_replace("http://uashub.tamucc.edu/","/var/www/html/",$data["DownloadPath"])."'";
//			exec($command);
//
//			chmod(str_replace("http://uashub.tamucc.edu/","/var/www/html/",$data["HtmlPath"]), 0777);
//			$command = "rm '".str_replace("http://uashub.tamucc.edu/","/var/www/html/",$data["HtmlPath"])."'";
//			exec($command);
//
//			chmod(str_replace("http://uashub.tamucc.edu/","/var/www/html/",$data["ThumbPath"]), 0777);
//			$command = "rm '".str_replace("http://uashub.tamucc.edu/","/var/www/html/",$data["ThumbPath"])."'";
//			exec($command);

        // If session hasn't been started, start it
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        chmod(str_replace($header_location, $alternative_root_path, $data["DownloadPath"]), 0777);
        $command = "rm '" . str_replace($header_location, $alternative_root_path, $data["DownloadPath"]) . "'";
        exec($command);

        chmod(str_replace($header_location, $alternative_root_path, $data["HtmlPath"]), 0777);
        $command = "rm '" . str_replace($header_location, $alternative_root_path, $data["HtmlPath"]) . "'";
        exec($command);

        chmod(str_replace($header_location, $alternative_root_path, $data["ThumbPath"]), 0777);
        $command = "rm '" . str_replace($header_location, $alternative_root_path, $data["ThumbPath"]) . "'";
        exec($command);


        $zip = new ZipArchive;
        //if ($zip->open(str_replace("http://uashub.tamucc.edu/","/var/www/html/",$zipPath)) === TRUE) {
        if ($zip->open(str_replace($header_location, $alternative_root_path, $zipPath)) === TRUE) {
            $zip->deleteName($data['Name']);
            $zip->close();
            echo "compressed";

        } else {
            echo "uncompressed";
        }

        umask($old);
    } else {
        echo mysqli_error($con);
    }
}

mysqli_close($con);
?>