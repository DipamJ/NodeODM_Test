<?php
// File containing System Variables
define("LOCAL_PATH_ROOT", $_SERVER["DOCUMENT_ROOT"]);
require LOCAL_PATH_ROOT . '/uas_tools/system_management/centralized_management.php';

//require_once("SetDBConnection.php");
require_once("../../../../resources/database/SetDBConnection.php");
require_once("CommonFunctions.php");

//_log("header_location: " . $header_location);

//echo $root_path;
//if (!session_id()) session_start();
//session_start();
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
//$root_path = $_SESSION['root_path'];
//echo $root_path, "<br>"; //output new value

//$header_location = $_SESSION['header_location'];
//echo $header_location; //output new value

$con = SetDBConnection();

if (mysqli_connect_errno()) {
    echo "Failed to connect to database server: " . mysqli_connect_error();
} else {
    $type = $_GET["type"];
    $project = $_GET["project"];
    $platform = $_GET["platform"];
    $sensor = $_GET["sensor"];
    $productType = $_GET["productType"];

    if ($type == "raw") {
        $sql = "select raw_data_upload_status.*, project.Name as ProjectName,  platform.Name as PlatformName, sensor.Name as SensorName, " .
            "replace(flight.Date, '-', '/') as Date, flight.Name as FlightName " .
            "from raw_data_upload_status, flight, project, platform, sensor " .
            "where flight.Project = project.ID and flight.Platform = platform.ID and flight.Sensor = sensor.ID " .
            "and raw_data_upload_status.status = 'Finished' and flight.ID = raw_data_upload_status.Flight and flight.Project like '$project' " .
            "and flight.Platform like '$platform' and flight.Sensor like '$sensor' " .
            "order by ProjectName, PlatformName, SensorName, Date, FlightName, raw_data_upload_status.FileName";
        //_log('1 select raw uploaded: ' . $sql);

    } else if ($type == "product") {
        $sql = "select imagery_product.*, project.Name as ProjectName,  platform.Name as PlatformName, sensor.Name as SensorName, " .
            "replace(flight.Date, '-', '/') as Date, flight.Name as FlightName, product_type.Name as TypeName " .
            "from imagery_product, flight, project, platform, sensor, product_type " .
            "where flight.Project = project.ID and flight.Platform = platform.ID and flight.Sensor = sensor.ID and imagery_product.Type = product_type.ID " .
            "and imagery_product.Status =  'Finished' and flight.ID = imagery_product.Flight  and flight.Project like '$project' " .
            "and flight.Platform like '$platform' and flight.Sensor like '$sensor' and imagery_product.Type like '$productType' " .
            "order by ProjectName, PlatformName, SensorName, Date, FlightName, imagery_product.FileName";
        //_log('2 select product uploaded: ' . $sql);
    }

    $result = mysqli_query($con, $sql);

    $list = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $row["DownloadPath"] = str_replace("/var/www/html", $header_location, $row["UploadFolder"]) . "/" . $row["FileName"];// Path should be automated
        $row["Size"] = FormatBytes($row["Size"]);
        $list[] = $row;
    }
    echo json_encode($list);
}

?>
