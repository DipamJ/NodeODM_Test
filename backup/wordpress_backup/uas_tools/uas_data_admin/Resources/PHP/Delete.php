<?php
require_once("SetDBConnection.php");

$con = SetDBConnection();

if (mysqli_connect_errno()) {
    echo "Failed to connect to database server: " . mysqli_connect_error();
} else {
    $id = $_GET["id"];
    $type = $_GET["type"];

    if ($type == "raw") {
        $sql = "select * from raw_data_upload_status where id = $id";
    } else if ($type == "product") {
        $sql = "select * from imagery_product where id = $id";
    }
    $result = mysqli_query($con, $sql);
    if ($result) {
        $upload = mysqli_fetch_assoc($result);
        $uploadFolder = $upload["UploadFolder"];
        $filePath = $upload["UploadFolder"] . "/" . $upload["FileName"];
        $tempFolder = $upload["TempFolder"];

        if ($type == "raw") {
            $sql = "delete from raw_data_upload_status where id = $id";
        } else if ($type == "product") {
            $sql = "delete from imagery_product where id = $id";
        }

        $result = mysqli_query($con, $sql);

        if (mysqli_query($con, $sql)) {
            if ($type == "raw") {
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            } else if ($type == "product") {
                if (file_exists($uploadFolder)) {
                    $cmd = "rm -rf $uploadFolder";
                    exec($cmd, $output);
                }
            }

            if (file_exists($tempFolder)) {
                $cmd = "rm -rf $tempFolder";
                exec($cmd, $output);
            }

            echo "deleted";
        } else {
            echo mysqli_error($con);
        }

    } else {
        echo mysqli_error($con);
    }


}
?>