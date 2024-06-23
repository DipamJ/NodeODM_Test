<?php
// ERRORS
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//DB Connection
include 'conn.php';

//$column = ["user_id", "first_name", "last_name", "email", "role_id"];
$column = ["user_id", "first_name", "last_name", "email"];//, "admin_approved"];

//$column2 = ["role_name"];



//$query = "SELECT * FROM tbl_user_roles, tbl_users INNER JOIN tbl_users ON tbl_user_roles.user_id =  tbl_users.user_id";
$query = "SELECT user_id, first_name, last_name, email  FROM tbl_users";

$query2 = "SELECT * FROM tbl_roles";


if (isset($_POST["search"]["value"])) {
    $query .=
        '
 WHERE first_name LIKE "%' .
        $_POST["search"]["value"] .
        '%"
 OR last_name LIKE "%' .
        $_POST["search"]["value"] .
        '%"
 OR email LIKE "%' .
        $_POST["search"]["value"] .
        '%"

 ';
}
//OR admin_approved LIKE "%' .
//        $_POST["search"]["value"] .
//        '%"

if (isset($_POST["order"])) {
    $query .= 'ORDER BY ' . $column[$_POST['order']['0']['column']] . ' ' . $_POST['order']['0']['dir'] . ' ';
} else {
    $query .= 'ORDER BY user_id ASC ';
}

$query1 = '';

if (isset($_POST['length']) || isset($_POST['start'])) {
    // code...
    $length = $_POST['length'];
    $start = $_POST['start'];
    //$length =
    //}

    if ($length != -1) {
        $query1 = 'LIMIT ' . $start . ', ' . $length;
    }
    //}
    /*
    if ($_POST["length"] != -1) {
        $query1 = 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
    }*/

    $statement = $connect->prepare($query);

    $statement->execute();

    //$statement = $connect->prepare($query2);

    //$statement->execute();

    $number_filter_row = $statement->rowCount();

    $statement = $connect->prepare($query . $query1);

    $statement->execute();

    $result = $statement->fetchAll();

    $data = [];

    foreach ($result as $row) {
        $sub_array = [];
        $sub_array[] = $row['user_id'];
        $sub_array[] = $row['first_name'];
        $sub_array[] = $row['last_name'];
        $sub_array[] = $row['email'];
//        if ($row['admin_approved'] == 1){
//            $sub_array[] = "Approved";
//        }
//        elseif ($row['admin_approved'] != 1) {
//            $sub_array[] = "Denied";
//        }

        //$sub_array[] = $row['user_role_id'];

//        if ($row['user_role_id'] == 1){
//            $sub_array[] = "Admin";
//        }
//        elseif ($row['user_role_id'] == 2) {
//            $sub_array[] = "Subscriber";
//        }
//        elseif ($row['user_role_id'] == 3) {
//            $sub_array[] = "Viewer";
//        }
//        elseif ($row['user_role_id'] == 4) {
//            $sub_array[] = "Data Uploader";
//        }



        //$sub_array[] = $row['role_id'];
        $data[] = $sub_array;
    }

    function count_all_data($connect)
    {
        $query = "SELECT * FROM tbl_users";
        $statement = $connect->prepare($query);
        $statement->execute();
        return $statement->rowCount();
    }
    $output = '';

    $output = [
        'draw' => intval($_POST['draw']),
        'recordsTotal' => count_all_data($connect),
        'recordsFiltered' => $number_filter_row,
        'data' => $data,
    ];

    echo json_encode($output);
}
