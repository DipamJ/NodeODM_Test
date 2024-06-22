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

<!DOCTYPE>
<html lang="html">

<head>
    <meta charset="utf-8" name=”viewport” content=”width=device-width, initial-scale=1″>
    <title>Upload Vector Data Product</title>

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
    <script type="text/javascript" src="Resources/JS/resumable.js"></script>
    <link rel="stylesheet" type="text/css" href="Resources/style.css">
    <script type="text/javascript" src="Resources/JS/main.js"></script>
    <script type="text/javascript" src="Resources/JS/spark-md5.min.js"></script>
    <script src="Resources/JS/FixedTable/fixed_table_rc.js"></script>
    <link rel="stylesheet" type="text/css" href="Resources/JS/FixedTable/fixed_table_rc.css">
    <link rel="stylesheet" href="Resources/JS/Leaflet/leaflet.css" />
    <script src="Resources/JS/Leaflet/leaflet.js"></script>
    <link rel="stylesheet" href="Resources/JS/ControlGeocoder/Control.Geocoder.css" />
    <script src="Resources/JS/ControlGeocoder/Control.Geocoder.js"></script>

    <link rel="stylesheet" href="Resources/JS/Lightbox/css/lightbox.min.css">
    <script src="Resources/JS/Lightbox/js/lightbox-plus-jquery.min.js"></script>

    <!-- <style>

                    #uploaded-list-container{
                        width: 93%;
                    }

                    #warning{
                        width:100%;
                        margin-top:10px;
                        text-align:center;
                        color: red;
                        display: none;
                    }

                    .redText
                    {
                        color:red;
                        font-weight:bold;
                    }
                    .blackText
                    {
                        color:black;
                    }
                </style> -->

    <style>
        .project {
            margin: 0px 0px 0px 0px;
            padding: 25px 35px;
            border-radius: 15px;
            background: #f6f7f9;
        }

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

        .redText {
            color: red;
            font-weight: bold;
        }

        .blackText {
            color: black;
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
            width: 100%;
            line-height: 55px;
            border-radius: 5px;
            border: none;
        }

        .ft_container,
        .ft_rwrapper,
        .ft_scroller {
            width: 100% !important;
        }

    </style>
</head>

<body>
    <input type="hidden" id="user-name" value="<?php echo $userName; ?>" />
    <input type="hidden" id="roles" value="<?= implode(",", $roles); ?>" />
    <input type="hidden" id="groups" value="<?= implode(",", $groups); ?>" />
    <div id="loading"></div>
    <div class="container py-3">
        <!-- <form > -->
        <!--                <h2>Upload Vector Data Product</h2>-->
        <!--                <br>-->
        <div id="upload">
            <div class="project">
                <h3>Select Flight</h3>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Project</label>
                            <select id="project" class="form-control"></select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Date</label>
                            <input type="text" id="date" class="normaldate form-control" value="09/26/2022">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>EPSG</label>
                            <input type="text" id="epsg" class="form-control" value="32614">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Type</label>
                            <select id="product-type" class="form-control"></select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <input id="upload-button" type='button' class='btnNew' value="Upload" onclick="CreateResumableInstance(''); return false;" />
                    </div>
                </div>

            </div>
            <div class="project" style="margin-top: 25px;">
                <h3>Uploading List</h3>

                <div id="unfinished-files" style="text-align:center; width: 1100px; margin: 0 auto">
                </div>
                <div style="clear:both"></div>
                <div id="upload-files" style="text-align:center;  width: 1100px; margin: 0 auto">
                </div>
                <div id="resumable-list" style="display:none"></div>
            </div>

            <div class="project" style="margin-top: 25px;">
                <h3>Uploaded List</h3>
                <div id="product-wrapper" class="table-responsive" style="height: fit-content;">
                </div>
            </div>
        </div>

        <div id="dialog-tms" title="TMS Path" style="display:none">
            <textarea id="tms-path" rows="3" cols="50" readonly></textarea>
            <div style="text-align:center">
                <input type="button" class="button" value="Copy TMS" onclick="CopyToClipBoard('tms'); return false;" />
                <input type="button" class="button" value="Preview" onclick="Preview(); return false;" />
            </div>
        </div>


        <div id="dialog-download" title="Download Link / Local Path" style="display:none">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Download Link</label>
                        <textarea id="download-link" rows="4" cols="50" readonly class="form-control"></textarea>
                    </div>
                </div>
                <div class="col-md-12">
                    <input type="button" class="btnNew" value="Copy Link" onclick="CopyToClipBoard('link'); return false;" />
                    <input type="button" class="btnNew mr-2" value="Download" onclick="Download(); return false;" />
                </div>
            <hr>
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Local Path</label>
                        <textarea id="local-path" rows="3" cols="50" readonly class="form-control"></textarea>
                    </div>
                </div>
                <div class="col-md-12">
                <input type="button" class="btnNew" value="Copy Path" onclick="CopyToClipBoard('path'); return false;" />
            </div>
            </div>
        </div>
        <!-- </form> -->
        <div id="dialog-confirm" title="Cancel the upload?" style="display:none">
            <p><span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span>The upload will be cancelled. Are you sure?</p>
        </div>
        <div id="dialog-different-file" title="The files are diffrent." style="display:none">
            <p><span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span>Please select the correct file to resume the upload.</p>
        </div>
    </div>
</body>

</html>

<?php
        } else {
            $memberOf = (implode("; ", $user_role_array)); ?>
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
        &subject=Requesting%20access%20to%20the%20data_visualization%20tool
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
