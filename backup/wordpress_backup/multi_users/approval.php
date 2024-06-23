<?php
// ERRORS
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

error_reporting(-1);
ini_set('display_errors', 'On');
set_error_handler("var_dump");

//// CONNECT TO DB
define("HOST", "localhost");
define("DB_USER", "hub_admin");
define("DB_PASS", "UasHtp_Rocks^^7");
define("DB_NAME", "users");

//require_once('add_user.php');

//ACCESS MYSQL DATABASE
  $conn = mysqli_connect(HOST, DB_USER, DB_PASS, DB_NAME);

  if (!$conn) {
      die(mysqli_error());
      exit;
  }

$hash = $_GET['h'] ?? '';
$email = $_GET['e'] ?? '';

if ($hash == hash('sha512', 'ACCEPT')) {

    //FIND THE USER AND SET user_approved = 1
    //$sql = mysqli_query($conn, "update tbl_users set admin_approved = true where email = '$email'");
    //$sql = mysqli_query($conn, "update tbl_users set admin_approved = true where email = 'lll@gmail.com'");
    $sql = "UPDATE tbl_users SET admin_approved = 1 WHERE email = '$email'";

    $result = mysqli_query($conn, $sql);
    if ($result) {
        echo "User has been approved";
    } else {
        echo "Error: " . $sql . "" . mysqli_error($conn);
    }
}
//elseif ($hash == hash('sha512', 'DECLINE')) {
//
//  //MAIL THE USER NOTIFYING THAT THE ACCOUNT HAS NOT BEEN APPROVED
//    echo "User has not been approved";
//}

if ($hash == hash('sha512', 'DECLINE')) {

    //MAIL THE USER NOTIFYING THAT THE ACCOUNT HAS NOT BEEN APPROVED

    $sql = "UPDATE tbl_users SET admin_approved = 0 WHERE email = '$email'";

    $result = mysqli_query($conn, $sql);
    if ($result) {
        echo "User has not been approved";
    } else {
        echo "Error: " . $sql . "" . mysqli_error($conn);
    }
}


mysqli_close($conn);  // close connection

echo '<script> window.setTimeout("window.close()", 2500); </script>';
