<?php
// File containing System Variables
define("LOCAL_PATH_ROOT", $_SERVER["DOCUMENT_ROOT"]);
require LOCAL_PATH_ROOT . '/uas_tools/system_management/centralized_management.php';

require_once("SetFilePath.php");
require_once("CommonFunctions.php");
require_once("SetDBConnection.php");

$pageID = $_GET["pageid"];

$con = SetDBConnection();

if (mysqli_connect_errno()) {
    echo "Failed to connect to database server: " . mysqli_connect_error();
} else {
    $sql = "select * from visualization_project where ID = $pageID";
    $result = mysqli_query($con, $sql);
    if ($result) {
        $page = mysqli_fetch_assoc($result);
        if ($page) {
            /*
            $sourcePath = str_replace("https://uashub.tamucc.edu/temp/" , SetTempFolderLocalPath(), $page["Path"]);
            $desPath = str_replace("https://uashub.tamucc.edu/temp/", SetVisualizationFolderLocalPath(), $page["Path"]);
            $viewPath = str_replace("/var/www/html/","https://uashub.tamucc.edu/", $desPath);				*/
            //$sourcePath = str_replace("http://basfhub.gdslab.org/temp/" , SetTempFolderLocalPath(), $page["Path"]);
            //$desPath = str_replace("http://basfhub.gdslab.org/temp/", SetVisualizationFolderLocalPath(), $page["Path"]);
            // If session hasn't been started, start it
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            //$main_temporary_folder = $_SESSION['main_temporary_folder'];
            $sourcePath = str_replace($main_temporary_folder, SetTempFolderLocalPath(), $page["Path"]);
//				_log('$sourcePath '.$sourcePath);

            $desPath = str_replace($main_temporary_folder, SetVisualizationFolderLocalPath(), $page["Path"]);
            //_log('$desPath '.$desPath);

            //$viewPath = str_replace("/var/www/html/","http://basfhub.gdslab.org/temp/", $desPath);

//				$viewPath = str_replace("/var/www/html/wordpress/","http://basfhub.gdslab.org/temp/", $desPath);
            // If session hasn't been started, start it
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            //$header_location = $_SESSION['header_location'] . '/';
            //$root_path = $_SESSION['root_path'];

            $header_location = "http://bhub.gdslab.org/";

            //$viewPath = str_replace($root_path, $header_location, $desPath);

            $viewPath = str_replace($root_path, $header_location, $desPath);
            //_log('$viewPath '.$viewPath);

            $desFolderPath = str_replace("index.html", "", $desPath);
            //////
            //_log('$header_location: ' . $header_location);
            if (!file_exists($desFolderPath)) {
                if (!mkdir($desFolderPath, 0777, true)) {
                    die("Failed to create folders");
                }
            }

            copy($sourcePath, $desPath);
            echo $viewPath;
        } else {
            echo "Failed. Could not find the visualization page";
        }
    } else {
        echo "Failed. Could not find the visualization page";
    }
}
?>
