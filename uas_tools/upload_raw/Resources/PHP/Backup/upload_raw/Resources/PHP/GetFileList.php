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
		$uploadID = $_GET["uploadID"];
		
		$sql =  "select * from raw_data_upload ".
				"where uploadID = $uploadID order by Name";
		$result = mysqli_query($con,$sql);
		
		$list = array();
		while($row = mysqli_fetch_assoc($result)) {
			
			$localPath = str_replace("https://uashub.tamucc.edu/","/var/www/html/", $row["DownloadPath"]);
			$row["Size"] = FormatBytes(filesize($localPath));
			$localDisplayPath = str_replace("https://uashub.tamucc.edu/","/var/www/html/", $row["DisplayPath"]);
			if(!file_exists($localDisplayPath)){
				$row["DisplayPath"] = "Resources/Images/NoThumb.jpg";
			}
			
			$list[] = $row;
		}
		echo json_encode($list);
	}
	
?>