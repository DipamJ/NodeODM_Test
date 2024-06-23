<?php
///**
// * Front to the WordPress application. This file doesn't do anything, but loads
// * wp-blog-header.php which does and tells WordPress to load the theme.
// *
// * @package WordPress
// */
//
///**
// * Tells WordPress to load the WordPress theme and output it.
// *
// * @var bool
// */
//define( 'WP_USE_THEMES', true );
//
///** Loads the WordPress Environment and Template */
//require __DIR__ . '/wp-blog-header.php';


// ERRORS
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once('uas_tools/user_management/inc/config.php');

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

            header('location:uas_tools/user_management/dashboard.php');
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
    header("location:http://bhub.gdslab.org/");
    //header("Location: " . 'http://' . $_SERVER['HTTP_HOST']);
    exit;
}

if (isset($_GET['lmsg']) && $_GET['lmsg'] == true) {
    $errorMsg = "Login required to access dashboard";
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
    <title>BHUB</title>
    <!-- Bootstrap core CSS-->
    <link href="multi_users/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom fonts for this template-->
    <link href="multi_users/assets/vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <!-- Custom styles for this template-->
    <link href="multi_users/assets/css/sb-admin.css" rel="stylesheet">
    <!-- Bootstrap core JavaScript-->
    <script src="uas_tools/user_management/assets/vendor/jquery/jquery.min.js"></script>
    <script src="uas_tools/user_management/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- Core plugin JavaScript-->
    <script src="uas_tools/user_management/assets/vendor/jquery-easing/jquery.easing.min.js"></script>
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
            <a href="http://bhub.gdslab.org/uas_tools/user_management/AddUser.php">Click here to add new user</a>
        </div>
    </div>
</div>
</body>
</html>

