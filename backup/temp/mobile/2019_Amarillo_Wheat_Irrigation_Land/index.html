<?php
// File containing System Variables
define("LOCAL_PATH_ROOT", $_SERVER["DOCUMENT_ROOT"]);
require LOCAL_PATH_ROOT . '/uas_tools/system_management/centralized_management.php';

//require_once("SetDBConnection.php");
require_once LOCAL_PATH_ROOT . '/uas_tools/visualization_generator/V2/Resources/PHP/SetDBConnection.php';

$con = SetDBConnection();

// Get the images data from photos_upload
$sql = "select * from photos_upload where Project = 1";// need to be changed from 1 to variable

$result = mysqli_query($con, $sql);
if ($result) {
// Fetches all result rows as an associative array
$page = mysqli_fetch_all($result);
} else {
    echo mysqli_error($con);
}

//SELECT X(Coordinate),Y(Coordinate) FROM photos_upload;
// Get the coordinates of images for an specific project from photos_upload
$sql = "select X(Coordinate),Y(Coordinate) from photos_upload where Project = 1";// need to be changed from 1 to variable

$result = mysqli_query($con, $sql);
// This contains the coordinates of the pictures stored under the Project
// Fetches all result rows as an associative array
//$coord_pictures = array();
$coord_pictures = mysqli_fetch_all($result);


//echo ($coord_pictures[0][1]);
//
//echo "<pre>";
//print_r($coord_pictures);
//echo "</pre>";

// Get the number of images for an specific project from photos_upload
$sql = "select count(Project) from photos_upload where Project = 1";// need to be changed from 1 to variable

$result = mysqli_query($con, $sql);
// This contains the number of pictures stored under the Project
$num_pictures = mysqli_fetch_row($result);

//echo('test: '.$num_pictures[0]);

//echo "<pre>";
//print_r($page);
//echo "</pre>";
mysqli_close($con);

//$js_array = array();
//$js_array = json_encode($coord_pictures);
//echo "var javascript_array = ". $js_array . ";\n";



?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
    <meta charset="utf-8" name=”viewport” content=”width=device-width, initial-scale=1″>
    <title>2019 Amarillo Wheat - Irrigation Land</title>
    <link rel="stylesheet" href="/css/leaflet.css"/>
    <link rel="stylesheet" href="/css/leaflet-panel-layers.css"/>

    <script src="/js/leaflet.js"></script>
    <script src="/js/leaflet-panel-layers.js"></script>
    <script src="/js/jquery.min.js"></script>
    <script src="/js/leaflet-ajax/dist/leaflet.ajax.js"></script>
    <script src="/js/legend.js"></script>
    <link rel="stylesheet" href="/css/legend.css"/>

    <style>
        body {
            margin: 0;
            padding: 0;
        }

        #map {
            position: absolute;
            /*z-index: -1;*/
            /*position: absolute;*/
            top: 0;
            bottom: 0;
            width: 100%;
        }

        /*#button {*/
        /*    top: 10%;*/
        /*    left: 0.3%;*/
        /*    position: absolute;*/
        /*    !* Let mouse events go through to reach the map underneath *!*/
        /*    !*pointer-events: none;*!*/
        /*    !* Make sure to be above the map pane (.leaflet-pane) *!*/
        /*    z-index: 450;*/
        /*        background-color: silver;*/
        /*        border-style: double;*/
        /*        border-color: black;*/
        /*        border-radius: 5px;*/
        /*}*/

        textarea {
            background-color: lightblue;
        }
    </style>

    <script>
        function all() {
            (function (i, s, o, g, r, a, m) {
                i['GoogleAnalyticsObject'] = r;
                i[r] = i[r] || function () {
                    (i[r].q = i[r].q || []).push(arguments)
                }, i[r].l = 1 * new Date();
                a = s.createElement(o),
                    m = s.getElementsByTagName(o)[0];
                a.async = 1;
                a.src = g;
                m.parentNode.insertBefore(a, m)
            })(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');

            ga('create', 'UA-74689450-1', 'auto');
            ga('send', 'pageview');

            var map = L.map('map', {
                center: L.latLng([35.190498,-102.084000]),
                zoom: 19,
                minZoom
        :
            17,
                maxZoom
        :
            25,
                attributionControl
        :
            false
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

            // Layers
            var layer_20190117_ar_p4p_wheat_irr_mosaic = L.tileLayer('https://chub.gdslab.org/uas_data/uploads/products/2019_Amarillo_Wheat_Irrigation_Land/Phantom_4_Pro/RGB/1-17-2019/20190117/RGB_Ortho/20190117_ar_p4p_wheat_irr_mosaic/Display/{z}/{x}/{y}.png', {tms: true, zIndex: 50, bounds: L.latLngBounds([L.latLng(35.1908,-102.08541944444),L.latLng(35.190741666667,-102.08296944444),L.latLng(35.190147222222,-102.08298888889),L.latLng(35.190205555556,-102.08544166667),L.latLng(35.1908,-102.08541944444)])}); 
var layer_20190226_ar_p4p_wheat_irr_mosaic = L.tileLayer('https://chub.gdslab.org/uas_data/uploads/products/2019_Amarillo_Wheat_Irrigation_Land/Phantom_4_Pro/RGB/2-26-2019/20190226/RGB_Ortho/20190226_ar_p4p_wheat_irr_mosaic/Display/{z}/{x}/{y}.png', {tms: true, zIndex: 51, bounds: L.latLngBounds([L.latLng(35.191047222222,-102.0855),L.latLng(35.190980555556,-102.08275),L.latLng(35.189972222222,-102.08278611111),L.latLng(35.190036111111,-102.08553611111),L.latLng(35.191047222222,-102.0855)])}); 
var layer_20190324_ar_p4p_wheat_irr_mosaic = L.tileLayer('https://chub.gdslab.org/uas_data/uploads/products/2019_Amarillo_Wheat_Irrigation_Land/Phantom_4_Pro/RGB/3-24-2019/20190324/RGB_Ortho/20190324_ar_p4p_wheat_irr_mosaic/Display/{z}/{x}/{y}.png', {tms: true, zIndex: 52, bounds: L.latLngBounds([L.latLng(35.1909,-102.08550555556),L.latLng(35.190838888889,-102.08290277778),L.latLng(35.190033333333,-102.08293055556),L.latLng(35.190094444444,-102.08553611111),L.latLng(35.1909,-102.08550555556)])}); 
var layer_20190416_ar_p4p_wheat_irr_mosaic = L.tileLayer('https://chub.gdslab.org/uas_data/uploads/products/2019_Amarillo_Wheat_Irrigation_Land/Phantom_4_Pro/RGB/4-16-2019/20190416/RGB_Ortho/20190416_ar_p4p_wheat_irr_mosaic/Display/{z}/{x}/{y}.png', {tms: true, zIndex: 53, bounds: L.latLngBounds([L.latLng(35.190863888889,-102.08549166667),L.latLng(35.190802777778,-102.08293055556),L.latLng(35.190088888889,-102.08295555556),L.latLng(35.19015,-102.08551666667),L.latLng(35.190863888889,-102.08549166667)])}); 
var layer_20190513_ar_m2p_wheat_irr_mosaic = L.tileLayer('https://chub.gdslab.org/uas_data/uploads/products/2019_Amarillo_Wheat_Irrigation_Land/Phantom_4_Pro/RGB/5-13-2019/20190513/RGB_Ortho/20190513_ar_m2p_wheat_irr_mosaic/Display/{z}/{x}/{y}.png', {tms: true, zIndex: 54, bounds: L.latLngBounds([L.latLng(35.190838888889,-102.08547777778),L.latLng(35.190777777778,-102.08290277778),L.latLng(35.190063888889,-102.08292777778),L.latLng(35.190125,-102.08550277778),L.latLng(35.190838888889,-102.08547777778)])}); 
var layer_20190603_ar_m2p_wheat_irr_mosaic = L.tileLayer('https://chub.gdslab.org/uas_data/uploads/products/2019_Amarillo_Wheat_Irrigation_Land/Phantom_4_Pro/RGB/6-3-2019/20190603/RGB_Ortho/20190603_ar_m2p_wheat_irr_mosaic/Display/{z}/{x}/{y}.png', {tms: true, zIndex: 55, bounds: L.latLngBounds([L.latLng(35.190869444444,-102.08547222222),L.latLng(35.190808333333,-102.08291666667),L.latLng(35.190094444444,-102.08294166667),L.latLng(35.190155555556,-102.08549722222),L.latLng(35.190869444444,-102.08547222222)])}); 
var layer_20190617_ar_m2p_wheat_irr_mosaic = L.tileLayer('https://chub.gdslab.org/uas_data/uploads/products/2019_Amarillo_Wheat_Irrigation_Land/Phantom_4_Pro/RGB/6-17-2019/20190617/RGB_Ortho/20190617_ar_m2p_wheat_irr_mosaic/Display/{z}/{x}/{y}.png', {tms: true, zIndex: 56, bounds: L.latLngBounds([L.latLng(35.190844444444,-102.08547777778),L.latLng(35.190783333333,-102.08290555556),L.latLng(35.190083333333,-102.08293055556),L.latLng(35.190144444444,-102.08550277778),L.latLng(35.190844444444,-102.08547777778)])}); 
var layer_20190630_ar_m2p_wheat_irr_mosaic = L.tileLayer('https://chub.gdslab.org/uas_data/uploads/products/2019_Amarillo_Wheat_Irrigation_Land/Phantom_4_Pro/RGB/6-30-2019/20190630/RGB_Ortho/20190630_ar_m2p_wheat_irr_mosaic/Display/{z}/{x}/{y}.png', {tms: true, zIndex: 57, bounds: L.latLngBounds([L.latLng(35.190802777778,-102.08544444444),L.latLng(35.190744444444,-102.08294166667),L.latLng(35.190147222222,-102.08296111111),L.latLng(35.190208333333,-102.08546388889),L.latLng(35.190802777778,-102.08544444444)])}); 


                map.addLayer(mapbox);

            var baseLayers = [
                {
                    name: "Open Street Map",
                    layer: osm_map
                },
                {
                    name: "Satellite Map",
                    layer: mapbox
                },
            ];

            var overLayers = [
                {
	group: 'RGB',
	layers: [
		{
			name: '1/17/2019',
active: 'true',
			layer: layer_20190117_ar_p4p_wheat_irr_mosaic
		},
		{
			name: '2/26/2019',
			layer: layer_20190226_ar_p4p_wheat_irr_mosaic
		},
		{
			name: '3/24/2019',
			layer: layer_20190324_ar_p4p_wheat_irr_mosaic
		},
		{
			name: '4/16/2019',
			layer: layer_20190416_ar_p4p_wheat_irr_mosaic
		},
		{
			name: '5/13/2019',
			layer: layer_20190513_ar_m2p_wheat_irr_mosaic
		},
		{
			name: '6/3/2019',
			layer: layer_20190603_ar_m2p_wheat_irr_mosaic
		},
		{
			name: '6/17/2019',
			layer: layer_20190617_ar_m2p_wheat_irr_mosaic
		},
		{
			name: '6/30/2019',
			layer: layer_20190630_ar_m2p_wheat_irr_mosaic
		},
	]
},

            ];

            var panelLayers = new L.Control.PanelLayers(baseLayers, overLayers, {collapsibleGroups: true});
            map.addControl(panelLayers);

            //Added
            // map.getContainer().appendChild(form1);

            //function geoFindMe() {
            //Inicia el mapa
            //var map = L.map('map').setView([0 , 0], 13);

            //L.tileLayer('https://{s}.tile.osm.org/{z}/{x}/{y}.png', {attribution: '© <a href="https://osm.org/copyright">OpenStreetMap</a> contributors' }).addTo(map);


            //const mapLink = document.querySelector('#map-link');

// Variable containing page URL
var siteURL = location.href;
// URL is saved on custId
document.getElementById("custId").value = siteURL;

// When click on blue marker, send URL and go to page
function onClick(e) {
    document.myform.submit();
}

//Start location marker
const marker = L.marker([0, 0]).on('click', onClick).addTo(map).bindTooltip("Click here to upload a picture",
    {
        permanent: true,
        direction: 'right'
    }
);

            // When mouse pass over, show message on marker
            //.bindTooltip("Click here to upload a picture")

            var redIcon = new L.Icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });

// Marker name
var dynamicname = 'marker';
//// Marker Latitude
//var picture_lat = '<?php //echo $coord_pictures[0][0];?>//';
//// Marker Longitude
//var picture_long = '<?php //echo $coord_pictures[0][1];?>//';
// Number of pictures on table
var num_pictures = '<?php echo $num_pictures[0];?>';

//Assign php generated json to JavaScript variable
var tempArray = <?php echo json_encode($coord_pictures); ?>;

// Header page
var header = '<?php echo $header_location;?>';

// Text and image on popup when click on red markers
//const src = "https://upload.wikimedia.org/wikipedia/commons/thumb/7/75/Stack_Exchange_logo_and_wordmark.svg/375px-Stack_Exchange_logo_and_wordmark.svg.png";
//const src = header =+ "/var/www/html/uas_tools/visualization_generator/V2/Resources/PHP/uploads/1.png";
const src = "https://chub.gdslab.org/uas_tools/visualization_generator/V2/Resources/PHP/uploads/image.jpg"; // Needs to be fixed and not harcoded
const popupContent = document.createElement("div")
popupContent.innerHTML = "<img style='max-height:300px; max-width:300px;' src='" + src + "'>"
    + "<a target='_blank' href='" + src + "'>See the image</a>"

// Function to create marker depending on the number of pictures
function Addmarker(markerArray) {
    for (var i = 0; i < markerArray; i++) {
        this[dynamicname + i] = L.marker(
            [tempArray[i][0], tempArray[i][1]],
            {icon: redIcon})
            .bindPopup(
                popupContent,
                { maxWidth: "auto" }
            )
            .addTo(map);
    }
}

Addmarker(num_pictures);

            // When click on red marker, open a new tab with the image full size
            // function onClick1(e) {
            //     //console.log("https://www.w3schools.com");
            //     window.open("/uas_tools/visualization_generator/V2/Resources/PHP/uploads/image.jpg");
            // }

            // // Variable containing page URL
            // var siteURL = location.href;
            //
            // // When click on blue marker, send URL and go to page
            // function onClick(e) {
            //     $.ajax({
            //         type: 'POST',
            //         url: '/uas_tools/visualization_generator/V2/Resources/PHP/upload_picture.php', // Where data is sent
            //         data: { url: siteURL }, // Data being sent
            //         success: function(response) { // If success
            //             // Open page
            //             window.open("/uas_tools/visualization_generator/V2/Resources/PHP/upload_picture.php");
            //         },
            //         error: function () { // If error
            //             // your error callback
            //         }
            //     });
            // }

            //let firstime = true;
            function success(position) {
                const latitude = position.coords.latitude; // Set latitude
                const longitude = position.coords.longitude;// Set longitude
                // Update markers location
                marker.setLatLng([latitude, longitude]).addTo(map);

                status.textContent = '';

                // if(firstime){
                //   map.setView([latitude, longitude], 15);
                //   firstime = false;
                // }
            }

            //Error checking
            function error() {
                status.textContent = 'Unable to retrieve your location';
            }

            //If geolocation is not supported
            if (!navigator.geolocation) {
                status.textContent = 'Geolocation is not supported by your browser';
            } else {
                status.textContent = 'Locating…';
                navigator.geolocation.watchPosition(success, error, {
                    timeout: Infinity,
                    enableHighAccuracy: true,
                    maximumAge: 0
                });
            }

            //setInterval(geoFindMe, 1000); // to set how often you want a function to load in ms.

            //}

            //Esto no se pa que es, creo que es para cuando se le hace click, haga a la funcion
            //document.querySelector('#find-me').addEventListener('click', geoFindMe);
        }
    </script>
</head>

<body onload="all();">
<!--<button id="button" type="button" onclick="location.href='https://chub.gdslab.org/web/uas_tools/visualization_generator/V2/Resources/PHP/upload_picture.html'">Take a picture</button>-->
<br/>

<div id="map"></div>

<form name="myform" action="/uas_tools/visualization_generator/V2/Resources/PHP/upload_picture.php" method="POST">
    <input type="hidden" id="custId" name="custId">
    <a href="javascript: submitform()"></a>
</form>

</body>
</html>
