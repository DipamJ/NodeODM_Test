<?php
//require_once("SetDBConnection.php");
//require_once("SetFilePath.php");
//
//function FormatFileName($rawName)
//{
//    $formattedName = str_replace(' ', '_', $rawName);
//    $formattedName = preg_replace('/[^A-Za-z0-9\_]/', '', $formattedName);
//    return preg_replace('/_+/', '_', $formattedName);
//}
//
//function CreateThumnail($localPath, $fileName, $fileExtension, $displayPath, $con, $type, $flightID)
//{
//    $fileFullName = $fileName . "." . $fileExtension;
//    $htmlLocalPath = $displayPath . "/" . $fileName . "_display.jpg";
//    $thumbLocalPath = $displayPath . "/" . $fileName . "_thumb.jpg";
//    $htmlPath = "";
//    $thumbPath = "";
//
//    $maxCoreNum = 4; // set maximum number of cores
//
//    if (strtoupper($fileExtension) == "TIF" || strtoupper($fileExtension) == "TIFF") {
//        $tempPath = $displayPath . "/" . $fileName . "_temp.tif";
//
//        $command = 'gdal_translate -co "TILED=YES" -co "COMPRESS=LZW" -co "BIGTIFF=YES" -ot Byte -scale "' . $localPath . '" "' . $tempPath . '"';
//        exec($command, $output);
//
//        fwrite($file, "----------------gdal result--------------------------\n");
//        fwrite($file, $output);
//        fwrite($file, "-----------------------------------------------------\n");
//
//        try {
//            $image = new Imagick($tempPath);
//        } catch (ImagickException $e) {
//        }
//
//        $image->setImageColorspace(255);
//        $image->setCompression(Imagick::COMPRESSION_JPEG);
//        $image->setCompressionQuality(60);
//        $image->setResourceLimit(6, $maxCoreNum); //Set maximum number of cores to use with ImageMagick
//        $image->setImageBackgroundColor('white');
//        try {
//            $image = $image->flattenImages();
//        } catch (ImagickException $e) {
//        } // Use this instead.
//
//        $image->setImageFormat('jpeg');
//
//        $image->resizeImage(600, 0, Imagick::FILTER_UNDEFINED, 1);
//        $image->writeImage($htmlLocalPath);
//
//        $image->resizeImage(120, 0, Imagick::FILTER_UNDEFINED, 1);
//        $image->writeImage($thumbLocalPath);
//
//        $command = 'rm "' . $tempPath . '"';
//        exec($command);
//    } elseif (strtoupper($fileExtension) == "GIF" || strtoupper($fileExtension) == "JPG" || strtoupper($fileExtension) == "JPEG" || strtoupper($fileExtension) == "PNG") {
//        try {
//            $image = new Imagick($localPath);
//        } catch (ImagickException $e) {
//        }
//
//        $image->setCompressionQuality(60);
//        $image->setResourceLimit(6, $maxCoreNum); //Set maximum number of cores to use with ImageMagick
//
//        $image->setImageFormat('jpeg');
//
//        $image->resizeImage(600, 0, Imagick::FILTER_UNDEFINED, 1);
//        $image->writeImage($htmlLocalPath);
//
//        $image->resizeImage(120, 0, Imagick::FILTER_UNDEFINED, 1);
//        $image->writeImage($thumbLocalPath);
//    }
//
//    if (file_exists($htmlLocalPath) && file_exists($thumbLocalPath)) {
//        // WHERE ARE THESE FILES _display.jpg, _thumb.jpg
//        //$htmlPath = mysqli_real_escape_string($con, str_replace("/var/www/html/", "http://uashub.tamucc.edu/", $displayPath)."/".$fileName."_display.jpg");
//        //$thumbPath = mysqli_real_escape_string($con, str_replace("/var/www/html/", "http://uashub.tamucc.edu/", $displayPath)."/".$fileName."_thumb.jpg");
//
//        $htmlPath = mysqli_real_escape_string($con, str_replace("/var/www/html/", "http://basfhub.gdslab.org/", $displayPath) . "/" . $fileName . "_display.jpg");
//        $thumbPath = mysqli_real_escape_string($con, str_replace("/var/www/html/", "http://basfhub.gdslab.org/", $displayPath) . "/" . $fileName . "_thumb.jpg");
//
//        $sql = "";
//
//        if ($type == "Raw") {
//            $sql = "update raw_data set HtmlPath = '$htmlPath', ThumbPath = '$thumbPath' " .
//                "where Flight = $flightID and Name = '$fileFullName'";
//        } else {
//            $sql = "update data_product set HtmlPath = '$htmlPath', ThumbPath = '$thumbPath' " .
//                "where Flight = $flightID and Name = '$fileFullName'";
//        }
//
//        if (mysqli_query($con, $sql)) {
//            return "created";
//        } else {
//            echo mysqli_error($con);
//        }
//    } else {
//        //echo mysqli_error("Failed to create thumbnail.");
//        echo "Failed to connect to database server: " . mysqli_connect_error();
//    }
//}
//
//$con = SetDBConnection();
//if (mysqli_connect_errno()) {
//    echo "Failed to connect to database server: " . mysqli_connect_error();
//} else {
//    $type = $_GET['type'];
//
//    //if ($path){
//    if ($_GET['path']) {
//        $path = $_GET['path'];
//        $fileFullName = pathinfo($path, PATHINFO_BASENAME);
//        $fileName = pathinfo($path, PATHINFO_FILENAME);
//        $fileExtension = pathinfo($fileFullName, PATHINFO_EXTENSION);
//
//        $path = dirname($path);
//        //$displayPath = str_replace("http://uashub.tamucc.edu/","/var/www/html/", $path)."/Display";
//
//        //$localPath = str_replace("http://uashub.tamucc.edu/","/var/www/html/", $path)."/".$fileFullName;
//        $displayPath = str_replace("http://basfhub.gdslab.org/", "/var/www/html/", $path) . "/Display";// CHECK
//
//        $localPath = str_replace("http://basfhub.gdslab.org/", "/var/www/html/", $path) . "/" . $fileFullName;// CHECK
//
//        $id = $_GET['id'];
//
//        $sql = "";
//        if ($type == "Raw") {
//            $sql = "select * from raw_data where id = $id";
//        } else {
//            $sql = "select * from data_product where id = $id";
//        }
//
//        $result = mysqli_query($con, $sql);
//        $flightData = mysqli_fetch_assoc($result);
//        $flightID = $flightData['Flight'];
//
//        echo CreateThumnail($localPath, $fileName, $fileExtension, $displayPath, $con, $type, $flightID);
//    } else {
//        $project = FormatFileName($_GET['project']);
//        $platform = FormatFileName($_GET['platform']);
//        $sensor = FormatFileName($_GET['sensor']);
//        $date = $_GET['date'];
//        $date = str_replace('/', "-", $date);
//        $flight = FormatFileName($_GET['flight']);
//        $flightID = $_GET['flightID'];
//
//        $path = SetFolderLocalPath() . $project . "/" . $platform . "/" . $sensor . "/" . $date . "/" . $flight . "/" . $type;
//
//        $displayPath = $path . "/Display";
//
//        if ($handle = opendir($path)) {
//            while (false !== ($file = readdir($handle))) {
//                if ($file != "." && $file != ".." && $file != $date . '_' . $type . '.zip' && $file != "Display") {
//                    $fileFullName = pathinfo($file, PATHINFO_BASENAME);
//                    $fileName = pathinfo($file, PATHINFO_FILENAME);
//                    $fileExtension = pathinfo($fileFullName, PATHINFO_EXTENSION);
//
//                    $localPath = $path . "/" . $fileFullName;
//
//                    //Check if file already has thumbnail, only make thumbnail if not exist
//                    if (!file_exists($path . "/Display/" . $fileName . "_display.jpg") || !file_exists($path . "/Display/" . $fileName . "_thumb.jpg")) {
//                        if ($fileExtension != "zip") {
//                            CreateThumnail($localPath, $fileName, $fileExtension, $displayPath, $con, $type, $flightID);
//                        }
//                    }
//                }
//            }
//
//            closedir($handle);
//        }
//
//        mysqli_close($con);
//        exit;
//    }
//}