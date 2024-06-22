<?php

require_once("SetDBConnection.php");

ini_set('display_errors', 1);

$con = SetDBConnection();

if (mysqli_connect_errno()) {
    echo "Failed to connect to database server: " . mysqli_connect_error();
} else {
    $datasetID = $_GET["dataset"];
    $filename = dirname(__DIR__) . "/Cache/" . $datasetID . ".txt";

    if (file_exists($filename)) {
        $list = file_get_contents($filename);
        echo $list;
    } else {

        $sql = "SELECT DISTINCT criteria_data.Name " .
            "FROM criteria_data, row_data " .
            "WHERE criteria_data.RowDataSet = row_data.ID AND row_data.CropDataSet = $datasetID";

        $result = mysqli_query($con, $sql);

        $list = array();

        while ($key = mysqli_fetch_array($result)) {
            $criteriaValueList = array();

            $sql = "SELECT DISTINCT criteria_data.Value " .
                "FROM criteria_data, row_data " .
                "WHERE criteria_data.RowDataSet = row_data.ID AND row_data.CropDataSet = $datasetID AND criteria_data.Name = '" . $key["Name"] . "'";

            $criteriaResult = mysqli_query($con, $sql);

            while ($value = mysqli_fetch_array($criteriaResult)) {
                $criteriaValueList[] = $value["Value"];
            }

            $list[] = array("Name" => $key["Name"], "ValueList" => $criteriaValueList);
        }

        mysqli_close($con);
        echo json_encode($list);

        $file = fopen($filename, "w") or die("Unable to open file!");

        fwrite($file, json_encode($list));
        fclose($file);
    }
}
?>