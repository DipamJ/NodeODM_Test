<?php
// ERRORS
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

$_VERIFY = $_SESSION['user_role_id'];

if ($_VERIFY != '1') {
    header('location:index.php?lmsg=true');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<html>
<head>
    <title>Modify Roles</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css" />
</head>
<body>
<div class="container">
    <h1 align="center"></h1>
    <br />
    <h3 align="center">Modifying Roles</h3>
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="table-responsive">
                <table id="role_list" class="table table-bordered">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Roles</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
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
        var dataTable = $("#role_list").DataTable({
            processing: true,
            serverSide: true,
            order: [],
            ajax: {
                url: "fetch_role.php",
                type: "POST",
            },
        });

        $("#role_list").on("draw.dt", function () {
            $("#role_list").Tabledit({
                url: "edit_role.php",
                dataType: "json",
                columns: {
                    identifier: [0, "role_id"],
                    editable: [
                        [1, "role_name"],
                    ],
                },
                restoreButton: false,
                onSuccess: function (data, textStatus, jqXHR) {
                    if (data.action == "delete") {
                        $("#" + data.id).remove();
                        $('#role_list').DataTable().ajax.reload();
                    }

                    /*
                    if (data.action == "insert") {
                        $("#" + data.id).add();
                        $("#role_list").DataTable().ajax.reload();
                    }
                                           */
                },
            });
        });




        /*
        $('#button_id').on('click', function(e) {
          $.ajax({
url : yourUrl,
type : 'GET',
dataType : 'json',
success : function(data) {
    $('#table_id tbody').append("<tr><td>" + data.column1 + "</td><td>" + data.column2 + "</td><td>" + data.column3 + "</td></tr>");
},
error : function() {
    console.log('error');
}
});
});                        */
    });
</script>

<div id = "main">
    <div class="container">
        <h1 align="center"></h1>
        <form action = "" method = "post">
            <div class="form-group">
                <label>Addding new role</label>
                <input class="form-control" id="role_name" name="role_name" type="text" placeholder="Enter role" required>
            </div>
            <input class="btn btn-primary btn-block" type = "submit" value ="Submit" name = "submit"/>
            <br />
        </form>
    </div>
</div>
<script>
    if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }
</script>
</body>
</html>

<?php

if (isset($_POST["submit"])) {
    $servername = "localhost";
    $username = "hub_admin";
    $password = "UasHtp_Rocks^^7";
    $dbname = "users";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if (isset($_POST['submit'])) {
        $role_name = $_POST['role_name'];

        $sql = "INSERT INTO tbl_roles(role_name)
           VALUES('".$role_name."')";

        if (mysqli_query($conn, $sql)) {
            exit;
        } else {
            echo "Error: " . $sql . "" . mysqli_error($conn);
        }
    }
    $conn->close();
}
?>

