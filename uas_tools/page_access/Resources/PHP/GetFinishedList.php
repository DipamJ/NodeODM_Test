<?php
//	require_once("SetDBConnection.php");
//	require_once("CommonFunctions.php");
//	$con = SetDBConnection();
//
//	if(mysqli_connect_errno($con))
//	{
//		echo "Failed to connect to database server: ".mysqli_connect_error();
//	}
//	else
//	{
//		$project = $_GET["project"];
//
//		$sql =  "select imagery_product.*, project.Name as ProjectName,  platform.Name as PlatformName, sensor.Name as SensorName, ".
//				"replace(flight.Date, '-', '/') as Date, flight.Name as FlightName ".
//				"from imagery_product, flight, project, platform, sensor ".
//				"where flight.Project = project.ID and flight.Platform = platform.ID and flight.Sensor = sensor.ID ".
//				"and imagery_product.Status =  'Finished' and flight.ID = imagery_product.Flight  and flight.Project like '$project' ".
//				"order by ProjectName, PlatformName, SensorName, Date, FlightName, imagery_product.FileName";
//		$result = mysqli_query($con,$sql);
//		$list = array();
//		while($row = mysqli_fetch_assoc($result)) {
//			$row["Size"] = FormatBytes($row["Size"]);
//			$row["DownloadPath"] = str_replace("/var/www/html/","https://uashub.tamucc.edu/",$row["UploadFolder"]."/".$row["FileName"]);
//			$list[] = $row;
//		}
//		echo json_encode($list);
//	}
//
//?>