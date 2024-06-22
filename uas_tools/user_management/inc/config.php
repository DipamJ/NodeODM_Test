<?php
    define("HOST", "localhost");
    define("DB_USER", "hub_admin");
    define("DB_PASS", "PurdueGdsl!@2w");
    define("DB_NAME", "uas_projects");

    $conn = mysqli_connect(HOST, DB_USER, DB_PASS, DB_NAME);
    //include('web/resources/database/SetDBConnection.php');
    // File containing System Variables
// define("LOCAL_PATH_ROOT", $_SERVER["DOCUMENT_ROOT"]);
// require LOCAL_PATH_ROOT . '/uas_tools/system_management/centralized_management.php';

    //require_once("../web/resources/database/SetDBConnection.php");
    //$conn = SetDBConnection();

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
