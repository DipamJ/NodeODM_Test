<?php
//DB Connection
$con = new mysqli("localhost", "hub_admin", "UasHtp_Rocks^^7", "users");

/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

$user_id = _REQUEST['user_id'] ?? '';