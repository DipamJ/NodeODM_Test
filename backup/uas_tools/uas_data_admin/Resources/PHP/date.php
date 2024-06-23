<?php
function GetProjectList($con)
{
    $sql = "select project.*" .
        "from project, date " .
        "where project.ID = date.Project";

    $result = mysqli_query($con, $sql);

    $projectList = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $projectList[] = $row;
    }
    echo json_encode($projectList);
}


function GetPlatformList($projectID, $con)
{

    $sql = "select platform.ID as ID, platform.Name as Name " .
        "from platform, date " .
        "where platform.ID = date.Platform and date.Project = $projectID";

    $result = mysqli_query($con, $sql);

    $platformList = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $platformList[] = $row;
    }
    echo json_encode($platformList);
}

function GetSensorList($projectID, $platformID, $con)
{

    $sql = "select sensor.ID as ID, sensor.Name as Name " .
        "from sensor, date " .
        "where sensor.ID = date.Sensor and date.Project = $projectID and date.Platform = $platformID";

    $result = mysqli_query($con, $sql);

    $sensorList = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $sensorList[] = $row;
    }
    echo json_encode($sensorList);
}

function GetDateList($projectID, $platformID, $sensorID, $con)
{

    $sql = "select date.ID as ID, date.Date as Date " .
        "from date " .
        "where date.Project = $projectID and date.Platform = $platformID and date.Sensor = $sensorID";

    $result = mysqli_query($con, $sql);

    $dateList = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $dateList[] = $row;
    }
    echo json_encode($dateList);
}

//require_once("SetDBConnection.php");
require_once("../../../../resources/database/SetDBConnection.php");

$con = SetDBConnection();

if (mysqli_connect_errno()) {
    echo "Failed to connect to database server: " . mysqli_connect_error();
} else {

    $type = $_GET["type"];

    switch ($type) {
        case "project":
            {
                GetProjectList($con);
            }
            break;

        case "platform":
            {
                $projectID = $_GET['project'];
                GetPlatformList($projectID, $con);

            }
            break;
        case "sensor":
            {
                $projectID = $_GET['project'];
                $platformID = $_GET['platform'];
                GetSensorList($projectID, $platformID, $con);
            }
            break;
        case "date":
            {
                $projectID = $_GET['project'];
                $platformID = $_GET['platform'];
                $sensorID = $_GET['sensor'];
                GetDateList($projectID, $platformID, $sensorID, $con);
            }
            break;

    }
}

mysqli_close($con);
?>
