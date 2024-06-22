<?php
// File containing System Variables
define("LOCAL_PATH_ROOT", $_SERVER["DOCUMENT_ROOT"]);
require LOCAL_PATH_ROOT . '/uas_tools/system_management/centralized_management.php';

$folder = $_POST["folder"];
$name = "Charts.zip";

//	$folderPath = "/var/www/html/temp/CropData/".$folder;
// If session hasn't been started, start it
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
//$crop_data_path = $_SESSION['crop_data_path'];
//$folderPath = $crop_data_path . $folder;
$folderPath = $crop_data_temporary_folder . $folder;

//echo $folderPath;

$zip_file = $folderPath . "/" . $name;

if ($handle = opendir($folderPath)) {
    $zip = new ZipArchive();

    if ($zip->open($zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
        exit("cannot open <$zip_file>\n");
    }

    while (false !== ($file = readdir($handle))) {
        if ($file != "." && $file != "..") {
            $zip->addFile($folderPath . "/" . $file, $file);
        }
    }
    closedir($handle);
    $zip->close();
//    echo "http://uashub.tamucc.edu/temp/CropData/" . $folder . "/" . $name;
    // If session hasn't been started, start it
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    //$crop_data_temporary_folder = $_SESSION['crop_data_temporary_folder'];
    echo $crop_data_temporary_folder . $folder . "/" . $name;
}
?>