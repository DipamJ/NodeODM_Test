<?php
// File containing System Variables
//define("LOCAL_PATH_ROOT", $_SERVER["DOCUMENT_ROOT"]);

require_once("SetFilePath.php");
require_once("CommonFunctions.php");
//require_once("SetDBConnection.php");
require_once("../../../../resources/database/SetDBConnection.php");

//_log("header_location: " .$header_location);
//header('Location: '. $header_location.'/uas_tools/las_upload/');

$con = SetDBConnection();

if (mysqli_connect_errno()) {
    echo "Failed to connect to database server: " . mysqli_connect_error();
} else {
    $identifier = $_GET["identifier"];

    $sql = "SELECT * FROM pointcloud " .
        "WHERE Identifier = '$identifier'";

    $result = mysqli_query($con, $sql);
    $pointcloud = mysqli_fetch_assoc($result);

    $fileNameParts = pathinfo($pointcloud["FileName"]);
    $fileName = FormatFileName($fileNameParts["filename"]) . "." . $fileNameParts["extension"]; // needs to be checked
    $displayName = FormatFileName($pointcloud["Name"]);


    $sourcePath = SetFolderLocalPath() . $displayName . "/" . $fileName;
    $destPath = SetFolderLocalPath() . $displayName . "/Display";
    if (!file_exists($destPath)) {
        if (!mkdir($destPath, 0775, true)) {
            die('Failed to create folders...');
        }
    }
    $displayName = $displayName;

    if (($fp = fopen(SetFolderLocalPath() . $displayName . "/convert_log.txt", 'a+')) !== false) {
        fclose($fp);
    }
    //$command = "/usr/bin/PotreeConverter $sourcePath -o $destPath -p $displayName >> ".SetFolderLocalPath().$displayName."/convert_log.txt";
    $command = "/home/ubuntu/PotreeConverter/build/PotreeConverter $sourcePath -o $destPath -p $displayName >> " . SetFolderLocalPath() . $displayName . "/convert_log.txt";

    //_log("command: " .$command);

    exec($command, $output, $result);

    //_log("result: " . $result);

    if ($result == 0) {
        $displayPath = SetDisplayHTMLPath() . $displayName . "/Display/" . $displayName . ".html";

        $sql = "UPDATE pointcloud " .
            "SET Displaypath = '$displayPath', Status = 'Finished' " .
            "WHERE Identifier = '$identifier'";
        if (mysqli_query($con, $sql)) {
            //header('Location: '. $header_location.'/uas_tools/las_upload/');
            echo "1";
        }
        //mysqli_close($con);

        // PHP Redirect
        //header("Location: http://bhub.gdslab.org/uas_tools/las_upload/");
        //header('Location: '. $header_location.'/uas_tools/las_upload/');

    }
}

// Check if last record on table has been completed, if so, go to index.php
//$con = SetDBConnection();
//
//if (mysqli_connect_errno()) {
//    echo "Failed to connect to database server: " . mysqli_connect_error();
//} else {
//
//    $sql = "SELECT Status FROM pointcloud ORDER BY ID DESC LIMIT 1";
//
//    $result = mysqli_query($con, $sql);
//
//    $list = array();
//    while ($row = mysqli_fetch_assoc($result)) {
//        $list[] = $row;
//    }
//}
//
//if ($list[0] == 'Finished') {
//    header("Refresh:0; url=http://bhub.gdslab.org/web/uas_tools/las_upload/index.php");
//}
////print_r($list);
mysqli_close($con);


?>
