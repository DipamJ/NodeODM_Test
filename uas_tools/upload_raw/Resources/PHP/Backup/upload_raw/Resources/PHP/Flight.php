<?php
	function AddFlight($flightName, $date, $project, $platform, $sensor, $altitude, $forward, $side, $con){
		$sql = "insert into flight_add (Name, Date, Project, Platform, Sensor, Altitude, Forward, Side) ".
			   "values ('$flightName', '$date', $project, $platform, $sensor, $altitude, $forward, $side)";
		//echo $sql;			   
					   
		if (mysqli_query($con, $sql))
		{
			echo $con->insert_id;
		} else {
			echo mysqli_error($con);
		}  
	}
	
	function GetFlightList($con){
		$sql =  "select flight_add.*, project.Name as ProjectName,  platform.Name as PlatformName, sensor.Name as SensorName ".
				"from flight_add, project, platform, sensor ".
				"where flight_add.Project = project.ID and flight_add.Platform = platform.ID and flight_add.Sensor = sensor.ID ".
				"order by ProjectName, flight_add.Name" ;
		
		$result = mysqli_query($con,$sql);
		
		$flightList = array();
		while($row = mysqli_fetch_assoc($result)) {
			$flightList[] = $row;
		}
		echo json_encode($flightList);
	}
	
	function UpdateFlight($flightID, $flightName, $date, $project, $platform, $sensor, $altitude, $forward, $side, $con){
		$sql = 	"update flight_add set Name='$flightName', Date='$date', Project=$project, Platform=$platform, ".
				"Sensor=$sensor, Altitude=$altitude, Forward= $forward, Side= $side where id=$flightID"; 
		
		$result = mysqli_query($con,$sql);
		
		if (mysqli_query($con, $sql))
		{
			echo "1";
		} else {
			echo mysqli_error($con);
			
		}
	}
	
	function DeleteFlight($flightID, $con){
		$sql = "delete from flight_add where id = $flightID";
		$result = mysqli_query($con,$sql);
		
		if (mysqli_query($con, $sql))
		{
			echo "1";
		} else {
			echo mysqli_error($con);
			
		}
	}
	
	require_once("SetDBConnection.php");
	
	$con = SetDBConnection();
	
 	if(mysqli_connect_errno($con))
	{
		echo "Failed to connect to database server: ".mysqli_connect_error();
	}
	else
	{
	
		$action = $_GET["action"];
	
		switch ($action) {
			case "add":
				{
					$name = mysqli_real_escape_string($con, $_GET['name']);
					$project = $_GET['project'];
					$platform = $_GET['platform'];
					$sensor = $_GET['sensor'];
					$date = $_GET['date'];
					$date = str_replace('/',"-",$date);
					$altitude = $_GET['altitude'];
					$forward = $_GET['forward'];
					$side = $_GET['side'];
					
					AddFlight($name, $date, $project, $platform, $sensor, $altitude, $forward, $side, $con);
				}break;
			case "list":
				{
					GetFlightList($con);
				}break;
			case "edit":
				{
					$id = $_GET["id"];
					$name = mysqli_real_escape_string($con, $_GET['name']);
					$project = $_GET['project'];
					$platform = $_GET['platform'];
					$sensor = $_GET['sensor'];
					$date = $_GET['date'];
					$date = str_replace('/',"-",$date);
					$altitude = $_GET['altitude'];
					$forward = $_GET['forward'];
					$side = $_GET['side'];
					
					UpdateFlight($id, $name, $date, $project, $platform, $sensor, $altitude, $forward, $side, $con);
				}break;
			case "delete":
				{
					$id = $_GET["id"];
					DeleteFlight($id,$con);
				}break;
			
		}
	}
	
	mysqli_close($con);
?>