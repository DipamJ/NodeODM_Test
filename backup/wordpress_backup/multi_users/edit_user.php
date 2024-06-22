<?php
//DB Connection
$con = new mysqli("localhost", "hub_admin", "UasHtp_Rocks^^7", "users");

/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

$user_id = _REQUEST['user_id'] ?? '';
$query = "SELECT * from tbl_users where user_id='".$user_id."'";
$result = mysqli_query($con, $query) or die ( mysqli_error());
$row = mysqli_fetch_assoc($result);
?>