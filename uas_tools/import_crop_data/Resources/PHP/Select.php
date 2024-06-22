<?php
if (0 < $_FILES["file"]["error"]) {
    echo "Error: " . $_FILES["file"]["error"] . "<br>";
} else {
    require_once("CommonFunctions.php");

    $file = fopen($_FILES["file"]["tmp_name"], "r");
    if ($file) {
        //Read first line to get list of criteria and values
        $header = fgets($file);
        $fieldList = explode(",", str_replace(array("\r", "\n"), '', $header));

        /*
        for($i = 0; $i < sizeof($fieldList); $i++){
            $fieldList[$i] = FormatString($fieldList[$i]);
        }
        */
        echo json_encode($fieldList);
        fclose($file);
    } else {
        echo "failed";
    }
}
?>