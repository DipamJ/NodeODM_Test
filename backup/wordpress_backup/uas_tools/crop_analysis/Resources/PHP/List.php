<?php
require_once("SetDBConnection.php");

$con = SetDBConnection();

if (mysqli_connect_errno()) {
    echo "Failed to connect to database server: " . mysqli_connect_error();
} else {

    $name = $_GET["name"];

    $sql = "Select $name as Name from crop_data group by $name";

    $result = mysqli_query($con, $sql);

    $list = array();
    while ($row = mysqli_fetch_assoc($result)) {
        if ($row["Name"] != "") {
            $list[] = $row;
        }
    }
    echo json_encode($list);
}

mysqli_close($con);
?>