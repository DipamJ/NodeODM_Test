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
        $con
    )
    {
        $sql =  "insert into project (Name, Crop, PlantingDate, HarvestDate, Description, CenterLat, CenterLng, MinZoom, MaxZoom, DefaultZoom) ".
                " values ('$projectName', $crop, '$plantingDate', '$harvestDate', '$description', $centerLat, $centerLng, $minZoom, $maxZoom, $defaultZoom)";
								_log('insert into project: '.$sql);

        if (mysqli_query($con, $sql)) {
            echo "1";
        } else {
            echo mysqli_error($con);
        }
    }

    function GetProjectList($con)
    {
        $sql =  "select project.*, crop.Name as CropName from project, crop ".
                "where project.Crop = crop.ID ".
                "order by project.Name";
        //_log('select project.*, crop.Name: '.$sql);

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
        $con
    )
    {
        $sql =  "update project set Name='$projectName', Crop=$crop, PlantingDate='$plantingDate', HarvestDate='$harvestDate', ".
                "Description='$description', CenterLat=$centerLat, CenterLng=$centerLng, MinZoom=$minZoom, MaxZoom=$maxZoom, DefaultZoom=$defaultZoom ".
                "where id=$projectID";
        //_log('update project set Name: '.$sql);

        $result = mysqli_query($con, $sql);

        if (mysqli_query($con, $sql)) {
            echo "1";
        } else {
            echo mysqli_error($con);
            echo "\n".$sql;
        }
    }

    function DeleteProject($projectID, $con)
    {
        $sql = "delete from project where id = $projectID";
							_log('delete from project: '.$sql);
        $result = mysqli_query($con, $sql);

        if (mysqli_query($con, $sql)) {
            echo "1";
        } else {
            echo mysqli_error($con);
        }
    }

    require_once("SetDBConnection.php");

    $con = SetDBConnection();

    if (mysqli_connect_errno($con)) {
        echo "Failed to connect to database server: ".mysqli_connect_error();
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
                        $con
                    );
                }break;
            case "list":
                {
                    GetProjectList($con);
                }break;
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
                        $con
                    );
                }break;
            case "delete":
                {
                    $id = $_GET["id"];
                    DeleteProject($id, $con);
                }break;

        }
    }
    mysqli_close($con);
?>
