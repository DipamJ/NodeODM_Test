<?php
// File containing System Variables
define("LOCAL_PATH_ROOT", $_SERVER["DOCUMENT_ROOT"]);
require LOCAL_PATH_ROOT . '/uas_tools/system_management/centralized_management.php';

// To check if User has the role required to access the page
require_once("Resources/PHP/SetDBConnection.php");

$mysqli = SetDBConnection();

// If session hasn't been started, start it
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
                <title>Upload Raw Data</title>

                <!-- Styles -->
                <link rel="stylesheet" type="text/css" href="Resources/style.css">

                <script type="text/javascript" src="Resources/JS/jquery.min.js"></script>
                <script src="Resources/JS/Chosen/chosen.jquery.min.js"></script>
                <link rel="stylesheet" type="text/css" href="Resources/JS/Chosen/chosen.css">

                <script src="Resources/JS/JqueryUI/jquery-ui.min.js"></script>
                <link rel="stylesheet" type="text/css" href="Resources/JS/JqueryUI/jquery-ui.css">
                <script type="text/javascript" src="Resources/JS/resumable.js"></script>
                <script type="text/javascript" src="Resources/JS/main.js"></script>
                <script type="text/javascript" src="Resources/JS/spark-md5.min.js"></script>

                <script src="Resources/JS/FixedTable/fixed_table_rc.js"></script>
                <link rel="stylesheet" type="text/css" href="Resources/JS/FixedTable/fixed_table_rc.css">

                <link rel="stylesheet" href="Resources/JS/Lightbox/css/lightbox.min.css">
                <script src="Resources/JS/Lightbox/js/lightbox-plus-jquery.min.js"></script>

                <style>
                    /*
                    #uploaded-list-container{
                        width: 93%;
                    }
                    */

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
            <input type="hidden" id="user-name" value="<?php echo $userName; ?>"/>
            <div id="loading"></div>
            <form style="z-index:10">
                <h2>Upload Raw Data</h2>
                <br>
                <div id="select-flight">
                    <fieldset>
                        <legend>Select Flight</legend>
                        <div>
                            <div class="label">Project</div>
                            <select id="project" class="select-large">
                            </select>
                        </div>
                        <div style="clear:both; margin-bottom:5px"></div>
                        <div class="half-width">
                            <div>
                                <div class="label">Platform</div>
                                <select id="platform">
                                </select>
                            </div>

                            <div style="clear:both; margin-bottom:5px"></div>

                            <div>
                                <div class="label">Sensor</div>
                                <select id="sensor">
                                </select>
                            </div>

                        </div>

                        <div class="half-width">
                            <div>
                                <div class="label">Date</div>
                                <select id="date">
                                </select>
                            </div>

                            <div style="clear:both; margin-bottom:5px"></div>

                            <div>
                                <div class="label">Flight</div>
                                <select id="flight">
                                </select>
                                <input type="button" value="Add Flight" onclick="ShowAddFlight(); return false;"/>
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

                    </fieldset>
                    <br>
                    <div style="clear:both"></div>

                    <fieldset>
                        <legend>Notification</legend>
                        <div class="full-width">

                            <div class="label">To</div>
                            <div class="input"><input type="text" id="email-to" placeholder="Email Address"></div>
                            <div class="label label-small">CC</div>
                            <div class="input"><input type="text" id="cc-address" placeholder="Email Address"></div>
                            <div style="clear:both"></div>
                            <div class="label">Note</div>
                            <div class="input"><textarea rows="3" cols="75" style="width: 550px" maxlength="1000"
                                                         id="note"></textarea></div>

                        </div>

                    </fieldset>

                    <div style="clear:both; margin-top: 15px"></div>
                    <input id="upload-button" type="button" class="button right-button" value="Upload"
                           onclick="CreateResumableInstance(''); return false;"/>
                    <div style="clear:both"></div>

                    <fieldset>
                        <legend>Uploading List</legend>
                        <div id="unfinished-files" style="text-align:center;">
                        </div>
                        <div style="clear:both"></div>
                        <div id="upload-files" style="text-align:center;"></div>
                        <div id="resumable-list" style="display:none"></div>
                    </fieldset>
                    <div style="clear:both"></div>
                    <br>
                    <fieldset>
                        <legend>Finished List</legend>
                        <div id="finished-list-wrapper">
                        </div>
                    </fieldset>

                </div>
                <div id="add-flight" style="display:none">
                    <fieldset>
                        <legend>Add Flight</legend>
                        <div class="half-width">
                            <div>
                                <div class="label">Name</div>
                                <div class="input"><input type="text" id="flight-name"></div>
                            </div>
                            <div style="clear:both"></div>
                            <div>
                                <div class="label">Project</div>
                                <div class="input"><input type="text" id="flight-project" disabled></div>
                            </div>

                            <div style="clear:both"></div>

                            <div>
                                <div class="label">Platform</div>
                                <div class="input"><input type="text" id="flight-platform" disabled></div>
                            </div>

                            <div style="clear:both"></div>

                            <div>
                                <div class="label">Sensor</div>
                                <div class="input"><input type="text" id="flight-sensor" disabled></div>
                            </div>
                        </div>
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
                        <div style="clear:both"></div>
                        <div style="text-align:center; margin-top: 10px">
                            <input type="button" value="Add" onclick="AddFlight(); return false;"/>
                            <input type="button" value="Cancel" onclick="ShowSelectFlight(); return false;"/>
                        </div>
                    </fieldset>

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
        &subject=Requesting%20access%20to%20the%20upload_raw%20tool
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