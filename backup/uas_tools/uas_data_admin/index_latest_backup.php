<?php
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
                <!--                <title>UAS Data Admin</title>-->
                <title>Data Administration</title>

                <link rel="stylesheet" type="text/css" href="Resources/style.css">
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
                </style>
            </head>
            <body>
            <div id="loading"></div>
            <form id='manage'>
                <!--                <h2>UAS Data Admin</h2>-->
                <h2>Data Administration</h2>
                <br>
                <div style="clear:both"></div>


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
                        <fieldset>
                            <legend>Add Project</legend>

                            <div class="half-width">
                                <div class="label">Name</div>
                                <div class="input"><input type="text" id="project-name" class="large"></div>
                                <div style="clear:both"></div>
                                <div class="label">Crop</div>
                                <select id="crop-type" class="select-large"></select>
                            </div>

                            <div class="half-width">
                                <div class="label">Planting Date</div>
                                <div class="input"><input type="text" id="planting-date" class="normal"></div>
                                <div style="clear:both"></div>
                                <div class="label">Harvest Date</div>
                                <div class="input"><input type="text" id="harvest-date" class="normal"></div>
                            </div>

                            <div style="clear:both"></div>

                            <div class="full-width">
                                <div class="label">Description</div>
                                <div class="input" style="width: 80%"><textarea rows="4" cols="75" style="width: 100%"
                                                                                maxlength="3000"
                                                                                id="description"></textarea></div>
                            </div>

                            <div style="clear:both"></div>

                            <div class="half-width">
                                <div class="label">Center Lat</div>
                                <!--                                <div class="input"><input type="text" id="center-lat" class="coordinate normal" value='27.78230'></div>-->
                                <div class="input"><input type="text" id="center-lat" class="coordinate normal"
                                                          value=''></div>
                            </div>

                            <div class="half-width">
                                <div class="label">Center Long</div>
                                <!--                                <div class="input"><input type="text" id="center-lng" class="coordinate normal" value='-97.56060' ></div>-->
                                <div class="input"><input type="text" id="center-lng" class="coordinate normal"
                                                          value=''></div>

                            </div>

                            <div style="clear:both"></div>

                            <div class="one-third-width">
                                <div class="label">Min Zoom</div>
                                <!--                                <div class="input"><input type="text" class="small zoom" id="min-zoom" value="17"></div>-->
                                <div class="input"><input type="text" class="small zoom" id="min-zoom" value=""></div>
                            </div>

                            <div class="one-third-width">
                                <div class="label">Max Zoom</div>
                                <!--                                <div class="input"><input type="text" class="small zoom" id="max-zoom" value="25"></div>-->
                                <div class="input"><input type="text" class="small zoom" id="max-zoom" value=""></div>
                            </div>

                            <div class="one-third-width">
                                <div class="label">Default Zoom</div>
                                <!--                                <div class="input"><input type="text" class="small zoom" id="default-zoom" value="19"></div>-->
                                <div class="input"><input type="text" class="small zoom" id="default-zoom" value="">
                                </div>
                            </div>

                            <div style="clear:both"></div>

                            <div class="full-width">
                                <div class="label">Visualization Page</div>
                                <div class="input"><textarea rows="2" cols="75" style="width: 100%" maxlength="3000"
                                                             id="visualization"></textarea></div>
                            </div>

                        </fieldset>

                        <div style="clear:both; margin: 5px"></div>

                        <input id="add-project" class="add-button" type="button" value="Add"
                               onclick="Add('project'); return false;"/>

                        <div style="clear:both"></div>

                        <fieldset>
                            <legend>Project List</legend>
                            <div>
                                <div class="label">Filter (Name)</div>
                                <div class="input"><input id="project-search" type="text"></div>
                            </div>
                            <div style="clear:both; margin: 5px"></div>
                            <br>
                            <div id="project-wrapper"></div>
                        </fieldset>
                    </div>

                    <div id="platform">
                        <fieldset>
                            <legend>Add Platform</legend>
                            <div style="width:400px; margin: 0 auto;">
                                <div class="label">Name</div>
                                <div class="input"><input type="text" id="platform-name"></div>
                            </div>
                        </fieldset>

                        <div style="clear:both; margin: 5px"></div>

                        <input id="add-platform" class="add-button" type="button" value="Add"
                               onclick="Add('platform'); return false;"/>

                        <div style="clear:both"></div>

                        <fieldset>
                            <legend>Platform List</legend>
                            <div id="platform-wrapper"></div>
                        </fieldset>
                    </div>

                    <div id="sensor">
                        <fieldset>
                            <legend>Add Sensor</legend>
                            <div style="width:400px; margin: 0 auto;">
                                <div class="label">Name</div>
                                <div class="input"><input type="text" id="sensor-name"></div>
                            </div>
                        </fieldset>

                        <div style="clear:both; margin: 5px"></div>

                        <input id="add-sensor" class="add-button" type="button" value="Add"
                               onclick="Add('sensor'); return false;"/>

                        <div style="clear:both"></div>

                        <fieldset>
                            <legend>Sensor List</legend>
                            <div id="sensor-wrapper"></div>
                        </fieldset>
                    </div>

                    <div id="flight">
                        <fieldset>
                            <legend>Search</legend>
                            <div>
                                <div class="label">Project</div>
                                <select id="flight-project" class="select-large">
                                </select>
                            </div>


                            <div>
                                <div class="label">Platform</div>
                                <select id="flight-platform">
                                </select>
                            </div>


                            <div>
                                <div class="label">Sensor</div>
                                <select id="flight-sensor">
                                </select>
                            </div>
                        </fieldset>

                        <div style="clear:both; margin: 5px"></div>
                        <input id="search-flight" class="add-button" type="button" value="Search"
                               onclick="GetList('flight'); return false;"/>
                        <div style="clear:both"></div>

                        <fieldset>
                            <legend>Add Flight</legend>
                            <div>
                                <div class="label">Name</div>
                                <div class="input"><input type="text" id="flight-name"></div>
                            </div>

                            <!--
                            <div>
                                <div class="label">Project</div>
                                <select id="flight-project"  class="select-large">
                                </select>
                            </div>

                            <div style="clear:both"></div>

                            <div>
                                <div class="label">Platform</div>
                                <select id="flight-platform">
                                </select>
                            </div>

                            <div style="clear:both"></div>

                            <div>
                                <div class="label">Sensor</div>
                                <select id="flight-sensor">
                                </select>
                            </div>
                        -->

                            <div>
                                <div class="label">Date</div>
                                <div class="input"><input type="text" id="flight-date" class="small"></div>
                            </div>


                            <div>
                                <div class="label">Flight Altitude</div>
                                <div class="input"><input type="text" id="flight-altitude" class="tiny"></div>
                            </div>


                            <div>
                                <div class="label">Forward Overlap</div>
                                <div class="input"><input type="text" id="flight-forward" class="tiny"></div>
                            </div>


                            <div>
                                <div class="label">Side Overlap</div>
                                <div class="input"><input type="text" id="flight-side" class="tiny"></div>
                                <div style="clear:both"></div>
                            </div>
                        </fieldset>

                        <div style="clear:both; margin: 5px"></div>


                        <input id="add-flight" class="add-button" type="button" value="Add"
                               onclick="Add('flight'); return false;"/>

                        <div style="clear:both"></div>

                        <fieldset>
                            <legend>Flight List</legend>
                            <div id="flight-wrapper"></div>
                        </fieldset>
                    </div>

                    <!--                                THIS PART NEEDS TO BE FIXED-->
                    <div id="product-type">
                        <fieldset>
                            <legend>Add Product Type</legend>
                            <div style="width:400px; margin: 0 auto;">
                                <div class="label">Name</div>
                                <div class="input"><input type="text" id="type-name"></div>
                            </div>
                            <!--                                            ADDED-->
                            <div>
                                <div class="label">Type</div>
                                <select id="product_type_select">
                                    <option value="R">R</option>
                                    <option value="V">V</option>
                                </select>
                            </div>
                            <!--                                            ADDED-->
                        </fieldset>

                        <div style="clear:both; margin: 5px"></div>

                        <input id="add-sensor" class="add-button" type="button" value="Add"
                               onclick="Add('type'); return false;"/><!--add-sensor should be add-flight-->

                        <div style="clear:both"></div>

                        <fieldset>
                            <legend>Product Type List</legend>
                            <div id="type-wrapper"></div>
                        </fieldset>
                    </div>
                    <!--     ---------------    -->


                    <div id="crop">
                        <fieldset>
                            <legend>Add Crop</legend>
                            <div style="width:400px; margin: 0 auto;">
                                <div class="label">Name</div>
                                <div class="input"><input type="text" id="crop-name"></div>
                            </div>
                        </fieldset>

                        <div style="clear:both; margin: 5px"></div>

                        <input id="add-crop" class="add-button" type="button" value="Add"
                               onclick="Add('crop'); return false;"/>

                        <div style="clear:both"></div>

                        <fieldset>
                            <legend>Crop List</legend>
                            <div id="crop-wrapper"></div>
                        </fieldset>
                    </div>
                    <div id="raw-data">
                        <fieldset>
                            <legend>Search</legend>
                            <div>
                                <div class="label">Project</div>
                                <select id="raw-data-project" class="select-large">
                                </select>
                            </div>
                            <div>
                                <div class="label">Platform</div>
                                <select id="raw-data-platform">
                                </select>
                            </div>


                            <div>
                                <div class="label">Sensor</div>
                                <select id="raw-data-sensor">
                                </select>
                            </div>

                        </fieldset>

                        <div style="clear:both; margin: 5px"></div>
                        <input type="button" class="add-button" value="Search" onclick="Search('raw'); return false;"/>
                        <div style="clear:both"></div>

                        <fieldset>
                            <legend>Unfinished List</legend>
                            <div id="unfinished-raw-list-wrapper" style="text-align:center;"></div>
                        </fieldset>
                        <div style="clear:both"></div>
                        <br>
                        <fieldset>
                            <legend>Finished List</legend>
                            <div id="finished-raw-list-wrapper"></div>
                        </fieldset>
                        <div style="clear:both"></div>
                    </div>
                    <div id="data-product">
                        <fieldset>
                            <legend>Search</legend>
                            <div>
                                <div class="label">Project</div>
                                <select id="data-product-project" class="select-large">
                                </select>
                            </div>
                            <div style="clear:both"></div>
                            <div>
                                <div class="label">Platform</div>
                                <select id="data-product-platform">
                                </select>
                            </div>


                            <div>
                                <div class="label">Sensor</div>
                                <select id="data-product-sensor">
                                </select>
                            </div>

                            <div>
                                <div class="label">Type</div>
                                <select id="data-product-type">
                                </select>
                            </div>
                        </fieldset>

                        <div style="clear:both; margin: 5px"></div>
                        <input type="button" class="add-button" value="Search"
                               onclick="Search('product'); return false;"/>
                        <div style="clear:both"></div>


                        <fieldset>
                            <legend>Unfinished List</legend>
                            <div id="unfinished-product-list-wrapper" style="text-align:center;"></div>
                        </fieldset>
                        <div style="clear:both"></div>
                        <br>
                        <fieldset>
                            <legend>Finished List</legend>
                            <div id="finished-product-list-wrapper"></div>
                        </fieldset>
                        <div style="clear:both"></div>
                        <br>

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
                                <input type="button" class="button" value="Download"
                                       onclick="Download(); return false;"/>
                            </div>
                            <hr>
                            <p>Local Path</p>
                            <textarea id="local-path" rows="3" cols="50" readonly></textarea>
                            <div style="text-align:center">
                                <input type="button" class="button" value="Copy Path"
                                       onclick="CopyToClipBoard('path'); return false;"/>
                            </div>
                        </div>
                    </div>
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
                <a href="mailto:<?= $_SESSION["admin_email"] ?>?
        &subject=Requesting%20access%20to%20the%20uas_data_admin%20tool
        &body=Hi,%0D%0A%0D%0AThis%20is%20<?= $_SESSION['email'] ?>.%20Please%20provide%20me%20access%20to%20the%20tool.">
                    <?= $_SESSION["admin_email"] ?></a>
                to request access to this tool.</p>
            </body>
            </html>
            <?php
        }
    }
}
?>