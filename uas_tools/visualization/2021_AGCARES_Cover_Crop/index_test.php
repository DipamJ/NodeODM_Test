<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta charset="utf-8" name=”viewport” content=”width=device-width, initial-scale=1″>
  <title>2021 AG-CARES Cover Crop</title>
  <link rel="stylesheet" href="/libraries/leaflet/leaflet.css"/>
  <link rel="stylesheet" href="/libraries/css/leaflet-panel-layers.css"/>

  <!-- <div class="header">
    <h3>
        Supported by:
    </h3>
</div> -->

    <style>
        body {
            margin: 0;
            padding: 0;
        }

        #map {
            position: absolute;
            top: 0;
            bottom: 0;
            width: 100%;
            z-index: 1;
        }

        .container {
          position: absolute;
          height: auto;
          right: 20px;
          bottom: 1%;
          z-index: 2;
        }

        .logo_merge {
            width: 420px;
            z-index: 2;
        }

        .img_title{
          background: #444;
          color: #f6f5f5;
          font-size: 15px;
          font-weight: 700;
          padding: 8px 12px;
          z-index: 2;
          width: 397.5px;
        }

        h3{
          padding: 0px;
          margin: 0px;
        }

    </style>
</head>

<body>
<br/>
<div id="map"></div>

<script src="/libraries/js/leaflet.js"></script>
<script src="/libraries/js/leaflet-panel-layers.js"></script>
<script src="/libraries/js/jquery.min.js"></script>
<script src="/libraries/js/leaflet-ajax/dist/leaflet.ajax.js"></script>
<script src="/libraries/js/legend.js"></script>
<link rel="stylesheet" href="/libraries/css/legend.css"/>

<script>
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
</script>

<script>
    var map = L.map('map', {
        center: L.latLng([32.775051,-101.947930]),
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
    })
    ;

    var osm_map = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        zIndex: 0
    });

    var mapbox = L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token={accessToken}', {
        attribution: 'Imagery from <a href="https://mapbox.com/about/maps/">MapBox</a> &mdash; Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        subdomains: 'abcd',
        id: 'mapbox.satellite',
        //accessToken: 'pk.eyJ1IjoiaGFtZG9yaSIsImEiOiJjaWZmZzBwbjI4ZGdqc21seDFhOHA5dGcxIn0.4An46DNTDt97W992MRRWoQ',
        accessToken: 'sk.eyJ1Ijoiam9zZWx1aXNsYW5kaXZhcnMiLCJhIjoiY2tvMnpkMG12MHlyNzJwcXd6eDY5enowcSJ9.H8vS09OkK9hVtn8OYE3jrA',
        maxNativeZoom: 19,
        zIndex: 0
    });

    // Layers
    var layer_20210127_lb_p4r_cover_crop_mosaic_shiftf = L.tileLayer('https://wtxcotton.uashubs.com/uas_data/uploads/products/2021_AGCARES_Cover_Crop/DJI_Phantom_4_RTK/RGB/01-27-2021/20210127/ortho/20210127_lb_p4r_cover_crop_mosaic_shift/Display/{z}/{x}/{y}.png', {tms: true, zIndex: 50, bounds: L.latLngBounds([L.latLng(32.776219444444,-101.94776944444),L.latLng(32.776247222222,-101.94661666667),L.latLng(32.773713888889,-101.94653333333),L.latLng(32.773686111111,-101.94768611111),L.latLng(32.776219444444,-101.94776944444)])});
var layer_20210226_lb_p4r_cover_crop_mosaic_shiftf = L.tileLayer('https://wtxcotton.uashubs.com/uas_data/uploads/products/2021_AGCARES_Cover_Crop/DJI_Phantom_4_RTK/RGB/02-26-2021/20210226/ortho/20210226_lb_p4r_cover_crop_mosaic_shift/Display/{z}/{x}/{y}.png', {tms: true, zIndex: 51, bounds: L.latLngBounds([L.latLng(32.776219444444,-101.94776944444),L.latLng(32.776247222222,-101.94661666667),L.latLng(32.773713888889,-101.94653333333),L.latLng(32.773686111111,-101.94768611111),L.latLng(32.776219444444,-101.94776944444)])});
var layer_20210413_lb_p4r_cover_crop_mosaic_shiftf = L.tileLayer('https://wtxcotton.uashubs.com/uas_data/uploads/products/2021_AGCARES_Cover_Crop/DJI_Phantom_4_RTK/RGB/04-13-2021/20210413/ortho/20210413_lb_p4r_cover_crop_mosaic_shift/Display/{z}/{x}/{y}.png', {tms: true, zIndex: 52, bounds: L.latLngBounds([L.latLng(32.776219444444,-101.94776944444),L.latLng(32.776247222222,-101.94661666667),L.latLng(32.773713888889,-101.94653333333),L.latLng(32.773686111111,-101.94768611111),L.latLng(32.776219444444,-101.94776944444)])});
var layer_20210522_lb_p4r_cover_crop_mosaic_shiftf = L.tileLayer('https://wtxcotton.uashubs.com/uas_data/uploads/products/2021_AGCARES_Cover_Crop/DJI_Phantom_4_RTK/RGB/05-22-2021/20210522/ortho/20210522_lb_p4r_cover_crop_mosaic_shift/Display/{z}/{x}/{y}.png', {tms: true, zIndex: 53, bounds: L.latLngBounds([L.latLng(32.776219444444,-101.94777222222),L.latLng(32.776247222222,-101.94661666667),L.latLng(32.773713888889,-101.94653333333),L.latLng(32.773686111111,-101.94768611111),L.latLng(32.776219444444,-101.94777222222)])});
map.createPane('pane_plot_boundary_wtx');
map.getPane('pane_plot_boundary_wtx').style.zIndex = 54;
map.getPane('pane_plot_boundary_wtx').style.pointerEvents = 'none';
var layer_plot_boundary_wtx = new L.GeoJSON.AJAX('/uas_data/uploads/products/2021_AGCARES_Cover_Crop/06/29/2021//GeoJSON/plot_boundary_wtx/plot_boundary_wtx_converted.geojson');


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
			name: '01/27/2021',
active: 'true',
			layer: layer_20210127_lb_p4r_cover_crop_mosaic_shiftf
		},
		{
			name: '02/26/2021',
			layer: layer_20210226_lb_p4r_cover_crop_mosaic_shiftf
		},
		{
			name: '04/13/2021',
			layer: layer_20210413_lb_p4r_cover_crop_mosaic_shiftf
		},
		{
			name: '05/22/2021',
			layer: layer_20210522_lb_p4r_cover_crop_mosaic_shiftf
		},
	]
},
{
	group: 'GeoJSON',
	layers: [
		{
			name: '06/29/2021',
			layer: layer_plot_boundary_wtx
		},
	]
},

    ];

    var panelLayers = new L.Control.PanelLayers(baseLayers, overLayers, {collapsibleGroups: true});
    map.addControl(panelLayers);

</script>

<div class="container">
  <h3 class="img_title">This project is supported by</h3>
<img alt="Sponsor" class="logo_merge" src="/resources/images/supported_words.png">
</div>

</body>
</html>
