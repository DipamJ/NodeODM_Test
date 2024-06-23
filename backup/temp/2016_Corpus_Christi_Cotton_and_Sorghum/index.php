<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>2016 Corpus Christi Cotton and Sorghum</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link rel="stylesheet" href="/css/leaflet.css"/>
    <link rel="stylesheet" href="/css/leaflet-panel-layers.css"/>

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
        }
    </style>
</head>

<body>
<br/>
<div id="map"></div>

<script src="/js/leaflet.js"></script>
<script src="/js/leaflet-panel-layers.js"></script>
<script src="/js/jquery.min.js"></script>
<script src="/js/leaflet-ajax/dist/leaflet.ajax.js"></script>
<script src="/js/legend.js"></script>
<link rel="stylesheet" href="/css/legend.css"/>

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

</script>

</body>
</html>
