<?php
require_once("SetDBConnection.php");
//require_once("../../resources/database/SetDBConnection.php");

$column = array("role_id", "role_name");

$query = "SELECT role_id, role_name FROM roles";

if (isset($_POST["search"]["value"])) {
    $query .= '
 WHERE role_name LIKE "%' . $_POST["search"]["value"] . '%"
 ';
}

if (isset($_POST["order"])) {
    $query .= 'ORDER BY ' . $column[$_POST['order']['0']['column']] . ' ' . $_POST['order']['0']['dir'] . ' ';
} else {
    //$query .= 'ORDER BY role_id DESC ';
    $query .= 'ORDER BY role_id ASC ';

}
$query1 = '';

if ($_POST["length"] != -1) {
    $query1 = 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
}

$statement = $connect->prepare($query);

$statement->execute();

$number_filter_row = $statement->rowCount();

$statement = $connect->prepare($query . $query1);

$statement->execute();

$result = $statement->fetchAll();

$data = array();

foreach ($result as $row) {
    $sub_array = array();
    $sub_array[] = $row['role_id'];
    $sub_array[] = $row['role_name'];
    $data[] = $sub_array;
}

function count_all_data($connect)
{
    $query = "SELECT role_id, role_name FROM roles";
    $statement = $connect->prepare($query);
    $statement->execute();
    return $statement->rowCount();
}

$output = array(
    'draw' => intval($_POST['draw']),
    'recordsTotal' => count_all_data($connect),
    'recordsFiltered' => $number_filter_row,
    'data' => $data
);

echo json_encode($output);

?>
