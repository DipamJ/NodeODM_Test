<?php

//actionNewUserTable.php

// ERRORS
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    $connect = new PDO("mysql:host=localhost; dbname=users", "hub_admin", "UasHtp_Rocks^^7");
    $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}

if($_POST['action'] == 'edit')
{
    $data = array(
        ':first_name'  => $_POST['first_name'],
        ':last_name'  => $_POST['last_name'],
        ':email'  => $_POST['email'],
        ':admin_approved'   => $_POST['admin_approved'],
        ':user_id'    => $_POST['user_id']
    );

    $query = "
 UPDATE new_tbl_users 
 SET first_name = :first_name, 
 last_name = :last_name, 
 email = :email,
 admin_approved = :admin_approved 
 WHERE user_id = :user_id
 ";
    $statement = $connect->prepare($query);
    $statement->execute($data);
    echo json_encode($_POST);
}

if($_POST['action'] == 'delete')
{
    $query = "
 DELETE FROM new_tbl_users 
 WHERE user_id = '".$_POST["user_id"]."'
 ";
    $statement = $connect->prepare($query);
    $statement->execute();
    echo json_encode($_POST);
}
