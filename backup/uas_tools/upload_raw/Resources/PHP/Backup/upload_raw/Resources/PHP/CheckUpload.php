<?php
	require_once("SetDBConnection.php");
	$con = SetDBConnection();
		
	if(mysqli_connect_errno($con))
	{
		echo "Failed to connect to database server: ".mysqli_connect_error();
	}
	else
	{
		$type = "Update";
		session_start();
		$userName = $_SESSION["username"];
		
		$sql = "SELECT *, TIME_TO_SEC(TIMEDIFF(NOW(),LastUpdate)) as TimeSinceLastUpdate FROM raw_data_upload_status ".
			 	"WHERE Status != 'Finished' and Uploader = '$userName'";  
			
		$result = mysqli_query($con,$sql);
		
		$list = array();
		while($row = mysqli_fetch_assoc($result)) {
			
			if ($row["Status"] == "Unzip")
			{
			
			}else {
			
				$directory = $row["TempFolder"]."/";
				$uploadedChunk = 0;
				$files = glob($directory . "*");
				if ($files){
					$uploadedChunk = count($files);
				}
				if ($row["ChunkCount"] > 0)
				{
					$progress =  floor($uploadedChunk * 100 / $row["ChunkCount"]);
					$currentProgress = floor($row["Progress"]);
					if ($currentProgress < $progress){ //there is progress in uploading -> update the progress
						$row["Progress"] = $progress ;
						$sql =  "UPDATE raw_data_upload_status ".
								"SET Progress = $progress, Status = 'Uploading' ".
								"WHERE Identifier = '".$row["Identifier"]."'";
					} else { //no progress, change status to "Unfinished" 
						
						if ($row["Status"] == "Uploading"){
							if ($row["TimeSinceLastUpdate"] >= 60){ //allow 60 seconds to continue upload
								$sql =  "UPDATE raw_data_upload_status ".
										"SET Status = 'Unfinished' ".
										"WHERE Identifier = '".$row["Identifier"]."'";
							} else {
								$type = "NoUpdate";
							}
						} else if ($row["Status"] == "Paused"){
							if ($row["TimeSinceLastUpdate"] >= 60){ //allow 60 seconds to resume upload
								$sql =  "UPDATE raw_data_upload_status ".
										"SET Status = 'Unfinished' ".
										"WHERE Identifier = '".$row["Identifier"]."'";
							} else {
								$type = "NoUpdate";
							}
						}
						
					}
					if ($type == "Update"){
						mysqli_query($con,$sql);
					}
				}
			}
			$list[] = $row;
		}
		echo json_encode($list);
	}
	
?>