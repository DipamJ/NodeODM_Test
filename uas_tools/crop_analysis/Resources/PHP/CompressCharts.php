<?php
// File containing System Variables
define("LOCAL_PATH_ROOT", $_SERVER["DOCUMENT_ROOT"]);
require LOCAL_PATH_ROOT . '/uas_tools/system_management/centralized_management.php';

$folder = $_POST["folder"];
$name = "Charts.zip";

$folderPath = "/var/www/html/temp/CropData/" . $folder;

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
    //echo "http://bhub.gdslab.org/temp/CropData/" . $folder . "/" . $name;
    echo $header_location . "/temp/CropData/" . $folder . "/" . $name;
}
?>