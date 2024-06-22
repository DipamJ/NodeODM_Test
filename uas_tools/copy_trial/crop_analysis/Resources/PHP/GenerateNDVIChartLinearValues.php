<?php
	$firstDap = $_GET["firstDap"];
	$firstValue = $_GET["firstValue"];
	$maxDap = $_GET["maxDap"];
	$maxValue = $_GET["maxValue"];
	$lastDap = $_GET["lastDap"];
	$lastValue = $_GET["lastValue"];
	$lastDay = $_GET["lastDay"];
	
	$y = array();
	
	for ($i = 0; $i < $maxDap; $i++){
		$val = $i * $maxValue / $maxDap;
		$y[]= $val;
	} 
	
	for ($i = $maxDap; $i < $lastDay; $i++){
		//$val = (($lastDap - $i) * $maxValue) / ($lastDap - $maxDap);
		$val = (($lastValue - $maxValue) / ($lastDap - $maxDap)) * $i + ($lastDap*$maxValue - $maxDap*$lastValue )/($lastDap - $maxDap); 
		
		$y[]= $val;
	} 
	
	file_put_contents("../Python/ndvi_chart.txt", implode(" ",$y));
	
	echo true;
?>