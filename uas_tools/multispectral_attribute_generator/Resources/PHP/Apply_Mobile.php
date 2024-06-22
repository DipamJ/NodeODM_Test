<?php
require_once("SetFilePath.php");
require_once("CommonFunctions.php");
//require_once("SetDBConnection.php");
require_once("../../../../resources/database/SetDBConnection.php");

// File containing System Variables
require LOCAL_PATH_ROOT . '/uas_tools/system_management/centralized_management.php';

$pageID = $_GET["pageid"];

//_log('$pageID: '.$pageID);

$con = SetDBConnection();

if (mysqli_connect_errno()) {
    echo "Failed to connect to database server: " . mysqli_connect_error();
} else {
    $sql = "select * from visualization_project where ID = '$pageID'";
    $result = mysqli_query($con, $sql);
    if ($result) {
        $page = mysqli_fetch_assoc($result);
        //print_r($page);
        if ($page) {
            // added
            //_log('page ' . $page['Project']);
            $projectID = $page['Project'];
            //_log('$main_temporary_folder ' . $main_temporary_folder);

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
            //_log('$main_temporary_folder: ' . $main_temporary_folder);

            //$sourcePath = str_replace($main_temporary_folder . 'mobile/', SetTempFolderLocalPathMobile(), $page["PathMobile"]);
            $sourcePath = str_replace($header_location . '/web/temp/mobile/', SetTempFolderLocalPathMobile(), $page["PathMobile"]);
            //_log('$sourcePath ' . $sourcePath);

            $desPath = str_replace($header_location . '/temp/mobile/', SetVisualizationFolderLocalPathMobile(), $page["PathMobile"]);
            //_log('$desPath ' . $desPath);

            //$viewPath = str_replace("/var/www/html/","http://basfhub.gdslab.org/temp/", $desPath);

//				$viewPath = str_replace("/var/www/html/wordpress/","http://basfhub.gdslab.org/temp/", $desPath);
            // If session hasn't been started, start it
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            //$header_location = $_SESSION['header_location'] . '/';
            //$root_path = $_SESSION['root_path'];

            //$viewPath = str_replace($root_path, $header_location, $desPath);
            //_log('$root_path ' . $root_path);
            //_log('$header_location ' . $header_location);
            //_log('$desPath ' . $desPath);

            $viewPath = str_replace($root_path, $header_location . '/', $desPath);
            //_log('viewPath ' . $viewPath);
            // _log('in desPath ' . $desPath);
            // _log('find root_path ' . $root_path);
            // _log('reeplace with header_location ' . $header_location . '/');

            $desFolderPath = str_replace("index.html", "", $desPath);
            //////
            //_log('desFolderPath: ' . $desFolderPath);
            // Added
            //clearstatcache();
            // If file doesn't exist
//            if (!file_exists($desFolderPath)) {
//                // If directory cannot be created
//                if (!mkdir($desFolderPath, 0777, true)) {
//                    die("Failed to create folders");
//                }
//            }

            if (!file_exists($desFolderPath)) {
                if (!mkdir($desFolderPath, 0777, true)) {
                    die("Failed to create folders");
                }
            }

            //_log('$sourcePath: ' . $sourcePath);
            //_log('$desPath: ' . $desPath);
            //copy($sourcePath, $desPath);
            copy($sourcePath, str_replace(".html", ".php", $desPath));

            //$tt = str_replace(".html", ".php", $desPath);
            //_log('$tt: ' . $tt);

            //echo shell_exec("cp $desPath str_replace(".html", "", $desPath) 2>&1");

//            $tt = str_replace("html", "", $desPath);
//
//            $desPath1 = $tt.'php';
//
//            copy($desPath, $desPath1);
            $test = str_replace(".html", ".php", $viewPath);
            $viewPath = $test;
            echo $viewPath; // has the link to the site with the product's images

            //_log('viewPath ' . $viewPath);

        } else {
            echo "Failed. Could not find the visualization page";
        }
    } else {
        echo "Failed. Could not find the visualization page";
    }
}
mysqli_close($con);
?>
