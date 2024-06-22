<?php
//require_once("Resources/PHP/SetDBConnection.php");

//$conn = SetDBConnection();

// CONNECT TO DB
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


// DEFAULT VALUES

// Sites
//$header_location = "http://bhub.gdslab.org/";

//$add_user_link = "http://bhub.gdslab.org/uas_tools/user_management/AddUser.php";
//$approve_user_link = "/uas_tools/user_management/Approval.php";

// Paths
//$root_path = "/var/www/html/wordpress/";
//$alternative_root_path = "/var/www/html/";
//$dashboard_path = "user_management/dashboard.php";

//$main_temporary_folder = "http://bhub.gdslab.org/temp/";
//$crop_data_path = "/var/www/html/temp/CropData/";

//$crop_data_temporary_folder = "http://bhub.gdslab.org/temp/CropData/";

// Libraries - jquery
//$jquery_min_js = "user_management/assets/vendor/jquery/jquery.min.js";
//$jquery_easing_min_js = "user_management/assets/vendor/jquery-easing/jquery.easing.min.js";

// Libraries - bootstrap
//$bootstrap_bundle_min_js = "user_management/assets/vendor/bootstrap/js/bootstrap.bundle.min.js";

// Libraries - leaflet
//$leaflet_js = "bhub.gdslab.org/js/leaflet.js";
//$leaflet_css = "bhub.gdslab.org/css/leaflet.css";

// Libraries - PHP Mailer
//$php_mailer_autoload = "../../../../multi_users/PHPMailer/PHPMailerAutoload.php";
//$php_mailer_autoload = "web/multi_users/PHPMailer/PHPMailerAutoload.php"; from root

// Configuration
//$cpu_number = "16";
//$admin_email = "uas.hub@gmail.com";

if (isset($_POST['submit'])) { // the POST form has been submitted
    // header_location
    if (!empty($_POST['header_location'])) {
        $header_location = ($_POST['header_location']);

        $sql = "UPDATE system_management set Value = '$header_location' where Name = 'site_name'";
        $result = mysqli_query($conn, $sql);

//        if ($result){
//            echo "test passed";
//        }
    }

    // PATHS
    // root_path
    if (!empty($_POST['root_path'])) {
        $root_path = ($_POST['root_path']);

        $sql = "UPDATE system_management set Value = '$root_path' where Name = 'root_path'";
        $result = mysqli_query($conn, $sql);
    }

    // main_temporary_folder
    if (!empty($_POST['main_temporary_folder'])) {
        $main_temporary_folder = ($_POST['main_temporary_folder']);

        $sql = "UPDATE system_management set Value = '$main_temporary_folder' where Name = 'visualization_pages'";
        $result = mysqli_query($conn, $sql);
    }

    // crop_data_temporary_folder
    if (!empty($_POST['crop_data_temporary_folder'])) {
        $crop_data_temporary_folder = ($_POST['crop_data_temporary_folder']);

        $sql = "UPDATE system_management set Value = '$crop_data_temporary_folder' where Name = 'crop_data'";
        $result = mysqli_query($conn, $sql);
    }

    // cpu_number
    if (!empty($_POST['cpu_number'])) {
        $cpu_number = ($_POST['cpu_number']);

        $sql = "UPDATE system_management set Value = '$cpu_number' where Name = 'threads_number'";
        $result = mysqli_query($conn, $sql);
    }

    // admin_email
    if (!empty($_POST['admin_email'])) {
        $admin_email = ($_POST['admin_email']);

        $sql = "UPDATE system_management set Value = '$admin_email' where Name = 'admin_email'";
        $result = mysqli_query($conn, $sql);
    }
}


$sql = "SELECT Name, Value FROM system_management WHERE ID = 1";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $root_path = $row["Value"];
    }
}

///
$sql = "SELECT Name, Value FROM system_management WHERE ID = 2";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        //$site_name = $row["Value"];
        $header_location = $row["Value"];
    }
}
///
$sql = "SELECT Name, Value FROM system_management WHERE ID = 3";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $main_temporary_folder = $row["Value"];
    }
}
///
$sql = "SELECT Name, Value FROM system_management WHERE ID = 4";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $crop_data_temporary_folder = $row["Value"];
    }
}
///
$sql = "SELECT Name, Value FROM system_management WHERE ID = 5";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $cpu_number = $row["Value"];
    }
}

///
$sql = "SELECT Name, Value FROM system_management WHERE ID = 6";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $admin_email = $row["Value"];
    }
}

mysqli_close($conn);


?>