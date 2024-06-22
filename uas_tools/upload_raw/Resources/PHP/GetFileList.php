<?php
// File containing System Variables
define("LOCAL_PATH_ROOT", $_SERVER["DOCUMENT_ROOT"]);
require LOCAL_PATH_ROOT . '/uas_tools/system_management/centralized_management.php';

//require_once("SetDBConnection.php");
require_once("../../../../resources/database/SetDBConnection.php");
require_once("CommonFunctions.php");

$con = SetDBConnection();

if (mysqli_connect_errno()) {
    echo "Failed to connect to database server: " . mysqli_connect_error();
} else {
    $uploadID = $_GET["uploadID"] ?? '';

    $sql = "select * from raw_data_upload " .
        "where uploadID = $uploadID order by Name";
    $result = mysqli_query($con, $sql);

    $list = array();
    while ($row = mysqli_fetch_assoc($result)) {
        //NEED TO MAKE A CHANGE HERE
        //$localPath = str_replace("https://uashub.tamucc.edu/", "/var/www/html/", $row["DownloadPath"]);
        // If session hasn't been started, start it
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        //$header_location = $_SESSION['header_location'];
        //$alternative_root_path = $_SESSION['alternative_root_path'];
        $localPath = str_replace($header_location, $alternative_root_path, $row["DownloadPath"]);
        $row["Size"] = FormatBytes(filesize($localPath));
        //NEED TO MAKE A CHANGE HERE
        //$localDisplayPath = str_replace("https://uashub.tamucc.edu/", "/var/www/html/", $row["DisplayPath"]);
        $localDisplayPath = str_replace($header_location, $alternative_root_path, $row["DisplayPath"]);
        if (!file_exists($localDisplayPath)) {
            $row["DisplayPath"] = "Resources/Images/NoThumb.jpg";
        }

        $list[] = $row;
    }
    echo json_encode($list);
}
?>
