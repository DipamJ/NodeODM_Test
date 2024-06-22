<?php
// File containing System Variables
define("LOCAL_PATH_ROOT", $_SERVER["DOCUMENT_ROOT"]);
require LOCAL_PATH_ROOT . '/uas_tools/system_management/centralized_management.php';

// To check if User has the role required to access the page
require_once("CommonFunctions.php");

session_start();

$mysqli = $connect;

$userName = $_SESSION["email"] ?? '';

// SELECT the role_name for each users_roles for the logged on user
// ? is a place holder for our parameter `user_id`
$sql = "
    SELECT r.role_name FROM users_roles AS ur
        JOIN roles AS r ON r.role_id = ur.role_id
    WHERE ur.user_id = ?
";

$query = $mysqli->prepare($sql);                // Prepare the query
$query->bind_param("i", $_SESSION["user_id"]);  // Bind the parameter (wherever you store user_id in $_SESSION)
$query->execute();                              // Run the query
$query->store_result();                         // Store the result
$query->bind_result($role_name);                // Bind the result to a variable

$user_role_array = [];                          // Initialise the user roles array
while ($query->fetch()) {                         // Loop returned records
    $user_role_array[] = $role_name;            // Add user role to array
}

if (mysqli_connect_errno()) {
    echo "Failed to connect to database server: " . mysqli_connect_error();
} else {
    if (!$user_role_array) {
        $_SESSION["page"] = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        //header("Location: http://basfhub.gdslab.org");
        header("Location: " . 'http://' . $_SERVER['HTTP_HOST']);
        exit();
    } else {
        $pageName = basename(__DIR__);
        if ($pageName == "V2") {
            $pageName = basename(realpath(__DIR__ . "/.."));
        }

        $sql1 = "SELECT * FROM page_access WHERE Page = '$pageName'";
        $allowedGroups = array();
        if ($result1 = mysqli_query($mysqli, $sql1)) {
            if ($row1 = mysqli_fetch_assoc($result1)) {
                $allowedGroups = explode(";", $row1["Page_Groups"]);
                $accessGroupsStr = $row1["Page_Groups"];
            }
        }

        $intersect = array_intersect($user_role_array, $allowedGroups);

        if (sizeof($intersect) > 0) {// if match found
            ?>

            <?php
//define("HOST", "localhost");
//define("DB_USER", "hub_admin");
//define("DB_PASS", "UasHtp_Rocks^^7");
//define("DB_NAME", "uas_projects");
//
//$connect = mysqli_connect(HOST, DB_USER, DB_PASS, DB_NAME);
//
//if (!$connect) {
//    die(mysqli_error());
//}

            if (isset($_POST['login'])) {
                if (!empty($_POST['email']) && !empty($_POST['password'])) {
                    $email = trim($_POST['email']);
                    $password = trim($_POST['password']);

                    $md5Password = md5($password);

                    $sql = "select * from users where email = '" . $email . "' and password = '" . $md5Password . "'";
                    _log('select users: ' . $sql);
                    var_dump($sql);
                    $rs = mysqli_query($connect, $sql);
                    //var_dump($rs);
                    $getNumRows = mysqli_num_rows($rs);

                    if ($getNumRows == 1) {
                        $getUserRow = mysqli_fetch_assoc($rs);
                        unset($getUserRow['password']);

                        $_SESSION = $getUserRow;

                        header('location:dashboard.php');
                        exit;
                    } else {
                        $errorMsg = "Wrong email or password";
                    }
                }
            }

            if (isset($_GET['logout']) && $_GET['logout'] == true) {
                session_destroy();
                header("location:index.php");
                exit;
            }

            if (isset($_GET['lmsg']) && $_GET['lmsg'] == true) {
                $errorMsg = "Login required with an approved account to access dashboard";
                //I keep getting true here after updating the tables
            }
            ?>


            <!DOCTYPE html>
            <html lang="en">

            <head>
                <meta charset="utf-8">
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
                <meta name="description" content="">
                <meta name="author" content="">
                <title>User Management</title>
                <!-- Bootstrap core CSS-->
                <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
                <!-- Custom fonts for this template-->
                <link href="assets/vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
                <!-- Custom styles for this template-->
                <link href="assets/css/sb-admin.css" rel="stylesheet">
            </head>

            <body class="bg-dark">
            <div class="container">
                <div class="card card-login mx-auto mt-5">
                    <div class="card-header">Login</div>
                    <div class="card-body">
                        <?php
                        if (isset($errorMsg)) {
                            echo '<div class="alert alert-danger">';
                            echo $errorMsg;
                            echo '</div>';
                            unset($errorMsg);
                        }
                        ?>
                        <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
                            <div class="form-group">
                                <label for="exampleInputEmail1">Email address</label>
                                <input class="form-control" id="exampleInputEmail1" name="email" type="email"
                                       placeholder="Enter email" required>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputPassword1">Password</label>
                                <input class="form-control" id="exampleInputPassword1" name="password" type="password"
                                       placeholder="Password" required>
                            </div>
                            <button class="btn btn-primary btn-block" type="submit" name="login">Login</button>
                        </form>

                        <a href="http://bhub.gdslab.org/uas_tools/user_management/AddUser.php">Click here to add new
                            user</a>

                    </div>
                </div>
            </div>
            <!-- Bootstrap core JavaScript-->
            <script src="assets/vendor/jquery/jquery.min.js"></script>
            <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
            <!-- Core plugin JavaScript-->
            <script src="assets/vendor/jquery-easing/jquery.easing.min.js"></script>

            </body>
            </html>

            <?php
        } else {
            $memberOf = (implode("; ", $user_role_array));
            ?>
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <title><?php echo $pageName; ?></title>
            </head>
            <body>
            </br>
            <p>You do not currently have permission to access this tool.</p>
            <p>Please contact admin at
                <a href="mailto:<?= $admin_email ?>?
        &subject=Requesting%20access%20to%20the%20crop_analysis%20tool
        &body=Hi,%0D%0A%0D%0AThis%20is%20<?= $admin_email ?>.%20Please%20provide%20me%20access%20to%20the%20tool.">
                    <?= $admin_email ?></a>
                to request access to this tool.</p>
            </body>
            </html>
            <?php
        }
    }
}
?>