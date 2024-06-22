<?php
session_start();
//require_once("SetDBConnection.php");
require_once("../../../../resources/database/SetDBConnection.php");
require_once("CommonFunctions.php");

$userName = $_SESSION["username"] ?? '';

$con = SetDBConnection();

if (mysqli_connect_errno()) {
    echo "Failed to connect to database server: " . mysqli_connect_error();
} else {
    $sql = "SELECT Receiver, COUNT(*) AS Num FROM notification WHERE Uploader = '$userName' and Receiver != '' GROUP BY Receiver ORDER BY Num DESC";

    $frequentReceiver = "";
    $result = mysqli_query($con, $sql);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        if ($row) {
            $frequentReceiver = $row["Receiver"];
        }
    }

    $sql = "SELECT CC, COUNT(*) AS Num FROM notification WHERE Uploader = '$userName' and CC != '' GROUP BY CC ORDER BY Num DESC";

    $frequentCC = "";
    $result = mysqli_query($con, $sql);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        if ($row) {
            $frequentCC = $row["CC"];
        }
    }

    $contact = array("Receiver" => $frequentReceiver, "CC" => $frequentCC);
    echo json_encode($contact);

    mysqli_close($con);
}
?>
