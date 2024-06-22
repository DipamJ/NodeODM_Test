<?php
include('class.uploader.php');

	require_once("../../PHP/SetDBConnection.php");
	require_once("../../PHP/SetFilePath.php");
	
	function FormatFileName($rawName) {
		$formattedName = str_replace(' ', '_', $rawName); 
		$formattedName = preg_replace('/[^A-Za-z0-9\_]/', '', $formattedName);
		return preg_replace('/_+/', '_', $formattedName);
	}
	
	$con = SetDBConnection();
	
 	if(mysqli_connect_errno($con))
	{
		echo "Failed to connect to database server: ".mysqli_connect_error();
	}
	else
	{
		$projectID = $_POST["projectID"];
		$projectName = FormatFileName(mysqli_real_escape_string($con, $_POST["projectName"] ));
		$platformID = $_POST["platformID"];
		$platformName = FormatFileName(mysqli_real_escape_string($con, $_POST["platformName"]));
		$sensorID = $_POST["sensorID"];
		$sensorName = FormatFileName(mysqli_real_escape_string($con, $_POST["sensorName"]));
		$date = $_POST["date"];
		$date = str_replace('/',"-",$date);
		$flightID = $_POST["flightID"];
		$flightName = FormatFileName(mysqli_real_escape_string($con, $_POST["flightName"]));
		$productType = $_POST["productType"];
		$tmsPath = mysqli_real_escape_string($con, $_POST["tmsPath"]);
		
		$type ="Product";
		
		$old = umask(0);
		
		$projectPath = SetFolderLocalPath().$projectName;
		
		if(!file_exists($projectPath)){
			if (!mkdir($projectPath, 0777, true)) {
				die('Failed to create folders...');
			}
		}
		
		$platformPath = $projectPath."/".$platformName; 
		if(!file_exists($platformPath)){
			if (!mkdir($platformPath, 0777, true)) {
				die('Failed to create folders...');
			}
		}
		
		$sensorPath = $platformPath."/".$sensorName; 
		if(!file_exists($sensorPath)){
			if (!mkdir($sensorPath, 0777, true)) {
				die('Failed to create folders...');
			}
		}
		
		$datePath = $sensorPath."/".$date; 
		if(!file_exists($datePath)){
			if (!mkdir($datePath, 0777, true)) {
				die('Failed to create folders...');
			}
		}
		
		$flightPath = $datePath."/".$flightName; 
		if(!file_exists($flightPath)){
			if (!mkdir($flightPath, 0777, true)) {
				die('Failed to create folders...');
			}
		}
		
		
		$path = $flightPath."/".$type; 
		if(!file_exists($path)){
			if (!mkdir($path, 0777, true)) {
				die('Failed to create folders...');
			}
		}
		
		$displayPath = $path."/Display"; 
		if(!file_exists($displayPath)){
			if (!mkdir($displayPath, 0777, true)) {
				die('Failed to create folders...');
			}
		}
		
		date_default_timezone_set('America/Chicago');
		$dateTime = date("Ymdhisa");
		$tempPath = SetTempFolderLocalPath().$flightName.$dateTime;
		
		if(!file_exists($tempPath)){
			if (!mkdir($tempPath, 0777, true)) {
				die('Failed to create folders...');
			}
		}
		
		umask($old);
		
		$uploader = new Uploader();
		$data = $uploader->upload($_FILES['files'], array(
			'limit' => 10, //Maximum Limit of files. {null, Number}
			'maxSize' => null, //Maximum Size of files {null, Number(in MB's)}
			'extensions' => null, //Whitelist for file extension. {null, Array(ex: array('jpg', 'png'))}
			'required' => false, //Minimum one file is required for upload {Boolean}
			'uploadDir' => $tempPath."/",
			'title' => array('name'), //New file name {null, String, Array} *please read documentation in README.md
			'removeFiles' => true, //Enable file exclusion {Boolean(extra for jQuery.filer), String($_POST field name containing json data with file names)}
			'replace' => false, //Replace the file if it already exists  {Boolean}
			'perms' => null, //Uploaded file permisions {null, Number}
			'onCheck' => null, //A callback function name to be called by checking a file for errors (must return an array) | ($file) | Callback
			'onError' => null, //A callback function name to be called if an error occured (must return an array) | ($errors, $file) | Callback
			'onSuccess' => null, //A callback function name to be called if all files were successfully uploaded | ($files, $metas) | Callback
			'onUpload' => null, //A callback function name to be called if all files were successfully uploaded (must return an array) | ($file) | Callback
			'onComplete' => null, //A callback function name to be called when upload is complete | ($file) | Callback
			'onRemove' => null //A callback function name to be called by removing files (must return an array) | ($removed_files) | Callback
		));

		if($data['isComplete'])
		{
			
			$files = $data['data'];
	
			echo json_encode($files['metas'][0]['name']);
			$fileFullName = $files['metas'][0]['name'];
			
			$fileExtension = pathinfo($fileFullName, PATHINFO_EXTENSION);
			
			
			if ($fileExtension == "zip" || $fileExtension == "tar")
			{
				$command = "";
				if ($fileExtension == "zip") {
					//----------------unzip to temp folder---------------------------
					$command = "unzip -j '".$tempPath."/".$fileFullName."' -d '".$tempPath."'";
				} else if ($fileExtension == "tar") {
					//----------------untar to temp folder---------------------------
					$command = "tar -C '".$tempPath."' -xvf '".$tempPath."/".$fileFullName."'";
				}
				
				exec($command);
				
				//----------------remove .zip file-------------------------------
				$command = "rm '".$tempPath."/".$fileFullName."'";
				exec($command);
				
			}
			
			//------------------------------------Check file if exist, file name and copy to data folder-----------------------
			
			if ($handle = opendir($tempPath))  
			{
				while (false !== ($file = readdir($handle))) 
				{
					if($file != "." && $file != "..")
					{
						
						$fileFullName = pathinfo($file, PATHINFO_BASENAME);
						$fileName = pathinfo($file, PATHINFO_FILENAME);
						$fileExtension = pathinfo($fileFullName, PATHINFO_EXTENSION);
				
						
						$newFileName = FormatFileName($fileName);
						$newFileFullName = $newFileName.".".$fileExtension;
				
						if (file_exists($path."/".$newFileFullName))
						{
							$copies = glob($path."/".$newFileName."_copy_*.".$fileExtension);
							
							$copiesNum = sizeof($copies);
							$paddingString = "_copy_".strval($copiesNum + 1);
						
							$newFileFullName = $newFileName.$paddingString.".".$fileExtension;
						}
				
						copy($tempPath."/".$fileFullName, $path."/".$newFileFullName);
						
						$downloadPath = SetFolderHTMLPath().$projectName."/".$platformName."/".$sensorName."/".$date."/".$flightName."/".$type."/".$newFileFullName;
						$htmlPath = "";
						$thumbPath = "";
						
						$sql = 	"INSERT INTO data_product (Name, Type, Flight, DownloadPath, HtmlPath, ThumbPath, TMSPath) " .
								"VALUES ('$newFileFullName', '$productType', $flightID, '$downloadPath', '$htmlPath', '$thumbPath', '$tmsPath')";  
						
						mysqli_query($con, $sql);
						
						$old = umask(0);
						
						chmod($path."/".$newFileFullName, 0444);
						
						umask($old);
					}
				}
			
				closedir($handle);
			}
			
			
		}

		if($data['hasErrors']){
			$errors = $data['errors'];
			echo json_encode($errors);
		}

		mysqli_close($con);
		exit;
		

	}

?>
