<?php
$connect = mysqli_connect("localhost", "hub_admin", "UasHtp_Rocks^^7", "users");
if(!$connect){
    echo "Error: Unable to connect to MySQL." . PHP_EOL;
    echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
    exit;}

$input = filter_input_array(INPUT_POST);

$first_name = mysqli_real_escape_string($connect, $input["first_name"]);
$last_name = mysqli_real_escape_string($connect, $input["last_name"]);

if($input["action"] === 'edit')
{
    $query = "
 UPDATE tbl_users 
 SET first_name = '".$first_name."', 
 last_name = '".$last_name."',
 email = '".$input["email"]."' 
 WHERE user_id = '".$input["user_id"]."'
 ";
    mysqli_query($connect, $query);
}

if($input["action"] === 'delete')
{
    $query = "
 DELETE FROM tbl_users
 WHERE user_id = '".$input["user_id"]."'
 ";
    mysqli_query($connect, $query);
}

echo json_encode($input);