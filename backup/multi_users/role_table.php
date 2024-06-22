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

$query = "SELECT role_id, role_name FROM tbl_roles ORDER BY role_id ASC";
$result = mysqli_query($connect, $query);

// Close DB connection
$connect->close();
?>

<!DOCTYPE html>
<html lang="en">
<html>

<head>
    <title>Modify Roles</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <script src="jquery-tabledit-1.2.3/jquery.tabledit.min.js"></script>
</head>

<body>
<div class="container">
    <br />
    <br />
    <br />
    <div class="table-responsive">
        <h3 align="center">Modifying Roles</h3><br />
        <table id="editable_table" class="table table-bordered table-striped">
            <thead>
            <style>
                th {
                    text-align: center;
                }
            </style>
            <tr>
                <th>ID</th>
                <th>Roles</th>
            </tr>
            </thead>
            <tbody>
            <?php

            while($row = mysqli_fetch_array($result))
            {
                echo '
                <tr>
                   <td>'.$row["role_id"].'</td>
                   <td>'.$row["role_name"].'</td>
                </tr>
                ';
            }
            ?>
            </tbody>
        </table>

        <!--        <div id = "main">-->
        <!--            <div class="container">-->
        <h1 align="center"></h1>
        <form action = "" method = "post">
            <div class="form-group">
                <label>Addding new role</label>
                <input class="form" id="role_name" name="role_name" type="text" placeholder="Enter role" required>
                <input class="btn btn-primary" type = "submit" value ="Submit" name = "submit"/>
            </div>
            <!--                    <input class="btn btn-primary" type = "submit" value ="Submit" name = "submit"/>-->
            <br />
        </form>
        <!--            </div>-->
        <!--        </div>-->
        <script>
            if ( window.history.replaceState ) {
                window.history.replaceState( null, null, window.location.href );
            }
        </script>
    </div>
</div>
</body>
</html>

<script>
    $(document).ready(function(){
        $('#editable_table').Tabledit({
            url:'action_role.php',
            columns:{
                identifier:[0, "role_id"],
                editable:[[1, 'role_name']]
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

if (isset($_POST["submit"])) {
// DB Connection
    $connect = mysqli_connect("localhost", "hub_admin", "UasHtp_Rocks^^7", "users");
    if(!$connect){
        echo "Error: Unable to connect to MySQL." . PHP_EOL;
        echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
        exit;}

    if (isset($_POST['submit'])) {
        $role_name = $_POST['role_name'];

        $sql = "INSERT INTO tbl_roles(role_name)
           VALUES('".$role_name."')";

        echo "<meta http-equiv='refresh' content='0'>";

        if (mysqli_query($connect, $sql)) {
            exit;
        } else {
            echo "Error: " . $sql . "" . mysqli_error($connect);
        }
    }
    $connect->close();
}
?>

