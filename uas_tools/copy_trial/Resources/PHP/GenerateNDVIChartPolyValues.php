<?php
$dates = $_GET["dates"];
$values = $_GET["values"];
$type = $_GET["type"];
$startDate = $_GET["startdate"];
$lastDay = $_GET["lastday"];
$degree = $_GET["degree"];
$gamma = $_GET["gamma"];
//$max = $_GET["max"];
//$mdate = $_GET["mdate"];

if (file_exists("../Python/ndvi_chart.txt")) {
    unlink("../Python/ndvi_chart.txt");
}

//$command = "python '../Python/fit_ndvi_poly.py' ".$dates." ".$values." ".$startDate." ".$lastDay." ".$c." ".$gamma." ".$max." ".$mdate." 2>&1";;
//$command = "python '../Python/fit_ndvi_poly_simple.py' ".$dates." ".$values." ".$startDate." ".$lastDay." ".$degree." ".$gamma." 2>&1";
$command = "python3 '../Python/fit_ndvi_poly_simple.py' " . $dates . " " . $values . " " . $startDate . " " . $lastDay . " " . $degree . " " . $gamma . " 2>&1";
//$command = "python '../Python/fit_ndvi_poly_sklearn.py' ".$dates." ".$values." ".$startDate." ".$lastDay." ".$degree." ".$gamma." 2>&1";;
//$command = "python '../Python/fit_ndvi_poly_scipy.py' ".$dates." ".$values." ".$startDate." ".$lastDay." ".$c." ".$gamma." 2>&1";;
exec($command, $output);
//print_r($output);

$chartResult = false;

if (file_exists("../Python/ndvi_chart.txt")) {
    $chartResult = true;
}

echo $chartResult;


/*
$firstDap = $_GET["firstDap"];
$firstValue = $_GET["firstValue"];
$maxDap = $_GET["maxDap"];
$maxValue = $_GET["maxValue"];
$lastDap = $_GET["lastDap"];
$lastValue = $_GET["lastValue"];
$lastDay = $_GET["lastDay"];

$y = array();

for ($i = 0; $i < $maxDap; $i++){
    $val = ($maxDap * $maxDap - ($i - $maxDap) * ($i - $maxDap)) * $maxValue / ($maxDap * $maxDap);
    $y[]= $val;
}


for ($i = $maxDap; $i < $lastDay; $i++){
    $val = ($lastDap - $i) * ( 2 * $maxDap - $lastDap - $i) * (($maxValue - $lastValue) / (($lastDap - $maxDap) * ($maxDap - $lastDap)))    + $lastValue;
    $y[]= $val;
}

file_put_contents("../Python/ndvi_chart.txt", implode(" ",$y));
echo true;
*/

?>