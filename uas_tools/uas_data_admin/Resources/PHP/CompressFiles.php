<?php
require_once("SetFilePath.php");

function FormatFileName($rawName)
{
    $formattedName = str_replace(' ', '_', $rawName);
    $formattedName = preg_replace('/[^A-Za-z0-9\_]/', '', $formattedName);
    return preg_replace('/_+/', '_', $formattedName);
}

/*
$project = $_GET["project"];
$platform = $_GET["platform"];
$sensor = $_GET["sensor"];
$date = $_GET["date"];
$date = str_replace('/',"-",$date);
$flight = $_GET["flight"];
$type = $_GET["type"];
*/
$project = FormatFileName($_GET["project"]);
$platform = FormatFileName($_GET["platform"]);
$sensor = FormatFileName($_GET["sensor"]);
$date = $_GET["date"];
$date = str_replace('/', "-", $date);
$flight = FormatFileName($_GET["flight"]);
$type = $_GET["type"];

$path = SetFolderLocalPath() . $project . "/" . $platform . "/" . $sensor . "/" . $date . "/" . $flight . "/" . $type;

$zip_file = $path . '/' . $flight . '_' . $type . '.zip';

if ($handle = opendir($path)) {
    $zip = new ZipArchive();

    if ($zip->open($zip_file, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE) !== TRUE) {
        exit("cannot open <$zip_file>\n");
    }

    while (false !== ($file = readdir($handle))) {
        if ($file != "." && $file != ".." && $file != $flight . '_' . $type . '.zip' && $file != "Display") {
            $zip->addFile($path . '/' . $file, $file);
        }
    }
    closedir($handle);
    echo "numfiles: " . $zip->numFiles . "\n";
    echo "status:" . $zip->status . "\n";
    $zip->close();
    echo 'Zip File:' . $zip_file . "\n";
}

?>
