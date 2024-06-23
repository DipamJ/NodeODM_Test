<?php
$slopes = array();

$fileLocalPath1 = "../Python/fit_firstline.txt";
if (file_exists($fileLocalPath1) == true) {
    $content1 = file_get_contents($fileLocalPath1);
    $content1 = str_replace("[", "", $content1);
    $content1 = str_replace("]", "", $content1);
    $coef1 = preg_split("/\s+/", $content1);
    //$a1 = (float) $coef1[0];
    //$b1 = (float) $coef1[1];
    $slopes[] = (float)$coef1[0];

} else {
}

$fileLocalPath2 = "../Python/fit_secondline.txt";
if (file_exists($fileLocalPath2) == true) {
    $content2 = file_get_contents($fileLocalPath2);
    $content2 = str_replace("[", "", $content2);
    $content2 = str_replace("]", "", $content2);
    $coef2 = preg_split("/\s+/", $content2);
    //$a2 = (float) $coef2[0];
    //$b2 = (float) $coef2[1];
    $slopes[] = (float)$coef2[0];
} else {
}

echo json_encode($slopes);
?>