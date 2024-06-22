<?php
//phpinfo();
// File containing System Variables
define("LOCAL_PATH_ROOT", $_SERVER["DOCUMENT_ROOT"]);
require LOCAL_PATH_ROOT . '/uas_tools/system_management/centralized_management.php';

// To check if User has the role required to access the page
//require_once("Resources/PHP/SetDBConnection.php");
require_once("resources/database/SetDBConnection.php");
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
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <title>WTX Cotton</title>
  <meta charset="utf-8" />
  <meta name=”viewport” content=”width=device-width, initial-scale=1″>
  <link rel="stylesheet" type="text/css" href="resources/landing_page/style.css">
  <script src="resources/landing_page/JS/jquery.min.js"></script>

  <link rel="stylesheet" type="text/css" href="/libraries/bootstrap/css/bootstrap.min.css">
  <script src="<?php echo $header_location; ?>/libraries/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
  <script src="<?php echo $header_location; ?>/libraries/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="/libraries/bootstrap/js/bootstrap.min.js"></script>
  <script src="/libraries/font-awesome/js/00d920835c.js" crossorigin="anonymous"></script>

  <link rel="stylesheet" href="resources/landing_page/JS/Leaflet/leaflet.css" />
  <script src="resources/landing_page/JS/Leaflet/leaflet.js"></script>
  <link rel="stylesheet" type="text/css" href="resources/landing_page/JS/JqueryUI/jquery-ui.css">
  <script src="resources/landing_page/JS/JqueryUI/jquery-ui.min.js"></script>

  <script src="/libraries/Zebra_Dialog/dist/zebra_dialog.min.js"></script>
  <script src="/libraries/js/MarkerCluster/leaflet.markercluster.js"></script>

  <link rel="stylesheet" type="text/css" href="/libraries/js/MarkerCluster/MarkerCluster.css">
  <link rel="stylesheet" type="text/css" href="/libraries/js/MarkerCluster/MarkerCluster.Default.css">
  <link rel="stylesheet" href="/libraries/Zebra_Dialog/dist/css/classic/zebra_dialog.min.css">

  <script src="/resources/landing_page/JS/main.js"></script>
    <style>
        /* button:focus {
            outline: none !important;
        } */

        .ZebraDialogBackdrop{
          position: relative !important;
        }

        .myclass{
          max-width:750px !important;
        }

        .myclass .ZebraDialog_Body {
            /* background-image: url("/libraries/Zebra_Dialog/dist/css/classic/question.png"); */
            /* background-image: url("/resources/images/plains_cotton_growers.png"); */
            /* content: url("/resources/images/plains_cotton_growers.png"); */
            background-image: url('/resources/images/supported_words.png');
            background-position: bottom;
            /* padding-top: 100px; */
            width: 480px !important;
            /* height: 100px; */
            /* font-size: 21px; */
            /* max-width:750px !important; */
        }

    </style>
    <script>
        $(document).ready(function() {


          /* Notice how we're targeting the dialog box's title bar through the custom class */
          // .myclass .ZebraDialog_Title {
          //     background: #330066;
          // }
          // .myclass .ZebraDialog_Body {
          //     background-image: url("/libraries/Zebra_Dialog/dist/css/classic/prompt.png");
          //     font-size: 21px;
          // }

          // new $.Zebra_Dialog("<p>For more information about the Indiana 3DEP program - <a href='https://indianamap.maps.arcgis.com/apps/MapJournal/index.html?appid=a0f9003d504446da8158f5efce5be5d5' target='_blank'>IGIC Story Map</a> <p>", {
          //   'type': "information",
          //   'title': "Data Disclaimer",
          //   'custom_class': "myclass",
          // });

          // new $.Zebra_Dialog("<p>This project is supported by<p>", {
          //   'type': "information",
          //   'title': "Data Disclaimer",
          //   'custom_class': "myclass",
          // });

          new $.Zebra_Dialog("", {
            'type': "information",
            'title': "West Texas Cotton UASHub is supported by",
            'custom_class': "myclass",
          });




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
            <!-- <div id="mySidebar" class="sidebar" style="margin-top: 20px;">
                <div class="site-left">
                    <div class="site1">
                        <span class="collapsed" href="#">Project List</span>
                        <div class="inner">
                            <input type="search" class="srch" name="" value="" id="project-search">
                            <ul id="project-list">
                            </ul>
                        </div>
                    </div>

                    <div class="site1" style="margin-top: 20px;">
                        <span class="collapsed" href="#">Pointcloud List</span>
                        <div class="inner">
                            <input type="search" class="srch" name="" value="" id="pointcloud-search">
                            <ul id="pointcloud-list">
                            </ul>
                        </div>
                    </div>

                </div>
            </div> -->
            <div id="main" class="site-right" style="margin-top: 30px;">
                <!-- <button style="float: left;" class="openbtn" onclick=""><img src="resources/landing_page/Images/menu_button.svg" alt=""></button> -->
                <div id="map" style="width: 94%;"></div>
            </div>
        </div>
    </div>
    <script>
        // var map = L.map('map', {
        //     center: [30.0122883, -98.6618257],
        //     zoom: 6,
        //     minZoom: 3,
        //     maxZoom: 25,
        // });

      var map = L.map('map', {
          center: [33.5779, -101.8552],
          zoom: 7,
          minZoom: 1,
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

        // var mapbox = L.tileLayer('https://api.mapbox.com/styles/v1/longhuynh/cjehig4jl6ucb2rozj2oy5lfr/tiles/256/{z}/{x}/{y}?access_token={accessToken}', {
        //     accessToken: 'pk.eyJ1IjoibG9uZ2h1eW5oIiwiYSI6ImNpbDQ1cmR2bDN2ODB1eW0zeG13MWxxaDgifQ.RMNGP_0kdYujZnz4bLoMUg',
        //     maxNativeZoom: 19,
        //     zIndex: 0
        // });
        //
        // map.addLayer(mapbox);

        var googleHybrid = L.tileLayer('https://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}',{
        maxZoom: 20,
        subdomains:['mt0','mt1','mt2','mt3']
        });

        map.addLayer(googleHybrid);

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
        // $(document).ready(function() {
        //     $(".openbtn").click(function() {
        //         $("#mySidebar").toggleClass("active");
        //         $("#main").toggleClass("active");
        //     });
        // });

    </script>
</body>

</html>


<?php
}
?>
