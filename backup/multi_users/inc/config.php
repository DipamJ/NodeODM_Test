<?php
    define("HOST", "localhost");
    define("DB_USER", "hub_admin");
    define("DB_PASS", "UasHtp_Rocks^^7");
    define("DB_NAME", "users");

    $conn = mysqli_connect(HOST, DB_USER, DB_PASS, DB_NAME);

    if (!$conn) {
        die(mysqli_error());
    }

    function getUserAccessRoleByID($user_id)
    {
        global $conn;

        //$query = "select user_role from tbl_user_role where  id = ".$id;
        $query = "select role_name from tbl_roles where role_id = ".$user_id;

        $rs = mysqli_query($conn, $query);
        $row = mysqli_fetch_assoc($rs);

        return $row['role_name'];
    }
