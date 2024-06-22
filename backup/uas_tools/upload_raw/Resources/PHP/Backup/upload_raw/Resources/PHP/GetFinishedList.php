<?php
	require_once("SetDBConnection.php");
	require_once("CommonFunctions.php");
	$con = SetDBConnection();
		
	if(mysqli_connect_errno($con))
	{
		echo "Failed to connect to database server: ".mysqli_connect_error();
	}
	else
	{
		session_start();
		$userName = $_SESSION["username"];
		$sql =  "select raw_data_upload_status.*, project.Name as ProjectName,  platform.Name as PlatformName, sensor.Name as SensorName, ".
				"replace(flight_add.Date, '-', '/') as Date, flight_add.Name as FlightName ".
				"from raw_data_upload_status, flight_add, project, platform, sensor ".
				"where flight_add.Project = project.ID and flight_add.Platform = platform.ID and flight_add.Sensor = sensor.ID ".
				"and raw_data_upload_status.Status =  'Finished' and flight_add.ID = raw_data_upload_status.Flight and raw_data_upload_status.Uploader = '$userName' ".
				"order by ProjectName, PlatformName, SensorName, Date, FlightName, raw_data_upload_status.FileName";
		$result = mysqli_query($con,$sql);
		
		$list = array();
		while($row = mysqli_fetch_assoc($result)) {
			$row["Size"] = FormatBytes($row["Size"]);
			$row["DownloadPath"] = str_replace("/var/www/html/","https://uashub.tamucc.edu/",$row["UploadFolder"]."/".$row["FileName"]);
			$list[] = $row;
		}
		echo json_encode($list);
	}
	
?>