<?php
//require_once("SetDBConnection.php");
require_once("../../../../resources/database/SetDBConnection.php");
require_once("CommonFunctions.php");
$con = SetDBConnection();

if (mysqli_connect_errno()) {
    echo "Failed to connect to database server: " . mysqli_connect_error();
} else {
    $type = $_GET["type"];
    $project = $_GET["project"];

    $tableName = "";
    // QUERIES NEED TO BE COMPARED WITH ORIGINAL FILE
    if ($type == "raw") {
        $tableName = "raw_data_upload_status";
        $sql = "select raw_data_upload_status.*, TIME_TO_SEC(TIMEDIFF(NOW(),LastUpdate)) as TimeSinceLastUpdate, project.Name as ProjectName,  platform.Name as PlatformName, sensor.Name as SensorName, " .
            "replace(flight.Date, '-', '/') as Date, flight.Name as FlightName " .
            "from raw_data_upload_status, flight, project, platform, sensor " .
            "where flight.Project = project.ID and flight.Platform = platform.ID and flight.Sensor = sensor.ID " .
            "and raw_data_upload_status.status != 'Finished' and flight.ID = raw_data_upload_status.Flight and flight.Project like '$project' " .
            //"and flight.Platform like '$platform' and flight.Sensor like '$sensor' ".
            "order by ProjectName, PlatformName, SensorName, Date, FlightName, raw_data_upload_status.FileName";
        //_log('1 check raw uploaded: ' . $sql);
    } else if ($type == "product") {
        $tableName = "imagery_product";
        $sql = "select imagery_product.*, TIME_TO_SEC(TIMEDIFF(NOW(),LastUpdate)) as TimeSinceLastUpdate, project.Name as ProjectName,  platform.Name as PlatformName, sensor.Name as SensorName, " .
            "replace(flight.Date, '-', '/') as Date, flight.Name as FlightName, product_type.Name as TypeName  " .
            "from imagery_product, flight, project, platform, sensor, product_type " .
            "where flight.Project = project.ID and flight.Platform = platform.ID and flight.Sensor = sensor.ID and imagery_product.Type = product_type.ID " .
            "and imagery_product.status != 'Finished' and flight.ID = imagery_product.Flight and flight.Project like '$project' " .
            //"and flight.Platform like '$platform' and flight.Sensor like '$sensor' and imagery_product.Type like '$productType' ".
            "order by ProjectName, PlatformName, SensorName, Date, FlightName, imagery_product.FileName";
        //_log('2 check product uploaded: ' . $sql);
    }

    $result = mysqli_query($con, $sql);

    $list = array();
    while ($row = mysqli_fetch_assoc($result)) {

        if ($row["Status"] == "Converting") {
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
                $update = "Update";
                if ($currentProgress < $progress) { //there is progress in uploading -> update the progress
                    $row["Progress"] = $progress;
                    $sql = "UPDATE " . $tableName .
                        " SET Progress = $progress, Status = 'Uploading' " .
                        "WHERE Identifier = '" . $row["Identifier"] . "'";
                } else { //no progress, change status to "Unfinished"
                    /*
                    if ($row["Status"] == "Uploading"){
                        if ($row["TimeSinceLastUpdate"] >= 3600){ //allow 300 second to continue upload
                            $sql =  "UPDATE ".$tableName.
                                    " SET Status = 'Unfinished' ".
                                    "WHERE Identifier = '".$row["Identifier"]."'";
                        } else {
                            $update = "NoUpdate";
                        }
                    } else if ($row["Status"] == "Paused"){
                        if ($row["TimeSinceLastUpdate"] >= 3600){ //allow 1 hour to resume upload
                            $sql =  "UPDATE ".$tableName.
                                    " SET Status = 'Unfinished' ".
                                    "WHERE Identifier = '".$row["Identifier"]."'";
                        } else {
                            $update = "NoUpdate";
                        }
                    }
                    */
                    if ($row["TimeSinceLastUpdate"] >= 3600) { //allow 1 hour to resume upload
                        $sql = "UPDATE " . $tableName .
                            " SET Status = 'Unfinished' " .
                            "WHERE Identifier = '" . $row["Identifier"] . "'";
                    } else {
                        $update = "NoUpdate";
                    }

                }

                if ($update == "Update") {
                    mysqli_query($con, $sql);
                }
            }
        }

        $row["Size"] = FormatBytes($row["Size"]);
        $list[] = $row;
    }
    echo json_encode($list);
//    echo json_encode($list, JSON_UNESCAPED_UNICODE);
}

?>
