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
    <title>Plot Boundary</title>
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

    <script src="Resources/JS/plot-boundary.js"></script>
    <script src="Resources/JS/LeafletAJAX/leaflet.ajax.js"></script>
    <!-- <script src="https://code.jquery.com/jquery-migrate-3.0.0.min.js"></script> -->
    <script src="<?php echo $header_location; ?>/libraries/jquery/jquery-migrate-3.0.0.min.js"></script>


    <style>
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

        .leaflet-left .leaflet-control {
            margin-bottom: 0;
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
                    <li><a href="#gen-boundary">Create</a></li>
                    <li><a href="#mod-boundary">Edit</a></li>
                    <li><a href="#import">Import</a></li>
                    <!--										<li><a href="#info">Add Info</a></li>-->
                    <li><a href="#export">Export</a></li>
                </ul>
                <div id="layer" class="project">
                    <div class="row pt-3">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Project</label>
                                <select id="project-list" class="form-control"></select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Data Product</label>
                                <select id="product-type-list" class="form-control"></select>
                            </div>
                        </div>
                    </div>
                    <div class="row py-3">
                        <div class="col-md-12">
                            <h5>Layer List</h5>
                            <!--<li><a href="#gen-boundary">Create</a></li>-->
                            <ul id="added-layer-list"></ul>
                        </div>
                    </div>
                </div>
                <div id="gen-boundary" class="project">
                    <fieldset id="gen-plot-boundary-properties" class="py-3">
                        <h3>Row Properties</h3>
                        <input id='rectangle-id' type="hidden" />
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="mb-0">Row #</label>
                                    <input id="row-number" type="text" class="form-control" value="1" />
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="mb-0">Top Left Lat</label>
                                    <input id="top-left-lat" type="text" class="form-control" value='27.77485' />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="mb-0">Top Left Lng</label>
                                    <input id="top-left-lng" type="text" class="form-control" value='-97.5608' />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="mb-0">Plot Width</label>
                                    <input id="plot-width" type="text" class="form-control" value='1' />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="mb-0">Plot Height</label>
                                    <input id="plot-height" type="text" class="form-control" value='10' />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="mb-0">Angle</label>
                                    <input id="rotation-angle" type="text" class="form-control" value='0' />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="mb-0">EPSG</label>
                                    <input id="epsg-code" type="text" class="form-control" value='32614' />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="mb-0">Var Per Col</label>
                                    <input id="var-count" type="text" class="form-control" value='70' />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="mb-0">&nbsp;</label>
                                    <input type="button" value="Set" onclick="SetVarNumber(''); return false;" class='btnNew lh-32 btn-block' style='margin:0' />
                                </div>
                            </div>
                        </div>

                        <!--<div class="input"><input type="button" value="Calculate" onclick="Calculate(); return false;" class='button' style='width: 75px; margin:0'/></div>-->


                        <div style="clear:both"></div>

                        <div class="full-width" style="margin: 5px">Offsets</div>
                        <div class='apply-all'>
                            <div class="label label-small">Apply to all</div>
                            <div class="input"><input id="applied-offset" type="text" style="width: 33px" /></div>
                            <div class="input"><input type="button" value="Apply" onclick="ApplyAll('offset',''); return false;" class='lh-23 btnNew' style='margin:0' /></div>
                        </div>
                        <div style="clear:both"></div>
                        <div class="full-width" id='offsets' style="height: 90px; overflow-y: overlay; border: 1px solid gray;padding: 2px; margin: 10px 0"></div>

                        <div style="clear:both"></div>

                        <div class="full-width" style="margin: 5px">Vertical Shift</div>
                        <div class='apply-all'>
                            <div class="label label-small">Apply to all</div>
                            <div class="input"><input id="applied-vshift" type="text" style="width: 33px" /></div>
                            <div class="input"><input type="button" value="Apply" onclick="ApplyAll('vshift',''); return false;" class='lh-23 btnNew' style='margin:0' /></div>
                        </div>
                        <div style="clear:both"></div>
                        <div class="full-width" id='vshifts' style="height: 90px; overflow-y: overlay; border: 1px solid gray;padding: 2px; margin: 10px 0"></div>

                        <div style="clear:both"></div>

                        <input id="generate-boundary" type="button" value="Generate" onclick="GeneratePlotBoundary(); return false;" class='btnNew' />

                    </fieldset>
                </div>

                <div id="mod-boundary" class="project">
                    <div class="row py-3">
                        <div class="col-md-12">
                            <label>Row</label>
                            <select id="plot-list" class="form-control"></select>
                        </div>
                    </div>


                    <fieldset id="mod-plot-boundary-properties" style="width: 90%; display:none">
                        <h3>Selected Row Properties</h3>

                        <div class="label label-small">Row #</div>
                        <div class="input"><input id="mod-row-number" type="text" class="small" /></div>
                        <div style="clear:both"></div>

                        <div class="label label-small">Top Left Lat</div>
                        <div class="input"><input id="mod-top-left-lat" type="text" class="small" value='27.77485' /></div>

                        <div class="label label-small">Top Left Lng</div>
                        <div class="input"><input id="mod-top-left-lng" type="text" class="small" value='-97.5608' /></div>

                        <div style="clear:both"></div>

                        <div class="label label-small">Plot Width</div>
                        <div class="input"><input id="mod-plot-width" type="text" class="small" value='1' /></div>

                        <div class="label label-small">Plot Height</div>
                        <div class="input"><input id="mod-plot-height" type="text" class="small" value='10' /></div>

                        <div style="clear:both"></div>

                        <div class="label label-small">Angle</div>
                        <div class="input"><input id="mod-rotation-angle" type="text" class="small" value='0' /></div>

                        <div class="label label-small">EPSG</div>
                        <div class="input"><input id="mod-epsg-code" type="text" class="small" value='32614' /></div>

                        <div style="clear:both"></div>

                        <div class="label label-small">Var Per Col</div>
                        <div class="input"><input id="mod-var-count" type="text" class="small" value='70' /></div>
                        <div class="input"><input type="button" value="Set" onclick="SetVarNumber('mod'); return false;" class='lh-23 btnNew' style='margin:0' /></div>

                        <div style="clear:both"></div>

                        <div class="full-width" style="margin: 5px">Offsets</div>
                        <div>
                            <div class="label label-small">Apply to all</div>
                            <div class="input"><input id="applied-mod-offset" type="text" style="width: 33px" /></div>
                            <div class="input"><input type="button" value="Apply" onclick="ApplyAll('offset','mod'); return false;" class='lh-23 btnNew' style='margin:0' /></div>
                        </div>
                        <div style="clear:both"></div>
                        <div class="full-width" id='mod-offsets' style="height: 90px; overflow-y: overlay; border: 1px solid gray;padding: 2px; margin: 10px 0"></div>

                        <div style="clear:both"></div>

                        <div class="full-width" style="margin: 5px">Vertical Shift</div>
                        <div>
                            <div class="label label-small">Apply to all</div>
                            <div class="input"><input id="applied-mod-vshift" type="text" style="width: 33px" /></div>
                            <div class="input"><input type="button" value="Apply" onclick="ApplyAll('vshift','mod'); return false;" class='lh-23 btnNew' style='margin:0' /></div>
                        </div>
                        <div style="clear:both"></div>
                        <div class="full-width" id='mod-vshifts' style="height: 90px; overflow-y: overlay; border: 1px solid gray;padding: 2px; margin: 10px 0"></div>

                        <div style="clear:both"></div>

                        <input type="button" value="Update" onclick="UpdatePlotBoundary(); return false;" class='lh-23 btnNew' />
                        <input type="button" value="Delete" onclick="DeletePlotBoundary(); return false;" class='lh-23 btnNew' />

                        <div style="clear:both"></div>
                    </fieldset>
                    <div style="clear:both"></div>
                </div>

                <div id="import" class="project">
                    <div class="row py-3">
                        <div class="col-md-12">
                            <input class="btnNew" type="file" id="importedFile" accept=".plot" style="display: none;" />
                            <input type="button" value="Import" onclick="$('#importedFile').click(); return false;" class='btnNew' />
                        </div>
                    </div>
                </div>

                <!--									<div id="info"> -->
                <!--										<fieldset style="width: 90%;">-->
                <!--											<h3>Info Map</h3>-->
                <!--											<input type="file" id="imported-info-map" accept=".csv" style="display: none;" />-->
                <!--											<input type="button" value="Import" onclick="$('#imported-info-map').click(); return false;" class="button" style="float:left; margin: 2px"/>-->
                <!--											<div style="clear:both"></div>-->
                <!--											<div id="info-map-wrapper" class="info-wrapper"></div>-->
                <!--										</fieldset>-->
                <!--										<fieldset style="width: 90%;">-->
                <!--											<h3>Info Code</h3>-->
                <!--											<input type="file" id="imported-info-code" accept=".csv" style="display: none;" />-->
                <!--											<input type="button" value="Import" onclick="$('#imported-info-code').click(); return false;" class="button" style="float:left; margin: 2px"/>-->
                <!--											<div style="clear:both"></div>-->
                <!--											<div id="info-code-header" class="info-header"></div>-->
                <!--											<div id="info-code-wrapper" class="info-wrapper"></div>-->
                <!--										</fieldset>-->
                <!--										<input type="button" value="Apply" onclick="ApplyInfo(); return false;" class="button" style="float:left; margin: 2px"/>-->
                <!--										<div style="clear:both"></div>-->
                <!--											-->
                <!--									</div>-->

                <div id="export" class="project">
                    <div class="row py-3">
                        <div class="col-md-12">
                            <div class="form-inline">
                                <label class="col-sm-2">Format</label>
                                <select id="export-format" class="form-control col-sm-7">
                                    <option value="plot">.plot</option>
                                    <option value="geojson">.geojson</option>
                                </select>

                                <input type="button" value="Export" onclick="Export(); return false;" class='btnNew col-sm-2 lh-32' style="float:left; margin: 2px;" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <br>
            <input type="text" id="geojson" style='display: none'>
            <input type="text" id="boundary-width" style='display: none'>
            <input type="text" id="boundary-height" style='display: none'>


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
            // var mapbox = L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token={accessToken}', {
            //     attribution: 'Imagery from <a href="https://mapbox.com/about/maps/">MapBox</a> &mdash; Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            //     subdomains: 'abcd',
            //     id: 'mapbox.satellite',
            //     accessToken: 'pk.eyJ1IjoiaGFtZG9yaSIsImEiOiJjaWZmZzBwbjI4ZGdqc21seDFhOHA5dGcxIn0.4An46DNTDt97W992MRRWoQ',
            //     maxNativeZoom: 19,
            //     zIndex: 0
            // });

            // map.addLayer(mapbox);

            var googleHybrid = L.tileLayer('https://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}',{
              maxZoom: 20,
              subdomains:['mt0','mt1','mt2','mt3']
              });

            map.addLayer(googleHybrid);

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

            var lat = $('#top-left-lat').val();
            var lng = $('#top-left-lng').val();
            var latlng = new L.LatLng(lat, lng);
            marker = new L.marker(latlng).addTo(map);


            map.on('click', function(e) {
                marker.setLatLng(e.latlng);
                $('#top-left-lat').val(e.latlng.lat);
                $('#top-left-lng').val(e.latlng.lng);
            });

        </script>
    </form>
    <div id="dialog-confirm" title="Ignore the mismatch?" style="display:none;">
        <p><span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span>One or more entries are not found in the info code list. Ignore?</p>
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
