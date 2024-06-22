<?php
//require_once("SetDBConnection.php");
require_once("../../../../resources/database/SetDBConnection.php");

ini_set('display_errors', 1);

$con = SetDBConnection();

if (mysqli_connect_errno()) {
    echo "Failed to connect to database server: " . mysqli_connect_error();
} else {
    $crop = $_GET["crop"];
    $type = $_GET["type"];
    $year = $_GET["year"];
    $location = $_GET["location"];
    $season = $_GET["season"];
    $subLocation = $_GET["sublocation"];

    $sql = "SELECT * FROM crop_data " .
        "WHERE Crop = '$crop' and Type = '$type' and Year = '$year' and " .
        "Location = '$location' and Season = '$season' and SubLocation = '$subLocation'";

    $result = mysqli_query($con, $sql);

    if ($result) {
        $rowcount = mysqli_num_rows($result);
        if ($rowcount > 0) {
            $row = mysqli_fetch_array($result);
            echo $row["ID"];
        } else {
            echo "OK";
        }
    }
}
?>
