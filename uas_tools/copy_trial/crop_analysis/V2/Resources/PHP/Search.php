<?php
require_once("SetDBConnection.php");
require_once("SetFilePath.php");

// When All is selected '%' is sent to the $_GET variables
$crop = $_GET["crop"];
$type = $_GET["type"];
$year = $_GET["year"];
$season = $_GET["season"];
$location = $_GET["location"];
$subLocation = $_GET["sublocation"];

$sql = "SELECT * FROM crop_data " .
    "WHERE Location like '$location' and Crop like '$crop' and Type like '$type' and " .
    "Year like '$year' and Season like '$season' and SubLocation like '$subLocation'";// and MultipleDates = 1";
$con = SetDBConnection();

if (mysqli_connect_errno()) {
    exit("Failed to connect to database server: " . mysqli_connect_error());
} else {
    $result = mysqli_query($con, $sql);

    $list = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $list[] = $row;
    }
    echo json_encode($list);

    // mysqli_close($con);
}
mysqli_close($con);
?>
