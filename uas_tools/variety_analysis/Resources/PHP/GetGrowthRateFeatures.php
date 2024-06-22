<?php
$fileLocalPath = "../Python/gr_chart_features.txt";

$data = array();

if (file_exists($fileLocalPath) == true) {
    $valueString = explode(',', file_get_contents($fileLocalPath));
    $maxGrowthDAP = (float)$valueString[0];
    $maxGrowth = (float)$valueString[1];
    $deltaD = (float)$valueString[2];
    $halfMaxGrowthD1 = (float)$valueString[3];
    $halfMaxGrowthD2 = (float)$valueString[4];

    $data = array('maxGrowth' => $maxGrowth, 'maxGrowthDAP' => $maxGrowthDAP,
        'halfMaxGrowthD1' => $halfMaxGrowthD1, 'halfMaxGrowthD2' => $halfMaxGrowthD2,
        'deltaD' => $deltaD);

} else {
    echo 'Could not read input file';
}

echo json_encode($data);
?>



   
