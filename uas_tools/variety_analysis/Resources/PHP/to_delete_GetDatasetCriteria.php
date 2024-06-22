<?php

require_once("SetDBConnection.php");

//ini_set('display_errors', 1);
// ERRORS
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$con = SetDBConnection();

if (mysqli_connect_errno()) {
    echo "Failed to connect to database server: " . mysqli_connect_error();
} else {
    if (isset($_GET['dataset'])) {
        $datasetID = $_GET['dataset'];
    } else {
        $datasetID = '';
    }


    //$datasetID = 30;//$_GET["select-data-set"];

    //$user_name = '';

    //if (isset($_GET['dataset'])) {

    //$datasetID = $_GET['dataset'];

    //}


    /*
    $sql = "SELECT * FROM row_data where CropDataSet = $datasetID";
    $result = mysqli_query($con,$sql);

    $valueList = array();

    while($row = mysqli_fetch_array($result))
    {

        $criteriaValueList = array();
        $sql = "SELECT Name,Value FROM criteria_data where RowDataSet = ".$row['ID'];
        $criteriaResult = mysqli_query($con,$sql);
        while($criteriaValue = mysqli_fetch_array($criteriaResult)){
            $criteriaValueList[] = array($criteriaValue["Name"]=>$criteriaValue["Value"]) ;
        }

        $valueList[] = $criteriaValueList;
    }

    mysqli_close($con);
    echo json_encode($valueList);
    */

    //$filename = "../Cache/".$datasetID.".txt";
    //_log('datasetID ' .$datasetID);
    $filename = dirname(__DIR__) . "/Cache/" . $datasetID . ".txt";
    //echo $filename;
    if (file_exists($filename)) {
        $list = file_get_contents($filename);
        echo $list;
    } else {

        $sql = "SELECT DISTINCT criteria_data.Name " .
            "FROM criteria_data, row_data " .
            "WHERE criteria_data.RowDataSet = row_data.ID AND row_data.CropDataSet = $datasetID";
        //_log('select criteria_data 1: '.$sql);
        $result = mysqli_query($con, $sql);

        $list = array();

        while ($key = mysqli_fetch_array($result)) {
            $criteriaValueList = array();
            $sql = "SELECT DISTINCT criteria_data.Value " .
                "FROM criteria_data, row_data " .
                "WHERE criteria_data.RowDataSet = row_data.ID AND row_data.CropDataSet = $datasetID AND criteria_data.Name = '" . $key["Name"] . "'";
            //_log('select criteria_data 2: '.$sql);
            $criteriaResult = mysqli_query($con, $sql);
            while ($value = mysqli_fetch_array($criteriaResult)) {
                $criteriaValueList[] = $value["Value"];
            }
            $list[] = array("Name" => $key["Name"], "ValueList" => $criteriaValueList["Value"]);
            var_dump($list);
        }

        mysqli_close($con);
        echo json_encode($list);

        $file = fopen($filename, "w") or die("Unable to open file!");
        fwrite($file, json_encode($list));
        fclose($file);
    }
}
?>