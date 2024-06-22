<?php
//function AddProject($projectName, $con){
function AddProject(
    $projectName,
    $crop,
    $plantingDate,
    $harvestDate,
    $description,
    $centerLat,
    $centerLng,
    $minZoom,
    $maxZoom,
    $defaultZoom,
    $visualization,
    $visible,
    $con
)
{
    $sql = "insert into project (Name, Crop, PlantingDate, HarvestDate, Description, CenterLat, CenterLng, MinZoom, MaxZoom, DefaultZoom, VisualizationPage, Visible) " .
        " values ('$projectName', $crop, '$plantingDate', '$harvestDate', '$description', $centerLat, $centerLng, $minZoom, $maxZoom, $defaultZoom, '$visualization', '$visible')";

    if (mysqli_query($con, $sql)) {

        echo "1";
        $path_name=str_replace(' ', '_', $projectName);
        $path =__DIR__ .'/../../../../uas_data/uploads/photos/'.$path_name;
         if (!file_exists($path)) {
            $old_umask = umask(0);
            mkdir($path, 0777, true);
        }
    } else {
        echo mysqli_error($con);
    }
}

function GetProjectList($con)
{
    $sql = "select project.*, crop.Name as CropName from project, crop " .
        "where project.Crop = crop.ID " .
        "order by project.Name";

    $result = mysqli_query($con, $sql);

    $projectList = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $projectList[] = $row;
    }
    echo json_encode($projectList);
}

//function UpdateProject($projectID, $projectName, $con){
function UpdateProject(
    $projectID,
    $projectName,
    $crop,
    $plantingDate,
    $harvestDate,
    $description,
    $centerLat,
    $centerLng,
    $minZoom,
    $maxZoom,
    $defaultZoom,
    $visualization,
    $visible,
    $con
)
{
    $sql = "update project set Name='$projectName', Crop=$crop, PlantingDate='$plantingDate', HarvestDate='$harvestDate', " .
        "Description='$description', CenterLat=$centerLat, CenterLng=$centerLng, MinZoom=$minZoom, MaxZoom=$maxZoom, DefaultZoom=$defaultZoom, VisualizationPage='$visualization', Visible='$visible' " .
        "where id=$projectID";
    //echo $sql;
    $result = mysqli_query($con, $sql);

    if (mysqli_query($con, $sql)) {
        echo "1";
    } else {
        echo mysqli_error($con);
        echo "\n" . $sql;
    }
}

function DeleteProject($projectID, $con)
{
    $sql = "delete from project where id = $projectID";
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
                $crop = $_GET['crop'];
                $plantingDate = $_GET['plantingDate'];
                $plantingDate = str_replace('/', "-", $plantingDate);
                $harvestDate = $_GET['harvestDate'];
                $harvestDate = str_replace('/', "-", $harvestDate);
                $description = mysqli_real_escape_string($con, $_GET['description']);
                $centerLat = $_GET['centerLat'];
                $centerLng = $_GET['centerLng'];
                $minZoom = $_GET['minZoom'];
                $maxZoom = $_GET['maxZoom'];
                $defaultZoom = $_GET['defaultZoom'];
                $visualization = $_GET['visualization'];
                //$visible = $_GET['visible'];
                $visible = 0;

                //AddProject($name, $con);
                AddProject(
                    $name,
                    $crop,
                    $plantingDate,
                    $harvestDate,
                    $description,
                    $centerLat,
                    $centerLng,
                    $minZoom,
                    $maxZoom,
                    $defaultZoom,
                    $visualization,
                    $visible,
                    $con
                );
            }
            break;
        case "list":
            {
                GetProjectList($con);
            }
            break;
        case "edit":
            {
                $id = $_GET["id"];
                $name = mysqli_real_escape_string($con, $_GET['name']);
                $crop = $_GET['crop'];
                $plantingDate = $_GET['plantingDate'];
                $plantingDate = str_replace('/', "-", $plantingDate);
                $harvestDate = $_GET['harvestDate'];
                $harvestDate = str_replace('/', "-", $harvestDate);
                $description = mysqli_real_escape_string($con, $_GET['description']);
                $centerLat = $_GET['centerLat'];
                $centerLng = $_GET['centerLng'];
                $minZoom = $_GET['minZoom'];
                $maxZoom = $_GET['maxZoom'];
                $defaultZoom = $_GET['defaultZoom'];
                $visualization = $_GET['visualization'];
                $visible = $_GET['visible'];
                //UpdateProject($id, $name, $con);
                UpdateProject(
                    $id,
                    $name,
                    $crop,
                    $plantingDate,
                    $harvestDate,
                    $description,
                    $centerLat,
                    $centerLng,
                    $minZoom,
                    $maxZoom,
                    $defaultZoom,
                    $visualization,
                    $visible,
                    $con
                );
            }
            break;
        case "delete":
            {
                $id = $_GET["id"];
                DeleteProject($id, $con);
            }
            break;

    }
}

mysqli_close($con);
?>
