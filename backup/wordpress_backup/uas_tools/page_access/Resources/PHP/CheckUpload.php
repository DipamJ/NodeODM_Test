<?php
require_once("SetDBConnection.php");
require_once("CommonFunctions.php");
$con = SetDBConnection();

if (mysqli_connect_errno()) {
    echo "Failed to connect to database server: " . mysqli_connect_error();
} else {
    $type = $_GET["type"];
    $project = $_GET["project"];

    $sql = "select imagery_product.*, TIME_TO_SEC(TIMEDIFF(NOW(),LastUpdate)) as TimeSinceLastUpdate, project.Name as ProjectName,  platform.Name as PlatformName, sensor.Name as SensorName, " .
        "replace(flight.Date, '-', '/') as Date, flight.Name as FlightName " .
        "from imagery_product, flight, project, platform, sensor " .
        "where flight.Project = project.ID and flight.Platform = platform.ID and flight.Sensor = sensor.ID " .
        "and imagery_product.status != 'Finished' and flight.ID = imagery_product.Flight and flight.Project like '$project' " .
        "order by ProjectName, PlatformName, SensorName, Date, FlightName, imagery_product.FileName";

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
                    $sql = "UPDATE imagery_product " .
                        "SET Progress = $progress, Status = 'Uploading' " .
                        "WHERE Identifier = '" . $row["Identifier"] . "'";
                } else { //no progress, change status to "Unfinished"

                    if ($row["Status"] == "Uploading") {
                        if ($row["TimeSinceLastUpdate"] >= 60) { //allow 300 second to continue upload
                            $sql = "UPDATE imagery_product " .
                                "SET Status = 'Unfinished' " .
                                "WHERE Identifier = '" . $row["Identifier"] . "'";
                        } else {
                            $type = "NoUpdate";
                        }
                    } else if ($row["Status"] == "Paused") {
                        if ($row["TimeSinceLastUpdate"] >= 60) { //allow 1 hour to resume upload
                            $sql = "UPDATE imagery_product " .
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

        $row["Size"] = FormatBytes($row["Size"]);
        $list[] = $row;
    }
    echo json_encode($list);
}
?>