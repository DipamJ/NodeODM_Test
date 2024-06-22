<?php
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

$sourcePath = "20210326_cc_p4r_driscoll_mosaic.tif";

$destPath = "20210326_cc_p4r_driscoll_mosaic.tif";

$command = 'vipsthumbnail ' .'"'.$sourcePath.'"'.' -o "'.$destPath. '[background=255]" --size=300x';
_log('$command convert: ' . $command);


 ?>


 vipsthumbnail "20210326_cc_p4r_driscoll_mosaic.tif" -o "20210326_cc_p4r_driscoll_mosaic_thumb.jpg[background=255]" --size=300x
