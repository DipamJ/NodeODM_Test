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

//echo $userName;
//echo $userapproved;

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

        //echo $pageName;

        $sql1 = "SELECT * FROM page_access WHERE Page = '$pageName'";
        $allowedGroups = array();
        if ($result1 = mysqli_query($mysqli, $sql1)) {
            if ($row1 = mysqli_fetch_assoc($result1)) {
                $allowedGroups = explode(";", $row1["Page_Groups"]);
                $accessGroupsStr = $row1["Page_Groups"];
            }
        }

        //print_r($user_role_array);
        //print_r($allowedGroups);

        $intersect = array_intersect($user_role_array, $allowedGroups);

        if (sizeof($intersect) > 0) {// if match found
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name=”viewport” content=”width=device-width, initial-scale=1″>
    <!--			<title>Upload Data Product</title>-->
    <title>Upload Product</title>

    <!-- Google Fonts -->
    <!--                <link href="https://fonts.googleapis.com/css?family=Roboto+Condensed" rel="stylesheet">-->

    <!-- Styles -->
    <link rel="stylesheet" type="text/css" href="Resources/style.css">
    <!--    <link rel="stylesheet" type="text/css" href="Resources/bootstrap.min.css">-->
    <link rel="stylesheet" href="Resources/css/bootstrap.min.css">
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
    <style>
        #unfinished-files {
            width: 100% !important;
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

        #project_chosen {
            width: 100% !important;
        }

        .ft_container,
        .ft_rwrapper,
        .ft_scroller {
            width: 100% !important;
        }

        p {
            font-size: 18px !important;
        }

        input.btnNew {
            float: right;
        }

    </style>

</head>


<body>
    <!--			<input type="hidden" id="user-name" value="--><?php //echo $userName;
            ?>
    <!--" />-->
    <!--			<input type="hidden" id="roles" value="-->
    <? //= implode(",",$roles);
            ?>
    <!--" />-->
    <!--			<input type="hidden" id="groups" value="-->
    <? //= implode(",",$groups);
            ?>
    <!--" />-->
    <div id="loading"></div>
    <div class="container py-3">
        <!--                <h2>Upload Product</h2>-->
        <!--                <br>-->
        <div id="upload">
            <div class="">
                <!--main-row-->
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
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Date</label>
                                <select id="date" class="form-control"></select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Flight</label>
                                <select id="flight" class="form-control"></select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label>&nbsp;</label>
                            <input class="btnNew btn-block" type="button" value="Add Flight" style="margin:0;" onclick="ShowAddFlight(); return false;" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>EPSG</label>
                                <input type="text" id="epsg" class="form-control" value="32614">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Type</label>
                                <select id="product-type" class="form-control"></select>
                            </div>
                        </div>
                        <div class="col-md-12" id="bands" style="display:none">
                            <label>Bands:</label>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">B1</span>
                                        </div>
                                        <input type="text" id="b1" class="form-control" value="4">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">B2</span>
                                        </div>
                                        <input type="text" id="b2" class="form-control" value="2">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">B3</span>
                                        </div>
                                        <input type="text" id="b3" class="form-control" value="1">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Alpha</span>
                                        </div>
                                        <input type="text" id="alpha" class="form-control" value="5">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Platform</label>
                                <select id="platform" class="form-control"></select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Sensor</label>
                                <select id="sensor" class="form-control"></select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Min Zoom</label>
                                <input type="text" id="min-zoom" class="form-control" value="17">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Max Zoom</label>
                                <input type="text" id="max-zoom" class="form-control" value="25">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Default Zoom</label>
                                <input type="text" id="zoom" class="form-control" value="19">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="text-center">
                                <p>
                                    Flight Attitude: <span type="text" id="altitude" class="normal"></span>, Forward Overlap: <span type="text" id="forward" class="normal"></span>, Side Overlap: <span type="text" id="side" class="normal"></span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="float-right">
                                <input id="upload-button" type='button' class='btnNew' value="Upload" onclick="CreateResumableInstance(''); return false;" />
                            </div>
                        </div>
                    </div>
                    <!--
                    <div style="text-align:center">
                        <img id="upload-button" src="Resources/Images/upload.png" alt="Upload" class="upload-button" title="Upload" style="cursor:pointer" onclick="CreateResumableInstance()">
                    </div>
                    <div style="clear:both; margin-bottom: 10px"></div>
                    -->
                </div>

                <div class="project" style="margin-top: 25px;">
                    <h3>Uploading List</h3>
                    <!--<div class='table-responsive'>
                        <table class='table table-bordered bg-white'>
                            <tbody>
                                <tr>
                                    <td class='text-center'>IMG_0805.heic</td>
                                    <td>Unfinished</td>
                                    <td>
                                        <div class='progress-bar'>
                                           <img class='progress-bar-image' src='Resources/Images/ProgressBar.jpg'>
                                            <div id='' class='progress-text'>189705KB / 4742626KB (4%)</div>
                                        </div>
                                    </td>
                                    <td class='td-actions text-right'>
                                        <img style='cursor:pointer;' src='Resources/Images/upload.png' alt='Pause' title='Resume' height='24' width='24'>
                                        <img style='cursor:pointer;' src='Resources/Images/remove.png' alt='Cancel' title='Cancel' height='24' width='24'>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>-->
                    <div id="unfinished-files" style="text-align:center; width: 1100px; margin: 0 auto">
                    </div>
                    <div style="clear:both"></div>
                    <div id="upload-files" style="text-align:center;  width: 1100px; margin: 0 auto">
                    </div>
                </div>
                <div class="project" style="margin-top: 25px;">
                    <div style="clear:both; margin: 20px 0"></div>
                    <div id="resumable-list" style="display:none"></div>
                    <h3>Uploaded List</h3>
                    <div id="product-wrapper" class="table-responsive"></div>
                </div>
            </div>
        </div>
        <div class="">
            <div id="add-flight" style="display:none">
                <div class="project" style="">
                    <h3>Add Flight</h3>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Date</label>
                                <input type="text" id="flight-date" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Flight Altitude</label>
                                <input type="text" id="flight-altitude" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Forward Overlap</label>
                                <input type="text" id="flight-forward" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Side Overlap</label>
                                <input type="text" id="flight-side" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" id="flight-name" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Project</label>
                                <input type="text" id="flight-project" disabled class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Platform</label>
                                <input type="text" id="flight-platform" disabled class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Sensor</label>
                                <input type="text" id="flight-sensor" disabled class="form-control">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <input type="button" class="btnNew" value="Add" onclick="AddFlight(); return false;" />
                            <input type="button" class="btnNew" value="Cancel" onclick="ShowUpload(); return false;" />
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div id="dialog-tms" title="TMS Path" style="display:none">
           <div class="row">
               <div class="col-md-12">
                  <div class="form-group">
                   <textarea id="tms-path" rows="5" cols="50" readonly class="form-control"></textarea>
               </div>
               </div>
               <div class="col-md-12">
                   <input type="button" class="btnNew" value="Copy TMS" onclick="CopyToClipBoard('tms'); return false;" />
                <input type="button" class="btnNew mr-2" value="Preview" onclick="Preview(); return false;" />
               </div>
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

        <div id="dialog-confirm" title="Cancel the upload?" style="display:none">
            <p><span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span>The upload
                will be cancelled. Are you sure?</p>
        </div>

        <div id="dialog-different-file" title="The files are diffrent." style="display:none">
            <p><span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span>Please select the correct file to resume the upload.</p>
        </div>
    </div>
</body>

</html>

<?php
            // Delete empty directories at path
            //removeEmptyDirs('../../uas_data/uploads/products', false);
            ?>

<?php
//            /**
//             * Remove all empty subdirectories
//             * @param string $dirPath path to base directory
//             * @param bool $deleteBaseDir - Delete also basedir if it is empty
//             */
//            function removeEmptyDirs($dirPath, $deleteBaseDir = false)
//            {
//
//                if (stristr($dirPath, "'")) {
//                    trigger_error('Disallowed character `Single quote` (\') in provided `$dirPath` parameter', E_USER_ERROR);
//                }
//
//                if (substr($dirPath, -1) != '/') {
//                    $dirPath .= '/';
//                }
//
//                $modif = $deleteBaseDir ? '' : '*';
//                exec("find '" . $dirPath . "'" . $modif . " -empty -type d -delete", $out);
//            }
//
//            //$dirPath = 'tt/';
//            $dirPath = '../../uas_data/uploads/products/2016_Weslaco_Tomato/Phantom_4_Pro/RGB';
//
//            removeEmptyDirs($dirPath, true);
            ?>

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
        &subject=Requesting%20access%20to%20the%20upload_product%20tool
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
