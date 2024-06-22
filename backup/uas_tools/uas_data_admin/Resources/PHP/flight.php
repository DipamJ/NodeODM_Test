<?php
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

function AddFlight($flightName, $date, $project, $platform, $sensor, $altitude, $forward, $side, $con)
{
    $sql = "insert into flight (Name, Date, Project, Platform, Sensor, Altitude, Forward, Side) " .
        "values ('$flightName', '$date', $project, $platform, $sensor, $altitude, $forward, $side)";
    //_log("sql: " . $sql);

    if (mysqli_query($con, $sql)) {
        echo "1";
    } else {
        echo mysqli_error($con);
    }
}

function GetFlightList($con, $project, $platform, $sensor)
{
    $sql = "select flight.*, project.Name as ProjectName,  platform.Name as PlatformName, sensor.Name as SensorName " .
        "from flight, project, platform, sensor " .
        "where flight.Project = project.ID and flight.Platform = platform.ID and flight.Sensor = sensor.ID " .
        "and project.ID = $project and platform.ID like '$platform' and sensor.ID like '$sensor' " .
        "order by ProjectName, flight.Name";
    $result = mysqli_query($con, $sql);

    $flightList = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $flightList[] = $row;
    }
    echo json_encode($flightList);
}

function UpdateFlight($flightID, $flightName, $date, $project, $platform, $sensor, $altitude, $forward, $side, $con)
{
    $sql = "update flight set Name='$flightName', Date='$date', Project=$project, Platform=$platform, " .
        "Sensor=$sensor, Altitude=$altitude, Forward= $forward, Side= $side where id=$flightID";

    $result = mysqli_query($con, $sql);

    if (mysqli_query($con, $sql)) {
        echo "1";
    } else {
        echo mysqli_error($con);

    }
}

function DeleteFlight($flightID, $con)
{
    $sql = "delete from flight where id = $flightID";
    $result = mysqli_query($con, $sql);

    if (mysqli_query($con, $sql)) {
        echo "1";
    } else {
        echo mysqli_error($con);

    }
}

//require_once("SetDBConnection.php");
require_once("../../../../resources/database/SetDBConnection.php");

$con = SetDBConnection();

if (mysqli_connect_errno()) {
    echo "Failed to connect to database server: " . mysqli_connect_error();
} else {
    $action = $_GET["action"];

    switch ($action) {
        case "add":
            {
                $name = mysqli_real_escape_string($con, $_GET['name']);
                $project = $_GET['project'];
                $platform = $_GET['platform'];
                $sensor = $_GET['sensor'];
                $date = $_GET['date'];
                $date = str_replace('/', "-", $date);
                $altitude = $_GET['altitude'];
                $forward = $_GET['forward'];
                $side = $_GET['side'];

                AddFlight($name, $date, $project, $platform, $sensor, $altitude, $forward, $side, $con);
            }
            break;
        case "list":
            {
                $project = $_GET['project'];
                $platform = $_GET['platform'];
                $sensor = $_GET['sensor'];
                GetFlightList($con, $project, $platform, $sensor);
            }
            break;
        case "edit":
            {
                $id = $_GET["id"];
                $name = mysqli_real_escape_string($con, $_GET['name']);
                $project = $_GET['project'];
                $platform = $_GET['platform'];
                $sensor = $_GET['sensor'];
                $date = $_GET['date'];
                $date = str_replace('/', "-", $date);
                $altitude = $_GET['altitude'];
                $forward = $_GET['forward'];
                $side = $_GET['side'];

                UpdateFlight($id, $name, $date, $project, $platform, $sensor, $altitude, $forward, $side, $con);
            }
            break;
        case "delete":
            {
                $id = $_GET["id"];
                DeleteFlight($id, $con);
            }
            break;

    }
}

mysqli_close($con);
?>