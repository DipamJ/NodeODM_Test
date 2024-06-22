<?php
// File containing System Variables
define("LOCAL_PATH_ROOT", $_SERVER["DOCUMENT_ROOT"]);
require LOCAL_PATH_ROOT . '/uas_tools/system_management/centralized_management.php';

$data = $_POST["data"];
$folder = $_POST["folder"];
$name = $_POST["name"];

// $folderPath = "/var/www/html/temp/CropData/".$folder;
// If session hasn't been started, start it

$crop_data_path = "/var/www/html/temp/CropData/";
$folderPath = $crop_data_path . $folder;
//echo $folderPath;
if (!file_exists($folderPath)) {
    if (!mkdir($folderPath, 0777, true)) {
        die('Failed to create folders...');
    }
}

$file = fopen($folderPath . "/" . $name, 'wb');

fwrite($file, base64_decode($data));
?>