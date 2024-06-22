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
    <title>Plot Grid</title>
    <meta charset="utf-8" />
    <meta name=”viewport” content=”width=device-width, initial-scale=1″>
    <link rel="stylesheet" type="text/css" href="Resources/style.css">
    <script src="Resources/JS/jquery.min.js"></script>

    <script src="Resources/JS/Chosen/chosen.jquery.min.js"></script>
    <link rel="stylesheet" type="text/css" href="/uas_tools/uas_data_admin/Resources/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="Resources/JS/Chosen/chosen.css">

    <link rel="stylesheet" href="Resources/JS/Leaflet/leaflet.css" />
    <script src="Resources/JS/Leaflet/leaflet-src.js"></script>
    <link rel="stylesheet" type="text/css" href="Resources/JS/JqueryUI/jquery-ui.css">
    <script src="Resources/JS/JqueryUI/jquery-ui.min.js"></script>
    <script src="Resources/JS/FixedTable/fixed_table_rc.js"></script>
    <link rel="stylesheet" type="text/css" href="Resources/JS/FixedTable/fixed_table_rc.css">

    <script src="Resources/JS/plot-grid.js"></script>
    <script src="Resources/JS/LeafletAJAX/leaflet.ajax.js"></script>
    <style>
        .legend {
            line-height: 18px;
            color: white;
        }

        .legend i {
            width: 18px;
            height: 18px;
            float: left;
            margin-right: 8px;
            opacity: 0.7;
        }

        .legend span,
        .legend label {
            display: block;
            width: 78px;
            height: 15px;
            float: left;
            opacity: 0.7;
            text-align: center;
            font-size: 80%
        }

        .leaflet-left .leaflet-control {
            margin-bottom: 0;
        }

        .h3 {
            line-height: 18px;
            color: white;
        }

        .h3 i {
            width: 18px;
            height: 18px;
            float: left;
            margin-right: 8px;
            opacity: 0.7;
        }

        .h3 span,
        .h3 label {
            display: block;
            width: 78px;
            height: 15px;
            float: left;
            opacity: 0.7;
            text-align: center;
            font-size: 80%
        }

        .ui-widget-header {
            border: none !important;
            background: none !important;
        }

        .ui-widget-content {
            border: none !important;
        }

        .ui-tabs .ui-tabs-nav {
            padding: .2em 1em 0 !important;
        }

        .ui-tabs .ui-tabs-panel {
            padding: 1em 1.4em !important;
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

        .project {
            margin: 0;
            padding: 25px 35px !important;
            border-radius: 15px !important;
            background: #f6f7f9 !important;
        }

        #project_list_chosen,
        #product-type-list,
        plot-list {
            width: 100% !important;
        }

        .lh-23 {
            line-height: 23px !important;
        }

        .lh-25 {
            line-height: 25px !important;
        }

        .lh-32 {
            line-height: 32px !important;
        }

    </style>

</head>

<body>
    <form class="full-width">
        <div id="loading"></div>

        <div id="map-control">
            <div id="tabs">
                <ul>
                    <li><a href="#layer">Layers</a></li>
                    <li><a href="#grid">Generate Grid</a></li>
                    <!--                            <li><a href="#info">Info</a></li>-->
                    <li><a href="#file">Export</a></li>
                </ul>
                <div id="layer" class="project">

                    <div class="row">
                        <div class="col-12 form-group">
                            <label>Project</label>
                            <select id="project-list" class="form-control"></select>
                        </div>
                        <div class="col-12 form-group">
                            <label>Data Product</label>
                            <select id="product-type-list" class="form-control"></select>
                            <!--<select id="product-type-list" class="select-small"></select>-->
                        </div>
                        <div class="col-12 form-group">
                            <h5>Layer List</h5>
                            <ul id="added-layer-list">
                            </ul>
                        </div>
                    </div>
                </div>

                <div id="grid" class="project">
                    <div class="row py-3">
                        <div class="col-md-12">
                            <div class="form-inline">
                                <label class="col-sm-2">Plot</label>
                                <select id="plot-list" class="form-control col-sm-7"></select>

                                <input class="btnNew col-sm-2 lh-32" type="file" id="importedFile" accept=".plot" style="display: none;" />
                                <input type="button" value="Import" onclick="$('#importedFile').click(); return false;" class="btnNew col-sm-2 lh-32" style="float:left; margin: 2px" />
                            </div>
                        </div>
                    </div>


                    <fieldset id="plot-boundary-properties" style="display:none" class="py-3">
                        <h3>Plot Boundary Properties</h3>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="mb-0">Top Left Lat</label>
                                    <input id="top-left-lat" type="text" class="form-control" disabled />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="mb-0">Top Left Lng</label>
                                    <input id="top-left-lng" type="text" class="form-control" disabled />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="mb-0">Plot Width</label>
                                    <input id="plot-width" type="text" class="form-control" disabled />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="mb-0">Plot Height</label>
                                    <input id="plot-height" type="text" class="form-control" disabled />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="mb-0">Angle</label>
                                    <input id="rotation-angle" type="text" class="form-control" disabled />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="mb-0">EPSG</label>
                                    <input id="epsg-code" type="text" class="form-control" disabled />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="mb-0">Var Per Col</label>
                                    <input id="var-count" type="text" class="form-control" disabled />
                                </div>
                            </div>
                        </div>


                        <div class="full-width" style="margin: 5px">Offsets</div>
                        <div style="clear:both"></div>
                        <div class="full-width" id='offsets' style="height: 60px; overflow-y: overlay; border: 1px solid gray;padding: 2px; margin: 10px 0"></div>

                        <div style="clear:both"></div>

                        <div class="full-width" style="margin: 5px">Vertical Shift</div>
                        <div style="clear:both"></div>
                        <div class="full-width" id='vshifts' style="height: 60px; overflow-y: overlay; border: 1px solid gray;padding: 2px; margin: 10px 0"></div>
                        <div style="clear:both"></div>
                    </fieldset>

                    <br>

                    <fieldset id="grid-properties" style="display:none" class="py-3">
                        <h3>Plot Grid Properties</h3>

                        <div class="label">Number of Rows</div>
                        <div class="input"><input id="row-count" type="text" class="small" style="width: 28px" />
                        </div>

                        <div class="label label-small">Row Height</div>
                        <div class="input"><input id="row-height" type="text" class="small" style="width: 28px" /></div>


                        <div style="clear:both"></div>

                        <div class="label">Number of Cols</div>
                        <div class="input"><input id="col-count" type="text" class="small" style="width: 28px" />
                        </div>

                        <div class="label label-small">Col Width</div>
                        <div class="input"><input id="col-width" type="text" class="small" style="width: 28px" />
                        </div>


                        <div style="clear:both"></div>

                        <input id="generate-grid" type="button" value="Generate" onclick="GeneratePlotGrid(); return false;" class='btnNew' />
                    </fieldset>
                    <div style="clear:both"></div>
                </div>

                <!--                        <div id="info">-->
                <!--                            <fieldset style="width: 90%;">-->
                <!--                                <legend>Info Map</legend>-->
                <!--                                <div style="clear:both"></div>-->
                <!--                                <div id="info-map-wrapper" class="info-wrapper"></div>-->
                <!--                            </fieldset>-->
                <!--                            <fieldset style="width: 90%;">-->
                <!--                                <legend>Info Code</legend>-->
                <!--                                <div style="clear:both"></div>-->
                <!--                                <div id="info-code-wrapper"></div>-->
                <!--                            </fieldset>-->
                <!--                            <div style="clear:both"></div>-->
                <!--                        </div>-->

                <div id="file" class="project">
                    <div class="row">
                    <div class="col-md-12">
                        <input type="button" value="Export" onclick="Export(); return false;" class='btnNew btn-block' />
                    </div>
                    </div>
                </div>
            </div>

            <input type="text" id="geojson" style='display: none'>
        </div>
        <div id="map"></div>
        <script>
            var map = L.map('map', {
                center: [27.77485, -97.5608],
                zoom: 19,
                minZoom: 17,
                maxZoom: 25,
            });


            var osm_map = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                zIndex: 0
            });
            var mapbox = L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token={accessToken}', {
                attribution: 'Imagery from <a href="https://mapbox.com/about/maps/">MapBox</a> &mdash; Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                subdomains: 'abcd',
                id: 'mapbox.satellite',
                accessToken: 'pk.eyJ1IjoiaGFtZG9yaSIsImEiOiJjaWZmZzBwbjI4ZGdqc21seDFhOHA5dGcxIn0.4An46DNTDt97W992MRRWoQ',
                maxNativeZoom: 19,
                zIndex: 0
            });

            map.addLayer(mapbox);

            var boundaryStyle = {
                "color": "#ff7800",
                "weight": 2,
                "fillOpacity": 0,
                "opacity": 1.0,
            };

            var gridStyle = {
                "color": "#0078ff",
                "weight": 2,
                "fillOpacity": 0,
                "opacity": 1.0,
            };

            var plotItems = new L.FeatureGroup();

        </script>
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
