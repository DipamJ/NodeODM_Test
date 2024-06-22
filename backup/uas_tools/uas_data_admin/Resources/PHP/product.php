<?php
// File containing System Variables
define("LOCAL_PATH_ROOT", $_SERVER["DOCUMENT_ROOT"]);
require LOCAL_PATH_ROOT . '/uas_tools/system_management/centralized_management.php';
function GetProductList($flightID, $con)
{
    $sql = "select data_product.*, product_type.Name as Type, flight.Name as Flight from data_product, flight, product_type " .
        "where data_product.Type = product_type.ID and data_product.Flight = flight.ID and flight.ID = $flightID";

    $result = mysqli_query($con, $sql);

    $projectList = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $projectList[] = $row;
    }
    echo json_encode($projectList);
}

function UpdateProduct($productID, $type, $tmsPath, $con)
{
    $sql = "update data_product set Type=$type, TMSPath='$tmsPath' " .
        "where id=$productID";

    $result = mysqli_query($con, $sql);

    if (mysqli_query($con, $sql)) {
        echo "1";
    } else {
        echo mysqli_error($con);
        echo "\n" . $sql;
    }
}

function DeleteProduct($productID, $con)
{

    $sql = "select * from data_product where id = $productID";
    //echo $sql;
    $result = mysqli_query($con, $sql);
    $data = mysqli_fetch_assoc($result);
    //print_r($data);

    $sql = "delete from data_product where id = $productID";
    $result = mysqli_query($con, $sql);

    if (mysqli_query($con, $sql)) {
        echo "1";

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

        chmod(str_replace($header_location, $root_path, $data["DownloadPath"]), 0777);
        $command = "rm '" . str_replace($header_location, $root_path, $data["DownloadPath"]) . "'";
        exec($command);

        chmod(str_replace($header_location, $root_path, $data["HtmlPath"]), 0777);
        $command = "rm '" . str_replace($header_location, $root_path, $data["HtmlPath"]) . "'";
        exec($command);

        chmod(str_replace($header_location, $root_path, $data["ThumbPath"]), 0777);
        $command = "rm '" . str_replace($header_location, $root_path, $data["ThumbPath"]) . "'";
        exec($command);

        umask($old);
    } else {
        echo mysqli_error($con);
    }
}

//require_once("SetDBConnection.php");
require_once("../../../../resources/database/SetDBConnection.php");

$con = SetDBConnection();

if (mysqli_connect_errno()) {
    echo "Failed to connect to database server: " . mysqli_connect_error();
} else {

    $action = $_GET["action"];

    switch ($action) {
        case "list":
            {
                $flightID = $_GET["flight"];
                GetProductList($flightID, $con);
            }
            break;
        case "edit":
            {
                $id = $_GET["id"];
                $type = $_GET["type"];
                $tmsPath = mysqli_real_escape_string($con, $_GET['tmsPath']);

                UpdateProduct($id, $type, $tmsPath, $con);
            }
            break;
        case "delete":
            {
                $id = $_GET["id"];
                DeleteProduct($id, $con);
            }
            break;
    }
}

mysqli_close($con);
?>
