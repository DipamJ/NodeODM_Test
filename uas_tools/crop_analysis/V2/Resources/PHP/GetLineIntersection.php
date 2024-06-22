<?php
$data = array();
//	$lastDay = $_GET($lastDay);
$lastDay = $_GET('lastDay');

$fileLocalPath1 = "../Python/fit_firstline.txt";

if (file_exists($fileLocalPath1) == true) {
    $content1 = file_get_contents($fileLocalPath1);
    $content1 = str_replace("[", "", $content1);
    $coef1 = preg_split("/\s+/", $content1);
    $a1 = $coef1[0];
    $b1 = $coef1[1];
} else {
}

$fileLocalPath2 = "../Python/fit_secondline.txt";

if (file_exists($fileLocalPath2) == true) {
    $content2 = file_get_contents($fileLocalPath2);
    $content2 = str_replace("[", "", $content2);
    $coef2 = preg_split("/\s+/", $content2);
    $a2 = $coef2[0];
    $b2 = $coef2[1];
} else {
}

$x = ($b2 - $b1) / ($a1 - $a2);

for ($i = 0; $i < $lastDay; $i++) {
    if ($i < $x) {
        $data[] = $a1 * $i + $b1;
    } else {
        $data[] = $a2 * $i + $b2;
    }
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