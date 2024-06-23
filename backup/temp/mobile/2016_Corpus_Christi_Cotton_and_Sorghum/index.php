<?php
// File containing System Variables
define("LOCAL_PATH_ROOT", $_SERVER["DOCUMENT_ROOT"]);
require LOCAL_PATH_ROOT . '/uas_tools/system_management/centralized_management.php';

require_once("SetDBConnection.php");

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

// Get the number of images for an specific project from photos_upload
$sql = "select count(Project) from photos_upload where Project = 1";

$result = mysqli_query($con, $sql);
// This contains the number of pictures stored under the Project
$num_pictures = mysqli_fetch_row($result);

//echo('test: '.$num_pictures[0]);

//echo "<pre>";
//print_r($page);
//echo "</pre>";
mysqli_close($con);
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

    <!--    <script>-->
    <!--        function fileSelected() {-->
    <!--            var count = document.getElementById('fileToUpload').files.length;-->
    <!--            document.getElementById('details').innerHTML = "";-->

    <!--            for (var index = 0; index < count; index++) {-->
    <!--                var file = document.getElementById('fileToUpload').files[index];-->
    <!--                var fileSize = 0;-->

    <!--                if (file.size > 1024 * 1024)-->
    <!--                    fileSize = (Math.round(file.size * 100 / (1024 * 1024)) / 100).toString() + 'MB';-->
    <!--                else-->
    <!--                    fileSize = (Math.round(file.size * 100 / 1024) / 100).toString() + 'KB';-->

    <!--                document.getElementById('details').innerHTML += 'Name: ' + file.name + '<br>Size: ' + fileSize + '<br>Type: ' + file.type;-->
    <!--                document.getElementById('details').innerHTML += '<p>';-->
    <!--            }-->
    <!--        }-->

    <!--        function uploadFile() {-->
    <!--            var fd = new FormData();-->
    <!--            var count = document.getElementById('fileToUpload').files.length;-->

    <!--            var latitude = 27.7823;//position.coords.latitude; //Get latitude-->
    <!--            var longitude = -97.5606;//position.coords.longitude; //Get longitude-->

    <!--                        -->

    <!--            for (var index = 0; index < count; index++) {-->
    <!--                var file = document.getElementById('fileToUpload').files[index];-->
    <!--                fd.append('myFile', file);-->
    <!--            }-->
    <!--            // Send input from Notes-->
    <!--            fd.append('notes', document.getElementById('notes').value);-->
    <!--            // Send coordinates-->
    <!--            fd.append('coord_latitude', latitude);-->
    <!--            fd.append('coord_longitude', longitude);-->

    <!--            var xhr = new XMLHttpRequest();-->

    <!--            xhr.upload.addEventListener("progress", uploadProgress, false);-->
    <!--            xhr.addEventListener("load", uploadComplete, false);-->
    <!--            xhr.addEventListener("error", uploadFailed, false);-->
    <!--            xhr.addEventListener("abort", uploadCanceled, false);-->
    <!--            xhr.open("POST", "/web/uas_tools/visualization_generator/V2/Resources/PHP/savetofile.php");-->
    <!--            xhr.send(fd);-->
    <!--        }-->

    <!--        function uploadProgress(evt) {-->

    <!--            if (evt.lengthComputable) {-->
    <!--                var percentComplete = Math.round(evt.loaded * 100 / evt.total);-->
    <!--                document.getElementById('progress').innerHTML = percentComplete.toString() + '%';-->
    <!--            } else {-->
    <!--                document.getElementById('progress').innerHTML = 'unable to compute';-->
    <!--            }-->
    <!--        }-->

    <!--        function uploadComplete(evt) {-->
    <!--            /* This event is raised when the server send back a response */-->
    <!--            alert(evt.target.responseText);-->
    <!--        }-->

    <!--        function uploadFailed(evt) {-->
    <!--            alert("There was an error attempting to upload the file.");-->
    <!--        }-->

    <!--        function uploadCanceled(evt) {-->
    <!--            alert("The upload has been canceled by the user or the browser dropped the connection.");-->
    <!--        }-->

    <!--    </script>-->

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
var layer_20160627_cc_p4_cotton_sorghum_20m_mosaic = L.tileLayer('https://chub.gdslab.org/uas_data/uploads/products/2016_Corpus_Christi_Cotton_and_Sorghum/Phantom_4_Pro/RGB/06-27-2016/20160627/RGB_Ortho/20160627_cc_p4_cotton_sorghum_20m_mosaic/Display/{z}/{x}/{y}.png', {tms: true, zIndex: 52, bounds: L.latLngBounds([L.latLng(27.783519444444,-97.561505555556),L.latLng(27.783502777778,-97.559883333333),L.latLng(27.7815,-97.559911111111),L.latLng(27.781516666667,-97.561533333333),L.latLng(27.783519444444,-97.561505555556)])}); 
map.createPane('pane_plot_boundary_JOSE'); 
map.getPane('pane_plot_boundary_JOSE').style.zIndex = 53; 
map.getPane('pane_plot_boundary_JOSE').style.pointerEvents = 'none'; 
var layer_plot_boundary_JOSE = new L.GeoJSON.AJAX('/uas_data/uploads/products/2016_Corpus_Christi_Cotton_and_Sorghum/Phantom_4_Pro/RGB/06-27-2016/20160627/GeoJSON/plot_boundary_JOSE/plot_boundary_JOSE.geojson'); 


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
			name: '06/27/2016',
			layer: layer_20160627_cc_p4_cotton_sorghum_20m_mosaic
		},
	]
},
{
	group: 'GeoJSON',
	layers: [
		{
			name: '06/27/2016',
			layer: layer_plot_boundary_JOSE
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

            //Inicia el marcador
            // const marker = L.marker([0, 0]).bindPopup("You are here").on('click', onClick).addTo(map);
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


            // Add red markers for images
            // const marker1 = L.marker([27.7823, -97.5606], {icon: greenIcon}).bindPopup("Image 1").addTo(map);

            // for (i = 0; i == 5; i++) {
            //     var test = -97.5606+i;
            var num_pictures = '<?php echo $num_pictures;?>';
            console.log(num_pictures);

            // var i;
            // for (i = 0; i === num_pictures; i++) {
            //     const marker1 = L.marker([27.78233, -97.5608], {icon: redIcon}).on('click', onClick1).bindPopup("Image 1").addTo(map);
            //     console.log('i');
            // }

            let str = '';

            for (let i = 0; i < 9; i++) {
                str = str + i;
            }

            console.log(str);

const marker1 = L.marker([27.78233, -97.5608], {icon: redIcon}).on('click', onClick1).bindPopup("Image 1").addTo(map);
const marker2 = L.marker([27.78255, -97.5610], {icon: redIcon}).on('click', onClick1).bindPopup("Image 2").addTo(map);
const marker3 = L.marker([27.78277, -97.5612], {icon: redIcon}).on('click', onClick1).bindPopup("Image 3").addTo(map);
const marker4 = L.marker([27.78299, -97.5613], {icon: redIcon}).on('click', onClick1).bindPopup("Image 4").addTo(map);

            // When click on red marker, open a new tab with the image full size
            function onClick1(e) {
                //console.log("https://www.w3schools.com");
                window.open("/uas_tools/visualization_generator/V2/Resources/PHP/uploads/image.jpg");
            }

            //var siteURL = document.URL;
            //window.location.href = "/uas_tools/visualization_generator/V2/Resources/PHP/upload_picture.html" + siteURL;

            // $.ajax({
            //     type: 'POST',
            //     url: '/uas_tools/visualization_generator/V2/Resources/PHP/upload_picture.html',
            //     data: 'url=' + window.location.toString(),
            //     success: function(response) {
            //         console.log(response);
            //     }
            // });

            // When click on blue marker, go to
            function onClick(e) {
                //console.log("https://www.w3schools.com");
                //window.open("/uas_tools/visualization_generator/V2/Resources/PHP/upload_picture.html");

                $.ajax({
                    type: 'POST',
                    url: '/uas_tools/visualization_generator/V2/Resources/PHP/upload_picture.php',
                    //url: 'https://chub.gdslab.org/uas_tools/visualization_generator/V2/Resources/PHP/upload_picture.php',
                    data: { url: location.href },
                    success: function(response) {
                        console.log(response);
                    },
                    error: function () {
                        // your error callback
                    }
                });

                window.open("/uas_tools/visualization_generator/V2/Resources/PHP/upload_picture.php");
                //alert(document.URL);
            }
            //}

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

<!--<form method="post" action="{next_page}">-->
<!--    <input type="hidden" name="cat" value="{category}" />-->
<!--    <input type="hidden" name="var1" value="val1" />-->
<!--    <input type="hidden" name="var2" value="val2" />-->
<!--    <input type="submit" value="Button_TO_Next_Page" />-->
<!--</form>-->

<!--<br><br><br><br>-->
</body>
</html>
