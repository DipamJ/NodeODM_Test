<?php
require_once("SetDBConnection.php");
$con = SetDBConnection();

if (mysqli_connect_errno()) {
    echo "Failed to connect to database server: " . mysqli_connect_error();
} else {
    $identifier = $_GET["identifier"] ?? '';
    // Table does not exist
    $sql = "UPDATE upload_status " .
        "SET Status='Error' " .
        "WHERE Identifier = '$identifier'";

    mysqli_query($con, $sql);
    mysqli_close($con);
}

//        foreach ($_GET as $key=>$get_data) {
//            echo "You posted:" . $key . " = " . $get_data . "<br>";
//        }
//
?>
