<?php
/**
 * Remove all empty subdirectories
 * @param string $dirPath path to base directory
 * @param bool $deleteBaseDir - Delete also basedir if it is empty
 */
function removeEmptyDirs($dirPath, $deleteBaseDir = false) {

    if (stristr($dirPath, "'")) {
        trigger_error('Disallowed character `Single quote` (\') in provided `$dirPath` parameter', E_USER_ERROR);
    }

    if (substr($dirPath, -1) != '/') {
        $dirPath .= '/';
    }

    $modif = $deleteBaseDir ? '' : '*';
    exec("find '".$dirPath."'".$modif." -empty -type d -delete", $out);
}

$dirPath = 'tt/';

removeEmptyDirs($dirPath, true);
?>
