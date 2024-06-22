<?php
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


$dates = $_GET["dates"];
$values = $_GET["values"];
$type = $_GET["type"];
$parameters = $_GET["parameters"];
$startDate = $_GET["startdate"];
$lastDay = $_GET["lastday"];

if (file_exists("../Python/chart.txt")) {
    unlink("../Python/chart.txt");
}

if (file_exists("../Python/gr_chart.txt")) {
    unlink("../Python/gr_chart.txt");
}

if (file_exists("../gr_chart_features.txt")) {
    unlink("../gr_chart_features.txt");
}

//$command = "python '../Python/make_curve.py' " . $dates . " " . $values . " " . $type . " " . $parameters . " " . $startDate . " " . $lastDay . " 2>&1";
$command = "python3 '../Python/make_curve.py' " . $dates . " " . $values . " " . $type . " " . $parameters . " " . $startDate . " " . $lastDay . " 2>&1";

exec($command, $output);

//_log('$command ' . $command);

$chartResult = false;
$grChartResult = false;
$grChartFeatureResult = false;

if (file_exists("../Python/chart.txt")) {
    $chartResult = true;
}

if (file_exists("../Python/gr_chart.txt")) {
    $grChartResult = true;
}

if (file_exists("../Python/gr_chart_features.txt")) {
    $grChartFeatureResult = true;
}

$result = array("ChartResult" => $chartResult, "GrChartResult" => $grChartResult, "GrChartFeatureResult" => $grChartFeatureResult);
echo json_encode($result);
?>