<?php
require_once("CommonFunctions.php");
//require_once("SetDBConnection.php");

$hash = $_GET['h'] ?? '';
$email = $_GET['e'] ?? '';

if ($hash == hash('sha512', 'ACCEPT')) {

    //FIND THE USER AND SET user_approved = 1
    //$sql = mysqli_query($conn, "update tbl_users set admin_approved = true where email = '$email'");
    //$sql = mysqli_query($conn, "update tbl_users set admin_approved = true where email = 'lll@gmail.com'");
    $sql = "UPDATE users SET admin_approved = 'Approved' WHERE email = '$email'";

    $result = mysqli_query($connect, $sql);
    if ($result) {
        echo "User has been approved";
    } else {
        echo "Error: " . $sql . "" . mysqli_error($connect);
    }
}
//elseif ($hash == hash('sha512', 'DECLINE')) {
//
//  //MAIL THE USER NOTIFYING THAT THE ACCOUNT HAS NOT BEEN APPROVED
//    echo "User has not been approved";
//}

if ($hash == hash('sha512', 'DECLINE')) {

    //MAIL THE USER NOTIFYING THAT THE ACCOUNT HAS NOT BEEN APPROVED

    $sql = "UPDATE users SET admin_approved = 'Disapproved' WHERE email = '$email'";

    $result = mysqli_query($connect, $sql);
    if ($result) {
        echo "User has not been approved";
    } else {
        echo "Error: " . $sql . "" . mysqli_error($connect);
    }
}

mysqli_close($connect);  // close connection

echo '<script> window.setTimeout("window.close()", 2500); </script>';
?>
