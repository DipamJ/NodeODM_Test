<?php
//
//// Log Document
////function _log($str)
////{
////    // log to the output
////    $log_str = date('d.m.Y') . ": {$str}\r\n";
////    echo $log_str;
////
////    // log to file
////    if (($fp = fopen('upload_log.txt', 'a+')) !== false) {
////        fputs($fp, $log_str);
////        fclose($fp);
////    }
////}
//
//// To get all the data from this file; copy and past the following in all the pages needed:
//// FOR index.php
////require_once("../system_management/centralized.php");
//
//// FOR WHEN FILE IS INSIDE /Resources/PHP
////require_once("../../../system_management/centralized.php");
//
//// echo $header_location;
////echo($test);
////$test = "name";
//// This works also: require_once $php_mailer_autoload;
//
//// Sites
////$header_location = "http://basfhub.gdslab.org";
//$header_location = "http://bhub.gdslab.org";
////$add_user_link = "http://basfhub.gdslab.org/uas_tools/user_management/AddUser.php";
//$add_user_link = "http://bhub.gdslab.org/uas_tools/user_management/AddUser.php";
//$approve_user_link = "/uas_tools/user_management/Approval.php";
//
//// Paths
//$root_path = "/var/www/html/wordpress/";
//$alternative_root_path = "/var/www/html/";
//$dashboard_path = "user_management/dashboard.php";
////$main_temporary_folder = "http://basfhub.gdslab.org/temp/";
//$main_temporary_folder = "http://bhub.gdslab.org/temp/";
//$crop_data_path = "/var/www/html/temp/CropData/";
////$crop_data_temporary_folder = "http://basfhub.gdslab.org/temp/CropData/";
//$crop_data_temporary_folder = "http://bhub.gdslab.org/temp/CropData/";
//
//// Libraries - jquery
//$jquery_min_js = "user_management/assets/vendor/jquery/jquery.min.js";
//$jquery_easing_min_js = "user_management/assets/vendor/jquery-easing/jquery.easing.min.js";
//
//// Libraries - bootstrap
//$bootstrap_bundle_min_js = "user_management/assets/vendor/bootstrap/js/bootstrap.bundle.min.js";
//
//// Libraries - leaflet
////$leaflet_js = "basfhub.gdslab.org/js/leaflet.js";
//$leaflet_js = "bhub.gdslab.org/js/leaflet.js";
////$leaflet_css = "basfhub.gdslab.org/css/leaflet.css";
//$leaflet_css = "bhub.gdslab.org/css/leaflet.css";
//
//// Libraries - PHP Mailer
//$php_mailer_autoload = "../../../../multi_users/PHPMailer/PHPMailerAutoload.php";
////$php_mailer_autoload = "web/multi_users/PHPMailer/PHPMailerAutoload.php"; from root
//
//// Configuration
//$cpu_number = "16";
//$admin_email = "uas.hub@gmail.com";
//
//// If session hasn't been started, start it
//if (session_status() == PHP_SESSION_NONE) {
//    session_start();
//}
//
////if (!session_id()) session_start();
//
////if (!isset($_SESSION['root_path'])) {
////    $_SESSION['root_path'] = $root_path;
////}
//
//
//if (isset($_POST['submit'])) { // the POST form has been submitted
//    // header_location
//    if (empty($_POST['header_location'])) {
//        //echo 'Header Location: ' . $header_location . "<br>";
//        $_SESSION['header_location'] = $header_location;
//    } elseif (!empty($_POST['header_location'])) {
//        $header_location = ($_POST['header_location']);
//        $_SESSION['header_location'] = $header_location;
//        //echo 'Header Location: ' . $header_location . "<br>";
//    }
//
//    // SITES
//    // add_user_link
//    if (empty($_POST['add_user_link'])) {
//        //echo 'Add User Link: ' . $add_user_link . "<br>";
//        $_SESSION['add_user_link'] = $add_user_link;
//
//    } elseif (!empty($_POST['add_user_link'])) {
//        $add_user_link = ($_POST['add_user_link']);
//        $_SESSION['add_user_link'] = $add_user_link;
//        //echo 'Add User Link: ' . $add_user_link . "<br>";
//    }
//
//    // approve_user_link
//    if (empty($_POST['approve_user_link'])) {
//        //echo 'Approve User Link: ' . $approve_user_link . "<br>";
//        $_SESSION['approve_user_link'] = $approve_user_link;
//
//    } elseif (!empty($_POST['approve_user_link'])) {
//        $approve_user_link = ($_POST['approve_user_link']);
//        $_SESSION['approve_user_link'] = $approve_user_link;
//        //echo 'Approve User Link: ' . $approve_user_link . "<br>";
//    }
//
//    // PATHS
//    // root_path
//    if (empty($_POST['root_path'])) {
//        //echo 'Root Path: ' . $root_path . "<br>";
//        $_SESSION['root_path'] = $root_path;
//
//    } elseif (!empty($_POST['root_path'])) {
//        $root_path = ($_POST['root_path']);
//        $_SESSION['root_path'] = $root_path;
//        //echo 'Root Path: ' . $root_path . "<br>";
//    }
//
//    // alternative_root_path
//    if (empty($_POST['alternative_root_path'])) {
//        //echo 'Alternative Root Path: ' . $alternative_root_path . "<br>";
//        $_SESSION['alternative_root_path'] = $alternative_root_path;
//
//    } elseif (!empty($_POST['alternative_root_path'])) {
//        $alternative_root_path = ($_POST['alternative_root_path']);
//        $_SESSION['alternative_root_path'] = $alternative_root_path;
//        //echo 'Alternative Root Path: ' . $alternative_root_path . "<br>";
//    }
//
//    // dashboard_path
//    if (empty($_POST['dashboard_path'])) {
//        //echo 'Dashbaord Path: ' . $dashboard_path . "<br>";
//        $_SESSION['dashboard_path'] = $dashboard_path;
//
//    } elseif (!empty($_POST['dashboard_path'])) {
//        $dashboard_path = ($_POST['dashboard_path']);
//        $_SESSION['dashboard_path'] = $dashboard_path;
//        //echo 'Dashbaord Path: ' . $dashboard_path . "<br>";
//    }
//
//    // main_temporary_folder
//    if (empty($_POST['main_temporary_folder'])) {
//        //echo 'Main Temporary Folder: ' . $main_temporary_folder . "<br>";
//        $_SESSION['main_temporary_folder'] = $main_temporary_folder;
//
//    } elseif (!empty($_POST['main_temporary_folder'])) {
//        $main_temporary_folder = ($_POST['main_temporary_folder']);
//        $_SESSION['main_temporary_folder'] = $main_temporary_folder;
//        //echo 'Main Temporary Folder: ' . $main_temporary_folder . "<br>";
//    }
//
//    // crop_data_path
//    if (empty($_POST['crop_data_path'])) {
//        //echo 'Crop Data Path: ' . $crop_data_path . "<br>";
//        $_SESSION['crop_data_path'] = $crop_data_path;
//
//    } elseif (!empty($_POST['crop_data_path'])) {
//        $crop_data_path = ($_POST['crop_data_path']);
//        $_SESSION['crop_data_path'] = $crop_data_path;
//        //echo 'Crop Data Path: ' . $crop_data_path . "<br>";
//    }
//
//    // crop_data_temporary_folder
//    if (empty($_POST['crop_data_temporary_folder'])) {
//        //echo 'Crop Data Temporary Folder: ' . $crop_data_temporary_folder . "<br>";
//        $_SESSION['crop_data_temporary_folder'] = $crop_data_temporary_folder;
//
//    } elseif (!empty($_POST['crop_data_temporary_folder'])) {
//        $crop_data_temporary_folder = ($_POST['crop_data_temporary_folder']);
//        $_SESSION['crop_data_temporary_folder'] = $crop_data_temporary_folder;
//        //echo 'Crop Data Temporary Folder: ' . $crop_data_temporary_folder . "<br>";
//    }
//
//    // LIBRARIES
//    // jquery_min_js
//    if (empty($_POST['jquery_min_js'])) {
//        //echo 'Jquery min JS: ' . $jquery_min_js . "<br>";
//        $_SESSION['jquery_min_js'] = $jquery_min_js;
//
//    } elseif (!empty($_POST['jquery_min_js'])) {
//        $jquery_min_js = ($_POST['jquery_min_js']);
//        $_SESSION['jquery_min_js'] = $jquery_min_js;
//        //echo 'Jquery min JS: ' . $jquery_min_js . "<br>";
//    }
//
//    // jquery_easing_min_js
//    if (empty($_POST['jquery_easing_min_js'])) {
//        //echo 'Jquery Easing min JS: ' . $jquery_easing_min_js . "<br>";
//        $_SESSION['jquery_easing_min_js'] = $jquery_easing_min_js;
//
//    } elseif (!empty($_POST['jquery_easing_min_js'])) {
//        $jquery_easing_min_js = ($_POST['jquery_easing_min_js']);
//        $_SESSION['jquery_easing_min_js'] = $jquery_easing_min_js;
//        //echo 'Jquery Easing min JS: ' . $jquery_easing_min_js . "<br>";
//    }
//
//    // leaflet_js
//    if (empty($_POST['leaflet_js'])) {
//        //echo 'Leaflet JS: ' . $leaflet_js . "<br>";
//        $_SESSION['leaflet_js'] = $leaflet_js;
//
//    } elseif (!empty($_POST['leaflet_js'])) {
//        $leaflet_js = ($_POST['leaflet_js']);
//        $_SESSION['leaflet_js'] = $leaflet_js;
//        //echo 'Leaflet JS: ' . $leaflet_js . "<br>";
//    }
//
//    // leaflet_css
//    if (empty($_POST['leaflet_css'])) {
//        //echo 'Leaflet CSS: ' . $leaflet_css . "<br>";
//        $_SESSION['leaflet_css'] = $leaflet_css;
//
//    } elseif (!empty($_POST['leaflet_css'])) {
//        $leaflet_css = ($_POST['leaflet_css']);
//        $_SESSION['leaflet_css'] = $leaflet_css;
//        //echo 'Leaflet CSS: ' . $leaflet_css . "<br>";
//    }
//
//    // bootstrap_bundle_min_js
//    if (empty($_POST['bootstrap_bundle_min_js'])) {
//        //echo 'Bootstrap Bundle min JS: ' . $bootstrap_bundle_min_js . "<br>";
//        $_SESSION['bootstrap_bundle_min_js'] = $bootstrap_bundle_min_js;
//
//    } elseif (!empty($_POST['bootstrap_bundle_min_js'])) {
//        $bootstrap_bundle_min_js = ($_POST['bootstrap_bundle_min_js']);
//        $_SESSION['bootstrap_bundle_min_js'] = $bootstrap_bundle_min_js;
//        //echo 'Bootstrap Bundle min JS: ' . $bootstrap_bundle_min_js . "<br>";
//    }
//
//    // php_mailer_autoload
//    if (empty($_POST['php_mailer_autoload'])) {
//        //echo 'PHP Mailer Autoload: ' . $php_mailer_autoload . "<br>";
//        $_SESSION['php_mailer_autoload'] = $php_mailer_autoload;
//
//    } elseif (!empty($_POST['php_mailer_autoload'])) {
//        $php_mailer_autoload = ($_POST['php_mailer_autoload']);
//        $_SESSION['php_mailer_autoload'] = $php_mailer_autoload;
//        //echo 'PHP Mailer Autoload: ' . $php_mailer_autoload . "<br>";
//    }
//
//    // cpu_number
//    if (empty($_POST['cpu_number'])) {
//        //echo 'CPU Number: ' . $cpu_number . "<br>";
//        $_SESSION['cpu_number'] = $cpu_number;
//
//    } elseif (!empty($_POST['cpu_number'])) {
//        $cpu_number = ($_POST['cpu_number']);
//        $_SESSION['cpu_number'] = $cpu_number;
//        //echo 'CPU Number: ' . $cpu_number . "<br>";
//    }
//
//    // admin_email
//    if (empty($_POST['admin_email'])) {
//        //echo 'CPU Number: ' . $cpu_number . "<br>";
//        $_SESSION['admin_email'] = $admin_email;
//
//    } elseif (!empty($_POST['admin_email'])) {
//        $admin_email = ($_POST['admin_email']);
//        $_SESSION['admin_email'] = $admin_email;
//        //echo 'CPU Number: ' . $cpu_number . "<br>";
//    }
//}
//// Create an input box for it
////$_SESSION['admin_email'] = $admin_email;
//?>
<!---->
<!---->
