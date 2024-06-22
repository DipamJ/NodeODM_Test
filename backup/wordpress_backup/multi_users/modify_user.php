<?php
//DB Connection
$con = new mysqli("localhost", "hub_admin", "UasHtp_Rocks^^7", "users");

/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

session_start();

$_VERIFY = $_SESSION['user_role_id'];

if ($_VERIFY != '1') {
    header('location:index.php?lmsg=true');
    exit;
}

$records = mysqli_query($con, "SELECT user_id, first_name, last_name, email, admin_approved  FROM tbl_users");
//$records_roles = mysqli_query($con, "SELECT role_name  FROM tbl_roles");
//$roles = array();
//$i = 0;
//$count = 0;
//while ($course_roles = mysqli_fetch_assoc($records_roles)){
//    $roles[$i] = $course_roles;
//    //var_dump($roles);
//    $i++;
//    $count++;
//}
//var_dump($roles[0]);

/*
require_once('inc/config.php');
require_once('layouts/header.php');
require_once('layouts/left_sidebar.php');	*/
?>

<!DOCTYPE html>
<html lang="en">

<html>
<head>
    <title>Modify Users</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css" />
</head>
<body>
<div class="container">
    <h1 align="center"></h1>
    <br />
    <h3>Modifying Users</h3>
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="table-responsive">

                <table id="emp_list" class="table table-bordered">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <!--                        <th>Approved</th>-->
                        <!--                        <th>Roles</th>-->
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>

                <!--                <select> <option value="Approved">Approved</option> <option value="Dissaproved">Dissaproved</option> </select>-->
                <!--                <button type="submit" name="submit"> Submit</button>-->


                <!--                <tr>-->
                <!--                    <td colspan="2">Select Roles:</td>-->
                <!--                </tr>-->
                <!--                <tr>-->
                <!--                    <td>Admin</td>-->
                <!--                    <td><input type="checkbox" name="techno[]" value="1"></td>-->
                <!--                </tr>-->
                <!--                <tr>-->
                <!--                    <td>Subscriber</td>-->
                <!--                    <td><input type="checkbox" name="techno[]" value="2"></td>-->
                <!--                </tr>-->
                <!--                <tr>-->
                <!--                    <td>Viewer</td>-->
                <!--                    <td><input type="checkbox" name="techno[]" value="3"></td>-->
                <!--                </tr>-->
                <!--                <tr>-->
                <!--                    <td>Data uploader</td>-->
                <!--                    <td><input type="checkbox" name="techno[]" value="4"></td>-->
                <!--                </tr>-->
                <!--                <tr>-->
                <!--                    <td colspan="2" align="center"><input type="submit" value="submit" name="sub"></td>-->
                <!--                </tr>-->


                <table id="UserTable" class="table table-bordered">
                    <tr bgcolor="#2ECCFA">
                        <th>ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Approval</th>
                        <!--                        <th>Roles</th>-->
                    </tr>

                    <?php
                    while ($course = mysqli_fetch_assoc($records)){
//                        $id = $course ['user_id'];?>

                        <tr>
                            <td><?php echo $course['user_id']?></td>
                            <td><?php echo $course['first_name']?></td>
                            <td><?php echo $course['last_name']?></td>
                            <td><?php echo $course['email']?></td>
                            <td>
                                <form method="post">
                                    <input type="hidden" name="user_id" value="<?php echo $course['user_id'] ?>">
                                    <!--                                <form action="select-form.php" method="post">-->

                                    <!--                                <select id="options">-->
                                    <select name="admin_approved">
                                        <option value="1">Approved</option>
                                        <option value="0">Dissaproved</option>
                                    </select>
                                    <button type="submit" name="selectSubmit" >Submit</button>
                                </form>
                            </td>

                                                        <td>
                                                            <form action="checkbox-form.php" method="post">

                                                                Select roles for the user<br />
                                                                <input type="checkbox" name="formDoor[]" value="1" />Admin<br />
                                                                <input type="checkbox" name="formDoor[]" value="2" />Subscriber<br />
                                                                <input type="checkbox" name="formDoor[]" value="3" />Viewer<br />
                                                                <input type="checkbox" name="formDoor[]" value="4" />Data Uploader<br />
                                                                <input type="submit" name="formSubmit" value="Submit" />
                                                            </form>
                                                        </td>

                        </tr>
                        <?php
                    }?>
                </table>
            </div>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
<script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/js/bootstrap.min.js"></script>
<script src="https://markcell.github.io/jquery-tabledit/assets/js/tabledit.min.js"></script>
<script type="text/javascript" language="javascript">
    $(document).ready(function () {
        var dataTable = $("#emp_list").DataTable({
            processing: true,
            serverSide: true,
            order: [],
            ajax: {
                url: "fetch.php",
                type: "POST",
            },
        });

        $("#emp_list").on("draw.dt", function () {
            $("#emp_list").Tabledit({
                url: "edit.php",
                dataType: "json",
                columns: {
                    identifier: [0, "user_id"],
                    editable: [
                        [1, "first_name"],
                        [2, "last_name"],
                        [3, "email"],
                        [4, "admin_approved"],
                        [5, "user_role_id"],
                    ],
                },
                restoreButton: false,
                onSuccess: function (data, textStatus, jqXHR) {
                    if (data.action == "delete") {
                        $("#" + data.id).remove();
                        $("#emp_list").DataTable().ajax.reload();
                    }
                },
            });
        });
    });
</script>
</body>
</html>

<?php
//if(isset($_POST['sub']))
//{
//$host="localhost";//host name
//$username="root"; //database username
//$word="";//database word
//$db_name="sub_db";//database name
//    $tbl_name="tbl_user_roles"; //table name
////$con=mysqli_connect("$host", "$username", "$word","$db_name")or die("cannot connect");//connection string
//    $checkbox1=$_POST['techno'];
//    $chk="";
//    foreach($checkbox1 as $chk1)
//    {
//        $chk .= $chk1.",";
//    }
//    $in_ch=mysqli_query($con,"insert into tbl_user_roles(technology) values ('$chk')");
//    if($in_ch==1)
//    {
//        echo'<script>alert("Inserted Successfully")</script>';
//    }
//    else
//    {
//        echo'<script>alert("Failed To Insert")</script>';
//    }
//}


//$user_id = $_GET["$user_id"];

//$sql =  "select user_role_id from tbl_users ";//where user_id = $user_id";// order by ID";
////_log('select user_role_id from tbl_users: '.$sql);
//
//$result = mysqli_query($con,$sql);
//
//$list = array();
//while($row = mysqli_fetch_assoc($result)) {
//  $list[] = $row;
//}
//echo json_encode($list);

if(isset($_POST['selectSubmit'])){ // Use button name
    $admin_approved = $_POST ['admin_approved'];// Use select ID
    $user_id = $_POST['user_id'];
    $query = "UPDATE tbl_users SET admin_approved = '$admin_approved' WHERE user_id = '$user_id'";

    $statement = mysqli_prepare($con, $query);
    mysqli_stmt_execute($statement);

    $con->close();

}
?>
