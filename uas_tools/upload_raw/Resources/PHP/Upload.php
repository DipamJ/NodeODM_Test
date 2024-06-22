<?php
//require_once("SetDBConnection.php");
require_once("../../../../resources/database/SetDBConnection.php");
require_once("SetFilePath.php");
require_once("CommonFunctions.php");
////////////////////////////////////////////////////////////////////
// THE FUNCTIONS
////////////////////////////////////////////////////////////////////

/**
 *
 * Logging operation - to a file (upload_log.txt) and to the stdout
 * @param string $str - the logging string
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
// function createFileFromChunks($temp_dir, $fileName, $chunkSize, $totalSize,$total_files) {
function CreateFileFromChunks($temp_dir, $dest_dir, $fileName, $chunkSize, $totalSize, $total_files, $identifier)
{
    //function createFileFromChunks($temp_dir, $dest_dir, $downloadPath, $fileName, $chunkSize, $totalSize,$total_files, $identifier) {

    // count all the parts of this file
    $total_files_on_server_size = 0;
    $temp_total = 0;
    foreach (scandir($temp_dir) as $file) {
        $temp_total = $total_files_on_server_size;
        $tempfilesize = filesize($temp_dir . '/' . $file);
        $total_files_on_server_size = $temp_total + $tempfilesize;
    }
    // check that all the parts are present
    // If the Size of all the chunks on the server is equal to the size of the file uploaded.
    if ($total_files_on_server_size >= $totalSize) {
        // create the final destination file
        if (!is_dir($dest_dir)) {
            //echo "make dir";
            mkdir($dest_dir, 0777, true);
            //_log('Final Destination Dir Created '.$dest_dir);
        }
        //if (($fp = fopen('temp/'.$fileName, 'w')) !== false) {
        if (($fp = fopen($dest_dir . "/" . $fileName, 'w')) !== false) {
            for ($i = 1; $i <= $total_files; $i++) {
                fwrite($fp, file_get_contents($temp_dir . '/' . $fileName . '.part' . $i));
                //_log('writing chunk '.$i);
            }
            fclose($fp);
        } else {
            _log('cannot create the destination file');
            return false;
        }

        // rename the temporary directory (to avoid access from other
        // concurrent chunks uploads) and than delete it
        if (rename($temp_dir, $temp_dir . '_UNUSED')) {
            RemoveDir($temp_dir . '_UNUSED');
            //_log('Temp Dir _UNUSED Removed ');
        } else {
            RemoveDir($temp_dir);
            //_log('Temp Dir Removed ');
        }

        $con = SetDBConnection();
        if (mysqli_connect_errno()) {
            _log("Failed to connect to database server: " . mysqli_connect_error());
        } else {
            $sql = "UPDATE raw_data_upload_status " .
                //"SET Status='Unzip', Progress=100, TempFolder='', UploadFolder='$dest_dir' ".
                "SET Status='Finished', Progress=100, TempFolder='', UploadFolder='$dest_dir' " .
                "WHERE Identifier = '$identifier'";

            if (mysqli_query($con, $sql)) {
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
    if (!(isset($_GET['resumableIdentifier']) && trim($_GET['resumableIdentifier']) != '')) {
        $_GET['resumableIdentifier'] = '';
    }
    $identifier = $_GET['resumableIdentifier'];
    $temp_dir = SetTempFolderLocalPath() . $identifier . "/";
    //$temp_dir = 'temp/'.$_GET['resumableIdentifier'];
    if (!(isset($_GET['resumableFilename']) && trim($_GET['resumableFilename']) != '')) {
        $_GET['resumableFilename'] = '';
    }
    if (!(isset($_GET['resumableChunkNumber']) && trim($_GET['resumableChunkNumber']) != '')) {
        $_GET['resumableChunkNumber'] = '';
    }
    $chunk_file = $temp_dir . '/' . $_GET['resumableFilename'] . '.part' . $_GET['resumableChunkNumber'];

    if (file_exists($chunk_file)) { //Chunk already exists, skip upload
        header("HTTP/1.0 200 Ok");
    } else { //Chunk not found, start uploading
        header("HTTP/1.0 204 Not Found");

        session_start();
        $userName = $_SESSION["email"];
        //print_r($userName);
        //var_dump($dest_dir);

        // CHECK IF ANY OF THIS VARIABLES ARE NEEDED
        //Refresh to keep the session variables alive
        //$_SESSION["username"] = $_SESSION["username"];
        //$_SESSION["userroles"] = $_SESSION["userroles"];
        //$_SESSION["groups"] = $_SESSION["groups"];
        //_log('Uploader is '.$userName);
        //print_r($dest_dir);
        //var_dump($dest_dir);

        $flightID = $_GET["flightID"];

        $fileNameParts = pathinfo($_GET['resumableFilename']);
        $fileName = FormatFileName($fileNameParts["filename"]) . "." . $fileNameParts["extension"];
        $token = $_GET['token'] ?? '';// CHECK
        $size = $_GET['resumableTotalSize'];
        //$identifier = $_GET['resumableIdentifier'];
        //$temp_dir = SetTempFolderLocalPath().$identifier."/";
        $chunkNum = $_GET['resumableChunkNumber'];
        $totalChunkNum = $_GET['resumableTotalChunks'];
        if ($chunkNum == 1) {//first chunk, add a record to database
            $con = SetDBConnection();

            $identifier = $_GET['resumableIdentifier'];
            $sql = "select * from raw_data_upload_status where Identifier = '$identifier'";
            //_log('first chunk, add a record to database ');
            //_log($sql);
            $result = mysqli_query($con, $sql);
            $product = mysqli_fetch_assoc($result);

            if (!$product) {
                //_log('There is no product');
                $projectName = FormatFileName(mysqli_real_escape_string($con, $_GET["project"]));
                $platformName = FormatFileName(mysqli_real_escape_string($con, $_GET["platform"]));
                $sensorName = FormatFileName(mysqli_real_escape_string($con, $_GET["sensor"]));
                $date = $_GET["date"];
                $date = str_replace('/', "-", $date);
                $flightID = $_GET["flightID"];
                $flightName = FormatFileName(mysqli_real_escape_string($con, $_GET["flightName"]));

                $old = umask(0);

                $projectPath = SetFolderLocalPath() . $projectName;

                if (!file_exists($projectPath)) {
                    if (!mkdir($projectPath, 0777, true)) {
                        die('Failed to create folders...');
                    }
                }

                $platformPath = $projectPath . "/" . $platformName;
                if (!file_exists($platformPath)) {
                    if (!mkdir($platformPath, 0777, true)) {
                        die('Failed to create folders...');
                    }
                }

                $sensorPath = $platformPath . "/" . $sensorName;
                if (!file_exists($sensorPath)) {
                    if (!mkdir($sensorPath, 0777, true)) {
                        die('Failed to create folders...');
                    }
                }

                $datePath = $sensorPath . "/" . $date;
                if (!file_exists($datePath)) {
                    if (!mkdir($datePath, 0777, true)) {
                        die('Failed to create folders...');
                    }
                }

                $path = $datePath . "/" . $flightName;
                if (!file_exists($path)) {
                    if (!mkdir($path, 0777, true)) {
                        die('Failed to create folders...');
                    }
                }
                //_log('Uploader is '.$userName);

                umask($old);
                $dest_dir = $path;

                $sql = "INSERT INTO raw_data_upload_status (Flight, FileName, Size, ChunkCount, Status, TempFolder, UploadFolder, Identifier, Progress, Uploader) " .
                    "VALUES ('$flightID', '$fileName', '$size', '$totalChunkNum', 'Uploading', '$temp_dir', '$dest_dir', '$identifier', NULL, '$userName')";
                mysqli_query($con, $sql);
                //_log('Create product for database ');
                //_log($sql);

                $receiver = $_GET["receiver"] ?? '';
                $cc = $_GET["cc"] ?? '';
                $note = $_GET["note"] ?? '';

                $sql = "INSERT INTO notification (Identifier, Uploader, Receiver, CC, Note, FileName, FileSize, Project, Folder, Date, Flight) " .
                    "VALUES ('$identifier', '$userName', '$receiver', '$cc', '$note', '$fileName', '$size', '$projectName', '$dest_dir', '$date', '$flightName')";
                mysqli_query($con, $sql);
            } else { //product is already in the database yet, change it status to "uploading"

                $sql = "UPDATE raw_data_upload " .
                    "SET Status='Uploading', Uploader='$userName' " .
                    "WHERE Identifier = '$identifier'";

                mysqli_query($con, $sql);
            }
            mysqli_close($con);
        }
    }
}

// loop through files and move the chunks to a temporarily created directory
if (!empty($_FILES)) foreach ($_FILES as $file) {
//if (!empty($_FILES)) {
//    foreach ($_FILES as $file) {
    //_log('Log 1');

    // check the error status
    if ($file['error'] != 0) {
        _log('error ' . $file['error'] . ' in file ' . $_GET['resumableFilename']);
        continue;
    }

    $con = SetDBConnection();
    $fileNameParts = pathinfo($_GET['resumableFilename']);
    $fileName = FormatFileName($fileNameParts["filename"]) . "." . $fileNameParts["extension"];

    // init the destination file (format <filename.ext>.part<#chunk>
    // the file is stored in a temporary directory
    if (isset($_GET['resumableIdentifier']) && trim($_GET['resumableIdentifier']) != '') {
        $identifier = $_GET['resumableIdentifier'];

        $sql = "select * from raw_data_upload_status where Identifier = '$identifier'";
        //_log('select product database: '.$sql);
        $result = mysqli_query($con, $sql);
        $product = mysqli_fetch_assoc($result);
        if ($product) {
            $temp_dir = $product["TempFolder"];
            $dest_dir = $product["UploadFolder"];

            //_log('Temp Dir Created '.$temp_dir);
            //_log('Upload Dir Created '.$dest_dir);
        }
    }

    $dest_file = $temp_dir . "/" . $fileName . '.part' . $_GET['resumableChunkNumber'];

    // create the temporary directory
    if (!is_dir($temp_dir)) {
        mkdir($temp_dir, 0777, true);
        //_log('Temp Dir Created '.$temp_dir);
    }

    // move the temporary file
    if (!move_uploaded_file($file['tmp_name'], $dest_file)) {
        _log('Error saving (move_uploaded_file) chunk ' . $_GET['resumableChunkNumber'] . ' for file ' . $_GET['resumableFilename']);
    } else {
        //_log($dest_dir);
        // check if all the parts present, and create the final destination file
        CreateFileFromChunks($temp_dir, $dest_dir, $fileName, $_GET['resumableChunkSize'], $_GET['resumableTotalSize'], $_GET['resumableTotalChunks'], $_GET['resumableIdentifier']);
    }
}
?>
