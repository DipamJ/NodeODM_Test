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
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name=”viewport” content=”width=device-width, initial-scale=1″>
    <title>Upload LAS</title>

    <script type="text/javascript" src="Resources/JS/jquery.min.js"></script>
    <script src="Resources/JS/JqueryUI/jquery-ui.min.js"></script>
    <link rel="stylesheet" type="text/css" href="/uas_tools/upload_product/Resources/css/bootstrap.min.css">
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
    <style>
        .ft_container {
            margin: 0 auto;
            z-index: 1;
        }

        .ft_rel_container {
            z-index: 1;
        }

        .redText {
            color: red;
            font-weight: bold;
        }

        .blackText {
            color: black;
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

        .ft_container,
        .ft_rwrapper,
        .ft_scroller {
            width: 100% !important;
        }

    </style>
</head>

<body>
    <div id="resumable-list" style="display:none"></div>
    <div class="container py-3">
        <div class="project">
            <!--<form>-->
            <!--    <h2 style="text-align:center">Upload LAS</h2>-->
            <!--    <br>-->
            <div class="row">
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" id="name" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
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
                                <input type="text" id="date" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Lat</label>
                                <input type="text" id="lat" class="form-control" value="30.0122883">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Lng</label>
                                <input type="text" id="lng" class="form-control" value="-98.6618257">
                            </div>
                        </div>
                        <!--<div class="col-md-12">
                            <span class="redText">Important:</span>&nbsp;<span class="blackText">LAS file should be saved with WGS 84 - UTM Coordinate System</span>
                        </div>-->
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Description</label>
                        <textarea rows="3" cols="75" class="form-control" maxlength="1000" id="description"></textarea>
                    </div>
                </div>
            </div>

            <div style="clear:both; margin-bottom: 10px"></div>

            <div id="map"></div>
            <script>
                var map = L.map('map', {
                    center: [30.0122883, -98.6618257],
                    zoom: 6,
                    minZoom: 3,
                    maxZoom: 25,
                });

                var osm_map = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                    zIndex: 0
                });

                map.setMaxBounds([
                    [84.67351256610522, -174.0234375],
                    [-58.995311187950925, 223.2421875]
                ]);

                var mapbox = L.tileLayer('https://api.mapbox.com/styles/v1/longhuynh/cjehig4jl6ucb2rozj2oy5lfr/tiles/256/{z}/{x}/{y}?access_token={accessToken}', {
                    accessToken: 'pk.eyJ1IjoibG9uZ2h1eW5oIiwiYSI6ImNpbDQ1cmR2bDN2ODB1eW0zeG13MWxxaDgifQ.RMNGP_0kdYujZnz4bLoMUg',
                    maxNativeZoom: 19,
                    zIndex: 0
                });


                map.addLayer(mapbox);

                L.Control.geocoder().addTo(map);
                //var geocoder = L.Control.geocoder().addTo(map);

                var lat = $('#lat').val();
                var lng = $('#lng').val();
                var latlng = new L.LatLng(lat, lng);
                marker = new L.marker(latlng).addTo(map);

                map.on('click', function(e) {
                    marker.setLatLng(e.latlng);
                    $('#lat').val(e.latlng.lat);
                    $('#lng').val(e.latlng.lng);
                });

                $("#lat").on('input', function() {
                    ChangeLocation();
                });

                $("#lng").on('input', function() {
                    ChangeLocation();
                });

            </script>

            <div style="clear:both; margin-bottom: 10px"></div>
            <div style="text-align:center">
                <img id="upload-button" src="Resources/Images/upload.png" alt="Upload" class="upload-button" title="Upload" style="cursor:pointer" onclick="CreateResumableInstance()">
            </div>
            <div style="clear:both; margin-bottom: 10px"></div>
        </div>
        <div class="project" style="margin-top: 25px;">
            <h3>Upload Status</h3>
            <div style="line-height: 40px; text-align:center; width: 100%; margin: 0 auto">
                <div style="width: 20%;float: left; background: #555555; color: #ffffff;">Name</div>
                <div style="width: 20%;float: left; background: #555555; color: #ffffff;">File Name</div>
                <div style="width: 20%;float: left; background: #555555; color: #ffffff;">Status</div>
                <div style="width: 30%;float: left; background: #555555; color: #ffffff;">Progress</div>
                <div style="width: 10%;float: left; background: #555555; color: #ffffff;">&nbsp;</div>

            </div>
            <div style="clear:both"></div>
            <div id="unfinished-files" style="text-align:center; width: 980px; margin: 0 auto">
            </div>
            <div style="clear:both"></div>
            <div id="upload-files" style="text-align:center;  width: 980px; margin: 0 auto">
            </div>

        </div>
        <div style="clear:both; margin: 20px 0"></div>

        <div class="project" style="margin-top: 25px;">
            <h3>Uploaded LAS</h3>
            <div id="las-wrapper" class="table-responsive"></div>
        </div>
        <!--</form>-->
    </div>
</body>

</html>

<!--            --><?php
//            $con = SetDBConnection();
//
//            if (mysqli_connect_errno()) {
//                echo "Failed to connect to database server: " . mysqli_connect_error();
//            } else {
//
//                $sql = "SELECT Status FROM pointcloud ORDER BY ID DESC LIMIT 1";
//
//                $result = mysqli_query($con, $sql);
//
//                $list = array();
//                while ($row = mysqli_fetch_assoc($result)) {
//                    $list[] = $row;
//                }
//            }
//
//            if ($list[0] == 'Finished'){
//                header("Refresh:0; url=http://bhub.gdslab.org/web/uas_tools/las_upload/index.php");
//            }
//            //print_r($list);
//            mysqli_close($con);
//            ?>

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
