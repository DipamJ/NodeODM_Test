<?php
    require_once("/home/ubuntu/web/resources/database/SetDBConnection.php");

    $conn = SetDBConnection();
    // File containing System Variables
// define("LOCAL_PATH_ROOT", $_SERVER["DOCUMENT_ROOT"]);
// require LOCAL_PATH_ROOT . '/uas_tools/system_management/centralized_management.php';

    if (!$conn) {
        die(mysqli_error($conn));
    }

    function getUserAccessRoleByID($user_id)
    {
        global $conn;

        $query = "select role_id from users_roles where user_id = '.$user_id' group by user_id";

        $rs = mysqli_query($conn, $query);
        $row = mysqli_fetch_assoc($rs);

        return $row['role_id'];
    }
