<?php
function AddPlatform($platformName, $con)
{
    $sql = "insert into platform (Name) values ('$platformName')";

    if (mysqli_query($con, $sql)) {
        echo "1";
    } else {
        echo mysqli_error($con);
    }
}

function GetPlatformList($con)
{
    $sql = "select * from platform";

    $result = mysqli_query($con, $sql);

    $platformList = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $platformList[] = $row;
    }
    echo json_encode($platformList);
}

function UpdatePlatform($platformID, $platformName, $con)
{
    $sql = "update platform set Name='$platformName' where id=$platformID";
    //echo $sql;
    $result = mysqli_query($con, $sql);

    if (mysqli_query($con, $sql)) {
        echo "1";
    } else {
        echo mysqli_error($con);

    }
}

function DeletePlatform($platformID, $con)
{
    $sql = "delete from platform where id = $platformID";
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
                AddPlatform($name, $con);
            }
            break;
        case "list":
            {
                GetPlatformList($con);
            }
            break;
        case "edit":
            {
                $id = $_GET["id"];
                $name = mysqli_real_escape_string($con, $_GET['name']);
                UpdatePlatform($id, $name, $con);
            }
            break;
        case "delete":
            {
                $id = $_GET["id"];
                DeletePlatform($id, $con);
            }
            break;

    }
}

mysqli_close($con);
?>
