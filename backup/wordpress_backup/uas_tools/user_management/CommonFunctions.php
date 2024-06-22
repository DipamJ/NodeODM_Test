<?php
// ERRORS
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//error_reporting(-1);
//ini_set('display_errors', 'On');
//set_error_handler("var_dump");

// DB Connection
$connect = mysqli_connect("localhost", "hub_admin", "UasHtp_Rocks^^7", "uas_projects");
if (!$connect) {
    echo "Error: Unable to connect to MySQL." . PHP_EOL;
    echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
    exit;
}

// Log Document
function _log($str)
{
    // log to the output
    $log_str = date('d.m.Y') . ": {$str}\r\n";
    echo $log_str;

    // log to file
    if (($fp = fopen('upload_log.txt', 'a+')) !== false) {
        fputs($fp, $log_str);
        fclose($fp);
    }
}

// NEEDS TO BE CHANGED TO WORK WITH tbl_user_roles.role_id
function getUserAccessRoleByID($user_id)
{
    global $conn;

    //$query = "select user_role from tbl_user_role where  id = ".$id;
    //$query = "select role_name from tbl_roles where role_id = ".$user_id;
    $query = "select role_id from tbl_user_roles where user_id = '.$user_id' group by user_id";
    //_log('select role_id: '.$query);

    $rs = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($rs);

    //return $row['role_name'];
    return $row['role_id'];
}

?>