<?php
require_once("CommonFunctions.php");

// ERRORS
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Get User ID
if (isset($_POST['userID'])) {
    $userID = $_POST['userID'];
//_log("userID: " . $userID);
//////////////////////////////////////////////////////////////////////////////////////////
}


// Update User Approval
if (isset($_POST['dataString'])) {
    $selectOption = $_POST['dataString'];
//_log("approved_status: " . $selectOption);
}

//    $user_row = $_POST['user_id'];
if (isset($selectOption)) {
    if ($selectOption == 'Approved') {
        $stmt_approved = "UPDATE users SET admin_approved = 'Approved' WHERE user_id = '" . $userID . "'";
        $result_approved = mysqli_query($connect, $stmt_approved);
    } elseif ($selectOption == 'Disapproved') {
        $stmt_disaproved = "UPDATE users SET admin_approved = 'Disapproved' WHERE user_id = '" . $userID . "'";
        //_log("disapproved_status: " . $stmt_disaproved);
        $result_disaproved = mysqli_query($connect, $stmt_disaproved);
    }
}
//////////////////////////////////////////////////////////////////////////////////////////


// Update User Roles
//$roles_assigned[] = $_POST['roles_assigned'];
if (isset($_POST['roles_assigned'])) {
    $roles_assigned = $_POST['roles_assigned'];
//print_r($roles_assigned);
//_log("insert: " . $roles_assigned);
}


//_log("userID: " . $userID);
//$userRoles = implode(',', $roles_assigned);
//_log("userRoles: " . $userRoles);

//$_POST['roles_checkbox'] = $_POST['roles_assigned'];
////_log("roles_assigned: " . $roles_assigned);
//$insert = "INSERT INTO users_roles(user_id, role_id) VALUES " . implode(',', $roles_assigned);
//$insert = "INSERT INTO users_roles(user_id, role_id) VALUES ($userID, $userRoles)";
////$_POST['roles_checkbox'] = implode(',', $roles_assigned);
//_log("insert: " . $insert);
//
//
//// First check if the request includes a user id
$user_id = !empty($_POST['userID']) ? (int)$_POST['userID'] : null;
if ($user_id) {
    // Second, lets delete all existing roles
    $delete_query = "DELETE FROM users_roles WHERE user_id = '$user_id'";
    mysqli_query($connect, $delete_query);
    //_log("delete_query: " . $delete_query);

    // Now we check if the request includes any selected role
    //if (!empty($_POST['roles_checkbox'])) {
    if (!empty($_POST['roles_assigned'])) {

        // This array will hold MySQL insert values
        $insert_values = [];

        // Loop through request values, sanitizing and validating before add to our query
        foreach ($_POST['roles_assigned'] as $role_id) {
            if ($role_id = (int)$role_id) {
                $insert_values[] = "('{$user_id}', '{$role_id}')";
            }
        }

        // Double check we have insert values before running query
        if (!empty($insert_values)) {
            $insert = "INSERT INTO users_roles(user_id, role_id) VALUES " . implode(', ', $insert_values);
            mysqli_query($connect, $insert);
            //_log("insert: " . $insert);
        }
    }

    //echo '<meta http-equiv="refresh" content="0">';
    //$connect->close();
}
//////////////////////////////////////////////////////////////////////////////////////////


// To edit User
// Use ISSET to verify if this actions needs to be done
//if (isset($input["action"])) {

$input = filter_input_array(INPUT_POST);
if (isset($input)) {
//        _log("editing user");
//    }
//print_r($input);
    if (isset($input["first_name"])) {
        $first_name = mysqli_real_escape_string($connect, $input["first_name"]);
    }

    if (isset($input["last_name"])) {
        $last_name = mysqli_real_escape_string($connect, $input["last_name"]);
    }

    // If action exists
    if (isset($input["action"])) {
        // If action is edit
        if ($input["action"] === 'edit') {
            // update user info
            $query = "
     UPDATE users
     SET first_name = '" . $first_name . "',
     last_name = '" . $last_name . "',
     email = '" . $input["email"] . "'
     WHERE user_id = '" . $input["user_id"] . "'
     ";
            //_log("query: " . $query);
            mysqli_query($connect, $query);
        }

        // If action is delete
        if ($input["action"] === 'delete') {
            $query = "
     DELETE FROM users
     WHERE user_id = '" . $input["user_id"] . "'
     ";
            //_log("query2: " . $query);
            if (mysqli_query($connect, $query)) {
                //echo '<meta http-equiv="refresh" content="0">';
            }
        }
    }
    //$connect->close();
}
//////////////////////////////////////////////////////////////////////////////////////////

$connect->close();
//echo '<meta http-equiv="refresh" content="0">';
//echo json_encode($input);
?>