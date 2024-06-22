<?php
	
	$fileLocalPath = "../Python/chart.txt";
	$data = array();
		
	if (file_exists($fileLocalPath) == true)
	{
		$content = file_get_contents($fileLocalPath);
		$content = str_replace("[","",$content);
		$values = preg_split('/\s+/', $content);
		
		
		for($i = 1; $i < count($values); $i++)
		{
			$dataValue = array('dae' => $i, 'value' => (float)$values[$i]);
			$data[] = $dataValue;
		}
		
	} else {
		echo 'Could not read input file';
	}

	echo json_encode($data);
?>