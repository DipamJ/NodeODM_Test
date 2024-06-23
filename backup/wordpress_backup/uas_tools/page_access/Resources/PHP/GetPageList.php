<?php
ini_set('display_errors', 1);
require_once("SetFilePath.php");
require_once("SetDBConnection.php");

$con = SetDBConnection();
$list = array();

$pages = array_filter(glob(SetToolFolderLocalPath() . "*"), "is_dir");
foreach ($pages as $page) {
    // this folders are ignored and not considered as tools for the system management
    //if (basename($page) != "backup" && basename($page) != "testing" && basename($page) != "visualization" && basename($page) != "system_management") {
    if (basename($page) != "backup" && basename($page) != "testing" && basename($page) != "visualization") {
        $list[] = basename($page);
    }
}
echo json_encode($list);

// IF FOLDER IS DELETED; DELETE IT ALSO FROM THE MYSQL TABLE
$comma_separated = implode("','", $list);

$sql = "DELETE FROM page_access WHERE Page NOT IN ('" . $comma_separated . "')";

$result = mysqli_query($con, $sql);
?>