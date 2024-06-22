<?php

require_once("SetDBConnection.php");

// Log Document
function _log($str)
{
    // log to the output
    $log_str = date('d.m.Y') . ": {$str}\r\n";
    echo $log_str;

    // log to file
    if (($fp = fopen('upload_log.txt', 'a+')) !== false) {
        fputs($fp, $log_str);
        fclose($fp);
    }
}

ini_set('display_errors', 1);

$con = SetDBConnection();

if (mysqli_connect_errno()) {
    echo "Failed to connect to database server: " . mysqli_connect_error();
} else {
    //$datasetID = $_GET["dataset"];

    //$sql = "SELECT * FROM row_data where CropDataSet = $datasetID";

    // Count the number of files inside path (only .csv files should be there)
    //$fi = new FilesystemIterator("/var/www/html/wordpress/temp/CropData/", FilesystemIterator::SKIP_DOTS);
    //printf("There are %d Files", iterator_count($fi));
    //$numberOfFiles = iterator_count($fi);

    $sql = "SELECT CropDataSet FROM row_data ORDER BY ID DESC LIMIT 1";
    //_log("sql: " . $sql);

    $result = mysqli_query($con, $sql);
    $last_CropDataSet = mysqli_fetch_assoc($result);


    //_log("last_CropDataSet: " . $last_CropDataSet["CropDataSet"]);

    // Last value recorded as CropDataSet
    $number0fCropDataSet = $last_CropDataSet["CropDataSet"];

    $sql = "SELECT * FROM row_data where CropDataSet = '$number0fCropDataSet'";

    //_log("sql: " . $sql);

    $result = mysqli_query($con, $sql);

    $dataList = array();
    while ($row = mysqli_fetch_array($result)) {

        $rowData = array();
        $sql = "SELECT Name,Value FROM criteria_data where RowDataSet = " . $row['ID'];
        $criteriaResult = mysqli_query($con, $sql);
        while ($criteriaValue = mysqli_fetch_array($criteriaResult)) {
            $rowData[] = array("criteria_" . $criteriaValue["Name"] => $criteriaValue["Value"]);
        }

        $sql = "SELECT Name, Value FROM value_data where RowDataSet = " . $row['ID'];
        $valueResult = mysqli_query($con, $sql);
        while ($dataValue = mysqli_fetch_array($valueResult)) {
            $rowData[] = array("data_" . $dataValue["Name"] => $dataValue["Value"]);
        }

        $valueList[] = $rowData;
    }

    mysqli_close($con);
    echo json_encode($valueList);
}

?>