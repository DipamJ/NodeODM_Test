<?php
require_once("SetFilePath.php");

//function AddProject($pointcloudName, $con){
function AddPointcloud($pointcloudName, $project, $date, $description, $lat, $lng, $con)
{
    $sql = "insert into pointcloud (Name, Project, Date, Description, Lat, Lng) " .
        " values ('$pointcloudName', $project, '$date', '$description', $lat, $lng)";

    if (mysqli_query($con, $sql)) {
        echo "1";
    } else {
        echo mysqli_error($con);
    }
}

function GetPointcloudList($con)
{
    //$sql = "SELECT pointcloud.*, project.Name as ProjectName FROM pointcloud, project ".
    //	   "WHERE pointcloud.Status = 'Finished' and pointcloud.Project = project.ID";
    $sql = "SELECT pointcloud.*, project.Name as ProjectName FROM pointcloud, project " .
        "WHERE pointcloud.Status = 'Finished' and (pointcloud.Project = project.ID or pointcloud.Project = 0) " .
        "GROUP BY pointcloud.ID";

    $result = mysqli_query($con, $sql);

    $list = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $list[] = $row;
    }
    echo json_encode($list);
}

//function UpdatePointcloud($pointcloudID, $pointcloudName, $con){
function UpdatePointcloud($pointcloudID, $pointcloudName, $project, $date, $description, $lat, $lng, $con)
{
    $sql = "update pointcloud set Name='$pointcloudName', Project='$project', Date='$date', Description='$description', Lat=$lat, Lng=$lng " .
        "where id='$pointcloudID'";
    //echo $sql;
    //$result = mysqli_query($con,$sql);

    if (mysqli_query($con, $sql)) {
        echo "1";
    } else {
        echo mysqli_error($con);
        echo "\n" . $sql;
    }
}

function DeletePointcloud($pointcloudID, $con)
{
    $sql = "select * from pointcloud where id = $pointcloudID";
    $result = mysqli_query($con, $sql);
    $pointcloud = mysqli_fetch_assoc($result);

    $sql = "delete from pointcloud where id = $pointcloudID";
    //$result = mysqli_query($con,$sql);

    if (mysqli_query($con, $sql)) {
        echo "1";

//        //Move uploaded data to trash
//        $old = umask(0);
//        $command = "chmod 777 '" . $pointcloud["UploadFolder"] . "'";
//        exec($command, $o, $r);
//        umask($old);
//
//        $command = "mv '" . $pointcloud["UploadFolder"] . "' '" . SetTrashFolderPath() . "' 2>&1";
//        exec($command, $o, $r);

        // Get the location of the file
        $file_folder =  $pointcloud["UploadFolder"];
        //_log("file_folder: " .$file_folder);
        //check if file exists
        if (file_exists($file_folder)) {
            //$cmd = "rm -rf $uploadFolder";
            // Remove file's folder
            if ($file_folder != '/var/www/html/uas_data/uploads/pointclouds/'){
                $cmd = "rm -rf $file_folder";
                exec($cmd, $output);
            }
        }
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
                $date = $_GET['date'];
                //$date = str_replace('/',"-",$date);
                $description = str_replace('/', "-", $plantingDate);

                $description = mysqli_real_escape_string($con, $_GET['description']);
                $lat = $_GET['lat'];
                $lng = $_GET['lng'];

                //AddPointcloud($name, $con);
                AddPointcloud($name, $project, $date, $description, $lat, $lng, $con);
            }
            break;
        case "list":
            {
                GetPointcloudList($con);
            }
            break;
        case "edit":
            {
                $id = $_GET["id"];
                $name = mysqli_real_escape_string($con, $_GET['name']);
                $project = $_GET['project'];
                $date = $_GET['date'];
                //$date = str_replace('/',"-",$date);
                $date = str_replace('/', "-", $date);

                $description = mysqli_real_escape_string($con, $_GET['description']);
                $lat = $_GET['lat'];
                $lng = $_GET['lng'];

                UpdatePointcloud($id, $name, $project, $date, $description, $lat, $lng, $con);
            }
            break;
        case "delete":
            {
                $id = $_GET["id"];
                DeletePointcloud($id, $con);
            }
            break;

    }
}

mysqli_close($con);
?>
