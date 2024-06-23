<?php
//require_once("SetDBConnection.php");
require_once("../../../../resources/database/SetDBConnection.php");
require_once("SetFilePath.php");
require_once("CommonFunctions.php");
////////////////////////////////////////////////////////////////////
// THE FUNCTIONS
////////////////////////////////////////////////////////////////////

///**
// *
// * Logging operation - to a file (upload_log.txt) and to the stdout
// * @param string $str - the logging string
// */
//function _log($str) {
//
//    // log to the output
//    $log_str = date('d.m.Y').": {$str}\r\n";
//    echo $log_str;
//
//    // log to file
//    if (($fp = fopen('upload_log.txt', 'a+')) !== false) {
//        fputs($fp, $log_str);
//        fclose($fp);
//    }
//}

/**
 *
 * Delete a directory RECURSIVELY
 * @param string $dir - directory path
 * @link http://php.net/manual/en/function.rmdir.php
 */

/*
function rrmdir($dir) {
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (filetype($dir . "/" . $object) == "dir") {
                    rrmdir($dir . "/" . $object);
                } else {
                    unlink($dir . "/" . $object);
                }
            }
        }
        reset($objects);
        rmdir($dir);
    }
}
*/
/**
 *
 * Check if all the parts exist, and
 * gather all the parts of the file together
 * @param string $temp_dir - the temporary directory holding all the parts of the file
 * @param string $fileName - the original file name
 * @param string $chunkSize - each chunk size (in bytes)
 * @param string $totalSize - original file size (in bytes)
 */
function CreateFileFromChunks($temp_dir, $dest_dir, $downloadPath, $fileName, $chunkSize, $totalSize,$total_files, $identifier) {
//function createFileFromChunks($temp_dir, $dest_dir, $downloadPath, $fileName, $chunkSize, $totalSize,$total_files, $identifier) {

    // count all the parts of this file
    $total_files_on_server_size = 0;
    $temp_total = 0;
    foreach(scandir($temp_dir) as $file) {
        $temp_total = $total_files_on_server_size;
        $tempfilesize = filesize($temp_dir.'/'.$file);
        $total_files_on_server_size = $temp_total + $tempfilesize;
    }
    // check that all the parts are present
    // If the Size of all the chunks on the server is equal to the size of the file uploaded.
    if ($total_files_on_server_size >= $totalSize) {

        _log("dest_dir: " .$dest_dir);

    // create the final destination file
		if (!is_dir($dest_dir)) {
			//echo "make dir";
			mkdir($dest_dir, 0777, true);
		}
        //if (($fp = fopen('temp/'.$fileName, 'w')) !== false) {
        if (($fp = fopen($dest_dir."/".$fileName, 'w')) !== false) {
            for ($i=1; $i<=$total_files; $i++) {
                fwrite($fp, file_get_contents($temp_dir.'/'.$fileName.'.part'.$i));
                //_log('writing chunk '.$i);
            }
            fclose($fp);
        } else {
            //_log('cannot create the destination file');
            return false;
        }

        // rename the temporary directory (to avoid access from other
        // concurrent chunks uploads) and than delete it

        if (rename($temp_dir, $temp_dir.'_UNUSED')) {
            //rrmdir($temp_dir.'_UNUSED');
			RemoveDir($temp_dir.'_UNUSED');
        } else {
            //rrmdir($temp_dir);
			RemoveDir($temp_dir);
        }

		$con = SetDBConnection();
		if(mysqli_connect_errno())
		{
			_log("Failed to connect to database server: ".mysqli_connect_error());
		}
		else
		{
			$sql =  "UPDATE pointcloud ".
					//"SET Status='Finished', TempFolder='', UploadFolder='$dest_dir', DownloadPath = '$downloadPath' ".
					"SET Status='Converting', Progress=100, TempFolder='', UploadFolder='$dest_dir', DownloadPath = '$downloadPath' ".
					"WHERE Identifier = '$identifier'";
			if (mysqli_query($con, $sql))
			{
				_log("added");
			}
			mysqli_close($con);
		}
    }
}


////////////////////////////////////////////////////////////////////
// THE SCRIPT
////////////////////////////////////////////////////////////////////

//check if request is GET and the requested chunk exists or not. this makes testChunks work
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    if(!(isset($_GET['resumableIdentifier']) && trim($_GET['resumableIdentifier'])!='')){
        $_GET['resumableIdentifier']='';
    }
    $temp_dir = 'temp/'.$_GET['resumableIdentifier'];
    if(!(isset($_GET['resumableFilename']) && trim($_GET['resumableFilename'])!='')){
        $_GET['resumableFilename']='';
    }
    if(!(isset($_GET['resumableChunkNumber']) && trim($_GET['resumableChunkNumber'])!='')){
        $_GET['resumableChunkNumber']='';
    }
    $chunk_file = $temp_dir.'/'.$_GET['resumableFilename'].'.part'.$_GET['resumableChunkNumber'];

	if (file_exists($chunk_file)) { //Chunk already exists, skip upload
        header("HTTP/1.0 200 Ok");

    } else { //Chunk not found, start uploading
		//header("HTTP/1.0 404 Not Found");
		header("HTTP/1.0 204 Not Found");
		//$con = SetDBConnection();
		$name = FormatFileName($_GET["name"]);
		$project = $_GET["project"];
		$date = $_GET["date"];
		$description = $_GET["description"];
		$lat = $_GET["lat"];
		$lng = $_GET["lng"];
		//$fileName = FormatFileName(mysqli_real_escape_string($con, $_GET['resumableFilename']));
		$fileNameParts = pathinfo($_GET['resumableFilename']);
		//$fileNameParts = pathinfo(FormatFileName($_GET['resumableFilename']));
		$fileName = FormatFileName($fileNameParts["filename"]).".".$fileNameParts["extension"];

		$size = $_GET['resumableTotalSize'];
		$identifier = $_GET['resumableIdentifier'];
		$temp_dir = SetTempFolderLocalPath().$identifier."/";
		//$temp_dir = 'temp/'.$identifier;
		$chunkNum = $_GET['resumableChunkNumber'];
		$totalChunkNum = $_GET['resumableTotalChunks'];
		if ($chunkNum == 1) {//first chunk, add a record to database
			$con = SetDBConnection();
			//$sql = "INSERT INTO pointcloud (Name, FileName, Size, ChunkCount, Status, Identifier, TempFolder) " .
			//	   "VALUES ('$name','$fileName', $size, $totalChunkNum, 'Uploading', '$identifier', '$temp_dir')";
			$sql = "INSERT INTO pointcloud (Name, Project, Description, Date, Lat, Lng, FileName, Size, ChunkCount, Status, Identifier, TempFolder) " .
				   "VALUES ('$name', $project, '$description', '$date', $lat, $lng, '$fileName', $size, $totalChunkNum, 'Uploading', '$identifier', '$temp_dir')";
			//_log("sql: " .$sql);

			mysqli_query($con, $sql);

			mysqli_close($con);
		}
    }
}

// loop through files and move the chunks to a temporarily created directory
if (!empty($_FILES)) foreach ($_FILES as $file) {

    // check the error status
    if ($file['error'] != 0) {
        //_log('error '.$file['error'].' in file '.$_POST['resumableFilename']);
        continue;
    }
	//$con = SetDBConnection();
	//$fileName = FormatFileName(mysqli_real_escape_string($con, $_GET['resumableFilename']));
	//$fileName = FormatFileName($_GET['resumableFilename']);
	$fileNameParts = pathinfo($_GET['resumableFilename']);
		//$fileNameParts = pathinfo(FormatFileName($_GET['resumableFilename']));
	$fileName = FormatFileName($fileNameParts["filename"]).".".$fileNameParts["extension"];

	$name = FormatFileName($_GET["name"]);
    // init the destination file (format <filename.ext>.part<#chunk>
    // the file is stored in a temporary directory
    if(isset($_POST['resumableIdentifier']) && trim($_POST['resumableIdentifier'])!=''){
        //$temp_dir = '../Temp/'.$_POST['resumableIdentifier'];
		//$dest_dir = '../LAS/';
	    $temp_dir = SetTempFolderLocalPath().$_POST['resumableIdentifier'];
		//$dest_dir = SetFolderLocalPath().str_replace(".las","",$_GET['resumableFilename']);
		$dest_dir = SetFolderLocalPath().$name;
		$downloadPath = SetFolderHTMLPath().$name."/".$fileName;
	}
    //$dest_file = $temp_dir."/".$_POST['resumableFilename'].'.part'.$_POST['resumableChunkNumber'];
	$dest_file = $temp_dir."/".$fileName.'.part'.$_POST['resumableChunkNumber'];

    // create the temporary directory
    if (!is_dir($temp_dir)) {
		mkdir($temp_dir, 0777, true);
    }

    // move the temporary file
    if (!move_uploaded_file($file['tmp_name'], $dest_file)) {
        _log('Error saving (move_uploaded_file) chunk '.$_POST['resumableChunkNumber'].' for file '.$_POST['resumableFilename']);
    } else {

		//require_once("SetDBConnection.php");
		//$con = SetDBConnection();

		// check if all the parts present, and create the final destination file
        //createFileFromChunks($temp_dir, $dest_dir, $_POST['resumableFilename'],$_POST['resumableChunkSize'], $_POST['resumableTotalSize'],$_POST['resumableTotalChunks'], $_GET['resumableIdentifier']);
        //createFileFromChunks($temp_dir, $dest_dir, $downloadPath, $fileName, $_POST['resumableChunkSize'], $_POST['resumableTotalSize'],$_POST['resumableTotalChunks'], $_GET['resumableIdentifier']);
        CreateFileFromChunks($temp_dir, $dest_dir, $downloadPath, $fileName, $_POST['resumableChunkSize'], $_POST['resumableTotalSize'],$_POST['resumableTotalChunks'], $_GET['resumableIdentifier']);
    }
}
?>
