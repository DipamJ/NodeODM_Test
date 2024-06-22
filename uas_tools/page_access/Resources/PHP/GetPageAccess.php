<?php
ini_set('display_errors', 1);
//require_once("SetDBConnection.php");
require_once("../../../../resources/database/SetDBConnection.php");

$con = SetDBConnection();

if (mysqli_connect_errno()) {
    echo "Failed to connect to database server: " . mysqli_connect_error();
} else {
    $name = $_GET["name"];
    //echo ('Selected name: ' .$name);

    $sql = "select * from page_access where Page = '$name'";
    if ($result = mysqli_query($con, $sql)) {
        if ($row = mysqli_fetch_assoc($result)) {
            echo $row["Page_Groups"];
        }
    }
    mysqli_close($con);
}

?>
