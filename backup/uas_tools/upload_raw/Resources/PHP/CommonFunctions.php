<?php
// Log Document
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

//Remove special characters, replace spaces with underscores, replace all proceeding underscores with 1 underscore
function FormatFileName($rawName)
{
    $formattedName = str_replace(' ', '_', $rawName);
    $formattedName = preg_replace('/[^A-Za-z0-9\_]/', '', $formattedName);
    return preg_replace('/_+/', '_', $formattedName);
}

//Remove a directory recursively
function RemoveDir($dir)
{
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (filetype($dir . "/" . $object) == "dir") {
                    RemoveDir($dir . "/" . $object);
                } else {
                    unlink($dir . "/" . $object);
                }
            }
        }
        reset($objects);
        rmdir($dir);
    }
}

function GenerateRandomString($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}


function FormatBytes($bytes, $precision = 2)
{
    $units = array('B', 'KB', 'MB', 'GB', 'TB');

    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= (1 << (10 * $pow));

    return round($bytes, $precision) . ' ' . $units[$pow];
}

function UnZip($sourceFile, $destinationFolder, $fileExtension, $uploadID, $con)
{
    $old = umask(0);
    $tempFolder = SetTempFolderLocalPath() . GenerateRandomString();
    if (!file_exists($tempFolder)) {
        if (!mkdir($tempFolder, 0777, true)) {
            die('Failed to create folders...');
        }
    }
    umask($old);

    $command = "";
    if ($fileExtension == "zip") {
        //----------------unzip to temp folder---------------------------
        $command = "unzip -j '" . $sourceFile . "' -d '" . $tempFolder . "' 2>&1";
    } elseif ($fileExtension == "tar") {
        //----------------untar to temp folder---------------------------
        $command = "tar -C '" . $tempFolder . "' -xvf '" . $sourceFile . "' 2>&1";
    }
    exec($command, $output, $result);

    if ($result != 0) { //Failed to unzip the file
        $command = "rm -rf '" . $tempFolder . "'"; // Remove temp folder
        exec($command);

        $command = "rm '" . $sourceFile . "'"; //Remove zip file
        exec($command);

        return 0;
    }

    //----------------remove .zip file-------------------------------
    //$command = "rm '".$tempPath."/".$fileFullName."'";
    //exec($command);


    //------------------------------------Check file if exist, file name and copy to data folder-----------------------

    if ($handle = opendir($tempFolder)) {
        while (false !== ($file = readdir($handle))) {
            if ($file != "." && $file != "..") {
                $fileFullName = pathinfo($file, PATHINFO_BASENAME);
                $fileName = pathinfo($file, PATHINFO_FILENAME);
                $fileExtension = pathinfo($fileFullName, PATHINFO_EXTENSION);

                $newFileName = FormatFileName($fileName);
                $newFileFullName = $newFileName . "." . $fileExtension;

                if (file_exists($destinationFolder . "/" . $newFileFullName)) {
                    $copies = glob($destinationFolder . "/" . $newFileName . "_copy_*." . $fileExtension);

                    $copiesNum = sizeof($copies);
                    $paddingString = "_copy_" . strval($copiesNum + 1);

                    $newFileFullName = $newFileName . $paddingString . "." . $fileExtension;
                    $newFileName = $newFileName . $paddingString;
                }

                copy($tempFolder . "/" . $fileFullName, $destinationFolder . "/" . $newFileFullName);
                //NEED TO MAKE A CHANGE HERE
                //$downloadPath = str_replace("/var/www/html/", "https://uashub.tamucc.edu/", $destinationFolder."/".$newFileFullName);
                //$displayPath = str_replace("/var/www/html/", "https://uashub.tamucc.edu/", $destinationFolder."/Thumbnails/".$newFileName."_thumb.jpg");
                $downloadPath = str_replace($alternative_root_path, $header_location, $destinationFolder . "/" . $newFileFullName);
                $displayPath = str_replace($alternative_root_path, $header_location, $destinationFolder . "/Thumbnails/" . $newFileName . "_thumb.jpg");
                AddRecord($newFileFullName, $uploadID, $downloadPath, $displayPath, $con);
            }
        }

        closedir($handle);
    }

    //----------------temp folder-------------------------------
    $command = "rm -rf '" . $tempFolder . "'";
    exec($command);
    return 1;
}

function AddRecord($name, $uploadID, $downloadPath, $displayPath, $con)
{
    $sql = "INSERT INTO raw_data_upload (Name, UploadID, DownloadPath, DisplayPath) " .
        "VALUES ('$name', $uploadID, '$downloadPath', '$displayPath')";
    mysqli_query($con, $sql);
}

function CreateThumbnails($sourceFolder)
{
    $old = umask(0);
    $thumbFolder = $sourceFolder . "/Thumbnails";
    if (!file_exists($thumbFolder)) {
        if (!mkdir($thumbFolder, 0777, true)) {
            die('Failed to create folders...');
        }
    }
    umask($old);

    if ($handle = opendir($sourceFolder)) {
        while (false !== ($file = readdir($handle))) {
            if ($file != "." && $file != ".." && $file != $date . '_' . $type . '.zip' && $file != "Thumbnails") {
                $fileFullName = pathinfo($file, PATHINFO_BASENAME);
                $fileName = pathinfo($file, PATHINFO_FILENAME);
                $fileExtension = pathinfo($fileFullName, PATHINFO_EXTENSION);

                $sourcePath = $sourceFolder . "/" . $fileFullName;
                $destPath = $sourceFolder . "/Thumbnails/" . $fileName . "_thumb.jpg";


                $maxCoreNum = 4; // set maximum number of cores
                $tempPath = "";

                if (!file_exists($destPath)) {
                    if (strtoupper($fileExtension) == "TIF" || strtoupper($fileExtension) == "TIFF") {
                        $tempPath = str_replace("thumb.jpg", "temp.tif", $destPath);

                        //flatten image
                        $command = 'gdal_translate -co "TILED=YES" -co "COMPRESS=LZW" -co "BIGTIFF=YES" -ot Byte -scale "' . $sourcePath . '" "' . $tempPath . '"  2>&1';
                        exec($command, $output);

                        $image = new Imagick($tempPath);

                        $image->setImageColorspace(255);
                        $image->setCompression(Imagick::COMPRESSION_JPEG);
                        $image->setCompressionQuality(60);
                        $image->setResourceLimit(6, $maxCoreNum); //Set maximum number of cores to use with ImageMagick
                        $image->setImageBackgroundColor('white');
                        $image = $image->flattenImages(); // Use this instead.

                        $image->setImageFormat('jpeg');

                        $image->resizeImage(300, 0, imagick::FILTER_UNDEFINED, 1);
                        $image->writeImage($destPath);

                        $command = 'rm "' . $tempPath . '"';
                        exec($command);
                    } elseif (strtoupper($fileExtension) == "GIF" || strtoupper($fileExtension) == "JPG" || strtoupper($fileExtension) == "JPEG" || strtoupper($fileExtension) == "PNG") {
                        $image = new Imagick($sourcePath);

                        $image->setCompressionQuality(60);
                        $image->setResourceLimit(6, $maxCoreNum); //Set maximum number of cores to use with ImageMagick

                        $image->setImageFormat('jpeg');

                        $image->resizeImage(300, 0, imagick::FILTER_UNDEFINED, 1);
                        $image->writeImage($destPath);
                    }
                }
            }
        }

        closedir($handle);
    }
}

?>
