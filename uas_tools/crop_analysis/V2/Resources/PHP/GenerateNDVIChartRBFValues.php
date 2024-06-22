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

//$command = "python '../Python/fit_ndvi_rbf.py' " . $dates . " " . $values . " " . $startDate . " " . $lastDay . " " . $degree . " " . $gamma . " 2>&1";
$command = "python3 '../Python/fit_ndvi_rbf.py' " . $dates . " " . $values . " " . $startDate . " " . $lastDay . " " . $degree . " " . $gamma . " 2>&1";
exec($command, $output);
//print_r($output);

$chartResult = false;

if (file_exists("../Python/ndvi_chart.txt")) {
    $chartResult = true;


    $content = file_get_contents("../Python/ndvi_chart.txt");
    $content = str_replace("[", "", $content);
    $content = str_replace("]", "", $content);
    $fitVals = preg_split('/\s+/', $content);

    $max = 0;
    $maxPos = 0;

    $valuesArray = explode(",", $values);
    $datesArray = explode(",", $dates);


    for ($i = 0; $i < count($fitVals); $i++) {
        //echo "max: ".$max."; val: ".$fitVals[$i]."\n";
        if ($max < $fitVals[$i]) {
            $max = $fitVals[$i];
            $maxPos = $i;
        }
    }

    //echo $maxPos;

    $firstHalfDays = $maxPos;
    $secondHalfDays = $lastDay - $maxPos;


    $firstHalfValues = array();
    $secondHalfValues = array();

    $index = 0;
    $dap = (strtotime($datesArray[$index]) - strtotime($startDate)) / (60 * 60 * 24);
    while ($dap < $maxPos && $index < count($datesArray)) {
        $index++;
        $dap = (strtotime($datesArray[$index]) - strtotime($startDate)) / (60 * 60 * 24);
    }

    $firstHalfValues = array_slice($valuesArray, 0, $index);
    $firstHalfDates = array_slice($datesArray, 0, $index);
    $firstStartDate = $startDate;

    $secondHalfValues = array_slice($valuesArray, $index);
    $secondHalfDates = array_slice($datesArray, $index);
    $secondStartDate = substr($secondHalfDates[0], 4, 2) . "/" . substr($secondHalfDates[0], 6, 2) . "/" . substr($secondHalfDates[0], 0, 4);

    if (file_exists("../Python/firstline.txt")) {
        unlink("../Python/firstline.txt");
    }
    //$command = "python '../Python/fit_ndvi_line.py' " . implode(",", $firstHalfDates) . " " . implode(",", $firstHalfValues) . " " . $firstStartDate . " " . $firstHalfDays . " firstline.txt 2>&1";
    $command = "python3 '../Python/fit_ndvi_line.py' " . implode(",", $firstHalfDates) . " " . implode(",", $firstHalfValues) . " " . $firstStartDate . " " . $firstHalfDays . " firstline.txt 2>&1";
    exec($command, $output);

    if (file_exists("../Python/secondline.txt")) {
        unlink("../Python/secondline.txt");
    }
    //$command = "python '../Python/fit_ndvi_line.py' " . implode(",", $secondHalfDates) . " " . implode(",", $secondHalfValues) . " " . $secondStartDate . " " . $secondHalfDays . " secondline.txt 2>&1";
    $command = "python3 '../Python/fit_ndvi_line.py' " . implode(",", $secondHalfDates) . " " . implode(",", $secondHalfValues) . " " . $secondStartDate . " " . $secondHalfDays . " secondline.txt 2>&1";
    exec($command, $output);
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