<?php
require_once('CommonFunctions.php');

function AddType($typeName, $typeType, $con)
{
    $sql = "insert into product_type (Name, Type) values ('$typeName', '$typeType')";// REPLACE $typeType WITH OPTION PICKED FROM SELECT BOX
    echo($sql);
    if (mysqli_query($con, $sql)) {
        echo "1";
    } else {
        echo mysqli_error($con);
    }
}

function GetTypeList($con)
{
    $sql = "select * from product_type";

    $result = mysqli_query($con, $sql);

    $typeList = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $typeList[] = $row;
    }
    echo json_encode($typeList);
}

function UpdateType($typeID, $typeName, $typeType, $con)
{
    $sql = "update product_type
        set Name='$typeName',
            Type = '$typeType'
        where id=$typeID";
    //echo $sql;
    $result = mysqli_query($con, $sql);

    if (mysqli_query($con, $sql)) {
        echo "1";
    } else {
        echo mysqli_error($con);
    }
}

function DeleteType($typeID, $con)
{
    $sql = "delete from product_type where id = $typeID";
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
                $type = mysqli_real_escape_string($con, $_GET['type']);// NEED TO CHECK IF GETS SET
                _log($type);

                AddType($name, $type, $con);
            }
            break;
        case "list":
            {
                GetTypeList($con);
            }
            break;
        case "edit":
            {
                $id = $_GET["id"];
                $name = mysqli_real_escape_string($con, $_GET['name']);
                $type = mysqli_real_escape_string($con, $_GET['type']);// NEED TO CHECK IF GETS SET

                UpdateType($id, $name, $type, $con);
            }
            break;
        case "delete":
            {
                $id = $_GET["id"];
                DeleteType($id, $con);
            }
            break;
    }
}

mysqli_close($con);
?>