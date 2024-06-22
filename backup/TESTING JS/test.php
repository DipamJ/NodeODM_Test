<?php
require_once("CommonFunctions.php");
//require_once("SetDBConnection.php");
// Check only Admins can access to it
session_start();
$_VERIFY = $_SESSION['email'];

// SELECT USING EMAIL TO GET THE role_id
$sql = "select role_id from users_roles, users where  users_roles.user_id = users.user_id and email = '" . $_VERIFY . "' ORDER BY role_id ASC";
$result = mysqli_query($connect, $sql);
$row = mysqli_fetch_assoc($result);
if ($row["role_id"] != '1') {
    header('location:index.php?lmsg=true');
    exit;
}

// Display user information on table
$query = "SELECT user_id, first_name, last_name, email FROM users ORDER BY user_id ASC";
$result = mysqli_query($connect, $query);

//$records_roles = mysqli_query($connect, "SELECT role_name FROM roles");
$records_roles = mysqli_query($connect, "SELECT * FROM roles");
$roles = array();
$count = 0;

while ($course_roles = mysqli_fetch_assoc($records_roles)) {
    $roles [] =
        $course_roles['role_name'];
    $count++;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Modify User</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"/>
    <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css"/>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <script src="https://markcell.github.io/jquery-tabledit/assets/js/tabledit.min.js"></script>

    <!--    MultiSelect-->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css"
          rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js"></script>


</head>

<body>
<div class="container">
    <h3 align="center">Modify User</h3>
    <br/>
    <div class="panel panel-default">
        <!--        <div class="panel-heading">Sample Data</div>-->
        <div class="panel-body">
            <div class="table-responsive">
                <table id="editable_table" class="table table-bordered table-striped">
                    <thead>
                    <!--            <style>-->
                    <!--                th {-->
                    <!--                    text-align: center;-->
                    <!--                }-->
                    <!--            </style>-->
                    <tr>
                        <th>ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Approval</th>
                        <th>Roles</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php // align=center
                    while ($row = mysqli_fetch_array($result)) {
                        echo "
            <tr>
               <td " ?><?php echo ">" . $row['user_id'] . "</td>
               <td>" . $row['first_name'] . "</td>
               <td>" . $row['last_name'] . "</td>
               <td>" . $row['email'] . "</td>
               <!--User Approval-->
                <td>
                <form method='POST'>
                <!--                <form id='myForm' method='POST'>-->
                <input type='hidden' name='user_id' value=" . $row["user_id"] . ">
                    ";
                        $user_row = $row["user_id"];
                        $approved_sql = "SELECT admin_approved from users WHERE user_id = '" . $user_row . "'";
                        $approved_or_not = mysqli_query($connect, $approved_sql);
                        $row_approved = mysqli_fetch_assoc($approved_or_not);
                        if ($row_approved['admin_approved'] == 'Approved') {
                            echo "<select name='selectbox' onchange='this.form.submit()'>
                            <option value='Approved' selected>Approved</option>
                            <option value='Disapproved'>Disapproved</option>
                         </select>";
                        } else if ($row_approved['admin_approved'] == 'Disapproved') {
                            echo "<select name='selectbox' onchange='this.form.submit()'>
                            <option value='Approved'>Approved</option>
                            <option value='Disapproved' selected>Disapproved</option>
                         </select>";
                        }
                        echo "
                </form>
               </td>
               <!--User Roles-->
               <td>
               <!--Form needs to be fixed. It needs to sent row[user_id]-->
               <!--<form action='change_query.php' method='post'>-->
               <form method='post'>
               <input type='hidden' name='user_id' value=" . $row["user_id"] . ">
            "; ?>

                        <!--<select id='roles_checkbox' multiple='multiple' name="roles_checkbox">-->
                        <!--                        <select class='roles_checkbox' multiple='multiple' name="roles_checkbox">-->
                        <select class='roles_checkbox' multiple='multiple' name="roles_checkbox[]"
                                onchange='this.form.submit()'>
                            <?php $a = 1; ?>
                            <?php foreach ($roles as $data):
                                $new_sql = "SELECT role_id from users_roles, users WHERE users_roles.user_id = '" . $row["user_id"] . "' AND users_roles.role_id = '" . $a . "' GROUP BY users_roles.user_id";
                                $checked_or_not = mysqli_query($connect, $new_sql);
                                ?>
                                <option value="<?php echo $a ?>" <?php if ($checked_or_not->num_rows != 0) echo "selected=\"selected\""; ?>><?php echo $data ?></option>
                                <?php $a++; ?>
                            <?php endforeach; ?>
                        </select>
                        <?php
                        echo "
              </form>
              </td>
            </tr> ";
                    } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!--Multiselect JS-->
<script>
    $(document).ready(function () {
        $('.roles_checkbox').multiselect({
            includeSelectAllOption: true,
            nonSelectedText: '--Select Role--'
        });
    });
</script>

<!--Table JS-->
<script>
    $(document).ready(function () {
        $('#editable_table').Tabledit({
            // when click on save; action_user_2.php gets called
            url: 'action_user_2.php',
            columns: {
                identifier: [0, "user_id"],
                editable: [[1, 'first_name'],
                    [2, 'last_name'],
                    [3, 'email']]
            },
            // hide the column that has the identifier
            hideIdentifier: true,

            // activate focus on first input of a row when click in save button
            autoFocus: false,

            // activate save button when click on edit button
            saveButton: true,

            restoreButton: false,
            onSuccess: function (data, textStatus, jqXHR) {
                // deal with success there

                // All actions inside here occur when save button is pressed.
                // here shoud occur the update for user approval
                // here shoud occur the update for role approval
                var htmlString = "<?php echo 'User information has been updated'; ?>";
                alert(htmlString);

                //var my_javascript_variable = <?php //echo json_encode($_POST['my_post'] ?? null) ?>//;
                //
                ////$selectOption = $_POST['selectbox'];
                <?php //$temp = 'hello';?>
                //console.log('<?php //echo $temp; ?>//');

                // custom action buttons
                if (data.action == 'delete') {
                    $('#' + data.id).remove();
                }

                var some_variable = <?php echo json_encode($_POST['selectbox'] ?? null) ?>;


                //alert(hey);

                // php_funtions.php will be called
                $.ajax({
                    url: 'php_funtions.php',
                    data: {action: 'updateUserApproval', name: some_variable},
                    type: 'post',
                    success: function (output) {
                        alert(output);
                    }
                });

                // if (data.html == 'Save') {
                //     //$('#' + data.id).remove();
                //     console.log('test');
                // }
            }
        });

    });
    $('#editable_table').DataTable();
</script>
</body>
</html>

<!--Update User Approval listenning to select box-->
<?php
//if (isset($_POST['selectbox'])) {
//    updateUserApproval();
//}
?>


<!--Update User Role listenning to select box-->
<?php
// First check if the request includes a user id
$user_id = !empty($_POST['user_id']) ? (int)$_POST['user_id'] : null;
if ($user_id) {

    // Second, lets delete all existing roles
    $delete_query = "DELETE FROM users_roles WHERE user_id = '$user_id'";
    mysqli_query($connect, $delete_query);

    // Now we check if the request includes any selected role
    if (!empty($_POST['roles_checkbox'])) {

        // This array will hold MySQL insert values
        $insert_values = [];

        // Loop through request values, sanitizing and validating before add to our query
        foreach ($_POST['roles_checkbox'] as $role_id) {
            if ($role_id = (int)$role_id) {
                $insert_values[] = "('{$user_id}', '{$role_id}')";
            }
        }

        // Double check we have insert values before running query
        if (!empty($insert_values)) {
            $insert = "INSERT INTO users_roles(user_id, role_id) VALUES " . implode(', ', $insert_values);
            mysqli_query($connect, $insert);
        }
    }

    echo '<meta http-equiv="refresh" content="0">';
    $connect->close();
}


//if (isset($_POST['roles_checkbox'])) { // Use select name ////CHANGE TO LISTEN TO SAVE BUTTON FROM . SAME TO CHANGE APPROVE STATUS
//    $user_id = $_POST['user_id']; // Use input name to get user id being modify = USER 1
//
//    // Start by deleting all the roles
//    for ($i = 0; $i < $count; $i++) {
//        $a = $i + 1;
//        //$query2 = "DELETE FROM users_roles WHERE user_id = '$user_id' AND role_id = '$a'";
//        //_log('$query2: ' . $query2);
//        //$in_ch2 = mysqli_query($connect, $query2);
//        $delete_query = "DELETE FROM users_roles WHERE user_id = '$user_id'";
//        $in_ch2 = mysqli_query($connect, $delete_query);
//    }
//
//    foreach ($_POST['roles_checkbox'] as $selectedOption) {
//        //echo $selectedOption . "\n";
//        // Insert selected roles
//        $insert_query = "INSERT INTO users_roles(user_id, role_id) VALUES ('$user_id', '" . $selectedOption . "')";
//        //_log('$query: ' . $query);
//        $in_ch = mysqli_query($connect, $insert_query);
//    }
//
////    if ($in_ch == 1 || $in_ch2 == 1) {
////        echo '<script>alert("Role Updated Successfully")</script>';
////    } else {
////        echo '<script>alert("Failed to Update Role")</script>';
////    }
//    echo "<meta http-equiv='refresh' content='0'>";
//    $connect->close();
//}

// Create a php function to read values from select box and update the database.
// Check if this function can be called from JS
?>
