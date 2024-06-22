<?php
function GetProjectList($con)
{
    $sql = "Select * from project order by Name";

    $result = mysqli_query($con, $sql);

    $list = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $list[] = $row;
    }
    echo json_encode($list);
}

function GetPlatformList($project, $con)
{
    $sql = "Select * from platform order by Name";

    $result = mysqli_query($con, $sql);

    $list = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $list[] = $row;
    }
    echo json_encode($list);
}

function GetSensorList($project, $platform, $con)
{
    $sql = "Select * from sensor order by Name";

    $result = mysqli_query($con, $sql);

    $list = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $list[] = $row;
    }
    echo json_encode($list);
}

function GetDateList($project, $platform, $sensor, $con)
{
    $sql = "Select replace(flight.Date, '-', '/') as ID, replace(flight.Date, '-', '/') as Name from flight, project " .
        "where flight.Project = $project and flight.Platform = $platform and flight.Sensor = $sensor group by ID order by STR_TO_DATE(replace(flight.Date, '-', '/'), '%m/%d/%Y')";

    $result = mysqli_query($con, $sql);

    $list = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $list[] = $row;
    }
    echo json_encode($list);
}

function GetFlighList($project, $platform, $sensor, $date, $con)
{
    $sql = "select flight.*, project.Name as ProjectName,  platform.Name as PlatformName, sensor.Name as SensorName " .
        "from flight, project, platform, sensor " .
        "where flight.Project = project.ID and flight.Platform = platform.ID and flight.Sensor = sensor.ID " .
        "and flight.Project = $project and flight.Platform = $platform and flight.Sensor = $sensor and flight.Date = '$date' order by Name";

    $result = mysqli_query($con, $sql);

    $list = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $list[] = $row;
    }
    echo json_encode($list);
}

function GetProductTypeList($con)
{
    $sql = "Select * from product_type";

    $result = mysqli_query($con, $sql);

    $list = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $list[] = $row;
    }
    echo json_encode($list);
}

require_once("SetDBConnection.php");

$con = SetDBConnection();

if (mysqli_connect_errno()) {
    echo "Failed to connect to database server: " . mysqli_connect_error();
} else {
    $type = $_GET["type"] ?? '';

    switch ($type) {
        case "project":
            {
                GetProjectList($con);
            }
            break;

        case "platform":
            {
                $projectID = $_GET['project'] ?? '';
                GetPlatformList($projectID, $con);

            }
            break;
        case "sensor":
            {
                $projectID = $_GET['project'] ?? '';
                $platformID = $_GET['platform'] ?? '';
                GetSensorList($projectID, $platformID, $con);
            }
            break;
        case "date":
            {
                $projectID = $_GET['project'] ?? '';
                $platformID = $_GET['platform'] ?? '';
                $sensorID = $_GET['sensor'] ?? '';
                GetDateList($projectID, $platformID, $sensorID, $con);
            }
            break;
        case "flight":
            {
                $projectID = $_GET['project'] ?? '';
                $platformID = $_GET['platform'] ?? '';
                $sensorID = $_GET['sensor'] ?? '';
                $date = $_GET['date'] ?? '';
                $date = str_replace('/', "-", $date);
                GetFlighList($projectID, $platformID, $sensorID, $date, $con);
            }
            break;
        case "product-type":
            {
                GetProductTypeList($con);
            }
            break;
    }
}

mysqli_close($con);
?>
