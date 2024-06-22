<?php
//DB Connection
require_once 'conn.php';

// ERRORS
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
$data1 = [
':user_id' => $_POST['user_id'],
];


if($_POST){
    $newValue = $_POST["newValue"];
//    $data = [
//        ':user_id' => $_POST['user_id'],
//    ];

//$query = "
// UPDATE tbl_users
// SET admin_approved = :newValue
// WHERE tbl_users.user_id = :user_id
// ";
//$statement = $connect->prepare($query);
//$statement->execute($data);

    $query1 = "
 UPDATE tbl_users
 SET admin_approved = $newValue
 WHERE tbl_users.user_id = :user_id
 ";
    $statement = $connect->prepare($query1);
    $statement->execute($data1);
    //$statement->close();
    //echo json_encode($_POST);
}

foreach ($_POST as $key => $post_data) {
    echo "You posted:" . $key . " = " . $post_data . "<br>";
}
