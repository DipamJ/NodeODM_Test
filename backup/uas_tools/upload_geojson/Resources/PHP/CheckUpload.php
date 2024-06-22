<?php
//require_once("SetDBConnection.php");
require_once("../../../../resources/database/SetDBConnection.php");
$con = SetDBConnection();

if (mysqli_connect_errno()) {
    echo "Failed to connect to database server: " . mysqli_connect_error();
} else {
    $type = "Update";
    session_start();
    //$userName = $_SESSION["username"];
    $userName = $_SESSION["email"];

//    $sql = "SELECT imagery_product.*, TIME_TO_SEC(TIMEDIFF(NOW(),LastUpdate)) as TimeSinceLastUpdate FROM imagery_product " .
//        "WHERE Status != 'Finished' and Uploader = '$userName' " .
//        "AND imagery_product.Type in (select product_type.ID from product_type where product_type.Type = 'V')";

//    $sql = "SELECT imagery_product.*, TIME_TO_SEC(TIMEDIFF(NOW(),LastUpdate)) as TimeSinceLastUpdate FROM imagery_product " .
//        "WHERE Status != 'Finished' and Uploader = '$userName' " .
//        "AND imagery_product.Type in (select product_type.ID from product_type where product_type.Type = '8')";

    $sql = "SELECT vector_data.*, TIME_TO_SEC(TIMEDIFF(NOW(),LastUpdate)) as TimeSinceLastUpdate FROM vector_data " .
        "WHERE Status != 'Finished' and Uploader = '$userName' " .
        "AND vector_data.Type in (select product_type.ID from product_type where product_type.Type = '8')";

    $result = mysqli_query($con, $sql);

    $list = array();
    while ($row = mysqli_fetch_assoc($result)) {

        if ($row["Status"] == "Converting") {
            $filename = $row["UploadFolder"] . "/convert_log.txt";
            $row["ConvertProgress"] = file_get_contents($filename);
        } else if ($row["Status"] == "Pending") {

        } else {

            $directory = $row["TempFolder"] . "/";
            $uploadedChunk = 0;
            $files = glob($directory . "*");
            if ($files) {
                $uploadedChunk = count($files);
            }
            if ($row["ChunkCount"] > 0) {
                $progress = floor($uploadedChunk * 100 / $row["ChunkCount"]);
                $currentProgress = floor($row["Progress"]);
                if ($currentProgress < $progress) { //there is progress in uploading -> update the progress
                    $row["Progress"] = $progress;
//                    $sql = "UPDATE imagery_product " .
//                        "SET Progress = $progress, Status = 'Uploading' " .
//                        "WHERE Identifier = '" . $row["Identifier"] . "'";
                    $sql = "UPDATE vector_data " .
                        "SET Progress = $progress, Status = 'Uploading' " .
                        "WHERE Identifier = '" . $row["Identifier"] . "'";

                } else { //no progress, change status to "Unfinished"

                    if ($row["Status"] == "Uploading") {
                        if ($row["TimeSinceLastUpdate"] >= 60) { //allow 300 second to continue upload
//                            $sql = "UPDATE imagery_product " .
//                                "SET Status = 'Unfinished' " .
//                                "WHERE Identifier = '" . $row["Identifier"] . "'";
                            $sql = "UPDATE vector_data " .
                                "SET Status = 'Unfinished' " .
                                "WHERE Identifier = '" . $row["Identifier"] . "'";
                        } else {
                            $type = "NoUpdate";
                        }
                    } else if ($row["Status"] == "Paused") {
                        if ($row["TimeSinceLastUpdate"] >= 60) { //allow 1 hour to resume upload
//                            $sql = "UPDATE imagery_product " .
//                                "SET Status = 'Unfinished' " .
//                                "WHERE Identifier = '" . $row["Identifier"] . "'";
                            $sql = "UPDATE vector_data " .
                                "SET Status = 'Unfinished' " .
                                "WHERE Identifier = '" . $row["Identifier"] . "'";
                        } else {
                            $type = "NoUpdate";
                        }
                    }

                }
                if ($type == "Update") {
                    mysqli_query($con, $sql);
                }
            }
        }
        $list[] = $row;
    }
    echo json_encode($list);
    mysqli_close($con);
}
?>
