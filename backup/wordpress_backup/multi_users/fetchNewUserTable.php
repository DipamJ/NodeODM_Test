<?php

//fetchNewUserTable.php

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

$column = array("user_id", "first_name", "last_name", "email", "admin_approved", "role_id");

$query = "SELECT user_id, first_name, last_name, email, admin_approved FROM new_tbl_users";

if(isset($_POST["search"]["value"]))
{
    $query .= '
 WHERE first_name LIKE "%'.$_POST["search"]["value"].'%" 
 OR last_name LIKE "%'.$_POST["search"]["value"].'%" 
 OR email LIKE "%'.$_POST["search"]["value"].'%"
 OR admin_approved LIKE "%'.$_POST["search"]["value"].'%"
   
 ';//OR role_id LIKE "%'.$_POST["search"]["value"].'%"
}

if(isset($_POST["order"]))
{
    $query .= 'ORDER BY '.$column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
}
else
{
    $query .= 'ORDER BY user_id DESC ';
}
$query1 = '';

if($_POST["length"] != -1)
{
    $query1 = 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
}

$statement = $connect->prepare($query);

$statement->execute();

$number_filter_row = $statement->rowCount();

$statement = $connect->prepare($query . $query1);

$statement->execute();

$result = $statement->fetchAll();

$data = array();

foreach($result as $row)
{
    $sub_array = array();
    $sub_array[] = $row['user_id'];
    $sub_array[] = $row['first_name'];
    $sub_array[] = $row['last_name'];
    $sub_array[] = $row['email'];
    $sub_array[] = $row['admin_approved'];
//    $sub_array[] = $row['role_id'];
    $data[] = $sub_array;
}

function count_all_data($connect)
{
    $query = "SELECT user_id, first_name, last_name, email, admin_approved FROM new_tbl_users";
    $statement = $connect->prepare($query);
    $statement->execute();
    return $statement->rowCount();
}

$output = array(
    'draw'   => intval($_POST['draw']),
    'recordsTotal' => count_all_data($connect),
    'recordsFiltered' => $number_filter_row,
    'data'   => $data
);

echo json_encode($output);