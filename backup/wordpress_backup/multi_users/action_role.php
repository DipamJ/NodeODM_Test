<?php
$connect = mysqli_connect("localhost", "hub_admin", "UasHtp_Rocks^^7", "users");
if(!$connect){
    echo "Error: Unable to connect to MySQL." . PHP_EOL;
    echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
    exit;}

$input = filter_input_array(INPUT_POST);

$role_name = mysqli_real_escape_string($connect, $input["role_name"]);

if($input["action"] === 'edit')
{
 $query = "
 UPDATE tbl_roles
 SET role_name = '".$role_name."'
 WHERE role_id = '".$input["role_id"]."'
 ";
 mysqli_query($connect, $query);
}

if($input["action"] === 'delete')
{
 $query = "
 DELETE FROM tbl_roles
 WHERE role_id = '".$input["role_id"]."'
 ";
 mysqli_query($connect, $query);
}
echo json_encode($input);