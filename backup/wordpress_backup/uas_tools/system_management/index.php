<?php
// File containing System Variables
define("LOCAL_PATH_ROOT", $_SERVER["DOCUMENT_ROOT"]);
require LOCAL_PATH_ROOT . '/uas_tools/system_management/centralized_management.php';

//echo $root_path;

// To check if User has the role required to access the page
require_once("Resources/PHP/SetDBConnection.php");
//require_once("../system_management/centralized.php");

$mysqli = SetDBConnection();

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
//$header_location = $_SESSION['header_location'];
//echo $header_location;

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
    // When user has no role assigned
    if (!$user_role_array) {
        // User is sent to Login Page
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

                    .adjust {
                        transition: width .15s;
                    }
                </style>
            </head>
            <body>

            <script>
                $(function () {
                    function adjust(elements, offset, min, max) {
                        // initialize parameters
                        offset = offset || 0;
                        min = min || 0;
                        max = max || Infinity;
                        elements.each(function () {
                            var element = $(this);
                            // add element to measure pixel length of text
                            var id = btoa(Math.floor(Math.random() * Math.pow(2, 64)));
                            var tag = $('<span id="' + id + '">' + element.val() + '</span>').css({
                                'display': 'none',
                                'font-family': element.css('font-family'),
                                'font-size': element.css('font-size'),
                            }).appendTo('body');

                            // adjust element width on keydown
                            function update() {
                                // give browser time to add current letter
                                setTimeout(function () {
                                    // prevent whitespace from being collapsed
                                    tag.html(element.val().replace(/ /g, '&nbsp'));
                                    // clamp length and prevent text from scrolling
                                    var size = Math.max(min, Math.min(max, tag.width() + offset));
                                    if (size < max)
                                        element.scrollLeft(0);
                                    // apply width to element
                                    element.width(size);
                                }, 0);
                            }

                            update();
                            element.keydown(update);
                        });
                    }

                    // apply to our element
                    adjust($('.adjust'), 10, 100, 500);
                });
            </script>

            <?php
            // ERRORS
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);

            // DB Connection
            //            function SetDBConnection()
            //            {
            //                return mysqli_connect("localhost", "hub_admin", "UasHtp_Rocks^^7", "uas_projects");
            //            }

            // Log Document
            //            function _log($str)
            //            {
            //                // log to the output
            //                $log_str = date('d.m.Y') . ": {$str}\r\n";
            //                echo $log_str;
            //
            //                // log to file
            //                if (($fp = fopen('upload_log.txt', 'a+')) !== false) {
            //                    fputs($fp, $log_str);
            //                    fclose($fp);
            //                }
            //            }

            $conn = SetDBConnection();

            $sql = "SELECT Name, Value FROM system_management WHERE ID = 1";
            $result = mysqli_query($conn, $sql);

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    //echo "Name: " . $row["Name"] . " - Value: " . $row["Value"] . " <br>";
                    $root_path = $row["Value"];
                }
            }

            ///
            $sql = "SELECT Name, Value FROM system_management WHERE ID = 2";
            $result = mysqli_query($conn, $sql);

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    //echo "Name: " . $row["Name"] . " - Value: " . $row["Value"] . " <br>";
                    $site_name = $row["Value"];
                }
            }
            ///
            $sql = "SELECT Name, Value FROM system_management WHERE ID = 3";
            $result = mysqli_query($conn, $sql);

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    //echo "Name: " . $row["Name"] . " - Value: " . $row["Value"] . " <br>";
                    $visualization_pages = $row["Value"];
                }
            }
            ///
            $sql = "SELECT Name, Value FROM system_management WHERE ID = 4";
            $result = mysqli_query($conn, $sql);

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    //echo "Name: " . $row["Name"] . " - Value: " . $row["Value"] . " <br>";
                    $crop_data = $row["Value"];
                }
            }
            ///
            $sql = "SELECT Name, Value FROM system_management WHERE ID = 5";
            $result = mysqli_query($conn, $sql);

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    //echo "Name: " . $row["Name"] . " - Value: " . $row["Value"] . " <br>";
                    $threads_number = $row["Value"];
                }
            }

            ///
            $sql = "SELECT Name, Value FROM system_management WHERE ID = 6";
            $result = mysqli_query($conn, $sql);

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    //echo "Name: " . $row["Name"] . " - Value: " . $row["Value"] . " <br>";
                    $admin_email = $row["Value"];
                }
            }

            //                } else {
            //                    echo "0 results";
            //                }

            mysqli_close($conn);
            ?>

            <div id="processing"></div>
            <form action="centralized_management.php" method="POST">

                <h2>System Management</h2>
                <br>

                <div>
                    <h4>Sites</h4>
                    <p>Site Name: <input name="header_location" type="text" value="<?= $site_name ?>"
                                         class="adjust"></p>
                    <!--        <p>Link to page where users can be added: <input name="add_user_link" type="text"-->
                    <!--                                                         value="http://bhub.gdslab.org/uas_tools/user_management/AddUser.php"-->
                    <!--                                                         class="adjust">-->
                    <!--        </p>-->
                    <!--        <p>Link for user approval: <input name="approve_user_link" type="text"-->
                    <!--                                          value="/uas_tools/user_management/Approval.php" class="adjust"></p>-->
                </div>
                <br>

                <div>
                    <h4>Paths</h4>
                    <!--                    <p>Root Path: <input name="root_path" type="text" value="/var/www/html/wordpress/" class="adjust">-->
                    <p>Root Path: <input name="root_path" type="text" value="<?= $root_path ?>" class="adjust">
                    </p>
                    <!--        <p>Secondary Root Path: <input name="alternative_root_path" type="text" value="/var/www/html/" class="adjust">-->
                    <!--        </p>-->
                    <!--        <p>Dashboard Path: <input name="dashboard_path" type="text" value="user_management/dashboard.php"-->
                    <!--                                  class="adjust"></p>-->
                    <!--                    <p>Folder Containing Visualization Pages: <input name="main_temporary_folder" type="text"-->
                    <!--                                                                     value="-->
                    <? //= $visualization_pages ?><!--"-->
                    <!--                                                                     class="adjust"></p>-->
                    <!--        <p>Crop Data Path: <input name="crop_data_path" type="text" value="/var/www/html/temp/CropData/" class="adjust">-->
                    <!--        </p>-->
                    <!--                    <p>Folder Containing Crop Data: <input name="crop_data_temporary_folder" type="text"-->
                    <!--                                                           value="-->
                    <? //= $crop_data ?><!--" class="adjust">-->
                    <!--                    </p>-->
                </div>
                <br>
                <!--    <div>-->
                <!--        <h4>Libraries - Jquery</h4>-->
                <!--        <p>Jquery min JS Library Location: <input name="jquery_min_js" type="text"-->
                <!--                                                  value="user_management/assets/vendor/jquery/jquery.min.js"-->
                <!--                                                  class="adjust">-->
                <!--        </p>-->
                <!--        <p>Jquery Easing min JS Library Location: <input name="jquery_easing_min_js" type="text"-->
                <!--                                                         value="user_management/assets/vendor/jquery-easing/jquery.easing.min.js"-->
                <!--                                                         class="adjust">-->
                <!--        </p>-->
                <!--    </div>-->
                <!--    <br>-->
                <!--    <div>-->
                <!--        <h4>Libraries - Leaflet</h4>-->
                <!--        <p>Leaflet JS Library Location: <input name="leaflet_js" type="text" value="bhub.gdslab.org/js/leaflet.js"-->
                <!--                                               class="adjust"></p>-->
                <!--        <p>Leaflet CSS Library Location: <input name="leaflet_css" type="text" value="bhub.gdslab.org/css/leaflet.css"-->
                <!--                                                class="adjust">-->
                <!--        </p>-->
                <!--    </div>-->
                <!--    <br>-->
                <!--    <div>-->
                <!--        <h4>Libraries - Bootstrap</h4>-->
                <!--        <p>Bootstrap Bundle min JS Library Location: <input name="bootstrap_bundle_min_js" type="text"-->
                <!--                                                            value="user_management/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"-->
                <!--                                                            class="adjust">-->
                <!--        </p>-->
                <!--    </div>-->
                <!--    <br>-->
                <!--    <div>-->
                <!--        <h4>Libraries - PHP Mailer</h4>-->
                <!--        <p>PHP Mailer Autoload Site Location: <input name="php_mailer_autoload" type="text"-->
                <!--                                                     value="../../../../multi_users/PHPMailer/PHPMailerAutoload.php"-->
                <!--                                                     class="adjust">-->
                <!--        </p>-->
                <!--    </div>-->
                <!--    <br>-->
                <div>
                    <h4>Configuration</h4>
                    <p>Number of Threads Used for Mapping Generation: <input name="cpu_number" type="number"
                                                                             value="<?= $threads_number ?>"
                                                                             class="adjust"></p>
                    <p>Administrator Email: <input name="admin_email" type="email" value="<?= $admin_email ?>"
                                                   class="adjust"></p>
                </div>
                <br>

                <div style="text-align:center ; border:none">
                    <input name="submit" type="submit" value="Submit">
                </div>

            </form>

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