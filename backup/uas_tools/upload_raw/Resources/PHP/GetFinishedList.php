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
    // If session hasn't been started, start it
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    //$userName = $_SESSION["username"] ?? '';
    $userName = $_SESSION["email"];

    $sql = "select raw_data_upload_status.*, project.Name as ProjectName,  platform.Name as PlatformName, sensor.Name as SensorName, " .
        "replace(flight.Date, '-', '/') as Date, flight.Name as FlightName " .
        "from raw_data_upload_status, flight, project, platform, sensor " .
        "where flight.Project = project.ID and flight.Platform = platform.ID and flight.Sensor = sensor.ID " .
        "and raw_data_upload_status.Status =  'Finished' and flight.ID = raw_data_upload_status.Flight and raw_data_upload_status.Uploader = '$userName' " .
        "order by ProjectName, PlatformName, SensorName, Date, FlightName, raw_data_upload_status.FileName";
    $result = mysqli_query($con, $sql);
    //_log('List of products on database');
    //_log($sql);
    $list = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $row["Size"] = FormatBytes($row["Size"]);
        //NEED TO MAKE A CHANGE HERE
        //$row["DownloadPath"] = str_replace("/var/www/html/","https://uashub.tamucc.edu/",$row["UploadFolder"]."/".$row["FileName"]);
        //$row["DownloadPath"] = str_replace("/var/www/html/", "http://basfhub.gdslab.org/", $row["UploadFolder"]."/".$row["FileName"]);
        // If session hasn't been started, start it
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        //$header_location = $_SESSION['header_location'];
        //$root_path = $_SESSION['root_path'];
//        echo "root_path: " . $root_path;
//        echo " ";
//        echo "header_location: " . $header_location;
//        echo " ";

        $row["DownloadPath"] = str_replace($root_path, $header_location . "/", $row["UploadFolder"] . "/" . $row["FileName"]);

//        echo " ";
//        echo "row[DownloadPath]: " . $row["DownloadPath"];
//        echo " ";

        $list[] = $row;
    }
    echo json_encode($list);

    //print_r($list);
}
?>
