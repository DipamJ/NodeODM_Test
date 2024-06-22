<?php

//ActionRoleTable.php

require_once("SetDBConnection.php");

if($_POST['action'] == 'edit')
{
    $data = array(
        ':role_name'  => $_POST['role_name'],
        ':role_id'    => $_POST['role_id']
    );

    $query = "
 UPDATE roles 
 SET role_name = :role_name
 WHERE role_id = :role_id
 ";
    $statement = $connect->prepare($query);
    $statement->execute($data);
    echo json_encode($_POST);
}

if($_POST['action'] == 'delete')
{
    $query = "
 DELETE FROM roles 
 WHERE role_id = '".$_POST["role_id"]."'
 ";
    $statement = $connect->prepare($query);
    $statement->execute();
    echo json_encode($_POST);
}

