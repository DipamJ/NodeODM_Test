<?php
// File containing System Variables
define("LOCAL_PATH_ROOT", $_SERVER["DOCUMENT_ROOT"]);
require LOCAL_PATH_ROOT . '/uas_tools/system_management/centralized_management.php';

// To check if User has the role required to access the page
require_once("Resources/PHP/SetDBConnection.php");

// Import CommonFunctions
//require_once("Resources/PHP/CommonFunctions.php");

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
        $_SESSION["page"] = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
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
                <!--			<title>Upload Data Product</title>-->
                <title>Upload Product</title>

                <!-- Google Fonts -->
                <!--                <link href="https://fonts.googleapis.com/css?family=Roboto+Condensed" rel="stylesheet">-->

                <!-- Styles -->
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
                <link rel="stylesheet" href="Resources/JS/Leaflet/leaflet.css"/>
                <script src="Resources/JS/Leaflet/leaflet.js"></script>
                <link rel="stylesheet" href="Resources/JS/ControlGeocoder/Control.Geocoder.css"/>
                <script src="Resources/JS/ControlGeocoder/Control.Geocoder.js"></script>

                <link rel="stylesheet" href="Resources/JS/Lightbox/css/lightbox.min.css">
                <script src="Resources/JS/Lightbox/js/lightbox-plus-jquery.min.js"></script>
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
                </style>

            </head>


            <body>
            <!--			<input type="hidden" id="user-name" value="--><?php //echo $userName;
            ?><!--" />-->
            <!--			<input type="hidden" id="roles" value="--><? //= implode(",",$roles);
            ?><!--" />-->
            <!--			<input type="hidden" id="groups" value="--><? //= implode(",",$groups);
            ?><!--" />-->
            <div id="loading"></div>
            <form>
                <!--				<h2>Upload Data Product</h2>-->
                <h2>Upload Product</h2>
                <br>
                <div id="upload">
                    <fieldset>
                        <legend>Select Flight</legend>
                        <div style="text-align:center">
                            <span>Project</span>
                            <select id="project" class="select-large">
                            </select>
                        </div>
                        <div style="clear:both; margin-bottom:5px"></div>
                        <div class="half-width">
                            <div>
                                <div class="label">Date</div>
                                <div class="input"><select id="date"></select></div>
                            </div>

                            <div style="clear:both"></div>
                            <div>
                                <div class="label">Flight</div>
                                <div class="input"><select id="flight"></select></div>
                                <input type="button" value="Add Flight" onclick="ShowAddFlight(); return false;"
                                       style="margin: 5px; float:left"/>
                            </div>

                            <div style="clear:both"></div>
                            <div>
                                <div class="label">EPSG</div>
                                <div class="input"><input type="text" id="epsg" class="normal" value="32614"></div>
                            </div>
                            <div style="clear:both"></div>

                            <div>
                                <div class="label">Type</div>
                                <div class="input"><select id="product-type"></select></div>
                            </div>
                            <div style="clear:both"></div>
                            <div id="bands" style="display:none">
                                <div class="label">Bands:</div>
                                <div class="label label-tiny">B1</div>
                                <div class="input"><input type="text" id="b1" class="tiny" value="4"></div>
                                <div class="label label-tiny">B2</div>
                                <div class="input"><input type="text" id="b2" class="tiny" value="2"></div>
                                <div class="label label-tiny">B3</div>
                                <div class="input"><input type="text" id="b3" class="tiny" value="1"></div>
                                <div class="label" style="width: 45px">Alpha</div>
                                <div class="input"><input type="text" id="alpha" class="tiny" value="5"></div>
                            </div>

                        </div>
                        <div class="half-width">
                            <div>
                                <div class="label">Platform</div>
                                <div class="input"><select id="platform"></select></div>
                            </div>

                            <div style="clear:both"></div>

                            <div>
                                <div class="label">Sensor</div>
                                <div class="input"><select id="sensor"></select></div>
                            </div>
                            <div style="clear:both"></div>
                            <div>
                                <div class="label">Min Zoom</div>
                                <div class="input"><input type="text" id="min-zoom" class="normal" value="17"></div>
                            </div>
                            <div style="clear:both"></div>
                            <div>
                                <div class="label">Max Zoom</div>
                                <div class="input"><input type="text" id="max-zoom" class="normal" value="25"></div>
                            </div>
                            <div style="clear:both"></div>
                            <div>
                                <div class="label">Default Zoom</div>
                                <div class="input"><input type="text" id="zoom" class="normal" value="19"></div>
                            </div>
                        </div>

                        <div style="clear:both"></div>
                        <div class="full-width">
                            <p>Flight Attitude: <span type="text" id="altitude" class="normal"></span>, Forward Overlap:
                                <span type="text" id="forward" class="normal"></span>, Side Overlap: <span type="text"
                                                                                                           id="side"
                                                                                                           class="normal"></span>
                            </p>
                        </div>
                        <div style="clear:both"></div>

                        <input id="upload-button" type='button' class='button right-button' value="Upload"
                               onclick="CreateResumableInstance(''); return false;"/>
                    </fieldset>
                    <div style="clear:both; margin-bottom: 20px"></div>
                    <!--
                    <div style="text-align:center">
                        <img id="upload-button" src="Resources/Images/upload.png" alt="Upload" class="upload-button" title="Upload" style="cursor:pointer" onclick="CreateResumableInstance()">
                    </div>
                    <div style="clear:both; margin-bottom: 10px"></div>
                    -->
                    <fieldset>
                        <legend>Uploading List</legend>
                        <!--
                        <div  style="height:30px; line-height: 30px; text-align:center; width: 1100px; margin: 0 auto">
                            <div style="width: 420px;float: left; border: 1px solid #CCCCCC; background: #c0c0c0;">File Name</div>
                            <div style="width: 150px;float: left; border: 1px solid #CCCCCC; background: #c0c0c0;">Status</div>
                            <div style="width: 400px;float: left; border: 1px solid #CCCCCC; background: #c0c0c0;">Progress</div>
                            <div style="width: 100px;float: left; border: 1px solid #CCCCCC; background: #c0c0c0;">&nbsp;</div>

                        </div>

                        <div style="clear:both"></div>
                        -->
                        <div id="unfinished-files" style="text-align:center; width: 1100px; margin: 0 auto">
                        </div>
                        <div style="clear:both"></div>
                        <div id="upload-files" style="text-align:center;  width: 1100px; margin: 0 auto">
                        </div>
                    </fieldset>

                    <div style="clear:both; margin: 20px 0"></div>
                    <div id="resumable-list" style="display:none"></div>
                    <fieldset>
                        <legend>Uploaded List</legend>
                        <div id="product-wrapper">
                        </div>
                    </fieldset>
                </div>


                <div id="add-flight" style="display:none">
                    <fieldset>
                        <legend>Add Flight</legend>

                        <div class="half-width">
                            <div>
                                <div class="label">Date</div>
                                <div class="input"><input type="text" id="flight-date" class="normal"></div>
                            </div>

                            <div style="clear:both"></div>

                            <div>
                                <div class="label">Flight Altitude</div>
                                <div class="input"><input type="text" id="flight-altitude" class="normal"></div>
                            </div>

                            <div style="clear:both"></div>

                            <div>
                                <div class="label">Forward Overlap</div>
                                <div class="input"><input type="text" id="flight-forward" class="normal"></div>
                            </div>

                            <div style="clear:both"></div>

                            <div>
                                <div class="label">Side Overlap</div>
                                <div class="input"><input type="text" id="flight-side" class="normal"></div>
                                <div style="clear:both"></div>
                            </div>
                        </div>
                        <div class="half-width">
                            <div>
                                <div class="label">Name</div>
                                <div class="input"><input type="text" id="flight-name"></div>
                            </div>
                            <div style="clear:both"></div>
                            <div>
                                <div class="label">Project</div>
                                <div class="input"><input type="text" id="flight-project" disabled></div>
                                <!--
                                <select id="flight-project" disabled>
                                </select>
                                -->
                            </div>

                            <div style="clear:both"></div>

                            <div>
                                <div class="label">Platform</div>
                                <div class="input"><input type="text" id="flight-platform" disabled></div>
                                <!--
                                <select id="flight-platform" disabled>
                                </select>
                                -->
                            </div>

                            <div style="clear:both"></div>

                            <div>
                                <div class="label">Sensor</div>
                                <div class="input"><input type="text" id="flight-sensor" disabled></div>
                                <!--
                                <select id="flight-sensor" disabled>
                                </select>
                                -->
                            </div>
                        </div>
                        <div style="clear:both"></div>
                        <div style="text-align:center; margin-top: 10px">
                            <input type="button" class="button" value="Add" onclick="AddFlight(); return false;"/>
                            <input type="button" class="button" value="Cancel" onclick="ShowUpload(); return false;"/>
                        </div>
                    </fieldset>
                </div>


                <div id="dialog-tms" title="TMS Path" style="display:none">
                    <textarea id="tms-path" rows="3" cols="50" readonly></textarea>
                    <div style="text-align:center">
                        <input type="button" class="button" value="Copy TMS"
                               onclick="CopyToClipBoard('tms'); return false;"/>
                        <input type="button" class="button" value="Preview" onclick="Preview(); return false;"/>
                    </div>
                </div>


                <div id="dialog-download" title="Download Link / Local Path" style="display:none">
                    <p>Download Link</p>
                    <textarea id="download-link" rows="3" cols="50" readonly></textarea>
                    <div style="text-align:center">
                        <input type="button" class="button" value="Copy Link"
                               onclick="CopyToClipBoard('link'); return false;"/>
                        <input type="button" class="button" value="Download" onclick="Download(); return false;"/>
                    </div>
                    <hr>
                    <p>Local Path</p>
                    <textarea id="local-path" rows="3" cols="50" readonly></textarea>
                    <div style="text-align:center">
                        <input type="button" class="button" value="Copy Path"
                               onclick="CopyToClipBoard('path'); return false;"/>
                    </div>
                </div>
            </form>
            <div id="dialog-confirm" title="Cancel the upload?" style="display:none">
                <p><span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span>The upload
                    will be cancelled. Are you sure?</p>
            </div>
            <div id="dialog-different-file" title="The files are diffrent." style="display:none">
                <p><span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span>Please select
                    the correct file to resume the upload.</p>
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
