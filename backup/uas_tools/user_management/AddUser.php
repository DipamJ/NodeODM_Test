<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!--    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">-->
    <meta name=”viewport” content=”width=device-width, initial-scale=1″>
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Add User</title>
    <!-- Bootstrap core CSS-->
    <!--    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">-->
    <!--    <link href="assets/vendor/bootstrap/css/bootstrap.css" rel="stylesheet">-->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom fonts for this template-->
    <link href="assets/vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <!-- Custom styles for this template-->
    <link href="assets/css/sb-admin.css" rel="stylesheet">
    <style>
        .project {
            margin: 0px 0px 0px 0px;
            padding: 25px 35px;
            border-radius: 15px;
            background: #f6f7f9;
        }

        input.btnNew {
            padding: 0;
            font-weight: 500;
            font-size: 17px;
            color: #ffffff;
            background: linear-gradient(#2c539e, #254488);
            line-height: 36px;
            border-radius: 5px;
            width: 108px;
            border: 1px solid #00236f;
            float: right;
        }

        p {
            font-size: 18px !important;
        }

    </style>
</head>

<body class="bg-dark">
    <div class="container" style="padding-bottom: 1rem!important; padding-top: 1rem!important;">
        <div class="project">
            <h3>Add User</h3>
            <div class="">
                <!--card card-login mx-auto-->
                <!--<div class="card-header">Add User</div>-->
                <div class="">
                    <!--card-body-->
                    <?php
                    if (isset($errorMsg)) {
                        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
                        echo '<strong>Warning!</strong> ';
                        echo $errorMsg;
                        echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                        echo '</div>';
                        unset($errorMsg);
                    }
                    ?>
                    <div id="main">
                        <form action="" method="post">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>First Name</label>
                                        <input class="form-control" id="first_name" name="first_name" type="text" placeholder="Enter your name" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Last Name</label>
                                        <input class="form-control" id="last_name" name="last_name" type="text" placeholder="Enter your last name" required>
                                    </div>
                                </div>
                                <!--<div class="col-md-6">
                                    <div class="form-group">
                                        <label>Role</label>
                                        <input class="form-control" id="user_role_id" name="user_role_id" type="number" placeholder="1, 2, or 3" required>
                                    </div>
                                </div>-->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input class="form-control" id="email" name="email" type="email" placeholder="Enter email" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Password</label>
                                        <input class="form-control" id="password" name="password" type="password" placeholder="Create your password" required>
                                        <small>Use 8 or more characters with a mix of letters, numbers &amp; symbols</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Password (Confirmation)</label>
                                        <input class="form-control" id="password" name="password2" type="password" placeholder="Confirm your password" required>
                                    </div>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <input class="btnNew btn-block" type="submit" value="Submit" name="submit" />
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap core JavaScript-->
    <script src="assets/vendor/jquery/jquery.min.js"></script>
    <!--<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>-->
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- Core plugin JavaScript-->
    <script src="assets/vendor/jquery-easing/jquery.easing.min.js"></script>

</body>

</html>

<?php
// File containing System Variables
define("LOCAL_PATH_ROOT", $_SERVER["DOCUMENT_ROOT"]);
require LOCAL_PATH_ROOT . '/uas_tools/system_management/centralized_management.php';

// ERRORS
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

//error_reporting(-1);
//ini_set('display_errors', 'On');
//set_error_handler("var_dump");

require_once("CommonFunctions.php");

if (isset($_POST["submit"])) {
    #$username = $_POST[''];
    $password = $_POST['password'];
    $password2 = $_POST['password2'];


    if (strlen($password) < 8) {
        //echo '<script>alert ("Password too short!. Password might have at least 8 characters")</script>';
        //$errors[] = die("Password too short!. Password might have at least 8 characters");
        die();
    }

    if (!preg_match("#[0-9]+#", $password)) {
        //echo '<script>alert ("Password must include at least one number!")</script>';
        die();
        //$errors[] = die("Password must include at least one number!");
    }

    if (!preg_match("#[a-zA-Z]+#", $password)) {
        //echo '<script>alert ("Password must include at least one letter!")</script>';
        die();
        //$errors[] = die("Password must include at least one letter!");
    }

    if (!preg_match("@[^\w]@", $password)) {
        //echo '<script>alert ("Password must include at least one special character!")</script>';
        die();
        //$errors[] = die("Password must include at least one special character!");
    }

    if ($password != $password2) {
        //echo '<script>alert ("Passwords do  not match")</script>';
        die();
        //die('Passwords do  not match');
    }

    if (isset($_POST['submit'])) {
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $email = $_POST['email'];

//    $sql = "INSERT INTO tbl_users(user_role_id, first_name, last_name, email, password)
//       #VALUES ('2','$_POST[first_name]','$_POST[last_name]','$_POST[email]',md5('$_POST[password]'))
//       VALUES('3','".$first_name."', '".$last_name."', '".$email."',md5('$password'))";
        $sql = "INSERT INTO users(first_name, last_name, email, password)
       VALUES('" . $first_name . "', '" . $last_name . "', '" . $email . "', md5('$password'))";
        //_log('Insert to tbl_users: '.$sql);


        if (mysqli_query($connect, $sql)) {
            //echo "New user created successfully";
            //_log('New user created successfully');
            echo '<script>alert ("New user created successfully. Please wait to be approved by administrator.")</script>';
            //$message='<div class="alert alert-success" role="alert">Success</div>';

            // SEND APPROVAL EMAIL
            require_once 'PHPMailer/PHPMailerAutoload.php';

            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->SMTPAuth = True;
            $mail->SMTPSecure = 'ssl';
            $mail->Host = 'smtp.gmail.com';
            $mail->Port = '465';
            $mail->isHTML();
            $mail->Username = 'uas.hub@gmail.com';// FROM
            $mail->Password = '#4*8H0u1!Vdb';
            $mail->SetFrom('uas.hub@gmail.com');// FROM
            $mail->AddAddress('landivarscott@gmail.com');// TO
            $mail->Subject = 'User needs approval';

            //$accept_link = "http://basfhub.gdslab.org/uas_tools/user_management/Approval.php?e=" . $email . "&h=" . hash('sha512', 'ACCEPT');
            // If session hasn't been started, start it
            // if (session_status() == PHP_SESSION_NONE) {
            //     session_start();
            // }
            //$header_location = $_SESSION['header_location'];
            //$approve_user_link = $_SESSION['approve_user_link'];

            //$accept_link = $header_location . $approve_user_link . "?e=" . $email . "&h=" . hash('sha512', 'ACCEPT');

            //$decline_link = $header_location . $approve_user_link . "?e=" . $email . "&h=" . hash('sha512', 'DECLINE');

            //$mail->Body = 'A test email!';// BODY
            $mail->Body = nl2br("The user $first_name $last_name has created and account and needs your approval." . "\r\n" .
                "Please, go to the website to make any changes." . "\r\n");
//                "----------------------------------- " . "\r\n" .
//                "Accept: " . $accept_link . "\r\n \r\n" .
//                "Decline: " . $decline_link . "\r\n");// BODY

            $mail->Send();

            header('location:index.php');
            exit;
        } else {
            echo "Error: " . $sql . "" . mysqli_error($connect);
        }
    }
    $connect->close();
}
?>
