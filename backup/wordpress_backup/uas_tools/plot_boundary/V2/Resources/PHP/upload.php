<?php
require_once("SetConfigurationFilePath.php");

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

    $displayFilePath = "http://bhub.gdslab.org/temp/PlotBoundary/" . $hash . "/" . $_FILES["file"]["name"];
    echo $displayFilePath;

}

?>