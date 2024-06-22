<?php

$data = array();
$part = $_GET["part"];
$startPoint = $_GET["startPoint"];
//$fileLocalPath = "../Python/fit_".$part."line.txt";
$fileLocalPath = "../Python/" . $part . "line.txt";

if (file_exists($fileLocalPath) == true) {

    $content = file_get_contents($fileLocalPath);
    $content = str_replace("[", "", $content);
    $values = preg_split("/\s+/", $content);


    for ($i = 0; $i < count($values); $i++) {
        $dae = $i + $startPoint;
        $dataValue = array("dae" => $dae, "value" => (float)$values[$i]);
        $data[] = $dataValue;
    }

    /*
    $content = file_get_contents($fileLocalPath);
    $content = str_replace("[","",$content);
    $content = str_replace("]","",$content);
    $coef = preg_split("/\s+/", $content);
    $a = $coef[0];
    $b = $coef[1];

    for($i = 0; $i < 140; $i++)
    {
        $dae = $i;
        $value = $a * $i + $b;
        $dataValue = array("dae" => $dae, "value" => (float)$value);
        $data[] = $dataValue;
    }
    */
} else {
}

/*
$secondFileLocalPath = "../Python/secondline.txt";
if (file_exists($secondFileLocalPath) == true)
{
    $content = file_get_contents($secondFileLocalPath);
    $content = str_replace("[","",$content);
    $values = preg_split("/\s+/", $content);


    for($i = 0; $i < count($values); $i++)
    {
        $dataValue = array("dae" => $dae, "value" => (float)$values[$i]);
        $data[] = $dataValue;
        $dae++;
    }

} else {
}
*/
echo json_encode($data);
?>