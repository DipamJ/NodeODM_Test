<?php
	//require_once("SetDBConnection.php");
	require_once("../../../../resources/database/SetDBConnection.php");
	$con = SetDBConnection();

	if(mysqli_connect_errno())
	{
		echo "Failed to connect to database server: ".mysqli_connect_error();
	}
	else
	{
		$type = $_GET["type"];
		$identifier = $_GET["identifier"];
		//$progress = $_GET["progress"];

		if ($type == "Pause"){
			$status  = "Paused";
		} else {
			$status  = "Uploading";
		}

		$sql =  "UPDATE pointcloud ".
					//"SET Status='".$status."' , Progress=$progress ".
					"SET Status='".$status."' ".
					"WHERE Identifier = '$identifier'";
		echo $sql;
		mysqli_query($con, $sql);
		mysqli_close($con);
	}

?>
