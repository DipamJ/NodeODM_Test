<?php
//require_once("SetDBConnection.php");
// ERRORS
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function _log($str)
{
    // log to the output
    $log_str = date('d.m.Y') . ": {$str}\r\n";
    echo $log_str;

    // log to file
    if (($fp = fopen('upload_log.txt', 'a+')) !== false) {
        fputs($fp, $log_str);
        fclose($fp);
    }
}

function SetDBConnection()
{
    return mysqli_connect("localhost", "hub_admin", "UasHtp_Rocks^^7", "uas_projects");
}

//Remove special characters, replace spaces with underscores, replace all proceeding underscores with 1 underscore
function FormatFileName($rawName)
{
    $formattedName = str_replace(' ', '_', $rawName);
    $formattedName = preg_replace('/[^A-Za-z0-9\_]/', '', $formattedName);
    return preg_replace('/_+/', '_', $formattedName);
}

// Function to generate random string
function GenerateRandomString()
{
    //$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $characters = '0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < 10; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

_log('ttt');

//
print_r ($_FILES['myFile']);
//
//
// if (isset($_FILES['myFile'])) {
//   //echo 'test';
//
//     // Example:
//     // if image was moved successfully
//     if (move_uploaded_file($_FILES['myFile']['tmp_name'], "uploads/" . $_FILES['myFile']['name']) == false) {
//         echo 'Image could not be uploaded.';
//     } //move_uploaded_file($_FILES['myFile']['tmp_name'], "uploads/" . $_FILES['myFile']['name']);
//     else {
//         echo 'Image has been uploaded.';
//
//         //print_r($_FILES['myFile']);
//
//         $con = SetDBConnection();
//
//         // Flight
//         //$flightID = $_GET["flightID"];
//         $flightID = 1;
//
//         // FileName
//         //$fileNameParts = pathinfo($_GET['resumableFilename']);
//         $fileNameParts = pathinfo($_FILES['myFile']['name']);
//         //_log('$fileNameParts: '.$fileNameParts);
//         $fileName = FormatFileName($fileNameParts["filename"]) . "." . $fileNameParts["extension"];
//         //$fileName = $_FILES['myFile']['name'];
//         //_log('$fileName: ' . $fileName);
//
//         // FileSize
//         $size = $_FILES['myFile']['size'];
//         //_log('$size: ' . $size);
//
//         //TotalChunk
//         $totalChunkNum = 1;
//
//         // Status
//         $status = 'Finished';
//
//         // Temporary Directory
//         $temp_dir = '';
//
//         // Destination Directory
//         $dest_dir = getcwd() . '/uploads/';
//         //_log('$dest_dir: ' . $dest_dir);
//
//         // Identifier
//         // random data of 10 characters + file name with no dots
//         // ex: 2324447011-20160427_p4_cotton_sorghumtargz
//         // strip file name from dots
//         $identifier = GenerateRandomString() . '-' . str_replace('.', '', $fileName);;
//         //_log('$identifier: ' . $identifier);
//
//         // Notes
//         $notes = $_POST['notes'];
//         //_log('$notes: ' . $notes);
//
//         // User who uploaded the file
//         //$userName = $_SESSION["email"];
//         $userName = 'jose.landivarscott@agnet.tamu.edu';
//         //_log('$userName: ' . $userName);
//         //echo $userName;
//
//         if (mysqli_connect_errno()) {
//             echo "Failed to connect to database server: " . mysqli_connect_error();
//         } else {
// //            INSERT INTO table( point )
// //            VALUES( POINT(0.0000,90.0000);27.7823, -97.5606
//             $latitude = 27.7823;
//             $longitude = -97.5606;
//
//             $sql = "INSERT INTO photos_upload (Flight, FileName, Size, ChunkCount, Status, TempFolder, UploadFolder, Identifier, Progress, Uploader, Notes, Coordinate) " .
//                 "VALUES ('$flightID', '$fileName', '$size', '$totalChunkNum', '$status', '$temp_dir', '$dest_dir', '$identifier', NULL, '$userName', '$notes', POINT('$latitude', '$longitude'))";
//
//             _log("sql: " .$sql);
//             mysqli_query($con, $sql);
//
//         }
//     }
//     mysqli_close($con);
// }
// mysqli_close($con);
?>
