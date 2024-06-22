<?php
require_once("SetConfigurationFilePath.php");

$name = $_GET["name"];
$lat = $_GET["lat"];
$lng = $_GET["lng"];
$width = $_GET["width"];
$height = $_GET["height"];
$count = $_GET["count"];
$angle = $_GET["angle"];
$epsg = $_GET["epsg"];
$offsets = $_GET["offsets"];
$vshifts = $_GET["vshifts"];
$rowHeight = $_GET["rowheight"];
$rowCount = $_GET["rowcount"];
$colWidth = $_GET["colwidth"];
$colCount = $_GET["colcount"];

$number = rand(100000, 999999);
$hash = md5($number);

$folderPath = SetGeoJsonFolderPath() . "PlotBoundary/" . $hash;

if (!mkdir($folderPath, 0777, true)) {
    die("Failed to create folders...");
}

chmod($folderPath, 0777);

$shapeFilePath = $folderPath . "/plot_boundary.shp";
$geojsonFilePath = $folderPath . "/plot_boundary.geojson";

//	$command =  "python '../Python/gen_boundary_grid.py' ".$lat." ".$lng." ".$width." ".$height." ".$count." ".
//				$offsets." ".$vshifts." ".$angle." ".$shapeFilePath." ".$name." ".$rowHeight." ".$rowCount." ".
//				$colWidth." ".$colCount." ".$epsg." 2>&1";

$command = "python3 '../Python/gen_boundary_grid.py' " . $lat . " " . $lng . " " . $width . " " . $height . " " . $count . " " .
    $offsets . " " . $vshifts . " " . $angle . " " . $shapeFilePath . " " . $name . " " . $rowHeight . " " . $rowCount . " " .
    $colWidth . " " . $colCount . " " . $epsg . " 2>&1";
exec($command, $output);

$command = "ogr2ogr -f GeoJSON -t_srs crs:84 " . $geojsonFilePath . " " . $shapeFilePath;
exec($command);

//$displayFilePath = "https://uashub.tamucc.edu/temp/PlotBoundary/".$hash."/plot_boundary.geojson";
$displayFilePath = "http://bhub.gdslab.org/temp/PlotBoundary/" . $hash . "/plot_boundary.geojson";

echo $displayFilePath;

?>