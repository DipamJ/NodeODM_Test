<?php
//require_once("SetDBConnection.php");
require_once("../../../../resources/database/SetDBConnection.php");

// Log Document
//function _log($str)
//{
//    // log to the output
//    $log_str = date('d.m.Y') . ": {$str}\r\n";
//    echo $log_str;
//
//    // log to file
//    if (($fp = fopen('upload_log.txt', 'a+')) !== false) {
//        fputs($fp, $log_str);
//        fclose($fp);
//    }
//}

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

    //_log("sql: " . $sql);

    $result = mysqli_query($con, $sql);
    if ($result) {
        $upload = mysqli_fetch_assoc($result);
        $uploadFolder = $upload["UploadFolder"];
        $filePath = $upload["UploadFolder"] . "/" . $upload["FileName"];
        $tempFolder = $upload["TempFolder"];

        // Strip the string from characters after and including "RGB_Ortho"
        $variable = substr($uploadFolder, 0, strpos($uploadFolder, "RGB_Ortho"));
        //Find the last 10 characters where the date is str2: /20160427/
        $str2 = substr($variable, -10);
        // This is the correct path that should be deleted
        $to_delete_uploadFolder = str_replace($str2, '', $variable);


        //_log("filePath: " . $filePath);
//        _log("tempFolder: " . $tempFolder);
//        _log("uploadFolder: " . $uploadFolder);
//        _log("variable: " . $variable);
//        _log("str2: " . $str2);
//        _log("goodUrl: " . $to_delete_uploadFolder);

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
                    //echo "File exists";
                }
            } else if ($type == "product") {
                if (file_exists($uploadFolder)) {
                    //$cmd = "rm -rf $uploadFolder";
                    $cmd = "rm -rf $to_delete_uploadFolder";
                    exec($cmd, $output);
                }
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
