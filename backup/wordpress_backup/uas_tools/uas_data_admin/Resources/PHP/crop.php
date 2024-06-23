<?php
function AddCrop($cropName, $con)
{
    $sql = "insert into crop (Name) values ('$cropName')";

    if (mysqli_query($con, $sql)) {
        echo "1";
    } else {
        echo mysqli_error($con);
    }
}

function GetCropList($con)
{
    $sql = "select * from crop order by Name";

    $result = mysqli_query($con, $sql);

    $cropList = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $cropList[] = $row;
    }
    echo json_encode($cropList);
}

function UpdateCrop($cropID, $cropName, $con)
{
    $sql = "update crop set Name='$cropName' where id=$cropID";
    //echo $sql;
    $result = mysqli_query($con, $sql);

    if (mysqli_query($con, $sql)) {
        echo "1";
    } else {
        echo mysqli_error($con);
    }
}

function DeleteCrop($cropID, $con)
{
    $sql = "delete from crop where id = $cropID";
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

                AddCrop($name, $con);
            }
            break;
        case "list":
            {
                GetCropList($con);
            }
            break;
        case "edit":
            {
                $id = $_GET["id"];
                $name = mysqli_real_escape_string($con, $_GET['name']);

                UpdateCrop($id, $name, $con);
            }
            break;
        case "delete":
            {
                $id = $_GET["id"];
                DeleteCrop($id, $con);
            }
            break;

    }
}

mysqli_close($con);
?>