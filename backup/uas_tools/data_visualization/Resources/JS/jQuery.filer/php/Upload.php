<?php
	include('class.uploader.php');

	require_once("../../../PHP/SetFilePath.php");
	
	date_default_timezone_set('America/Chicago');
	$dateTime = date("Ymdhisa");
	$hash = md5($dateTime); 
	$tempPath = SetTempFolderLocalPath().$hash;
	
	if(!file_exists($tempPath)){
		if (!mkdir($tempPath, 0775, true)) {
			$result = array("Result" => "Fail", "Error" => "Failed to create folders"); 
			echo json_encode($result);
			exit;
		}
	}
		
	$uploader = new Uploader();
	$data = $uploader->upload($_FILES['files'], array(
		'limit' => 1, //Maximum Limit of files. {null, Number}
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
		$fileFullName = $files['metas'][0]['name'];
		$fileFullPath = $tempPath."/".$fileFullName;
		$fileExtension = pathinfo($fileFullName, PATHINFO_EXTENSION);
		
		
		if ($fileExtension == "xml")
		{
			$xml = simplexml_load_file($fileFullPath);
			
			$title = (string)$xml->xpath("//title")[0];
			$abstract  = (string) $xml->xpath("//abstract")[0];
			$startDate =  (string) $xml->xpath("//begdate")[0];
			
			$metadata = array("Title" => $title, "Abstract" => $abstract, "StartDate" => $startDate); 
			
			$result = array("Result" => "Success", "FilePath" => $fileFullPath, "Metadata" => $metadata); 
			
		} else {
			$result = array("Result" => "Success", "FilePath" => $fileFullPath); 
		}
		echo json_encode($result);
	}

	if($data['hasErrors']){
		$errors = $data['errors'];
		$result = array("Result" => "Fail", "Error" => $errors); 
		echo json_encode($result);
	}
?>
