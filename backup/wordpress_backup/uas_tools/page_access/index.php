<?php
// File containing System Variables
define("LOCAL_PATH_ROOT", $_SERVER["DOCUMENT_ROOT"]);
require LOCAL_PATH_ROOT . '/uas_tools/system_management/centralized_management.php';

// To check if User has the role required to access the page
require_once("Resources/PHP/SetDBConnection.php");
//require_once("../system_management/centralized.php");

$mysqli = SetDBConnection();

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

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

            <!DOCTYPE html>
            <html lang="en">

            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
                <!--                <title>Set Page Access</title>-->
                <title>Access Management</title>
                <!-- Styles -->
                <link rel="stylesheet" type="text/css" href="Resources/style.css">

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
            <form>

                <h2>Access Management</h2>
                <br>

                <div class="half-width">
                    <fieldset style="width: 90%">
                        <legend>Page List</legend>
                        <input type="text" id="page-search" class="search">
                        <ul id="page-list" class="item-list">
                        </ul>
                    </fieldset>

                </div>
                <div class="half-width">
                    <fieldset style="width: 90%">
                        <legend>Role List</legend>
                        <input type="text" id="group-search" class="search">
                        <ul id="group-list" class="item-list">
                        </ul>
                    </fieldset>

                </div>
                <div style="clear:both"></div>
                <div style="width: 98%; text-align: center; margin-top: 20px">
                    <input id="apply" type="button" class="button" value="Apply" onclick="Apply(); return false;"/>
                </div>
            </form>
            </body>
            </html>

            <?php
        } else {
            $memberOf = (implode("; ", $user_role_array));
            ?>
            <!DOCTYPE>
            <html lang="html">
            <head>
                <title><?php echo $pageName; ?></title>
            </head>
            <body>
            </br>
            <p>You do not currently have permission to access this tool.</p>
            <p>Please contact admin at
                <a href="mailto:<?= $admin_email ?>?
        &subject=Requesting%20access%20to%20the%20import_crop_data%20tool
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