<?php
include('class.uploader.php');

	require_once("../../../PHP/SetDBConnection.php");
	require_once("../../../PHP/SetFilePath.php");
	
	
	function FormatFileName($rawName) {
		$formattedName = str_replace(' ', '_', $rawName); 
		$formattedName = preg_replace('/[^A-Za-z0-9\_]/', '', $formattedName);
		return preg_replace('/_+/', '_', $formattedName);
	}
	
	
	function FormatFileSize($bytes, $decimals = 2) 
	{
		$sz = 'BKMGTP';
		$factor = floor((strlen($bytes) - 1) / 3);
		return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
	}
	
	$con = SetDBConnection();
	
 	if(mysqli_connect_errno($con))
	{
		echo "Failed to connect to database server: ".mysqli_connect_error();
	}
	else
	{
		$site = $_POST["site"];
		$name = $_POST["name"];
		$date = $_POST["date"];
		$description = $_POST["description"];
		
		$type ="pointcloud";
		
		
		$sitePath = SetFolderLocalPath().$site;
		
		if(!file_exists($sitePath)){
			if (!mkdir($sitePath, 0775, true)) {
				die('Failed to create folders...');
			}
		}
		
		$pointcloudPath = $sitePath."/".$type;
		if(!file_exists($pointcloudPath)){
			if (!mkdir($pointcloudPath, 0775, true)) {
				die('Failed to create folders...');
			}
		}
		
		$uploader = new Uploader();
		$data = $uploader->upload($_FILES['files'], array(
			'limit' => 10, //Maximum Limit of files. {null, Number}
			'maxSize' => null, //Maximum Size of files {null, Number(in MB's)}
			'extensions' => null, //Whitelist for file extension. {null, Array(ex: array('jpg', 'png'))}
			//'extensions' => Array(array('las')), //Whitelist for file extension. {null, Array(ex: array('jpg', 'png'))}
			'required' => false, //Minimum one file is required for upload {Boolean}
			'uploadDir' => $pointcloudPath."/",
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
			
			$lasPath = $pointcloudPath."/".$fileFullName;
			$size = FormatFileSize(filesize($lasPath));
			
			
			$outputPath = $pointcloudPath."/potree";
			if(!file_exists($outputPath)){
				if (!mkdir($outputPath, 0775, true)) {
					die('Failed to create folders...');
				}
			}
			
			if ($fileExtension == "las")
			{
				$formattedName = FormatFileName($name);
				
				$command = "LD_LIBRARY_PATH=/usr/local/gcc-4.9.4/lib64:/usr/local/lib /usr/local/bin/PotreeConverter \"$lasPath\" -o $outputPath -p $formattedName 2>&1";
				exec($command, $output);
				//echo $command;
				
				$downloadPath = SetFolderHTMLPath().$site."/pointcloud/".$fileFullName;
				$displayPath = SetFolderHTMLPath().$site."/pointcloud/potree/".$formattedName.".html";
				$metaPath = "";
						
				$sql = 	"INSERT INTO pointcloud (name, date, description, site, download_path, size, display_path, meta_path) " .
					"VALUES ('$name', '$date', '$description', $site, '$downloadPath', '$size', '$displayPath', '$metaPath')";  
						
				//echo $sql;		
				mysqli_query($con, $sql);
				
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
