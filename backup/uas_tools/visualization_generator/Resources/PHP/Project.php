<?php
	function GetProjectList($con){
		$sql =  "select * from project order by Name";

		$result = mysqli_query($con,$sql);

		$projectList = array();
		while($row = mysqli_fetch_assoc($result)) {
			$projectList[] = $row;
		}
		echo json_encode($projectList);
	}

	//require_once("SetDBConnection.php");
	require_once("../../../../resources/database/SetDBConnection.php");

	$con = SetDBConnection();

 	if(mysqli_connect_errno())
	{
		echo "Failed to connect to database server: ".mysqli_connect_error();
	}
	else
	{

		$action = $_GET["action"];

		switch ($action) {
			case "list":
				{
					GetProjectList($con);
				}break;
		}
	}

	mysqli_close($con);
?>
