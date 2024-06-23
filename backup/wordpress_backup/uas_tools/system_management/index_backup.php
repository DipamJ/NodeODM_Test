<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>System Management</title>
    <!-- Styles -->
    <script type="text/javascript" src="Resources/JS/jquery.min.js"></script>
    <script src="Resources/JS/Chosen/chosen.jquery.min.js"></script>
    <link rel="stylesheet" type="text/css" href="Resources/JS/Chosen/chosen.css">
    <script src="Resources/JS/JqueryUI/jquery-ui.min.js"></script>
    <link rel="stylesheet" type="text/css" href="Resources/JS/JqueryUI/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="Resources/style.css">
    <script type="text/javascript" src="Resources/JS/main.js"></script>
    <script src="Resources/JS/FixedTable/fixed_table_rc.js"></script>
    <link rel="stylesheet" type="text/css" href="Resources/JS/FixedTable/fixed_table_rc.css">

    <style>
        .ft_container {
            margin: 0 auto;
        }

        .add-button {
            float: right;
            margin: 10px 0 0 0;
            cursor: pointer;
        }

        #warning {
            width: 100%;
            margin-top: 10px;
            text-align: center;
            color: red;
            display: none;
        }
    </style>
</head>
<body>
<div id="processing"></div>
<form action="centralized.php" method="POST">

    <h2>System Management</h2>
    <br>

    <div>
        <h4>Sites</h4>
        <p>Header Location: <input name="header_location" type="text"
                                   value="<?php echo $_SESSION['header_location']; ?>"></p>
        <p>Add User Link: <input name="add_user_link" type="text"
                                 value="http://basfhub.gdslab.org/uas_tools/user_management/AddUser.php"></p>
        <p>Approve User Link: <input name="approve_user_link" type="text"
                                     value="/uas_tools/user_management/Approval.php"></p>
    </div>
    <br>
    <div>
        <h4>Paths</h4>
        <p>Root Path: <input name="root_path" type="text" value="/var/www/html/wordpress/"></p>
        <p>Alternative Root Path: <input name="alternative_root_path" type="text" value="/var/www/html/"></p>
        <p>Dashboard Path: <input name="dashboard_path" type="text" value="user_management/dashboard.php"></p>
        <p>Main Temporary Folder: <input name="main_temporary_folder" type="text"
                                         value="http://basfhub.gdslab.org/temp/"></p>
        <p>Crop Data Path: <input name="crop_data_path" type="text" value="/var/www/html/temp/CropData/"></p>
        <p>Crop Data Temporary Folder: <input name="crop_data_temporary_folder" type="text"
                                              value="http://basfhub.gdslab.org/temp/CropData/"></p>
    </div>
    <br>
    <div>
        <h4>Libraries - Jquery</h4>
        <p>Jquery min JS: <input name="jquery_min_js" type="text"
                                 value="user_management/assets/vendor/jquery/jquery.min.js"></p>
        <p>Jquery Easing min JS: <input name="jquery_easing_min_js" type="text"
                                        value="user_management/assets/vendor/jquery-easing/jquery.easing.min.js"></p>
    </div>
    <br>
    <div>
        <h4>Libraries - Leaflet</h4>
        <p>Leaflet JS: <input name="leaflet_js" type="text" value="basfhub.gdslab.org/js/leaflet.js"></p>
        <p>Leaflet CSS: <input name="leaflet_css" type="text" value="basfhub.gdslab.org/css/leaflet.css"></p>
    </div>
    <br>
    <div>
        <h4>Libraries - Bootstrap</h4>
        <p>Bootstrap Bundle min JS: <input name="bootstrap_bundle_min_js" type="text"
                                           value="user_management/assets/vendor/bootstrap/js/bootstrap.bundle.min.js">
        </p>
    </div>
    <br>
    <div>
        <h4>Libraries - PHP Mailer</h4>
        <p>PHP Mailer Autoload: <input name="php_mailer_autoload" type="text"
                                       value="../../../../multi_users/PHPMailer/PHPMailerAutoload.php"></p>
    </div>
    <br>
    <div>
        <h4>Configuration</h4>
        <p>CPU Number: <input name="cpu_number" type="number" value="16"></p>
    </div>
    <br>

    <div style="text-align:center ; border:none">
        <input name="submit" type="submit" value="Submit">
    </div>

</form>

</body>
</html>