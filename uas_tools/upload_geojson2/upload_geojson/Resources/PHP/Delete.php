<?php
//require_once("SetDBConnection.php");
require_once("../../../../resources/database/SetDBConnection.php");

$con = SetDBConnection();

if (mysqli_connect_errno()) {
    echo "Failed to connect to database server: " . mysqli_connect_error();
} else {
    $id = $_GET["id"];
    $sql = "select * from vector_data where id = '$id'";
    $result = mysqli_query($con, $sql);
    if ($result) {
        $upload = mysqli_fetch_assoc($result);
        $uploadFolder = $upload["UploadFolder"];
        $tempFolder = $upload["TempFolder"];

        $sql = "delete from vector_data where id = '$id'";
        $result = mysqli_query($con, $sql);

        //if (mysqli_query($con, $sql)) {
        if ($result) {

            if (file_exists($uploadFolder)) {
                $cmd = "rm -rf $uploadFolder";
                exec($cmd, $output);
            }

            if (file_exists($tempFolder)) {
                $cmd = "rm -rf $tempFolder";
                exec($cmd, $output);
            }

            echo "File has been deleted.";
        } else {
            echo mysqli_error($con);
        }

    } else {
        echo mysqli_error($con);
    }
}
?>
