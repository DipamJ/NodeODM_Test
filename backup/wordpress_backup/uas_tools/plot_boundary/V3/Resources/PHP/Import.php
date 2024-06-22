<?php
	require_once("SetConfigurationFilePath.php");

    if ( 0 < $_FILES["file"]["error"] ) {
        echo "Error: " . $_FILES["file"]["error"] . "<br>";
    }
    else {
		//$plots = file_get_contents($_FILES["file"]["tmp_name"]);
		//echo $plots;
		
		$info = file_get_contents($_FILES["file"]["tmp_name"]);
		echo $info;
    }

?>