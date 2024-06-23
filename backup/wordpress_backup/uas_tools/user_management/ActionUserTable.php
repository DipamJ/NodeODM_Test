<?php

//ActionUserTable.php

require_once("SetDBConnection.php");

if ($_POST['action'] == 'edit') {
    $data = array(
        ':first_name' => $_POST['first_name'],
        ':last_name' => $_POST['last_name'],
        ':email' => $_POST['email'],
        ':admin_approved' => $_POST['admin_approved'],
        ':user_id' => $_POST['user_id']
    );

    $query = "
 UPDATE users 
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

if ($_POST['action'] == 'delete') {
    $query = "
 DELETE FROM users 
 WHERE user_id = '" . $_POST["user_id"] . "'
 ";
    $statement = $connect->prepare($query);
    $statement->execute();
    echo json_encode($_POST);
}
