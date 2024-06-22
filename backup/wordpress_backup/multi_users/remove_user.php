<!--<!DOCTYPE html>-->
<!--<html lang="en">-->
<!---->
<!--<html>-->
<!--<head>-->
<!--  <meta charset="utf-8">-->
<!--  <meta http-equiv="X-UA-Compatible" content="IE=edge">-->
<!--  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">-->
<!--  <meta name="description" content="">-->
<!--  <meta name="author" content="">-->
<!--  <title>Remove User</title>-->
<!--  <!-- Bootstrap core CSS-->-->
<!--  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">-->
<!--  <!-- Custom fonts for this template-->-->
<!--  <link href="assets/vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">-->
<!--  <!-- Custom styles for this template-->-->
<!--  <link href="assets/css/sb-admin.css" rel="stylesheet">-->
<!--</head>-->
<!---->
<!--<body class="bg-dark">-->
<!--  <div class="container">-->
<!--    <div class="card card-login mx-auto mt-5">-->
<!--      <div class="card-header">Removing User</div>-->
<!--<!--      <div class="card-body">-->-->
<!--        --><?php
//          if (isset($errorMsg)) {
//              echo '<div class="alert alert-danger">';
//              echo $errorMsg;
//              echo '</div>';
//              unset($errorMsg);
//          }
//        ?>
<!---->
<!--<!--<form>-->-->
<!--<!--  Please select user to be removed-->-->
<!--<!--  <br>Name: <select><option disabled selected>-- Select Name --</option></br>-->-->
<!--<!--</form>-->-->
<!---->
<?php
//// ERRORS
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
//
////// CONNECT TO DB
//define("HOST", "localhost");
//define("DB_USER", "hub_admin");
//define("DB_PASS", "UasHtp_Rocks^^7");
//define("DB_NAME", "users");
//
//
//$conn = mysqli_connect(HOST, DB_USER, DB_PASS, DB_NAME);
//
//if (!$conn) {
//    die(mysqli_error());
//}
//
//        //$records = mysqli_query($conn, "select first_name, last_name from tbl_users");  // Use select query here
//        $records = mysqli_query($conn, "select first_name, last_name from tbl_users");  // Use select query here
//
//
//        while ($data = mysqli_fetch_array($records)) {
//            //echo "<option value="' .$data['first_name'].'">" .$data['first_name'] .$data['last_name'] ."</option>";  // displaying data in option menu
//
//            echo '<option value="'.$data['first_name'].'">'.$data['first_name'].'</option>';
//            //echo '<option value="'.$data['last_name'].'">'.$data['last_name'].'</option>';
//
//
//
//            //echo "<option value='". $data['last_name'] ."'>" .$data['last_name'] ."</option>";  // displaying data in option menu
//        }
//    ?>
<!--  </select>-->
<!--</form>-->
<!---->
<!--<div class='block'>-->
<!--  <button class='btn'>Remove</button>-->
<!--</div>-->
<!---->
<?php //mysqli_close($conn);  // close connection?>
<!---->
<!--<!-- Bootstrap core JavaScript-->-->
<!--<script src="assets/vendor/jquery/jquery.min.js"></script>-->
<!--<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>-->
<!--<!-- Core plugin JavaScript-->-->
<!--<script src="assets/vendor/jquery-easing/jquery.easing.min.js"></script>-->
<!---->
<!--</body>-->
<!--</html>-->
