<?php
//DB Connection
include 'conn.php';

//var_dump($_POST);

if ($_POST['action'] == 'edit') {
    $data1 = [
        ':first_name' => $_POST['first_name'],
        ':last_name' => $_POST['last_name'],
        ':email' => $_POST['email'],
//        ':admin_approved' => $_POST['admin_approved'],
        ':user_id' => $_POST['user_id'],
    ];

    $query1 = "
 UPDATE tbl_users
 SET first_name = :first_name,
 last_name = :last_name,
 email = :email,
 admin_approved = :admin_approved
 WHERE tbl_users.user_id = :user_id
 ";
    $statement = $connect->prepare($query1);
    $statement->execute($data1);
    //$statement->close();
    //echo json_encode($_POST);
}

if ($_POST['action'] == 'delete') {
    $data2 = [
        ':user_id' => $_POST['user_id'],
    ];

    $query2 =
        "
DELETE FROM tbl_users
WHERE user_id = :user_id
";

    $statement = $connect->prepare($query2);
    $statement->execute($data2);
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
