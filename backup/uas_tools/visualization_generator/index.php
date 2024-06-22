<?php
//phpinfo();
// File containing System Variables
define("LOCAL_PATH_ROOT", $_SERVER["DOCUMENT_ROOT"]);
require LOCAL_PATH_ROOT . '/uas_tools/system_management/centralized_management.php';

// To check if User has the role required to access the page
//require_once("Resources/PHP/SetDBConnection.php");
require_once("../../resources/database/SetDBConnection.php");
//require_once("../system_management/centralized.php");

$mysqli = SetDBConnection();

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
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name=”viewport” content=”width=device-width, initial-scale=1″>
    <!--    <title>Visualization Page Generator</title>-->
    <title>Visualization Generator</title>

    <!-- Google Fonts -->
    <!-- <link href="https://fonts.googleapis.com/css?family=Roboto+Condensed" rel="stylesheet"> -->
    <link href="<?php echo $header_location; ?>/libraries/css/Roboto+Condensed.css" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" type="text/css" href="/uas_tools/upload_product/Resources/css/bootstrap.min.css">
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
    <link rel="stylesheet" href="Resources/JS/Leaflet/leaflet.css" />
    <script src="Resources/JS/Leaflet/leaflet.js"></script>
    <link rel="stylesheet" href="Resources/JS/ControlGeocoder/Control.Geocoder.css" />
    <script src="Resources/JS/ControlGeocoder/Control.Geocoder.js"></script>

    <style>
        #uploaded-list-container {
            width: 93%;
        }

        #warning {
            width: 100%;
            margin-top: 10px;
            text-align: center;
            color: red;
            display: none;
        }

        .project {
            margin: 0px 0px 0px 0px;
            padding: 25px 35px;
            border-radius: 15px;
            background: #f6f7f9;
        }

        .form-control-1 {
            background: #ededed;
            box-shadow: inset 0px 0px 5px 0px rgb(0 0 0 / 5%);
            margin: 0px 0px 0px 0px;
            padding: 0px 10px 0px 15px;
            font-weight: normal;
            font-size: 18px;
            color: #000000;
            display: block;
            background-color: #ededed;
            line-height: 55px;
            border-radius: 5px;
            border: none;
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

        #project_chosen {
            width: 100% !important;
        }

    </style>

</head>


<body>
    <div id="processing"></div>
    <div class="container py-3">
        <div class="project">
            <!--<form>-->
            <!--                <h2>Visualization Generator</h2>-->
            <!--                <br>-->

            <div class="row align-items-center pb-5">
                <div class="col-lg-6 col-7">
                    <h3>Visualization Pages</h3>
                </div>
                <div class="col-lg-6 col-5 text-right">
                    <input type="button" class="btnNew" value="Add New" onclick="AddPage(); return false;">
                </div>
            </div>

            <div id="page-list-wrapper" class="table-responsive"></div>
        </div>

        <div id="page-info" style="display:none">
            <div class="project" style="margin-top: 25px;">
                <h3>Project</h3>
                <div class="row">
                    <div class="col-md-12">
                        <label>Project</label>
                        <select id="project" class="form-control"></select>
                    </div>
                    <div class="col-md-12">
                        <p class="text-center my-3">
                            Center Lat: <span type="text" id="lat" class="normal"></span>,
                            Center Lng: <span type="text" id="lng" class="normal"></span>,
                            Default Zoom: <span type="text" id="zoom" class="normal"></span>,
                            Min Zoom: <span type="text" id="min-zoom" class="normal"></span>,
                            Max Zoom: <span type="text" id="max-zoom" class="normal"></span>
                        </p>
                    </div>
                    <div class="col-md-12">
                        <input type="button" class="btnNew" value="Add Group" onclick="AddGroup(); return false;">
                    </div>
                </div>
            </div>
            <div class="project" style="margin-top: 25px;">
                <h3>Groups</h3>
                <div id="group-wrapper">
                    <div id="groups">
                        <ul id="group-headers">
                        </ul>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="form-group row">
                            <label for="name" class="col-sm-1 col-form-label">Name</label>
                            <div class="col-sm-9">
                                <input type="text" id="name" class="form-control">
                            </div>
                            <div class="col-sm-2">
                                <input type="button" class="btnNew mb-2 btn-block" value="Generate" onclick="Generate(); return false;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="result" style="padding-top: 30px; display:none">
                <h3>Result</h3>
                <div class="label">Link</div>
                <div class="input"><input type="text" id="result-link" class="large" readonly></div>
                <div class="input"><input type="button" class="button" style="margin: 0 0 20px 10px" value="View" onclick="View(); return false;"></div>
            </div>
            <div style="clear:both"></div>
        </div>

        <div id="dialog-review" title="Apply Result" style="display:none">
            <span id="result-text"></span>
        </div>
        <!--</form>-->

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
