<?php

    require_once("SetDBConnection.php");
    
	$con = SetDBConnection();
	if(mysqli_connect_errno())
	{
		echo "Failed to connect to database server: ".mysqli_connect_error();
	}
	else
	{ 
		$projectID = $_GET['project'];
		$type = $_GET['type'];

		$sql = "select imagery_product.ID as ID, flight.Date as Name, imagery_product.TMSPath as TMSPath, imagery_product.Boundary as Boundary ".
			   "from flight, imagery_product  ".
		       "where imagery_product.Flight = flight.ID and flight.Project = $projectID and imagery_product.Type = $type ".
			   "order by Name";
		
		$result = mysqli_query($con,$sql);
		
		$list = array();
		while($row = mysqli_fetch_assoc($result)) {
			$list[] = $row;
		}
		mysqli_close($con);
		echo json_encode($list);
	}
?>