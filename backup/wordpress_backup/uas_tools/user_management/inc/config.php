<?php
    define("HOST", "localhost");
    define("DB_USER", "hub_admin");
    define("DB_PASS", "UasHtp_Rocks^^7");
    define("DB_NAME", "uas_projects");

    $conn = mysqli_connect(HOST, DB_USER, DB_PASS, DB_NAME);

    if (!$conn) {
        die(mysqli_error($conn));
    }

    function getUserAccessRoleByID($user_id)
    {
        global $conn;

        //$query = "select user_role from tbl_user_role where  id = ".$id;
        //$query = "select role_name from tbl_roles where role_id = ".$user_id;
        $query = "select role_id from users_roles where user_id = '.$user_id' group by user_id";

        $rs = mysqli_query($conn, $query);
        $row = mysqli_fetch_assoc($rs);

        return $row['role_id'];
    }
