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
    $userName = $_SESSION["email"];
    $sql = "select imagery_product.*, project.Name as ProjectName,  platform.Name as PlatformName, sensor.Name as SensorName, " .
        "replace(flight.Date, '-', '/') as Date, flight.Name as FlightName, product_type.Name as TypeName " .
        "from imagery_product, flight, project, platform, sensor, product_type " .
        "where flight.Project = project.ID and flight.Platform = platform.ID and flight.Sensor = sensor.ID and imagery_product.type = product_type.ID and product_type.Type = 'R' " . // PROBLEM HERE
        "and imagery_product.Status =  'Finished' and flight.ID = imagery_product.Flight and imagery_product.Uploader = '$userName' " .
        "order by ProjectName, PlatformName, SensorName, Date, FlightName, imagery_product.FileName";

    //_log('1 select finished product uploaded: ' . $sql);
    //echo('1 select finished product uploaded: ' . $sql);
    $result = mysqli_query($con, $sql);
    $list = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $row["Size"] = FormatBytes($row["Size"]);
        // If session hasn't been started, start it
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        //_log('root_path: ' . $root_path);
        //_log('header_location: ' . $header_location);
        //_log('path: ' . $row["UploadFolder"] . "/" . $row["FileName"]);

        // $row["DownloadPath"] = str_replace($root_path.'web', $header_location, $row["UploadFolder"] . "/" . $row["FileName"]);
        $row["DownloadPath"] = str_replace($root_path, $header_location .'/', $row["UploadFolder"] . "/" . $row["FileName"]);

        //_log('download path: ' . $row["DownloadPath"]);

        $list[] = $row;
    }
    echo json_encode($list);
    //print_r($list);
}
?>
