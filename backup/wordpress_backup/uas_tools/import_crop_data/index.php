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

            <!DOCTYPE>
            <html lang="en">
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
                <title>Import Crop Data</title>

                <!-- Google Fonts -->
                <link href="https://fonts.googleapis.com/css?family=Roboto+Condensed" rel="stylesheet">

                <!-- Styles -->
                <link rel="stylesheet" type="text/css" href="Resources/style.css">

                <script type="text/javascript" src="Resources/JS/jquery.min.js"></script>
                <script src="Resources/JS/JqueryUI/jquery-ui.min.js"></script>
                <link rel="stylesheet" type="text/css" href="Resources/JS/JqueryUI/jquery-ui.css">
                <script type="text/javascript" src="Resources/JS/main.js"></script>
                <script src="Resources/JS/FixedTable/fixed_table_rc.js"></script>
                <link rel="stylesheet" type="text/css" href="Resources/JS/FixedTable/fixed_table_rc.css">

            </head>


            <body>
            <div id="processing"></div>
            <form>
                <h2>Import Crop Data</h2>
                <br>
                <div id='impoorted-file-info'>
                    <fieldset style="width:93%; padding-top: 30px">
                        <legend>Import File</legend>
                        <!--Link to download User Guide-->
                        <a href="Resources/Files/User_Guide.md" download="User Guide.md">User Guide</a>
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <!--Link to download File Template-->
                        <a href="Resources/Files/File_Template.csv" download="2016_cc_cotton_cc_spring_parking.csv">File
                            Template</a>
                        <div>
                            Select a file: <input type="file" accept=".csv" id="imported-file" style="width:1000px">
                        </div>
                        <div style="clear:both"></div>
                        <span>(File name format: year_location_crop_type_season_sublocation. Ex: 2017_wl_tomato_cc_spring_south)</span>

                        <div style="clear:both; margin-bottom: 10px"></div>
                        <br>
                        <div class="one-third-width">
                            <div>
                                <div class="label">Crop</div>
                                <div class="input"><input type="text" id="crop" disabled></div>
                                <div class="label mandatory" style="width:5px">*</div>
                            </div>
                            <div style="clear:both"></div>
                            <div>
                                <div class="label">Season</div>
                                <div class="input"><input type="text" id="season" disabled></div>
                            </div>
                        </div>

                        <div class="one-third-width">
                            <div>
                                <div class="label">Type</div>
                                <div class="input"><input type="text" id="type" disabled></div>
                                <div class="label mandatory" style="width:5px">*</div>
                            </div>
                            <div style="clear:both"></div>
                            <div>
                                <div class="label">Location</div>
                                <div class="input"><input type="text" id="location" disabled></div>
                                <div class="label mandatory" style="width:5px">*</div>
                            </div>


                        </div>
                        <div class="one-third-width">
                            <div>
                                <div class="label">Year</div>
                                <div class="input"><input type="text" id="year" disabled></div>
                                <div class="label mandatory" style="width:5px">*</div>
                            </div>
                            <div style="clear:both"></div>
                            <div>
                                <div class="label">Sub-location</div>
                                <div class="input"><input type="text" id="sublocation" disabled></div>
                            </div>

                        </div>
                    </fieldset>
                </div>
                <div style="clear:both; margin-bottom: 10px"></div>
                <input id="select" type='button' class='right-button' value='Select' onclick='Select(); return false;'
                       disabled/>
                <div style="clear:both; margin-bottom: 10px"></div>

                <div id='imported-field-info' style="display:none">
                    <fieldset style="width:93%;">
                        <legend>Criteria and Values</legend>
                        <p>Please select the first value column</p>
                        <div id="imported-field-wrapper" style="width: 1100px; overflow-x:auto"></div>
                    </fieldset>
                    <div style="clear:both; margin-bottom: 10px"></div>
                    <input id="import" type='button' class='right-button' value='Import'
                           onclick='Import(); return false;' disabled/>
                </div>

                <div style="clear:both; margin-bottom: 10px"></div>

                <div id='imported-data-info' style="display:none">
                    <fieldset id='imported-data-info' style="width:93%; padding-top: 30px">
                        <legend>Review Imported Data</legend>
                        <div id="imported-list-wrapper"></div>
                        <div id="page-control" style="width: 95%; margin: 20px auto; display: none; text-align: center">
                            <span>Row Per Page</span>
                            <select id="row-per-page" class="small">
                                <option value="20" selected>20</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                                <option value="200">200</option>
                            </select>
                            <span class="label-small">Page</span>
                            <input type="text" id="page" class="tiny">
                            <span class="label-tiny">/</span>
                            <span class="label-tiny" id="page-num"></span>
                            <input type='button' id="prev" value='Prev' onclick='Prev(); return false;'
                                   style="margin: 7px 5px;"/>
                            <input type='button' id="next" value='Next' onclick='Next(); return false;'
                                   style="margin: 7px 5px"/>

                        </div>
                    </fieldset>
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
