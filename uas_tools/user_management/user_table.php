<?php
require_once("CommonFunctions.php");

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
    <meta name=”viewport” content=”width=device-width, initial-scale=1″>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap.min.css" />

    <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <script src="https://markcell.github.io/jquery-tabledit/assets/js/tabledit.min.js"></script>

    <!--    MultiSelect-->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js"></script>
    <style>
        .project {
            margin: 0px 0px 0px 0px;
            padding: 25px 35px;
            border-radius: 15px;
            background: #f6f7f9;
        }
        #editable_table .tabledit-toolbar-column {
            border: none;
        }
    </style>
</head>

<body>
    <div class="container" style="padding-bottom: 1rem!important; padding-top: 1.6rem!important;">
        <!--    <h3 align="center">Modify User</h3>-->
        <!--    <br/>-->
        <div class="project">
           <div class="panel panel-default">
            <!--        <div class="panel-heading">Sample Data</div>-->
            <!--        <div class="panel-body">-->
            <div class="table-responsive">
                <table id="editable_table" class="table table-bordered table-striped">
                    <thead>
                        <tr style="background: #555555; color: #ffffff;">
                            <th>ID</th>
                            <th style="text-align:center !important; border: none;">First Name</th>
                            <th style="text-align:center !important; border: none;">Last Name</th>
                            <th style="text-align:center !important; border: none;">Email</th>
                            <th style="text-align:center !important; border: none;">Approval</th>
                            <th style="text-align:center !important; border: none;">Roles</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php // align=center
                    while ($row = mysqli_fetch_array($result)) {
                        echo "
            <tr align=center>
               <td " ?><?php echo ">" . $row['user_id'] . "</td>
               <td>" . $row['first_name'] . "</td>
               <td>" . $row['last_name'] . "</td>
               <td>" . $row['email'] . "</td>
               <!--User Approval-->
                <td>
                <form method='POST'>
                <input type='hidden' name='user_id' value=" . $row["user_id"] . ">
                    ";
                        $user_row = $row["user_id"];
                        $approved_sql = "SELECT admin_approved from users WHERE user_id = '" . $user_row . "'";
                        $approved_or_not = mysqli_query($connect, $approved_sql);
                        $row_approved = mysqli_fetch_assoc($approved_or_not);
                        if ($row_approved['admin_approved'] == 'Approved') {
                            echo "<select class='myselect' disabled name='selectbox''>
                            <option value='Approved' selected>Approved</option>
                            <option value='Disapproved'>Disapproved</option>
                         </select>";
                        } else if ($row_approved['admin_approved'] == 'Disapproved') {
                            echo "<select class='myselect' disabled name='selectbox''>
                            <option value='Approved'>Approved</option>
                            <option value='Disapproved' selected>Disapproved</option>
                         </select>";
                        }
                        echo "
                </form>
               </td>
               
               <!--User Roles-->
               <td>
               <form method='POST'>
               <input type='hidden' name='user_id' value=" . $row["user_id"] . ">
            "; ?>
                        <select disabled class='roles_checkbox' multiple='multiple' name="roles_checkbox[]"'><? //onchange=' this.form.submit()?>
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
            <!--        </div>-->
        </div>
    </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#editable_table').Tabledit({
                // When click on save; action_user_2.php gets called
                url: 'action_user_2.php', // where data will be sent
                // Data sent
                columns: {
                    identifier: [0, "user_id"],
                    editable: [
                        [1, 'first_name'],
                        [2, 'last_name'],
                        [3, 'email']
                    ]
                },
                // hide the column that has the identifier
                hideIdentifier: true,

                // activate focus on first input of a row when click in save button
                autoFocus: true,

                // activate save button when click on edit button
                saveButton: true,

                // deactivate restore button
                restoreButton: false,

                onSuccess: function(data, textStatus, jqXHR) {
                    // custom action buttons
                    if (data.action === 'delete') {
                        $('#' + data.id).remove();
                    }
                }
            });

            // // when click on edit, select box and dropdown select gets enabled
            // $(document).on("click", ".tabledit-edit-button", function () {
            //     //get closest tr and then find slectbox and disable same
            //     $(this).closest("tr").find("[name=selectbox]").removeAttr('disabled')
            //     //remove attribute from button as well
            //     $(this).closest("tr").find("[name*=roles_checkbox] , .dropdown-toggle ").removeAttr('disabled')
            //     //remove disable class from button
            //     $(this).closest("tr").find(".dropdown-toggle").removeClass('disabled')
            // })

            //<!--Multiselect JS-->
            $('.roles_checkbox').multiselect({
                includeSelectAllOption: true,
                nonSelectedText: '--Select Role--'
            });

        });

        // when click on save, user approval gets sent to update
        $(document).on("click", ".tabledit-save-button", function() {
            // For user ID
            var userID = $(this).closest("tr").find("[name=user_id]").val();
            //console.log(userID);

            // For user approval
            var checkedapproval = $(this).closest("tr").find(".myselect").val();
            //console.log(checkedapproval);

            // For user roles
            var checkedRoles = $(this).closest("tr").find(".roles_checkbox").val();
            //console.log(checkedRoles);

            $(function() {
                $.ajax({
                    type: 'POST',
                    url: 'action_user_2.php',
                    // dataString is variable being sent. checkedapproval is value sent
                    data: {
                        dataString: checkedapproval,
                        userID: userID,
                        roles_assigned: checkedRoles
                    }
                });
            });
        })

        // when click on edit, select box and dropdown select gets enabled
        $(document).on("click", ".tabledit-edit-button", function() {
            //get closest tr and then find slectbox and disable same
            $(this).closest("tr").find("[name=selectbox]").removeAttr('disabled')
            //remove attribute from button as well
            $(this).closest("tr").find("[name*=roles_checkbox] , .dropdown-toggle ").removeAttr('disabled')
            //remove disable class from button
            $(this).closest("tr").find(".dropdown-toggle").removeClass('disabled')
        })

        // When click on save, page reloads
        $(document).on("click", ".tabledit-save-button", function() {
            // Reload the table
            //$('#editable_table').DataTable();
            // Reload the Page
            // refresh();
            //window.location.reload(false);
            //location.reload(true);

            // Reload page after 0.5 secons
            $(document).ready(function() {
                setTimeout(function() {
                    //alert('Reloading Table');
                    location.reload(true);
                }, 500);
            });
        })

        // When click on confirm, page reloads
        $(document).on("click", ".tabledit-confirm-button", function() {
            // // Reload the table
            // $('#editable_table').DataTable();
            // // Reload the Page
            // window.location.reload(false);


            // $('#editable_table').DataTable().ajax.reload();
            // alert('Confirm button has been pressed');


            // Reload page after 0.5 secons
            $(document).ready(function() {
                setTimeout(function() {
                    //alert('Reloading Table');
                    location.reload(true);
                }, 500);
            });
        })

        // When 'enter' key is pressed, reload page
        $('#editable_table').keypress(function(event) {
            if (event.keyCode === 10 || event.keyCode === 13) {
                //event.preventDefault();
                setTimeout(function() {
                    //alert('Reloading Table');
                    location.reload(true);
                }, 500);
            }
        });

        //$('#editable_table').DataTable();

    </script>
</body>

</html>
