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
		
		$sql =  "UPDATE upload_status ".
				"SET Status='Error' ".
				"WHERE Identifier = '$identifier'";
		
		mysqli_query($con, $sql);
		mysqli_close($con);
	}
	
?>