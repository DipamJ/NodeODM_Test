<?php
//require_once("SetDBConnection.php");
require_once("../../../../resources/database/SetDBConnection.php");
$con = SetDBConnection();

if (mysqli_connect_errno()) {
    echo "Failed to connect to database server: " . mysqli_connect_error();
} else {
    $identifier = $_GET["identifier"];
    $sql = "SELECT * FROM vector_data WHERE Identifier = '$identifier'";
    $result = mysqli_query($con, $sql);
    if ($result) {
        $upload = mysqli_fetch_assoc($result);
        $tempFolder = $upload["TempFolder"];

        $sql = "DELETE FROM vector_data WHERE Identifier = '$identifier'";

        if (mysqli_query($con, $sql)) {
            echo "cancelled";
        } else {
            echo mysqli_error($con);
        }

        $cmd = "rm -rf $tempFolder";
        exec($cmd, $output);
    }

    mysqli_close($con);
}
?>