<?php
// ERRORS
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//DB Connection
include 'conn.php';

$column = ["role_id", "role_name"];

$query = "SELECT * FROM tbl_roles";

if (isset($_POST["search"]["value"])) {
    $query .=
        '
 WHERE role_name LIKE "%' .
        $_POST["search"]["value"] .
        '%"
 ';
}

if (isset($_POST["order"])) {
    $query .= 'ORDER BY ' . $column[$_POST['order']['0']['column']] . ' ' . $_POST['order']['0']['dir'] . ' ';
} else {
    $query .= 'ORDER BY role_id ASC ';
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

    $statement = $connect->prepare($query);

    $statement->execute();

    $number_filter_row = $statement->rowCount();

    $statement = $connect->prepare($query . $query1);

    $statement->execute();

    $result = $statement->fetchAll();

    $data = [];

    foreach ($result as $row) {
        $sub_array = [];
        $sub_array[] = $row['role_id'];
        $sub_array[] = $row['role_name'];
        $data[] = $sub_array;
    }

    function count_all_data($connect)
    {
        $query = "SELECT * FROM tbl_roles";
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




