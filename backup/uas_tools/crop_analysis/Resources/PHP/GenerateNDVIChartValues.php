<?php
$dates = $_GET["dates"];
$values = $_GET["values"];
$startDate = $_GET["startDate"];
$lastDay = $_GET["lastDay"];
$type = $_GET["type"];
$origin = $_GET["origin"];

if (file_exists("../Python/ndvi_chart.txt")) {
    unlink("../Python/ndvi_chart.txt");
}

switch ($type) {
    case "svr":
        {
            $c = $_GET["c"];
            $gamma = $_GET["gamma"];
            //$command = "python '../Python/fit_ndvi_svr.py' " . $dates . " " . $values . " " . $startDate . " " . $lastDay . " " . $c . " " . $gamma . " 2>&1";
            $command = "python3 '../Python/fit_ndvi_svr.py' " . $dates . " " . $values . " " . $startDate . " " . $lastDay . " " . $c . " " . $gamma . " 2>&1";
        }
        break;
    case "polysimple":
        {
            $degree = $_GET["degree"];
            //$command = "python '../Python/fit_ndvi_poly_simple.py' " . $dates . " " . $values . " " . $startDate . " " . $lastDay . " " . $degree . " 2>&1";
            $command = "python3 '../Python/fit_ndvi_poly_simple.py' " . $dates . " " . $values . " " . $startDate . " " . $lastDay . " " . $degree . " 2>&1";
        }
        break;
    case "polyzero":
        {
            $degree = $_GET["degree"];
            //$command = "python '../Python/fit_ndvi_poly_zero.py' " . $dates . " " . $values . " " . $startDate . " " . $lastDay . " " . $degree . " 2>&1";
            $command = "python3 '../Python/fit_ndvi_poly_zero.py' " . $dates . " " . $values . " " . $startDate . " " . $lastDay . " " . $degree . " 2>&1";
        }
        break;
    case "polysklearn":
        {
            $degree = $_GET["degree"];
            //$command = "python '../Python/fit_ndvi_poly_sklearn.py' " . $dates . " " . $values . " " . $startDate . " " . $lastDay . " " . $degree . " 2>&1";
            $command = "python3 '../Python/fit_ndvi_poly_sklearn.py' " . $dates . " " . $values . " " . $startDate . " " . $lastDay . " " . $degree . " 2>&1";
        }
        break;
    case "rbf":
        {
            $epsilon = $_GET["epsilon"];
            $smooth = $_GET["smooth"];
            //$command = "python '../Python/fit_ndvi_rbf.py' " . $dates . " " . $values . " " . $startDate . " " . $lastDay . " " . $epsilon . " " . $smooth . " 2>&1";
            $command = "python3 '../Python/fit_ndvi_rbf.py' " . $dates . " " . $values . " " . $startDate . " " . $lastDay . " " . $epsilon . " " . $smooth . " 2>&1";
        }
        break;
}

exec($command, $output);
//echo $command;
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

    $myfile = fopen("../Python/max.txt", "w") or die("Unable to open file!");
    fwrite($myfile, $maxPos . ";" . $max);

    $firstHalfDays = $maxPos;
    $secondHalfDays = $lastDay - $maxPos;
    //$secondHalfDays = $lastDay;


    $myfile = fopen("../Python/cutoff.txt", "w") or die("Unable to open file!");
    fwrite($myfile, $firstHalfDays);


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

    /*
    print_r ($firstHalfValues);
    print_r ($firstHalfDates);
    echo $firstStartDate."\n";
    echo $firstHalfDays."\n";
    */

    $secondHalfValues = array_slice($valuesArray, $index);
    $secondHalfDates = array_slice($datesArray, $index);
    //$secondStartDate = $startDate;
    $secondStartDate = substr($secondHalfDates[0], 4, 2) . "/" . substr($secondHalfDates[0], 6, 2) . "/" . substr($secondHalfDates[0], 0, 4);

    /*
    print_r ($secondHalfValues);
    print_r ($secondHalfDates);
    echo $secondStartDate."\n";
    echo $secondHalfDays."\n";
    */

    if (file_exists("../Python/firstline.txt")) {
        unlink("../Python/firstline.txt");
    }

    if ($origin == "true") {
        //$command = "python '../Python/fit_ndvi_line_zero.py' " . implode(",", $firstHalfDates) . " " . implode(",", $firstHalfValues) . " " . $firstStartDate . " " . $firstHalfDays . " firstline.txt 2>&1";
        $command = "python3 '../Python/fit_ndvi_line_zero.py' " . implode(",", $firstHalfDates) . " " . implode(",", $firstHalfValues) . " " . $firstStartDate . " " . $firstHalfDays . " firstline.txt 2>&1";
    } else {
        //$command = "python '../Python/fit_ndvi_line.py' " . implode(",", $firstHalfDates) . " " . implode(",", $firstHalfValues) . " " . $firstStartDate . " " . $firstHalfDays . " firstline.txt 2>&1";
        $command = "python3 '../Python/fit_ndvi_line.py' " . implode(",", $firstHalfDates) . " " . implode(",", $firstHalfValues) . " " . $firstStartDate . " " . $firstHalfDays . " firstline.txt 2>&1";

    }
    //$command = "python '../Python/fit_ndvi_line.py' ".implode(",",$firstHalfDates)." ".implode(",",$firstHalfValues)." ".$firstStartDate." ".$firstHalfDays." firstline.txt 2>&1";
    //$command = "python '../Python/fit_ndvi_line_zero.py' ".implode(",",$firstHalfDates)." ".implode(",",$firstHalfValues)." ".$firstStartDate." ".$lastDay." firstline.txt 2>&1";
    //echo $command;
    exec($command, $output);

    if (file_exists("../Python/secondline.txt")) {
        unlink("../Python/secondline.txt");
    }
    //$command = "python '../Python/fit_ndvi_line.py' " . implode(",", $secondHalfDates) . " " . implode(",", $secondHalfValues) . " " . $secondStartDate . " " . $secondHalfDays . " secondline.txt 2>&1";
    $command = "python3 '../Python/fit_ndvi_line.py' " . implode(",", $secondHalfDates) . " " . implode(",", $secondHalfValues) . " " . $secondStartDate . " " . $secondHalfDays . " secondline.txt 2>&1";
    //$command = "python '../Python/fit_ndvi_line.py' ".implode(",",$secondHalfDates)." ".implode(",",$secondHalfValues)." ".$secondStartDate." ".$lastDay." secondline.txt 2>&1";
    exec($command, $output);
}
echo $chartResult;
?>