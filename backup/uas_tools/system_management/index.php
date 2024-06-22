<?php
//phpinfo();
// File containing System Variables
define("LOCAL_PATH_ROOT", $_SERVER["DOCUMENT_ROOT"]);
require LOCAL_PATH_ROOT . '/uas_tools/system_management/centralized_management.php';

require_once("../../resources/database/SetDBConnection.php");

$mysqli = SetDBConnection();

// To check if User has the role required to access the page
//require_once("Resources/PHP/SetDBConnection.php");
//
//$mysqli = SetDBConnection();
//require_once("../../resources/database/SetDBConnection.php");

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$userName = $_SESSION["email"] ?? '';
$userapproved = $_SESSION['admin_approved'] ?? '';

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
    if (!$user_role_array || $userapproved == "Disapproved") {
        echo '<script>alert("You do not have permission to access this page. You will be logout now.")</script>';
        echo "<html>";
        echo "<script>";
        echo "window.top.open('/index.php?logout=true')"; //$_SERVER['HTTP_HOST'] . '/index.php?logout=true'
        echo "</script>";
        echo "</html>";
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
    <meta name=”viewport” content=”width=device-width, initial-scale=1″>
    <title>System Management</title>
    <!-- Styles -->
    <script type="text/javascript" src="Resources/JS/jquery.min.js"></script>
    <script src="Resources/JS/Chosen/chosen.jquery.min.js"></script>
    <link rel="stylesheet" type="text/css" href="/uas_tools/upload_product/Resources/css/bootstrap.min.css">

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

        .project {
            margin: 0px 0px 0px 0px;
            padding: 25px 35px;
            border-radius: 15px;
            background: #f6f7f9;
        }

        input.btnNew {
            margin: 20px 0px 0px 0px;
            padding: 0px 0px 0px 0px;
            font-weight: 500;
            font-size: 17px;
            color: #ffffff;
            display: block;
            background: linear-gradient(#2c539e, #254488);
            width: 108px;
            line-height: 40px;
            border-radius: 5px;
            border: 1px solid #00236f;
            float: right;
        }

    </style>
</head>

<body>

    <script>
        $(function() {
            function adjust(elements, offset, min, max) {
                // initialize parameters
                offset = offset || 0;
                min = min || 0;
                max = max || Infinity;
                elements.each(function() {
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
                        setTimeout(function() {
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
    <div class="container" style="padding-bottom: 1rem!important; padding-top: 1rem!important;">
        <form action="centralized_management.php" method="POST">
            <!--<h2>System Management</h2>-->
            <div class="project">
                <h3>Sites</h3>
                <div class="row">
                    <div class="col-md-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1">Site Name: </span>
                            </div>
                            <input name="header_location" type="text" value="<?= $site_name ?>" class="form-control">
                            <!--old class was adjust-->
                        </div>
                    </div>
                </div>
                <h3 class="mt-2">Paths</h3>
                <!--<p>Root Path: <input name="root_path" type="text" value="/var/www/html/wordpress/" class="adjust">-->

                <div class="row">
                    <div class="col-md-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1">Root Path: </span>
                            </div>
                            <input name="root_path" type="text" value="<?= $root_path ?>" class="form-control">
                            <!--old class was adjust-->
                        </div>
                    </div>
                </div>

                <h3 class="mt-2">Configuration</h3>
                <div class="row">
                    <div class="col-md-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1">Number of Threads Used for Mapping Generation: </span>
                            </div>
                            <input name="cpu_number" type="number" value="<?= $threads_number ?>" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1">Administrator Email: </span>
                            </div>
                            <input name="admin_email" type="email" value="<?= $admin_email ?>" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <input name="submit" type="submit" value="Submit" class="btnNew">
                    </div>
                </div>

                <!-- Added -->

                <h3 class="mt-2">Landing Page</h3>
<!--<p>Root Path: <input name="root_path" type="text" value="/var/www/html/wordpress/" class="adjust">-->

<div class="row">
    <div class="col-md-12">
        <!-- <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon1">Root Path: </span>
            </div>
            <input name="root_path" type="text" value="" class="form-control">
        </div> -->

  <form action="/action_page.php">
    <input type="checkbox" id="login_page" name="login_page" value="login_page">
    <label for="vehicle1"> Login Page</label><br>
    <input type="checkbox" id="map_visualization" name="map_visualization" value="map_visualization">
    <label for="vehicle3"> Map Visualization</label><br><br>
    <input type="submit" value="Submit" class="btnNew">
  </form>
    </div>
</div>

                <!-- Added -->

            </div>
        </form>
    </div>
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
