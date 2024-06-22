<?php
ini_set('display_errors', 1);
require_once("SetDBConnection.php");

$con = SetDBConnection();

if (mysqli_connect_errno()) {
    echo "Failed to connect to database server: " . mysqli_connect_error();
} else {
//		$sql =  "select * from uas_group";
    $sql = "select * from roles";
    $result = mysqli_query($con, $sql);
    if ($result) {
        $list = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $list[] = $row;
        }
        echo json_encode($list);
    }
    mysqli_close($con);
}
?>