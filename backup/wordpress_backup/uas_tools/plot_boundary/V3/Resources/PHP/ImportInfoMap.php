<?php
	require_once("SetConfigurationFilePath.php");

    if ( 0 < $_FILES["file"]["error"] ) {
        echo "Error: " . $_FILES["file"]["error"] . "<br>";
    }
    else {
		$file = fopen($_FILES["file"]["tmp_name"], "r");
		if ($file) {
			$infoList = array();
			
			while (($line = fgets($file)) !== false) {
				$lineValueList = explode(",", str_replace(array("\r", "\n"), '', $line)); 
				$infoList[] = $lineValueList;
			}
			
			fclose($file);
			echo json_encode($infoList);
		} else {
		} 
    }

?>