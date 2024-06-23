<?php
// ERRORS
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once('user_management/inc/config.php');
//require_once("system_management/centralized.php");

if (isset($_POST['login'])) {
    if (!empty($_POST['email']) && !empty($_POST['password'])) {
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);

        $md5Password = md5($password);

        $sql = "select * from users where email = '" . $email . "' and password = '" . $md5Password . "'";
        $rs = mysqli_query($conn, $sql);
        $getNumRows = mysqli_num_rows($rs);

        if ($getNumRows == 1) {
            $getUserRow = mysqli_fetch_assoc($rs);
            unset($getUserRow['password']);

            $_SESSION = $getUserRow;
            // If session hasn't been started, start it
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $dashboard_path = $_SESSION['dashboard_path'];
            header('location: ' . $dashboard_path);
            exit;
        } else {
            $errorMsg = "Wrong email or password";
        }
    }
}

//if (isset($_GET['logout']) && $_GET['logout'] == true) {
//    session_destroy();
//    header("location:uas_tools/user_management/index.php");
//    exit;
//}

if (isset($_GET['logout']) && $_GET['logout'] == true) {
    session_destroy();
    header("location:index.php");
    exit;
}

if (isset($_GET['lmsg']) && $_GET['lmsg'] == true) {
    $errorMsg = "Login required to access dashboard";
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
    <title>BASFHUB</title>
    <!-- Bootstrap core CSS-->
    <link href="../multi_users/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom fonts for this template-->
    <link href="../multi_users/assets/vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <!-- Custom styles for this template-->
    <link href="../multi_users/assets/css/sb-admin.css" rel="stylesheet">
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

            <!--            <a href="http://basfhub.gdslab.org/uas_tools/user_management/AddUser.php">Click here to add new user</a>-->
            <?
            // If session hasn't been started, start it
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $add_user_link = $_SESSION['add_user_link'];
            ?>

            <a href="<?= $add_user_link ?>">Click here to add new user</a>

        </div>
    </div>
</div>
<!-- Bootstrap core JavaScript-->
<?
// If session hasn't been started, start it
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$jquery_min_js = $_SESSION['jquery_min_js'];
$jquery_easing_min_js = $_SESSION['jquery_easing_min_js'];
$bootstrap_bundle_min_js = $_SESSION['bootstrap_bundle_min_js'];
?>
<script src="<?= $jquery_min_js ?>"></script>
<script src="<?= $bootstrap_bundle_min_js ?>"></script>
<!-- Core plugin JavaScript-->
<script src="<?= $jquery_easing_min_js ?>"></script>
</body>
</html>