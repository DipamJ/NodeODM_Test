<?php
// Check only Admins can access to it
session_start();
$_VERIFY = $_SESSION['user_role_id'];
if ($_VERIFY != '1') {
    header('location:index.php?lmsg=true');
    exit;
}

// DB Connection
$connect = mysqli_connect("localhost", "hub_admin", "UasHtp_Rocks^^7", "users");
if(!$connect){
    echo "Error: Unable to connect to MySQL." . PHP_EOL;
    echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
    exit;}

// Log Document
function _log($str)
{
    // log to the output
    $log_str = date('d.m.Y').": {$str}\r\n";
    echo $log_str;

    // log to file
    if (($fp = fopen('upload_log.txt', 'a+')) !== false) {
        fputs($fp, $log_str);
        fclose($fp);
    }
}
// Display user information on table
$query = "SELECT user_id, first_name, last_name, email FROM tbl_users ORDER BY user_id ASC";
$result = mysqli_query($connect, $query);

//// Display user groups on table
//$query = "select role_id from tbl_users, tbl_user_roles where tbl_user_roles.user_id = tbl_users.user_id;";
//$result = mysqli_query($connect, $query);



$records_roles = mysqli_query($connect, "SELECT role_name FROM tbl_roles");
$roles = array();
$count = 0;
//while ($course_roles = mysqli_fetch_assoc($records_roles)){
//    $roles []= $course_roles;
////    //var_dump($roles);
//    $count++;
//}
//var_dump($roles);
while ($course_roles = mysqli_fetch_assoc($records_roles)){
    $roles []= $course_roles['role_name'];
    $count++;
}
?>

<!DOCTYPE html>
<html lang="en">
<html>

<head>
    <title>Modify Users</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <script src="jquery-tabledit-1.2.3/jquery.tabledit.min.js"></script>

    <script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css" />
    <script src="https://markcell.github.io/jquery-tabledit/assets/js/tabledit.min.js"></script>

</head>

<body>
<div class="container">
    <br />
    <br />
    <br />
    <div class="table-responsive">
        <h3 align="center">Modifying Users</h3><br />
        <table id="editable_table" class="table table-bordered table-striped">
            <thead>
            <style>
                th {
                    text-align: center;
                }
            </style>
            <tr>
<!--                <th>ID</th>-->
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Approval</th>
                <th>Roles</th>
                <th>Groups</th>
            </tr>
            </thead>
            <tbody>
            <?php // align=center
            while($row = mysqli_fetch_array($result))
            {
                echo "
              <tr>
               <td "?> style="display:none" <?php echo">".$row['user_id']."</td>
               <td>".$row['first_name']."</td>
               <td>".$row['last_name']."</td>
               <td>".$row['email']."</td>
               <td>
                    <form method='post'>
                        <input type='hidden' name='user_id' value=".$row["user_id"].">

                        <select name='admin_approved' onchange='this.form.submit()'>
                            <option disabled selected value>-- Select --</option>
                            <option value='1'>Approved</option>
                            <option value='0'>Dissaproved</option>
                        </select>
                    </form>
               </td>
               
               <td>
                    <form method='post'>
                        Select user roles<br/>
                        <input type='hidden' name='user_id' value=".$row["user_id"].">
               ";

                for ($a = 1; $a <= $count ; $a++){
                    echo "<input type='checkbox' name='techno[]' value='$a' />" .$roles[($a-1)]. "<br>";
                }

                echo "
                    <button class='btn btn-primary' type='submit' name='checkSubmit' >Submit</button>
                    </form>
                </td>
                
                <td>
                <input type='hidden' name='user_id' value=".$row["user_id"].">
                "; //echo 'hi' .$row["user_id"]. ' ';
                //$records_groups = mysqli_query($connect, "SELECT role_name FROM tbl_roles");
                $records_groups = mysqli_query($connect, "select role_id from tbl_users, tbl_user_roles where tbl_user_roles.user_id = '".$row["user_id"]. "' group by role_id ORDER BY role_id ASC;");
                $records_roles_names = mysqli_query($connect, "select role_name from tbl_roles;");
                $a = 15;// Max number of roles
                //echo "" . $records_groups . "";
                while ($row = $records_groups->fetch_assoc()) {
                    $row_roles = $records_roles_names->fetch_assoc();
                    //var_dump($row_roles);
                    //echo $row['role_id']."<br>";
                    for ($b = 1; $b <= $a ; $b++){
                        if ($row['role_id'] == $b){
                            echo $row_roles['role_name']. ' <br>';
                            //echo 'admin <br>';
                        }
                        //$a++;
                    }
                    //$a++;

//                    if ($row['role_id'] == 1){
//                        echo $row_roles['role_name']. ' <br>';
//                        //echo 'admin <br>';
//                    }
//                    if ($row['role_id'] == 2){
//                        //echo 'subscriber <br>';
//                        echo $row_roles['role_name']. ' <br>';
//                    }
//                    if ($row['role_id'] == 3){
//                        //echo 'viewer <br>';
//                        echo $row_roles['role_name']. ' <br>';
//                    }
//                    if ($row['role_id'] == 4){
//                        //echo 'data uploader <br>';
//                        echo $row_roles['role_name']. ' <br>';
//                    }
                    //$a++;
                }
                //echo"$a";

                echo"
                </td>
                
               </tr>
                ";
            }
            ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>

<script>
    $(document).ready(function(){




        $('#editable_table').Tabledit({
            url:'action_user.php',
            columns:{
                identifier:[0, "user_id"],
                editable:[  [1, 'first_name'],
                    [2, 'last_name'],
                    [3, 'email']]
            },
            restoreButton:false,
            onSuccess:function(data, textStatus, jqXHR)
            {
                if(data.action == 'delete')
                {
                    $('#'+data.id).remove();
                }
            }
        });

    });
</script>

<?php
//if(isset($_POST['selectSubmit'])){ // Use button name
if(isset($_POST['admin_approved'])){
    $admin_approved = $_POST['admin_approved'] ?? '';// Use select name
    $user_id = $_POST['user_id'];
    $query = "UPDATE tbl_users SET admin_approved = '$admin_approved' WHERE user_id = '$user_id'";
    $statement = mysqli_prepare($connect, $query);
    //mysqli_stmt_execute($statement);

    if(mysqli_stmt_execute($statement))
    {
        echo'<script>alert("User approval has been updated")</script>';
    }
    $connect->close();
}

if(isset($_POST['checkSubmit'])){ // Use button name
    $user_id = $_POST['user_id'];
    $checkbox1=$_POST['techno'];

    for ($i=0; $i<sizeof ($checkbox1);$i++) {
        $query="INSERT INTO tbl_user_roles(user_id, role_id) VALUES ('$user_id', '".$checkbox1[$i]."')";
        _log('Insert new role to user: ' . $query);
        $in_ch=mysqli_query($connect, $query);
    }

    if($in_ch==1)
    {
        echo'<script>alert("Inserted Successfully")</script>';
    }
    else
    {
        echo'<script>alert("Failed to Insert")</script>';
    }
    echo "<meta http-equiv='refresh' content='0'>";
    $connect->close();
}
