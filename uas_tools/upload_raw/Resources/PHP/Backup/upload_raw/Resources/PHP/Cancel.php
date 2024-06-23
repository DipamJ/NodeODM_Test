<?php
	require_once("SetDBConnection.php");
	$con = SetDBConnection();
		
	if(mysqli_connect_errno($con))
	{
		echo "Failed to connect to database server: ".mysqli_connect_error();
	}
	else
	{
		$identifier = $_GET["identifier"];
		$sql = "SELECT * FROM raw_data_upload_status WHERE Identifier = '$identifier'";
		$result = mysqli_query($con,$sql);
		if ($result){
			$upload = mysqli_fetch_assoc($result);
			$tempFolder = $upload["TempFolder"];
			
			
			$sql =  "DELETE FROM raw_data_upload_status WHERE Identifier = '$identifier'";
			
			if (mysqli_query($con, $sql))
			{
				echo "cancelled";
			} else {
				echo mysqli_error($con);
				
			}
			
			$cmd = "rm -rf $tempFolder";
			exec($cmd, $output);
		}
		
		mysqli_close($con);
	}
	
?>