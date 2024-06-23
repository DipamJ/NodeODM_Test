<?php
	require_once("SetDBConnection.php");
	$con = SetDBConnection();
		
	if(mysqli_connect_errno($con))
	{
		echo "Failed to connect to database server: ".mysqli_connect_error();
	}
	else
	{
		$type = $_GET["type"];
		$identifier = $_GET["identifier"];
		
		if ($type == "Pause"){
			$status  = "Paused";
		} else {
			$status  = "Uploading";
		}
		
		$sql =  "UPDATE raw_data_upload_status ".
					"SET Status='".$status."' ".
					"WHERE Identifier = '$identifier'";
		echo $sql;
		mysqli_query($con, $sql);
		mysqli_close($con);
	}
	
?>