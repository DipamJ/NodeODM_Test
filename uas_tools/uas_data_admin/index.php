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
    <!--                <title>UAS Data Admin</title>-->
    <title>Data Administration</title>

    <link rel="stylesheet" type="text/css" href="Resources/style.css">
    <link rel="stylesheet" href="Resources/bootstrap.min.css">
    <script src="Resources/JS/jquery.min.js" type="text/javascript"></script>
    <script src="Resources/JS/main.js" type="text/javascript"></script>
    <script src="Resources/JS/Chosen/chosen.jquery.min.js"></script>
    <link rel="stylesheet" type="text/css" href="Resources/JS/Chosen/chosen.css">

    <script src="Resources/JS/JqueryUI/jquery-ui.min.js"></script>
    <link rel="stylesheet" type="text/css" href="Resources/JS/JqueryUI/jquery-ui.css">

    <script src="Resources/JS/FixedTable/fixed_table_rc.js"></script>
    <link rel="stylesheet" type="text/css" href="Resources/JS/FixedTable/fixed_table_rc.css">

    <script src="Resources/JS/jquery.alphanum.js"></script>

    <style>
        .add-button {
            float: right;
            margin: 10px 0 0 0;
        }

        .ft_container {
            margin: 0 auto;
            z-index: 1;
        }

        .ft_rel_container {
            z-index: 1;
        }

        .ui-widget-header {
            border: none !important;
            background: none !important;
        }

        .ui-widget-content {
            border: none !important;
        }

        .ui-tabs .ui-tabs-nav {
            padding: .2em 2.5em 0 !important;
        }

        .ui-tabs .ui-tabs-panel {
            padding: 0em 1.4em !important;
        }

        .ui-tabs .ui-tabs-nav li {
            margin: 1px 0.5em 0 0 !important;
            padding: 4px !important;
        }

        .ui-state-default,
        .ui-widget-content .ui-state-default,
        .ui-widget-header .ui-state-default {
            /*background: #f9f9f9;*/
            border: none !important;
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

        .input-group-text {
            padding: 0.175rem 0.75rem !important;
        }

        .searcher {
            background: white;
            box-shadow: none;
            border: 1px solid #ced4da;
        }

        .ft_container,
        .ft_rwrapper,
        .ft_scroller {
            width: 100% !important;
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

        #raw_data_project_chosen,
        #data_product_project_chosen {
            width: 100% !important;
        }

        thead>tr {
            height: 30px;
        }

        table thead tr th {
            border: none !important;
        }

        .image-button {
            border: none !important;
            width: auto !important;
            height: auto !important;
            padding: 6px !important;
            /*            background: #d9534f !important;*/
            border-radius: none !important;
        }

    </style>
</head>

<body>
    <div id="loading"></div>
    <div class="container py-3">
        <!--<form id='manage'>-->
        <!--        <h1 class="font-weight-bold text-center py-4">Data Administration</h1>-->
        <div id="tabs">
            <ul>
                <li><a href="#project">Project</a></li>
                <li><a href="#crop">Crop</a></li>
                <li><a href="#platform">Platform</a></li>
                <li><a href="#sensor">Sensor</a></li>
                <li><a href="#flight">Flight</a></li>
                <li><a href="#product-type">Product Type</a></li>
                <li><a href="#raw-data">Raw Data</a></li>
                <li><a href="#data-product">Data Product</a></li>
            </ul>
            <div id="project">
                <div class="project">
                    <h3>Add Project</h3>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" id="project-name" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Crop</label>
                                <select id="crop-type" class="form-control"></select>
                                <!--select-large -->
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Planting Date</label>
                                <input type="text" id="planting-date" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Harvest Date</label>
                                <input type="text" id="harvest-date" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Description</label>
                                <textarea rows="4" cols="75" class="form-control" maxlength="3000" id="description"></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Center Lat</label>
                                <input type="text" id="center-lat" class="coordinate form-control" value=''>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Center Long</label>
                                <input type="text" id="center-lng" class="coordinate form-control" value=''>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Min Zoom</label>
                                <input type="text" class="form-control" id="min-zoom" value="">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Max Zoom</label>
                                <!--small zoom-->
                                <input type="text" class="form-control" id="max-zoom" value="">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Default Zoom</label>
                                <!--small zoom-->
                                <input type="text" class="form-control" id="default-zoom" value="">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Visualization Page</label>
                                <textarea class="form-control" rows="2" cols="75" style="width: 100%" maxlength="3000" id="visualization"></textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <input id="add-project" class="btnNew" type="button" value="Add" onclick="Add('project'); return false;" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="project" style="margin-top: 25px;">
                    <h3>Project List</h3>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Filter (Name)</span>
                                </div>
                                <input id="project-search" type="text" class="form-control searcher">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div id="project-wrapper" class="table-responsive"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="platform">
                <div class="project">
                    <h3>Add Platform</h3>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" id="platform-name" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <input id="add-platform" class="btnNew" type="button" value="Add" onclick="Add('platform'); return false;" />
                        </div>
                    </div>
                </div>
                <div class="project" style="margin-top: 25px;">
                    <h3>Platform List</h3>
                    <div id="platform-wrapper"></div>
                </div>
            </div>

            <div id="sensor">
                <div class="project">
                    <h3>Add Sensor</h3>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" id="sensor-name" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <input id="add-sensor" class="btnNew" type="button" value="Add" onclick="Add('sensor'); return false;" />
                        </div>
                    </div>
                </div>
                <div class="project" style="margin-top: 25px;">
                    <h3>Sensor List</h3>
                    <div id="sensor-wrapper"></div>
                </div>
            </div>

            <div id="flight">
                <div class="project">
                    <h3>Search</h3>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Project</label>
                                <select id="flight-project" class="form-control"></select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Platform</label>
                                <select id="flight-platform" class="form-control">
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Sensor</label>
                                <select id="flight-sensor" class="form-control">
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <input id="search-flight" class="btnNew" type="button" value="Search" onclick="GetList('flight'); return false;" />
                        </div>
                    </div>
                </div>
                <div class="project" style="margin-top: 25px;">
                    <h3>Add Flight</h3>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" id="flight-name" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group"><label>Date</label>
                                <input type="text" id="flight-date" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group"><label>Flight Altitude</label>
                                <input type="text" id="flight-altitude" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group"><label>Forward Overlap</label>
                                <input type="text" id="flight-forward" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Side Overlap</label>
                                <input type="text" id="flight-side" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <input id="add-flight" class="btnNew" type="button" value="Add" onclick="Add('flight'); return false;" />
                        </div>
                    </div>
                </div>
                <div class="project" style="margin-top: 25px;">
                    <h3>Flight List</h3>
                    <div id="flight-wrapper"></div>
                </div>
            </div>

            <!--                                THIS PART NEEDS TO BE FIXED-->
            <div id="product-type">
                <div class="project">
                    <h3>Add Product Type</h3>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" id="type-name" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Type</label>
                                <select id="product_type_select" class="form-control">
                                    <option value="R">R</option>
                                    <option value="V">V</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <input id="add-type" class="btnNew" type="button" value="Add" onclick="Add('type'); return false;" />
                            <!--add-sensor should be add-type-->
                        </div>
                    </div>
                </div>

                <div class="project" style="margin-top: 25px;">
                    <h3>Product Type List</h3>
                    <div id="type-wrapper"></div>
                </div>
            </div>

            <div id="crop">
                <div class="project">
                    <h3>Add Crop</h3>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" id="crop-name" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <input id="add-crop" class="btnNew" type="button" value="Add" onclick="Add('crop'); return false;" />
                        </div>
                    </div>
                </div>
                <div class="project" style="margin-top: 25px;">
                    <h3>Crop List</h3>
                    <div id="crop-wrapper"></div>
                </div>
            </div>

            <div id="raw-data">
                <div class="project">
                    <h3>Search</h3>
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label>Project</label>
                                <select id="raw-data-project" class="form-control"></select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Platform</label>
                                <select id="raw-data-platform" class="form-control"></select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Sensor</label>
                                <select id="raw-data-sensor" class="form-control"></select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <input type="button" class="btnNew" value="Search" onclick="Search('raw'); return false;" />
                        </div>
                    </div>
                </div>

                <div class="project" style="margin-top: 25px;">
                    <h3>Unfinished List</h3>
                    <div id="unfinished-raw-list-wrapper" style="text-align:center;"></div>
                </div>

                <div class="project" style="margin-top: 25px;">
                    <h3>Finished List</h3>
                    <div id="finished-raw-list-wrapper"></div>
                </div>
            </div>

            <div id="data-product">
                <div class="project">
                    <h3>Search</h3>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Project</label>
                                <select id="data-product-project" class="form-control">
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Platform</label>
                                <select id="data-product-platform" class="form-control"></select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Sensor</label>
                                <select id="data-product-sensor" class="form-control"></select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Type</label>
                                <select id="data-product-type" class="form-control"></select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mt-3">
                            <input type="button" class="btnNew" value="Search" onclick="Search('product'); return false;" />
                        </div>
                    </div>
                </div>

                <div class="project" style="margin-top: 25px;">
                    <h3>Unfinished List</h3>
                    <div id="unfinished-product-list-wrapper" style="text-align:center;"></div>
                </div>
                <div class="project" style="margin-top: 25px;">
                    <h3>Finished List</h3>
                    <div id="finished-product-list-wrapper"></div>
                </div>
                <div style="clear:both"></div>
                <br>

                <div id="dialog-tms" title="TMS Path" style="display:none">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <textarea id="tms-path" rows="5" cols="50" readonly class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <input type="button" class="button btnNew" value="Copy TMS" onclick="CopyToClipBoard('tms'); return false;" />
                        <input type="button" class="button btnNew mr-2" value="Preview" onclick="Preview(); return false;" />
                        </div>
                    </div>
                </div>
                <div id="dialog-download" title="Download Link / Local Path" style="display:none">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Download Link</label>
                                <textarea id="download-link" rows="3" cols="50" readonly class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <input type="button" class="button btnNew" value="Copy Link" onclick="CopyToClipBoard('link'); return false;" />
                            <input type="button" class="button btnNew mr-2" value="Download" onclick="Download(); return false;" />
                        </div>
                        <hr>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Local Path</label>
                                <textarea id="local-path" rows="3" cols="50" class="form-control" readonly></textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <input type="button" class="button btnNew" value="Copy Path" onclick="CopyToClipBoard('path'); return false;" />
                        </div>
                    </div>
                </div>
            </div>
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
