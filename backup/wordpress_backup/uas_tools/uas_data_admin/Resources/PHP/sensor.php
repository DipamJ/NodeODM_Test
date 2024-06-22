<?php
function AddSensor($sensorName, $con)
{
    $sql = "insert into sensor (Name) values ('$sensorName')";

    if (mysqli_query($con, $sql)) {
        echo "1";
    } else {
        echo mysqli_error($con);
    }
}

function GetSensorList($con)
{
    $sql = "select * from sensor";

    $result = mysqli_query($con, $sql);

    $sensorList = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $sensorList[] = $row;
    }
    echo json_encode($sensorList);
}

function UpdateSensor($sensorID, $sensorName, $con)
{
    $sql = "update sensor set Name='$sensorName' where id=$sensorID";
    //echo $sql;
    $result = mysqli_query($con, $sql);

    if (mysqli_query($con, $sql)) {
        echo "1";
    } else {
        echo mysqli_error($con);

    }
}

function DeleteSensor($sensorID, $con)
{
    $sql = "delete from sensor where id = $sensorID";
    $result = mysqli_query($con, $sql);

    if (mysqli_query($con, $sql)) {
        echo "1";
    } else {
        echo mysqli_error($con);

    }
}

require_once("SetDBConnection.php");

$con = SetDBConnection();

if (mysqli_connect_errno()) {
    echo "Failed to connect to database server: " . mysqli_connect_error();
} else {

    $action = $_GET["action"];

    switch ($action) {
        case "add":
            {
                $name = mysqli_real_escape_string($con, $_GET['name']);
                AddSensor($name, $con);
            }
            break;
        case "list":
            {
                GetSensorList($con);
            }
            break;
        case "edit":
            {
                $id = $_GET["id"];
                $name = mysqli_real_escape_string($con, $_GET['name']);
                UpdateSensor($id, $name, $con);
            }
            break;
        case "delete":
            {
                $id = $_GET["id"];
                DeleteSensor($id, $con);
            }
            break;

    }
}

mysqli_close($con);
?>