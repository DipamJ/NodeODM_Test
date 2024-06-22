<?php
require_once("SetConfigurationFilePath.php");
require_once("SetDBConnection.php");

$geoJson = $_GET["geojson"];
$addedLayers = $_REQUEST["layers"];

$dateTime = date("Ymdhisa");

$hash = md5($dateTime);

$folderPath = SetGeoJsonFolderPath() . $hash;
if (!mkdir($folderPath, 0777, true)) {
    die("Failed to create folders...");
}

chmod($folderPath, 0777);


$geoJsonFilePath = $folderPath . "/cutline.geojson";

$file = fopen($geoJsonFilePath, "w") or die("Unable to open file!");
fwrite($file, $geoJson);
fclose($file);

chmod($geoJsonFilePath, 0777);


$con = SetDBConnection();
if (mysqli_connect_errno()) {
    echo "Failed to connect to database server: " . mysqli_connect_error();
} else {
    $projectID = $_GET["project"];
    $type = $_GET["type"];
    $layerID = $_GET["layer"];

    $sql = "select data_product.ID as ID, data_product.Name as Name, data_product.DownloadPath as DownloadPath " .
        "from flight, data_product  " .
        "where data_product.Flight = flight.ID and flight.Project = $projectID and data_product.Type = $type and data_product.ID = $layerID";

    $result = mysqli_query($con, $sql);

    $layer = mysqli_fetch_assoc($result);


    $shapeFile = $geoJsonFilePath;
    //$inputFile = str_replace("http://uashub.tamucc.edu", "/var/www/html", $layer["DownloadPath"]);
    $inputFile = str_replace("http://bhub.gdslab.org", "/var/www/html", $layer["DownloadPath"]);
    $layerName = str_replace(".tif", "", $layer["Name"]);

    $outputTiff = $folderPath . "/" . $layerName . ".tif";
    $outputJpg = $folderPath . "/" . $layerName . ".jpg";
    $outputCanopyCoverJpg = $folderPath . "/" . $layerName . "_cc.jpg";
    $outputThumbnail = $folderPath . "/" . $layerName . "_cc_thumb.jpg";

    $command = "gdalwarp -cutline  \"" . $shapeFile . "\" -crop_to_cutline " . $inputFile . " " . $outputTiff;
    exec($command);

    /*
    $command = "gdal_translate ".$outputTiff." -b 1 -b 2 -b 3 -of JPEG -co 'QUALITY=60' -co 'WORLDFILE=YES' ".$outputJpg;
    exec($command);
*/


    //$command = "python '../Python/gen_cc_fast.py' ".$outputTiff;
    $command = "python3 '../Python/gen_cc_fast.py' " . $outputTiff;
    exec($command);

    $canopyCoverFile = $folderPath . "/" . $layerName . "_cc_result.txt";
    //echo $canopyCoverFile;
    $canopyCover = file_get_contents($canopyCoverFile);

    echo $canopyCover;
}
?>