<?php

    require_once("SetDBConnection.php");
    
	$con = SetDBConnection();
	if(mysqli_connect_errno())
	{
		echo "Failed to connect to database server: ".mysqli_connect_error();
	}
	else
	{ 
		$sql = "select * from project order by Name";  
		$result = mysqli_query($con,$sql);
		
		$projectList = array();
		while($row = mysqli_fetch_assoc($result)) {
			$projectList[] = $row;
		}
		mysqli_close($con);
		echo json_encode($projectList);
	}
?>