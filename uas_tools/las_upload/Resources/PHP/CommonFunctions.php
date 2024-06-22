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
	function FormatFileName($rawName) {
		$formattedName = str_replace(' ', '_', $rawName);
		$formattedName = preg_replace('/[^A-Za-z0-9\_]/', '', $formattedName);
		return preg_replace('/_+/', '_', $formattedName);
	}

	//Remove a directory recursively
	function RemoveDir($dir) {
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
?>
