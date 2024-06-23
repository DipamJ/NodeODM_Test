<?php
function delete_directory($target) {
         if (is_dir($target))
           $dir_handle = opendir($target);
     if (!$dir_handle)
          return false;
     while($file = readdir($dir_handle)) {
           if ($file != "." && $file != "..") {
                if (!is_dir($dirname."/".$file))
                     unlink($dirname."/".$file);
                else
                     delete_directory($target.'/'.$file);
           }
     }
     closedir($dir_handle);
     rmdir($target);
     return true;
}

$path = '/tt/';

delete_directory($path);
?>
