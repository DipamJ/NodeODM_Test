<?php
// File containing System Variables
define("LOCAL_PATH_ROOT", $_SERVER["DOCUMENT_ROOT"]);
require LOCAL_PATH_ROOT . '/uas_tools/system_management/centralized_management.php';

require_once("SetConfigurationFilePath.php");

//_log("header_location: " . $header_location);

if (0 < $_FILES["file"]["error"]) {
    echo "Error: " . $_FILES["file"]["error"] . "<br>";
} else {

    date_default_timezone_set("America/Chicago");
    $dateTime = date("Ymdhisa");

    $hash = md5($dateTime);

    $folderPath = SetGeoJsonFolderPath() . "PlotBoundary/" . $hash;

    if (!mkdir($folderPath, 0777, true)) {
        die("Failed to create folders...");
    }

    chmod($folderPath, 0777);

    move_uploaded_file($_FILES["file"]["tmp_name"], $folderPath . "/" . $_FILES["file"]["name"]);

    //$displayFilePath = "http://uashub.tamucc.edu/temp/PlotBoundary/".$hash."/".$_FILES["file"]["name"];
    //$displayFilePath = "http://bhub.gdslab.org/temp/PlotBoundary/" . $hash . "/" . $_FILES["file"]["name"];
    $displayFilePath = $header_location . "/temp/PlotBoundary/" . $hash . "/" . $_FILES["file"]["name"];

    echo $displayFilePath;
}
?>