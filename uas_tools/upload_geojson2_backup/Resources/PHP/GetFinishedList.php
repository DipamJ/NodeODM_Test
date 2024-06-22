<?php
// File containing System Variables
define("LOCAL_PATH_ROOT", $_SERVER["DOCUMENT_ROOT"]);
require LOCAL_PATH_ROOT . '/uas_tools/system_management/centralized_management.php';

//require_once("SetDBConnection.php");
require_once("../../../../resources/database/SetDBConnection.php");
require_once("CommonFunctions.php");
$con = SetDBConnection();

function _log($str)
{
    // log to the output
    $log_str = date('d.m.Y') . ": {$str}\r\n";
    echo $log_str;

    // log to file
    if (($fp = fopen('upload_log.txt', 'a+')) !== false) {
        fputs($fp, $log_str);
        fclose($fp);
    }
}

if (mysqli_connect_errno()) {
    echo "Failed to connect to database server: " . mysqli_connect_error();
} else {
    session_start();
    //$userName = $_SESSION["username"];
    $userName = $_SESSION["email"];

//    $sql = "select imagery_product.*, project.Name as ProjectName,  platform.Name as PlatformName, sensor.Name as SensorName, " .
//        "replace(flight.Date, '-', '/') as Date, flight.Name as FlightName, product_type.Name as TypeName " .
//        "from imagery_product, flight, project, platform, sensor, product_type " .
//        "where flight.Project = project.ID and flight.Platform = platform.ID and flight.Sensor = sensor.ID and imagery_product.type = product_type.ID and product_type.Type = 'V' " .
//        "and imagery_product.Status =  'Finished' and flight.ID = imagery_product.Flight and imagery_product.Uploader = '$userName' " .
//        "order by ProjectName, PlatformName, SensorName, Date, FlightName, imagery_product.FileName";

    // Only need project and EPSG
//    $sql = "select vector_data.*, project.Name as ProjectName, " .
//        "replace(flight.Date, '-', '/') as Date, flight.Name as FlightName, product_type.Name as TypeName " .
//        "from vector_data, project, product_type " .
//        "where flight.Project = project.ID and vector_data.type = product_type.ID and product_type.Type = 'V' " .
//        "and vector_data.Status =  'Finished' and vector_data.Uploader = '$userName' " .
//        "order by ProjectName, Date, FlightName, vector_data.FileName";


    //_log('project: '.$_GET["project"]);
    //$tt = $_POST["project"];
    //echo ($tt);

//    $sql = "SELECT DISTINCT vector_data.*, project.Name as ProjectName, product_type.Name as TypeName " .
//        "from vector_data, project, product_type " .
//        "where vector_data.Type = product_type.ID and product_type.Type = 'V' " .
//        //"and project.Name =  '2016 Corpus Christi Cotton and Sorghum' and vector_data.Status =  'Finished' and vector_data.Uploader = '$userName' " . // hardcoded. change project name
//        "and vector_data.Status =  'Finished' and vector_data.Uploader = '$userName' " .
//        "order by ProjectName, vector_data.FileName";

//    $sql = "SELECT v.*, j.Name as ProjectName, t.Name as TypeName " .
//        "from vector_data v inner join project j on j.crop = v.ID inner join product_type t on v.Type = t.ID " .
//        "where v.Type = 'V' and v.Status = 'Finished' " .
//        "and v.Uploader = '$userName' " .
//        "order by ProjectName, TypeName";

    // $sql = "SELECT v.*, project.Name as ProjectName, t.Name as TypeName " .
    //     "from project, product_type, vector_data v inner join product_type t on v.Type = t.ID " .
    //     "where product_type.Type = 'V' and v.Status = 'Finished' " .
    //     "and v.Project =  project.Name " .
    //     "and v.Uploader = '$userName' " .
    //     "order by ProjectName, TypeName";
    //

    // Instead of product type, use product name. This name should come from HTML page
    // From the Type selection
    $sql = "SELECT v.*, project.Name as ProjectName, t.Name as TypeName " .
        "from project, product_type, vector_data v inner join product_type t on v.Type = t.ID " .
        //"where product_type.Name = 'GeoJSON' and v.Status = 'Finished' " .
        "where product_type.Name = 'SHAPE' and v.Status = 'Finished' " .
        "and v.Project =  project.Name " .
        "and v.Uploader = '$userName' " .
        "order by ProjectName, TypeName";


    //_log("sql: " . $sql);

    $result = mysqli_query($con, $sql);
    $list = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $row["Size"] = FormatBytes($row["Size"]);
        //$row["DownloadPath"] = str_replace("/var/www/html/", "https://uashub.tamucc.edu/", $row["UploadFolder"] . "/" . $row["FileName"]);
        $row["DownloadPath"] = str_replace("/var/www/html/", $header_location . "/", $row["UploadFolder"] . "/" . $row["FileName"]);
        $list[] = $row;
    }
    echo json_encode($list);
}
?>
