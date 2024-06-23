<?php
$data = array();
$lastDay = $_GET["lastDay"];

$maxFile = "../Python/max.txt";
$content = file_get_contents($maxFile);
$maxPos = explode(";", $content)[0];

$fileLocalPath1 = "../Python/fit_firstline.txt";
if (file_exists($fileLocalPath1) == true) {
    $content1 = file_get_contents($fileLocalPath1);
    $content1 = str_replace("[", "", $content1);
    $content1 = str_replace("]", "", $content1);
    $coef1 = preg_split("/\s+/", $content1);
    $a1 = (float)$coef1[0];
    $b1 = (float)$coef1[1];
} else {
}

$fileLocalPath2 = "../Python/fit_secondline.txt";
if (file_exists($fileLocalPath2) == true) {
    $content2 = file_get_contents($fileLocalPath2);
    $content2 = str_replace("[", "", $content2);
    $content2 = str_replace("]", "", $content2);
    $coef2 = preg_split("/\s+/", $content2);
    $a2 = (float)$coef2[0];
    $b2 = (float)$coef2[1];
} else {
}

$x = ceil(($b2 - $b1 - $a2 * $maxPos) / ($a1 - $a2));
$y = $a1 * $x + $b1;
$myfile = fopen("../Python/intersection.txt", "w") or die("Unable to open file!");
fwrite($myfile, $x . ";" . $y);


for ($i = 0; $i < $lastDay; $i++) {

    if ($i < $x) {
        $value = $a1 * $i + $b1;
    } else {
        $value = $a2 * ($i - $maxPos) + $b2;
    }

    $dataValue = array("dae" => $i, "value" => (float)$value);
    $data[] = $dataValue;
}

echo json_encode($data);
?>