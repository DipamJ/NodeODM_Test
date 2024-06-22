<?php
// File containing System Variables
define("LOCAL_PATH_ROOT", $_SERVER["DOCUMENT_ROOT"]);
require LOCAL_PATH_ROOT . '/uas_tools/system_management/centralized_management.php';

//require_once("SetDBConnection.php");
require_once LOCAL_PATH_ROOT . '/uas_tools/visualization_generator/Resources/PHP/SetDBConnection.php';

$con = SetDBConnection();
$uriSegments = explode("/", parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$project_name_dash=$uriSegments[4];
$project_name=str_replace("_"," ",$uriSegments[4]);

 $sql = "select * from project where Name = '$project_name'";
// Execute the query
$result = mysqli_query($con, $sql);
// Create array from query
$row = mysqli_fetch_assoc($result);

// Project ID
$project_id = $row["ID"];

// Get the images data from photos_upload
$sql = "select * from photos_upload where Project = '$project_id'";// need to be changed from 1 to variable

$result = mysqli_query($con, $sql);
if ($result) {
// Fetches all result rows as an associative array
$page = mysqli_fetch_all($result);
} else {
    echo mysqli_error($con);
}

//SELECT X(Coordinate),Y(Coordinate) FROM photos_upload;
// Get the coordinates of images for an specific project from photos_upload
$sql = "select X(Coordinate),Y(Coordinate) from photos_upload where Project = '$project_id'";// need to be changed from 1 to variable

$result = mysqli_query($con, $sql);
// This contains the coordinates of the pictures stored under the Project
// Fetches all result rows as an associative array
//$coord_pictures = array();
$coord_pictures = mysqli_fetch_all($result);
$pictures = mysqli_fetch_all( mysqli_query($con, "SELECT * FROM photos_upload where Project = '$project_id'"));

//echo ($coord_pictures[0][1]);
//
//echo "<pre>";
//print_r($coord_pictures);
//echo "</pre>";

// Get the number of images for an specific project from photos_upload
$sql = "select count(Project) from photos_upload where Project = '$project_id'";// need to be changed from 1 to variable

$result = mysqli_query($con, $sql);
// This contains the number of pictures stored under the Project
$num_pictures = mysqli_fetch_row($result);

$picture_data=array();
foreach ($coord_pictures as $key => $value) {
    $email=$pictures[$key][8];
   $picture_data[$key]['x']=$value[0];
   $picture_data[$key]['y']=$value[1];
   $picture_data[$key]['path']=$pictures[$key][6];
   $picture_data[$key]['image']=$pictures[$key][2];
   $picture_data[$key]['name']=$pictures[$key][7];
   $picture_data[$key]['date_time']=$pictures[$key][9];
   $picture_data[$key]['user_email']=$email;

   $user = mysqli_fetch_all( mysqli_query($con, "SELECT * FROM users WHERE email='$email'"));
   if (isset($user[0][1]) && isset($user[0][2])) {
      $picture_data[$key]['user_name']=$user[0][1] .' '. $user[0][2];
   }else{
    $picture_data[$key]['user_name']="";
   }


}

//echo('test: '.$num_pictures[0]);
// echo "<pre>";
//  var_dump($num_pictures);
//  var_dump($coord_pictures);
//  var_dump($pictures);
//  var_dump($picture_data);
// var_dump($uriSegments);
// var_dump($project_id);
// var_dump($row);
//  echo "</pre>";
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
    <title>2016 Corpus Christi Cotton and Sorghum</title>
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

        textarea {
            background-color: lightblue;
        }
    </style>
<input type="hidden" value='<?php echo json_encode($picture_data); ?>' id="picture_data" name="picture_data">
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
                center: L.latLng([27.782301,-97.560600]),
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
            var layer_20160412_P2VP_Cotton_Sorghum_30m_mosaic = L.tileLayer('https://chub.gdslab.org/uas_data/uploads/products/2016_Corpus_Christi_Cotton_and_Sorghum/Phantom_4_Pro/RGB/04-12-2016/20160412/RGB_Ortho/20160412_P2VP_Cotton_Sorghum_30m_mosaic/Display/{z}/{x}/{y}.png', {tms: true, zIndex: 50, bounds: L.latLngBounds([L.latLng(27.784047222222,-97.561905555556),L.latLng(27.784019444444,-97.559380555556),L.latLng(27.781002777778,-97.559422222222),L.latLng(27.781030555556,-97.561947222222),L.latLng(27.784047222222,-97.561905555556)])}); 
var layer_20160427_P4_Cotton_Sorghum_30m_mosaic = L.tileLayer('https://chub.gdslab.org/uas_data/uploads/products/2016_Corpus_Christi_Cotton_and_Sorghum/Phantom_4_Pro/RGB/04-27-2016/20160427/RGB_Ortho/20160427_P4_Cotton_Sorghum_30m_mosaic/Display/{z}/{x}/{y}.png', {tms: true, zIndex: 51, bounds: L.latLngBounds([L.latLng(27.783555555556,-97.561641666667),L.latLng(27.783536111111,-97.559780555556),L.latLng(27.781330555556,-97.559808333333),L.latLng(27.78135,-97.561672222222),L.latLng(27.783555555556,-97.561641666667)])}); 
var layer_20160516_cc_p4_cotton_sorghum_mosaic = L.tileLayer('https://chub.gdslab.org/uas_data/uploads/products/2016_Corpus_Christi_Cotton_and_Sorghum/Phantom_4_Pro/RGB/05-16-2016/20160516/RGB_Ortho/20160516_cc_p4_cotton_sorghum_mosaic/Display/{z}/{x}/{y}.png', {tms: true, zIndex: 52, bounds: L.latLngBounds([L.latLng(27.783661111111,-97.561738888889),L.latLng(27.783641666667,-97.5597),L.latLng(27.781461111111,-97.559727777778),L.latLng(27.781480555556,-97.561766666667),L.latLng(27.783661111111,-97.561738888889)])}); 
var layer_20160531_cc_p4_cotton_sorghum_mosaic = L.tileLayer('https://chub.gdslab.org/uas_data/uploads/products/2016_Corpus_Christi_Cotton_and_Sorghum/Phantom_4_Pro/RGB/05-31-2016/20160531/RGB_Ortho/20160531_cc_p4_cotton_sorghum_mosaic/Display/{z}/{x}/{y}.png', {tms: true, zIndex: 53, bounds: L.latLngBounds([L.latLng(27.783725,-97.561763888889),L.latLng(27.783702777778,-97.559647222222),L.latLng(27.781386111111,-97.559677777778),L.latLng(27.781408333333,-97.561794444444),L.latLng(27.783725,-97.561763888889)])}); 
var layer_20160614_cc_p4_cotton_sorghum_mosaic = L.tileLayer('https://chub.gdslab.org/uas_data/uploads/products/2016_Corpus_Christi_Cotton_and_Sorghum/Phantom_4_Pro/RGB/06-14-2016/20160614/RGB_Ortho/20160614_cc_p4_cotton_sorghum_mosaic/Display/{z}/{x}/{y}.png', {tms: true, zIndex: 54, bounds: L.latLngBounds([L.latLng(27.783586111111,-97.561544444444),L.latLng(27.783572222222,-97.559975),L.latLng(27.781447222222,-97.560002777778),L.latLng(27.781463888889,-97.561572222222),L.latLng(27.783586111111,-97.561544444444)])}); 
var layer_20160627_cc_p4_cotton_sorghum_20m_mosaic = L.tileLayer('https://chub.gdslab.org/uas_data/uploads/products/2016_Corpus_Christi_Cotton_and_Sorghum/Phantom_4_Pro/RGB/06-27-2016/20160627/RGB_Ortho/20160627_cc_p4_cotton_sorghum_20m_mosaic/Display/{z}/{x}/{y}.png', {tms: true, zIndex: 55, bounds: L.latLngBounds([L.latLng(27.783519444444,-97.561505555556),L.latLng(27.783502777778,-97.559883333333),L.latLng(27.7815,-97.559911111111),L.latLng(27.781516666667,-97.561533333333),L.latLng(27.783519444444,-97.561505555556)])}); 
var layer_20160716_cc_p4_cotton_sorghum_mosaic = L.tileLayer('https://chub.gdslab.org/uas_data/uploads/products/2016_Corpus_Christi_Cotton_and_Sorghum/Phantom_4_Pro/RGB/07-16-2016/20160716/RGB_Ortho/20160716_cc_p4_cotton_sorghum_mosaic/Display/{z}/{x}/{y}.png', {tms: true, zIndex: 56, bounds: L.latLngBounds([L.latLng(27.783636111111,-97.561563888889),L.latLng(27.783619444444,-97.559763888889),L.latLng(27.781188888889,-97.559797222222),L.latLng(27.781208333333,-97.561597222222),L.latLng(27.783636111111,-97.561563888889)])}); 
var layer_20160728_cc_p4_cotton_sorghum_mosaic = L.tileLayer('https://chub.gdslab.org/uas_data/uploads/products/2016_Corpus_Christi_Cotton_and_Sorghum/Phantom_4_Pro/RGB/07-28-2016/20160728/RGB_Ortho/20160728_cc_p4_cotton_sorghum_mosaic/Display/{z}/{x}/{y}.png', {tms: true, zIndex: 57, bounds: L.latLngBounds([L.latLng(27.783477777778,-97.561505555556),L.latLng(27.783461111111,-97.559869444444),L.latLng(27.781338888889,-97.559897222222),L.latLng(27.781355555556,-97.561533333333),L.latLng(27.783477777778,-97.561505555556)])}); 
var layer_20160802_cc_p4_cotton_mosaic = L.tileLayer('https://chub.gdslab.org/uas_data/uploads/products/2016_Corpus_Christi_Cotton_and_Sorghum/Phantom_4_Pro/RGB/08-02-2016/20160802/RGB_Ortho/20160802_cc_p4_cotton_mosaic/Display/{z}/{x}/{y}.png', {tms: true, zIndex: 58, bounds: L.latLngBounds([L.latLng(27.783486111111,-97.561497222222),L.latLng(27.783477777778,-97.560522222222),L.latLng(27.78145,-97.56055),L.latLng(27.781461111111,-97.561525),L.latLng(27.783486111111,-97.561497222222)])}); 
var layer_20160812_cc_p4_cotton_mosaic = L.tileLayer('https://chub.gdslab.org/uas_data/uploads/products/2016_Corpus_Christi_Cotton_and_Sorghum/Phantom_4_Pro/RGB/08-12-2016/20160812/RGB_Ortho/20160812_cc_p4_cotton_mosaic/Display/{z}/{x}/{y}.png', {tms: true, zIndex: 59, bounds: L.latLngBounds([L.latLng(27.783441666667,-97.561480555556),L.latLng(27.783430555556,-97.560538888889),L.latLng(27.781469444444,-97.560563888889),L.latLng(27.781477777778,-97.561505555556),L.latLng(27.783441666667,-97.561480555556)])}); 
var layer_20180719_cc_p4p_cotton_mosaic = L.tileLayer('https://chub.gdslab.org/uas_data/uploads/products/2016_Corpus_Christi_Cotton_and_Sorghum/Phantom_4_Pro/RGB/07-19-2018/20180719/RGB_Ortho/20180719_cc_p4p_cotton_mosaic/Display/{z}/{x}/{y}.png', {tms: true, zIndex: 60, bounds: L.latLngBounds([L.latLng(27.783433333333,-97.573544444444),L.latLng(27.783416666667,-97.571777777778),L.latLng(27.781041666667,-97.571808333333),L.latLng(27.781061111111,-97.573575),L.latLng(27.783433333333,-97.573544444444)])}); 
map.createPane('pane_plot_boundary_1'); 
map.getPane('pane_plot_boundary_1').style.zIndex = 61; 
map.getPane('pane_plot_boundary_1').style.pointerEvents = 'none'; 
var layer_plot_boundary_1 = new L.GeoJSON.AJAX('/uas_data/uploads/products/2016_Corpus_Christi_Cotton_and_Sorghum/03/25/2021/GeoJSON/plot_boundary_1/plot_boundary_1_converted.geojson'); 
map.createPane('pane_plot_boundary5'); 
map.getPane('pane_plot_boundary5').style.zIndex = 62; 
map.getPane('pane_plot_boundary5').style.pointerEvents = 'none'; 
var layer_plot_boundary5 = new L.GeoJSON.AJAX('/uas_data/uploads/products/2016_Corpus_Christi_Cotton_and_Sorghum/04/01/2021//GeoJSON/plot_boundary5/plot_boundary5_converted.geojson'); 
map.createPane('pane_plot_boundary3'); 
map.getPane('pane_plot_boundary3').style.zIndex = 63; 
map.getPane('pane_plot_boundary3').style.pointerEvents = 'none'; 
var layer_plot_boundary3 = new L.GeoJSON.AJAX('/uas_data/uploads/products/2016_Corpus_Christi_Cotton_and_Sorghum/04/01/2021//GeoJSON/plot_boundary3/plot_boundary3_converted.geojson'); 
map.createPane('pane_plot_boundary8'); 
map.getPane('pane_plot_boundary8').style.zIndex = 64; 
map.getPane('pane_plot_boundary8').style.pointerEvents = 'none'; 
var layer_plot_boundary8 = new L.GeoJSON.AJAX('/uas_data/uploads/products/2016_Corpus_Christi_Cotton_and_Sorghum/04/09/2021//GeoJSON/plot_boundary8/plot_boundary8_converted.geojson'); 


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
			name: '04/12/2016',
active: 'true',
			layer: layer_20160412_P2VP_Cotton_Sorghum_30m_mosaic
		},
		{
			name: '04/27/2016',
			layer: layer_20160427_P4_Cotton_Sorghum_30m_mosaic
		},
		{
			name: '05/16/2016',
			layer: layer_20160516_cc_p4_cotton_sorghum_mosaic
		},
		{
			name: '05/31/2016',
			layer: layer_20160531_cc_p4_cotton_sorghum_mosaic
		},
		{
			name: '06/14/2016',
			layer: layer_20160614_cc_p4_cotton_sorghum_mosaic
		},
		{
			name: '06/27/2016',
			layer: layer_20160627_cc_p4_cotton_sorghum_20m_mosaic
		},
		{
			name: '07/16/2016',
			layer: layer_20160716_cc_p4_cotton_sorghum_mosaic
		},
		{
			name: '07/28/2016',
			layer: layer_20160728_cc_p4_cotton_sorghum_mosaic
		},
		{
			name: '08/02/2016',
			layer: layer_20160802_cc_p4_cotton_mosaic
		},
		{
			name: '08/12/2016',
			layer: layer_20160812_cc_p4_cotton_mosaic
		},
		{
			name: '07/19/2018',
			layer: layer_20180719_cc_p4p_cotton_mosaic
		},
	]
},
{
	group: 'GeoJSON',
	layers: [
		{
			name: '03/25/2021',
			layer: layer_plot_boundary_1
		},
		{
			name: '04/01/2021',
			layer: layer_plot_boundary5
		},
		{
			name: '04/01/2021',
			layer: layer_plot_boundary3
		},
		{
			name: '04/09/2021',
			layer: layer_plot_boundary8
		},
	]
},

            ];

            var panelLayers = new L.Control.PanelLayers(baseLayers, overLayers, {collapsibleGroups: true});
            map.addControl(panelLayers);

// Variable containing page URL
var siteURL = location.href;
// URL is saved on custId
document.getElementById("custId").value = siteURL;

// When click on BLUE marker, send URL and go to page
function onClick(e) {
    document.myform.submit();
}

//Start location BLUE marker
const marker = L.marker([0, 0]).on('click', onClick).addTo(map).bindTooltip("Click here to upload a picture",
    {
        permanent: true,
        direction: 'right'
    }
);

            // When mouse pass over, show message on marker
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

var picture_data=JSON.parse($('#picture_data').val());

var project_name_dash='<?php echo($project_name_dash) ?>';

// Function to create marker depending on the number of pictures
function Addmarker(markerArray) {

    for (var i = 0; i < markerArray; i++) {
            // Text and image on popup when click on RED markers
    const src = "https://chub.gdslab.org/uas_data/uploads/photos/"+project_name_dash+'/'+picture_data[i]['image']; // Needs to be fixed and not harcoded
    var name=picture_data[i]['user_name'];
     var date_time=picture_data[i]['date_time'];
    const popupContent = document.createElement("div")
    popupContent.innerHTML = "<img style='max-height:300px; max-width:300px;' src='" + src + "'>"+"<a target='_blank' href='" + src + "'>See the image</a>"+"<p><strong>User: </strong>"+name+"</p>"+"<p><strong>Date & Time: </strong>"+date_time+"</p>";
        this[dynamicname + i] = L.marker(
            [tempArray[i][0], tempArray[i][1]],
            {icon: redIcon})
            .bindPopup(
                popupContent,
                { maxWidth: "auto" }
            )
            .addTo(map);

            // alert(i);
    }
    console.log(tempArray);
    console.log(picture_data);


}
Addmarker(num_pictures);


            //let firstime = true;
            function success(position) {
                const latitude = position.coords.latitude; // Set latitude
                const longitude = position.coords.longitude;// Set longitude
                // Update markers location
                 marker.setLatLng([latitude, longitude]).addTo(map);
                 //marker.setLatLng(["27.7823", "-97.5606"]).addTo(map);

                status.textContent = '';
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
        }
    </script>

</head>

<body onload="all();">
<br/>

<div id="map"></div>

<form name="myform" action="/uas_tools/visualization_generator/Resources/PHP/upload_picture.php" method="POST">
    <input type="hidden" id="custId" name="custId">
    <a href="javascript: submitform()"></a>
</form>

</body>
</html>