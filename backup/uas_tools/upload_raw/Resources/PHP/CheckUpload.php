<?php
// Log Document
function _log($str)
{
    // log to the output
    $log_str = date('d.m.Y') . ": {$str}\r\n";
    echo $log_str;

    // log to file
    if (($fp = fopen('upload_log.txt', 'a+')) !== false) {
        fputs($fp, $log_str);
        fclose($fp);
    }
}

//require_once("SetDBConnection.php");
require_once("../../../../resources/database/SetDBConnection.php");

$con = SetDBConnection();

if (mysqli_connect_errno()) {
    echo "Failed to connect to database server: " . mysqli_connect_error();
} else {
    $type = "Update";
    session_start();
    $userName = $_SESSION["email"] ?? '';

    $sql = "SELECT *, TIME_TO_SEC(TIMEDIFF(NOW(),LastUpdate)) as TimeSinceLastUpdate FROM raw_data_upload_status " .
        "WHERE Status != 'Finished' and Uploader = '$userName'";
    //_log('1 check raw data uploadeding: ' . $sql);
    $result = mysqli_query($con, $sql);

    $list = array();
    while ($row = mysqli_fetch_assoc($result)) {
        if ($row["Status"] == "Unzip") {
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
                    $sql = "UPDATE raw_data_upload_status " .
                        "SET Progress = $progress, Status = 'Uploading' " .
                        "WHERE Identifier = '" . $row["Identifier"] . "'";
                } else { //no progress, change status to "Unfinished"

                    if ($row["Status"] == "Uploading") {
                        if ($row["TimeSinceLastUpdate"] >= 60) { //allow 60 seconds to continue upload
                            $sql = "UPDATE raw_data_upload_status " .
                                "SET Status = 'Unfinished' " .
                                "WHERE Identifier = '" . $row["Identifier"] . "'";
                        } else {
                            $type = "NoUpdate";
                        }
                    } elseif ($row["Status"] == "Paused") {
                        if ($row["TimeSinceLastUpdate"] >= 60) { //allow 60 seconds to resume upload
                            $sql = "UPDATE raw_data_upload_status " .
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
