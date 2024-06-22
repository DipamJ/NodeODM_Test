<?php
	require_once("SetConfigurationFilePath.php");

    if ( 0 < $_FILES["file"]["error"] ) {
        echo "Error: " . $_FILES["file"]["error"] . "<br>";
    }
    else {
		$file = fopen($_FILES["file"]["tmp_name"], "r");
		if ($file) {
			$header = fgets($file);
			$fieldList = explode(",", str_replace(array("\r", "\n"), '', $header)); 
			
			$infoList = array();
			
			while (($line = fgets($file)) !== false) {
				$lineValueList = explode(",", str_replace(array("\r", "\n"), '', $line)); 
				$lineData = array();
				for ($i = 0; $i < count($fieldList); $i++){
					$lineData += array($fieldList[$i]=>$lineValueList[$i]);
				}
				$infoList[] = $lineData;
			}
			
			fclose($file);
			echo json_encode($infoList);
		} else {
		} 
    }

?>