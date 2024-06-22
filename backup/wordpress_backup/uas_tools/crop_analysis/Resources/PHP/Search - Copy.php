<?php
require_once("SetDBConnection.php");
require_once("SetFilePath.php");


$crop = $_GET["crop"];
$type = $_GET["type"];
$year = $_GET["year"];
$season = $_GET["season"];
$location = $_GET["location"];
$subLocation = $_GET["sublocation"];

if ($crop == "" || $type == "" || $year == "" || $location == "") {
    exit("Input Error");
}


$sql = "SELECT ID FROM crop_data_set " .
    "WHERE Location = '$location' and Crop = '$crop' and Type = '$type' and " .
    "Year = $year and Season = '$season' and SubLocation = '$subLocation' ";
$con = SetDBConnection();

if (mysqli_connect_errno()) {
    exit("Failed to connect to database server: " . mysqli_connect_error());
} else {
    $result = mysqli_query($con, $sql);

    $dataSet = mysqli_fetch_array($result);
    echo $dataSet["ID"];
    mysqli_close($con);
}

?>