<?php
require_once("CommonFunctions.php");
// Check only Admins can access to it
session_start();
$_VERIFY = $_SESSION['email'];

//// DB Connection
//$connect = mysqli_connect("localhost", "hub_admin", "UasHtp_Rocks^^7", "uas_projects");
//if(!$connect){
//    echo "Error: Unable to connect to MySQL." . PHP_EOL;
//    echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
//    exit;}

// SELECT USING EMAIL TO GET THE role_id
$sql = "select role_id from users_roles, users where  users_roles.user_id = users.user_id and email = '" . $_VERIFY . "' ORDER BY role_id ASC";
$result = mysqli_query($connect, $sql);
//_log('select role_id: '.$sql);
$row = mysqli_fetch_assoc($result);
//echo $row["role_id"];
if ($row["role_id"] != '1') {
    header('location:index.php?lmsg=true');
    exit;
}
?>

    <html>
    <head>
        <title>Modify Role</title>
        <!--    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>-->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

        <!--    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />-->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"/>

        <!--    <script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>-->
        <!--    <script src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>-->
        <!--    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css" />-->

        <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap.min.js"></script>
        <link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap.min.css"/>

        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

        <script src="https://markcell.github.io/jquery-tabledit/assets/js/tabledit.min.js"></script>
        <!--        <style>-->
        <!--            #sample_data tr > *:nth-child(1) {-->
        <!--                display: none;-->
        <!--            }-->
        <!--        </style>-->
    </head>
    <body>
    <div class="container">
        <h3 align="center">Modify Role</h3>
        <br/>
        <div class="panel panel-default">
            <!--        <div class="panel-heading">Modifying Roles</div>-->
            <div class="panel-body">
                <div class="table-responsive">
                    <table id="sample_data" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Roles</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>

                    <h1 align="center"></h1>
                    <form action="" method="post">
                        <div class="form-group">
                            <label>Addding new role</label>
                            <input class="form" id="role_name" name="role_name" type="text" placeholder="Enter role"
                                   required>
                            <input class="btn btn-primary" type="submit" value="Submit" name="submit"/>
                        </div>

                </div>
            </div>
        </div>
    </div>
    <br/>
    <br/>
    </body>
    </html>

    <script type="text/javascript" language="javascript">
        $(document).ready(function () {
            var dataTable = $('#sample_data').DataTable({
                "processing": true,
                "serverSide": true,
                "order": [],
                "ajax": {
                    url: "FetchRoleTable.php",
                    type: "POST"
                }
            });

            $('#sample_data').on('draw.dt', function () {
                $('#sample_data').Tabledit({
                    url: 'ActionRoleTable.php',
                    dataType: 'json',
                    columns: {
                        identifier: [0, 'role_id'],
                        editable: [
                            [1, 'role_name']
                        ]
                    },
                    hideIdentifier: true,
                    restoreButton: false,
                    onSuccess: function (data, textStatus, jqXHR) {
                        if (data.action == 'delete') {
                            $('#' + data.id).remove();
                            $('#sample_data').DataTable().ajax.reload();
                        }
                    }
                });
            });
        });
    </script>

<?php
if (isset($_POST["submit"])) {
//// DB Connection
//    $connect = mysqli_connect("localhost", "hub_admin", "UasHtp_Rocks^^7", "uas_projects");
//    if(!$connect){
//        echo "Error: Unable to connect to MySQL." . PHP_EOL;
//        echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
//        exit;}

    if (isset($_POST['submit'])) {
        $role_name = $_POST['role_name'];

        $sql = "INSERT INTO roles(role_name)
           VALUES('" . $role_name . "')";

//        echo "<meta http-equiv='refresh' content='0'>";

        if (mysqli_query($connect, $sql)) {
            exit;
        } else {
            echo "Error: " . $sql . "" . mysqli_error($connect);
        }
    }
    $connect->close();
}
?>