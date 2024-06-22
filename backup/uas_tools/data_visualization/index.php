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

<!--            <!DOCTYPE html>-->
<!--            <html lang="en">-->
<!--            <head>-->
<!--                <title>Project list</title>-->
<!--                <meta charset="utf-8"/>-->
<!--                <meta name=”viewport” content=”width=device-width, initial-scale=1″>-->
<!--                <link rel="stylesheet" type="text/css" href="Resources/style.css">-->
<!--                <script src="Resources/JS/jquery.min.js"></script>-->
<!---->
<!--                <link rel="stylesheet" href="Resources/JS/Leaflet/leaflet.css"/>-->
<!--                <script src="Resources/JS/Leaflet/leaflet.js"></script>-->
<!--                <link rel="stylesheet" type="text/css" href="Resources/JS/JqueryUI/jquery-ui.css">-->
<!--                <script src="Resources/JS/JqueryUI/jquery-ui.min.js"></script>-->
<!--                <script src="Resources/JS/MarkerCluster/leaflet.markercluster.js"></script>-->
<!--                <link rel="stylesheet" type="text/css" href="Resources/JS/MarkerCluster/MarkerCluster.css">-->
<!--                <link rel="stylesheet" type="text/css" href="Resources/JS/MarkerCluster/MarkerCluster.Default.css">-->
<!---->
<!--                <script src="Resources/JS/main.js"></script>-->
<!---->
<!--                <script>-->
<!--                    $(document).ready(function () {-->
<!--                        //map.addLayer(projectMarkers);-->
<!--                        map.addLayer(markers);-->
<!---->
<!--                        projectMarkers.on('clustermouseover', function (e) {-->
<!--                            ShowClusterPopup(e.originalEvent);-->
<!--                        });-->
<!---->
<!--                        projectMarkers.on('clustermouseout', function (e) {-->
<!--                            $("#cluster-popup").hide();-->
<!--                            isClusterPopupShow = false;-->
<!--                        });-->
<!---->
<!--                        projectMarkers.on('clusterclick', function (e) {-->
<!--                            $("#cluster-popup").hide();-->
<!--                            isClusterPopupShow = false;-->
<!--                        });-->
<!---->
<!--                        GetProjectList();-->
<!---->
<!--                        GetPointcloudList();-->
<!---->
<!--                        $("#project-search").on('keyup', function () {-->
<!--                            $("#project-list li").each(function () {-->
<!--                                var filter = $("#project-search").val().toUpperCase();-->
<!--                                if ($(this).text().toUpperCase().indexOf(filter) > -1) {-->
<!--                                    $(this).show();-->
<!--                                } else {-->
<!--                                    $(this).hide();-->
<!--                                }-->
<!--                            });-->
<!--                        });-->
<!---->
<!--                        $("#pointcloud-search").on('keyup', function () {-->
<!--                            $("#pointcloud-list li").each(function () {-->
<!--                                var filter = $("#pointcloud-search").val().toUpperCase();-->
<!--                                if ($(this).text().toUpperCase().indexOf(filter) > -1) {-->
<!--                                    $(this).show();-->
<!--                                } else {-->
<!--                                    $(this).hide();-->
<!--                                }-->
<!--                            });-->
<!--                        });-->
<!--                    });-->
<!--                </script>-->
<!---->
<!--            </head>-->
<!--            <body>-->
<!--           <div id="cluster-popup" class="cluster-popup">Click to expand</div>-->
<!--          <h2 style="text-align:center">Map Viewer</h2>-->
<!--          <br>-->
<!---->
<!--            <div id="map-control">-->
<!--                <fieldset>-->
<!--                    <legend>Project List</legend>-->
<!--                    <input type="text" id="project-search" class="search">-->
<!--                    <ul id="project-list">-->
<!--                    </ul>-->
<!--                </fieldset>-->
<!---->
<!--                <div style="clear:both; margin: 20px 0"></div>-->
<!--                <fieldset>-->
<!--                    <legend>Pointcloud List</legend>-->
<!--                    <input type="text" id="pointcloud-search" class="search">-->
<!--                    <ul id="pointcloud-list">-->
<!--                    </ul>-->
<!--                </fieldset>-->
<!--            </div>-->
<!---->
<!--            <div id="map"></div>-->
<!---->
<!--            <script>-->
<!--                var map = L.map('map', {-->
<!--                    center: [30.0122883, -98.6618257],-->
<!--                    zoom: 6,-->
<!--                    minZoom: 3,-->
<!--                    maxZoom: 25,-->
<!--                });-->
<!---->
<!--                var osm_map = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {-->
<!--                    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',-->
<!--                    zIndex: 0-->
<!--                });-->
<!---->
<!--                map.setMaxBounds([[84.67351256610522, -174.0234375], [-58.995311187950925, 223.2421875]]);-->
<!---->
<!--                var mapbox = L.tileLayer('https://api.mapbox.com/styles/v1/longhuynh/cjehig4jl6ucb2rozj2oy5lfr/tiles/256/{z}/{x}/{y}?access_token={accessToken}', {-->
<!--                    accessToken: 'pk.eyJ1IjoibG9uZ2h1eW5oIiwiYSI6ImNpbDQ1cmR2bDN2ODB1eW0zeG13MWxxaDgifQ.RMNGP_0kdYujZnz4bLoMUg',-->
<!--                    maxNativeZoom: 19,-->
<!--                    zIndex: 0-->
<!--                });-->
<!---->
<!--                map.addLayer(mapbox);-->
<!--            </script>-->
<!---->
<!--            </body>-->
<!--            </html>-->

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Project list</title>
    <meta charset="utf-8" />
    <meta name=”viewport” content=”width=device-width, initial-scale=1″>
    <link rel="stylesheet" type="text/css" href="Resources/style.css">
    <script src="Resources/JS/jquery.min.js"></script>

    <!--                <link rel="stylesheet" type="text/css" href="Resources/bootstrap.min.css">-->
    <link rel="stylesheet" type="text/css" href="/libraries/bootstrap/css/bootstrap.min.css">
    <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script> -->
    <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script> -->
    <script src="<?php echo $header_location; ?>/libraries/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script> -->
    <script src="<?php echo $header_location; ?>/libraries/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <!--                <script src="Resources/bootstrap.min.js"></script>-->
    <script src="/libraries/bootstrap/js/bootstrap.min.js"></script>
    <!-- <script src="https://kit.fontawesome.com/00d920835c.js" crossorigin="anonymous"></script> -->
    <script src="<?php echo $header_location; ?>/libraries/font-awesome/js/00d920835c.js" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="Resources/JS/Leaflet/leaflet.css" />
    <script src="Resources/JS/Leaflet/leaflet.js"></script>
    <link rel="stylesheet" type="text/css" href="Resources/JS/JqueryUI/jquery-ui.css">
    <script src="Resources/JS/JqueryUI/jquery-ui.min.js"></script>
    <script src="Resources/JS/MarkerCluster/leaflet.markercluster.js"></script>
    <link rel="stylesheet" type="text/css" href="Resources/JS/MarkerCluster/MarkerCluster.css">
    <link rel="stylesheet" type="text/css" href="Resources/JS/MarkerCluster/MarkerCluster.Default.css">

    <script src="Resources/JS/main.js"></script>
    <style>
        button:focus {
            outline: none !important;
        }

    </style>
    <script>
        $(document).ready(function() {
            //map.addLayer(projectMarkers);
            map.addLayer(markers);

            projectMarkers.on('clustermouseover', function(e) {
                ShowClusterPopup(e.originalEvent);
            });

            projectMarkers.on('clustermouseout', function(e) {
                $("#cluster-popup").hide();
                isClusterPopupShow = false;
            });

            projectMarkers.on('clusterclick', function(e) {
                $("#cluster-popup").hide();
                isClusterPopupShow = false;
            });

            GetProjectList();

            GetPointcloudList();

            $("#project-search").on('keyup', function() {
                $("#project-list li").each(function() {
                    var filter = $("#project-search").val().toUpperCase();
                    if ($(this).text().toUpperCase().indexOf(filter) > -1) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });

            $("#pointcloud-search").on('keyup', function() {
                $("#pointcloud-list li").each(function() {
                    var filter = $("#pointcloud-search").val().toUpperCase();
                    if ($(this).text().toUpperCase().indexOf(filter) > -1) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });
        });

    </script>

</head>

<body>
    <!--            <div id="cluster-popup" class="cluster-popup">Click to expand</div>-->
    <!--            <h2 style="text-align:center">Map Viewer</h2>-->
    <!--            <br>-->
    <div class="site">
        <div class="site-row">
            <div id="mySidebar" class="sidebar" style="margin-top: 20px;">
                <div class="site-left">
                    <div class="site1">
                        <span class="collapsed" href="#">Project List</span>
                        <div class="inner">
                            <input type="search" class="srch" name="" value="" id="project-search">
                            <ul id="project-list">
                            </ul>
                        </div>
                    </div>

                    <!--                                Pointcloud List-->
                    <div class="site1" style="margin-top: 20px;">
                        <span class="collapsed" href="#">Pointcloud List</span>
                        <div class="inner">
                            <input type="search" class="srch" name="" value="" id="pointcloud-search">
                            <ul id="pointcloud-list">
                            </ul>
                        </div>
                    </div>

                </div>
            </div>
            <div id="main" class="site-right" style="margin-top: 30px;">
                <button style="float: left;" class="openbtn" onclick="openNav()"><img src="Resources/Images/menu_button.svg" alt=""></button>
                <div id="map" style="width: 94%;"></div>
            </div>
        </div>
    </div>
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

    </script>
    <script>
        //    function openNav() {
        //      document.getElementById("mySidebar").style.width = "320px";
        //      document.getElementById("main").style.marginLeft = "320px";
        //    }
        //
        //    function closeNav() {
        //      document.getElementById("mySidebar").style.width = "0";
        //      document.getElementById("main").style.marginLeft= "0";
        //    }
        $(document).ready(function() {
            $(".openbtn").click(function() {
                $("#mySidebar").toggleClass("active");
                $("#main").toggleClass("active");
            });
        });

    </script>
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