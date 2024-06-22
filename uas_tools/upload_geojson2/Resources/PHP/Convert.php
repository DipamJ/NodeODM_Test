<?php
require_once("SetFilePath.php");
require_once("CommonFunctions.php");
//require_once("SetDBConnection.php");
require_once("../../../../resources/database/SetDBConnection.php");
require_once("Email.php");

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

function Convert($identifier, $con)
{
    // ogr2ogr -s_srs EPSG:4326 -t_srs EPSG:4326 -f GEOJSON plot_boundary_1_converted.geojson plot_boundary_1.geojson

    $converterPath = "ogr2ogr";
    $epsgInput = $_GET["epsg"];
    //_log('epsg: '.$epsgInput);

//    $sql = "SELECT imagery_product.*, product_type.Name as TypeName FROM imagery_product, product_type " .
//        "WHERE Identifier = '$identifier' and imagery_product.type = product_type.ID";
    $sql = "SELECT vector_data.*, product_type.Name as TypeName FROM vector_data, product_type " .
        "WHERE Identifier = '$identifier' and vector_data.type = product_type.ID";
    //_log('sql: '.$sql);

    $result = mysqli_query($con, $sql);
    $file = mysqli_fetch_assoc($result);

    $fileNameParts = pathinfo($file["FileName"]);
    $fileName = FormatFileName($fileNameParts["filename"]) . "." . $fileNameParts["extension"];
//    $minZoom = $file["MinZoom"];
//    $maxZoom = $file["MaxZoom"];
    $epsg = $file["EPSG"];

    //_log('epsg: ' .$epsg);
    //_log('file["FileName"]: ' .$file["FileName"]);
    //_log('"FileName": ' .$fileName);
    //print_r($file);

    $old = umask(0);

    // commented this out

     $sourcePath = $file["UploadFolder"]."/".$fileName;
     //_log('$sourcePath: '.$sourcePath);
     if($file["TypeName"] == "GeoJSON"){
     //File location:

     //ogr2ogr -s_srs EPSG:4326 -t_srs EPSG:4326 -f GEOJSON plot_boundary_1_converted.geojson plot_boundary_1.geojson
         $newName = str_replace(".geojson","_converted.geojson",$sourcePath);

         $toUseEPSG = 4326;
         // If the file is not EPSG 4326 already, convert it
         if($epsgInput != 4326){
             $command = "$converterPath -s_srs EPSG:" . $epsgInput . " -t_srs EPSG:" . $toUseEPSG . " -f GEOJSON " . $newName . " " . $sourcePath;
             //_log('$command: '.$command);
             exec($command, $output, $result);
             //_log('$result" '.$result);
         }
        // If the file is already EPSG 4326, continue
         else{
                $result = 0;
         }
     }
     // If file is other than GeoJSON, skip conversion
     /////// THERE IS NO NEED TO CONVERT ///////
     //elseif ($file["TypeName"] == "SHAPE") {
     else {
       $result = 0;
     }

     //_log('$result" '.$result);

    umask($old);

        /////// THERE IS NO NEED TO CONVERT ///////
//     if ($result == 0) {
//         //_log('File has been converted.');
//
//         $displayPath = "";
//         $TMSPath = "";
//         $displayThumbPath = "";
//         $boundary = "";
//
// //        $sql = "UPDATE imagery_product " .
// //            "SET Displaypath = '$displayPath', TMSPath= '$TMSPath', ThumbPath='$displayThumbPath', Boundary = '$boundary',Status = 'Finished' " .
// //            "WHERE Identifier = '$identifier'";
//
//        $sql = "UPDATE vector_data " .
//            "SET TMSPath= '$TMSPath', Boundary = '$boundary', Status = 'Finished' " .
//            "WHERE Identifier = '$identifier'";
//
//         // $sql = "UPDATE vector_data " .
//         //     "SET Status = 'Finished' " .
//         //     "WHERE Identifier = '$identifier'";
//
//         if (mysqli_query($con, $sql)) {
//             //Email($identifier, "Success");
//             echo "File has been converted sucessfully.";
//         }
//     } else {
//         //Email($identifier, "Failed");
//         //DeleteProduct($con, $identifier);
//         echo "Failed to process the data product. Please make sure to select the correct product type and try again.";
//     }

        /////// THERE IS NO NEED TO CONVERT ///////
//     $sql = "SELECT * FROM vector_data " .
//         "WHERE Identifier != '$identifier' and Status = 'Pending' Order by ID ";
//
//     $result = mysqli_query($con, $sql);
//     $file = mysqli_fetch_assoc($result);
//     if ($file) { //Found a file in queue, start converting it
// //        $sql = "UPDATE imagery_product " .
// //            "SET Status = 'Converting' " .
// //            "WHERE Identifier = '" . $file["Identifier"] . "'";
//         $sql = "UPDATE vector_data " .
//             "SET Status = 'Converting' " .
//             "WHERE Identifier = '" . $file["Identifier"] . "'";
//
//         if (mysqli_query($con, $sql)) {
//             Convert($file["Identifier"], $con);
//         }
//     }

}

//_log("sourcePath1: " .$sourcePath);

$con = SetDBConnection();
if (mysqli_connect_errno()) {
    echo "Failed to connect to database server: " . mysqli_connect_error();
} else {
    $identifier = $_GET["identifier"];

    $sql = "SELECT * FROM vector_data " .
        "WHERE Identifier != '$identifier' and Status = 'Converting' ";

    $result = mysqli_query($con, $sql);
    $file = mysqli_fetch_assoc($result);
    if ($file) { //There is another file being converted, change this file's status to pending
//        $sql = "UPDATE imagery_product " .
//            "SET Status = 'Pending' " .
//            "WHERE Identifier = '$identifier'";
        $sql = "UPDATE vector_data " .
            "SET Status = 'Pending' " .
            "WHERE Identifier = '$identifier'";

        if (mysqli_query($con, $sql)) {
            echo "Pending.";
        }
    } else { // No other file is being convert, start converting this file
        Convert($identifier, $con);
    }

    mysqli_close($con);
}

// ADDED
$epsgInput = $_GET["epsg"];
//_log('epsg: '.$epsgInput);

if ($epsgInput == 4326){
    $identifier = $_GET["identifier"];
    $con = SetDBConnection();

//    $sql = "SELECT imagery_product.*, product_type.Name as TypeName FROM imagery_product, product_type " .
//        "WHERE Identifier = '$identifier' and imagery_product.type = product_type.ID";

    $sql = "SELECT vector_data.*, product_type.Name as TypeName FROM vector_data, product_type " .
        "WHERE Identifier = '$identifier' and vector_data.type = product_type.ID";

    $result = mysqli_query($con, $sql);
    $file = mysqli_fetch_assoc($result);

//_log("sql: " .$sql);
//print_r($file);

    $fileNameParts = pathinfo($file["FileName"]);
    $fileName = FormatFileName($fileNameParts["filename"]) . "." . $fileNameParts["extension"];

    $sourcePath = $file["UploadFolder"]."/".$fileName;

///////////////////////////////////////////////////////////////////////////

    // Get new name
    $newName = str_replace(".geojson","_converted.geojson",$sourcePath);
    // Copy file to have files with EPSG 4326 with _converted
    $command = "cp " .$sourcePath ." " .$newName ." 2>&1";
    exec($command, $output, $result);

//    _log('$command: '.$command);
//    print_r($output);
//    _log('result: '.$result);

// ADDED
    mysqli_close($con);
}
?>
