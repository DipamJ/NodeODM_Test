<?php
require_once("SetDBConnection.php");

$con = SetDBConnection();

if (mysqli_connect_errno()) {
    echo "Failed to connect to database server: " . mysqli_connect_error();
} else {
    // Get file ID
    $id = $_GET["id"];

//    _log("id: " . $id);

    $sql = "select Filename from crop_data where id = $id";
    //$result_filename = mysqli_query($con, $sql);
    $result_filename = $con->query($sql);

    //_log("sql: " . $sql);
    //_log("result_filename: " . $result_filename);

    if ($result_filename->num_rows > 0) {
        // output data of each row
        while ($row = $result_filename->fetch_assoc()) {
            //echo "FileName: " . $row["Filename"];
            //print_r($row);

            //added
            // Delete file from server
            $file_pointer = "../../../../temp/CropData/" . $row["Filename"];
            //_log("file_pointer: " . $file_pointer);

            // Use unlink() function to delete a file
            if (!unlink($file_pointer)) {
                echo($row["Filename"] . " cannot be deleted due to an error");
            } else {
                echo($row["Filename"] . " has been deleted.");
            }
            //added
        }
        //print_r($row);
    }
    //echo "FileName: " . $row["Filename"];

    $sql = "delete from crop_data where id = $id";
    //_log("sql: " . $sql);
    // Execute query
    $result = mysqli_query($con, $sql);
    // If query was successfully executed
    if ($result) {
        //echo "File has been deleted.";
        echo "";
    } // If query was not successfully executed
    else {
        echo mysqli_error($con);
    }
}
?>