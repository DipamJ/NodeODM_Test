<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Add New User</title>
    <!-- Bootstrap core CSS-->
    <!--    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">-->
<!--    <link href="assets/vendor/bootstrap/css/bootstrap.css" rel="stylesheet">-->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom fonts for this template-->
    <link href="assets/vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <!-- Custom styles for this template-->
    <link href="assets/css/sb-admin.css" rel="stylesheet">
</head>

<body class="bg-dark">
<div class="container">
    <div class="card card-login mx-auto mt-5">
        <div class="card-header">Adding User</div>
        <div class="card-body">
            <?php
            if (isset($errorMsg)) {
                echo '<div class="alert alert-danger">';
                echo $errorMsg;
                echo '</div>';
                unset($errorMsg);
            }
            ?>
            <div id = "main">
                <form action = "" method = "post">

                    <div class="form-group">
                        <label>First Name</label>
                        <input class="form-control" id="first_name" name="first_name" type="text" placeholder="Enter your name" required>
                    </div>
                    <div class="form-group">
                        <label>Last Name</label>
                        <input class="form-control" id="last_name" name="last_name" type="text" placeholder="Enter your last name" required>
                    </div>
                    <!--<div class="form-group">
                      <label>Role</label>
                      <input class="form-control" id="user_role_id" name="user_role_id" type="number" placeholder="1, 2, or 3" required>
                    </div>-->
                    <div class="form-group">
                        <label>Email</label>
                        <input class="form-control" id="email" name="email" type="email" placeholder="Enter email" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input class="form-control" id="password" name="password" type="password" placeholder="Create your password" required>
                    </div>
                    <div class="form-group">
                        <label>Password (Confirmation)</label>
                        <input class="form-control" id="password" name="password2" type="password" placeholder="Confirm your password" required>
                    </div>

                    <p>Use 8 or more characters with a mix of letters, numbers & symbols</p>

                    <input class="btn btn-primary btn-block" type = "submit" value ="Submit" name = "submit"/>

                    <br />
                </form>
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
// ERRORS
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//error_reporting(-1);
//ini_set('display_errors', 'On');
//set_error_handler("var_dump");

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

if (isset($_POST["submit"])) {
    //_log('User pressed submit button ');
    $servername = "localhost";
    $username = "hub_admin";
    $password = "UasHtp_Rocks^^7";
    $dbname = "uas_projects";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

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
    $email 	= $_POST['email'];

//    $sql = "INSERT INTO tbl_users(user_role_id, first_name, last_name, email, password)
//       #VALUES ('2','$_POST[first_name]','$_POST[last_name]','$_POST[email]',md5('$_POST[password]'))
//       VALUES('3','".$first_name."', '".$last_name."', '".$email."',md5('$password'))";
        $sql = "INSERT INTO users(first_name, last_name, email, password)
       VALUES('".$first_name."', '".$last_name."', '".$email."',md5('$password'))";
    //_log('Insert to tbl_users: '.$sql);


    if (mysqli_query($conn, $sql)) {
        //echo "New user created successfully";
        //_log('New user created successfully');
        echo '<script>alert ("New user created successfully. Please wait to be approved by administrator")</script>';
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
        $mail->Username = 'uas.hub@gmail.com';
        $mail->Password = '#4*8H0u1!Vdb';
        $mail->SetFrom('uas.hub@gmail.com');// FROM
        $mail->Subject = 'User needs approval';

        $accept_link = "http://basfhub.gdslab.org/multi_users/approval.php?e=" . $email . "&h=" . hash('sha512', 'ACCEPT');

        $decline_link = "http://basfhub.gdslab.org/multi_users/approval.php?e="  . $email . "&h=" . hash('sha512', 'DECLINE');

        //$mail->Body = 'A test email!';// BODY
        $mail->Body = nl2br("The user $first_name $last_name needs your approval" . "\r\n" .
            "----------------------------------- " . "\r\n" .
            "Accept: " . $accept_link . "\r\n \r\n".
            "Decline: " . $decline_link . "\r\n");// BODY

        $mail->AddAddress('landivarscott@gmail.com');// TO

        $mail->Send();

        //////////////////////
        header('location:index.php');
        exit;
    } else {
        echo "Error: " . $sql . "" . mysqli_error($conn);
    }
    }
    $conn->close();
}
