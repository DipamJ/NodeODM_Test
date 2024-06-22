<?php
require_once("SetDBConnection.php");

ini_set('display_errors', 1);

$con = SetDBConnection();

if(mysqli_connect_errno())

{
    echo "Failed to connect to database server: ".mysqli_connect_error();
}

else

{
    $datasetID = $_GET["dataset"];
    $conditions = $_REQUEST["conditions"];

    //_log('conditions ');
    //print_r($conditions);

    $rowDatasetList = array();
    $firstSet = true;

    foreach ($conditions as $condition){
        //if($condition["Value"] != "%"){

            $sql = 	"SELECT row_data.ID FROM row_data, criteria_data ".
                "WHERE criteria_data.RowDataSet = row_data.ID and row_data.CropDataSet = $datasetID and ".
                "criteria_data.Name = '".$condition["Name"]."' and criteria_data.Value like '".$condition["Value"]."'";

            $resultArray = array();

            $result = mysqli_query($con,$sql);

            while($row = mysqli_fetch_array($result)){
                $resultArray[] = $row["ID"];
            }

            if ($firstSet) {
                $rowDatasetList = $resultArray;
                $firstSet = false;
            }else {
                $rowDatasetList = array_intersect($rowDatasetList, $resultArray);
            }
        //}
    }

    $dataList = array();
    //while($row = mysqli_fetch_array($result))
    foreach ($rowDatasetList as $rowDataset)

    {
        $rowData = array();
        $sql = "SELECT Name,Value FROM criteria_data where RowDataSet = ".$rowDataset;
        $criteriaResult = mysqli_query($con,$sql);
        while($criteriaValue = mysqli_fetch_array($criteriaResult)){
            $rowData[] = array("criteria_".$criteriaValue["Name"]=>$criteriaValue["Value"]) ;
        }

        $sql = "SELECT Name, Value FROM value_data where RowDataSet = ".$rowDataset;
        $valueResult = mysqli_query($con,$sql);
        while($dataValue = mysqli_fetch_array($valueResult)){
            $rowData[] =  array("data_".$dataValue["Name"]=>$dataValue["Value"]);
        }
        $dataList[] = $rowData;
    }
    mysqli_close($con);

    echo json_encode($dataList);
}
?>
