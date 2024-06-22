<?php
//DB Connection
include 'conn.php';

if ($_POST['action'] == 'edit') {
    $data3 = [
        ':role_name' => $_POST['role_name'],
        ':role_id' => $_POST['role_id'],
    ];

    $query1 = "
    UPDATE tbl_roles
    SET role_name = :role_name
    WHERE tbl_roles.role_id = :role_id
    ";
    $statement = $connect->prepare($query1);
    $statement->execute($data3);
    //$statement->close();
    //echo json_encode($_POST);
}

if ($_POST['action'] == 'delete') {
    $data4 = [
        ':role_id' => $_POST['role_id'],
    ];

    $query2 = "
    DELETE FROM tbl_roles
    WHERE role_id = :role_id
    ";

    $statement = $connect->prepare($query2);
    $statement->execute($data4);
    //$statement->close();
    //echo json_encode($_POST);
}

if ($_POST['action'] == 'insert') {
    $data5 = [
        ':role_name' => $_POST['role_name'],
        ':role_id' => $_POST['role_id'],
    ];
//INSERT INTO `tbl_roles` (`role_id`, `role_name`) VALUES (5, 'viewerar');

    $query3 = "
    INSERT INTO tbl_roles (role_id, role_name)
    VALUES (role_id, 'role_name')
    ";

    $statement = $connect->prepare($query3);
    $statement->execute($data5);
    //$statement->close();
    //echo json_encode($_POST);
}

//echo json_encode($_POST);
?>

<?php
foreach ($_POST as $key=>$post_data) {
    echo "You posted:" . $key . " = " . $post_data . "<br>";
}
?>
