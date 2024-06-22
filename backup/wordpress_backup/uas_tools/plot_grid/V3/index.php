<?php
// File containing System Variables
define("LOCAL_PATH_ROOT", $_SERVER["DOCUMENT_ROOT"]);
require LOCAL_PATH_ROOT . '/uas_tools/system_management/centralized_management.php';

?>

            <!DOCTYPE html>
            <html lang="en">
            <head>
                <title>Plot Grid</title>
                <meta charset="utf-8"/>
                <link rel="stylesheet" type="text/css" href="Resources/style.css">
                <script src="Resources/JS/jquery.min.js"></script>

                <script src="Resources/JS/Chosen/chosen.jquery.min.js"></script>
                <link rel="stylesheet" type="text/css" href="Resources/JS/Chosen/chosen.css">

                <link rel="stylesheet" href="Resources/JS/Leaflet/leaflet.css"/>
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

                    .legend span, .legend label {
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
                            <li><a href="#info">Info</a></li>
                            <li><a href="#file">Export</a></li>
                        </ul>
                        <div id="layer">

                            <div style="text-align:left; margin: 5px auto; width: 98%">
                                <div class="label">Project</div>
                                <select id="project-list" class="select-large"></select>
                            </div>

                            <div style="clear:both"></div>
                            <div style="text-align:left; margin: 5px auto; width: 98%">
                                <div class="label">Data Product</div>
                                <select id="product-type-list" class="select-small"></select>
                            </div>

                            <div style="clear:both"></div>

                            <fieldset style="width: 60%;">
                                <legend>Layer List</legend>
                                <ul id="added-layer-list">
                                </ul>
                            </fieldset>
                        </div>

                        <div id="grid">
                            <div class="label label-small">Plot</div>
                            <select id="plot-list" class="small"></select>

                            <input type="file" id="importedFile" accept=".plot" style="display: none;"/>
                            <input type="button" value="Import" onclick="$('#importedFile').click(); return false;"
                                   class="button" style="float:left; margin: 2px"/>

                            <br>

                            <fieldset id="plot-boundary-properties" style="width: 90%; display:none">
                                <legend>Plot Boundary Properties</legend>

                                <div class="label label-small">Top Left Lat</div>
                                <div class="input"><input id="top-left-lat" type="text" class="small" disabled/></div>

                                <div class="label label-small">Top Left Lng</div>
                                <div class="input"><input id="top-left-lng" type="text" class="small" disabled/></div>

                                <div style="clear:both"></div>

                                <div class="label label-small">Plot Width</div>
                                <div class="input"><input id="plot-width" type="text" class="small" disabled/></div>

                                <div class="label label-small">Plot Height</div>
                                <div class="input"><input id="plot-height" type="text" class="small" disabled/></div>

                                <div style="clear:both"></div>

                                <div class="label label-small">Angle</div>
                                <div class="input"><input id="rotation-angle" type="text" class="small" disabled/></div>

                                <div class="label label-small">EPSG</div>
                                <div class="input"><input id="epsg-code" type="text" class="small" disabled/></div>

                                <div style="clear:both"></div>

                                <div class="label label-small">Var Per Col</div>
                                <div class="input"><input id="var-count" type="text" class="small" disabled/></div>

                                <div style="clear:both"></div>

                                <div class="full-width" style="margin: 5px">Offsets</div>
                                <div style="clear:both"></div>
                                <div class="full-width" id='offsets'
                                     style="height: 60px; overflow-y: overlay; border: 1px solid gray;padding: 2px; margin: 10px 0"></div>

                                <div style="clear:both"></div>

                                <div class="full-width" style="margin: 5px">Vertical Shift</div>
                                <div style="clear:both"></div>
                                <div class="full-width" id='vshifts'
                                     style="height: 60px; overflow-y: overlay; border: 1px solid gray;padding: 2px; margin: 10px 0"></div>
                                <div style="clear:both"></div>
                            </fieldset>

                            <br>

                            <fieldset id="grid-properties" style="width: 90%; display:none">
                                <legend>Plot Grid Properties</legend>

                                <div class="label">Number of Rows</div>
                                <div class="input"><input id="row-count" type="text" class="small" style="width: 28px"/>
                                </div>

                                <div class="label label-small">Row Height</div>
                                <div class="input"><input id="row-height" type="text" class="small"
                                                          style="width: 28px"/></div>


                                <div style="clear:both"></div>

                                <div class="label">Number of Cols</div>
                                <div class="input"><input id="col-count" type="text" class="small" style="width: 28px"/>
                                </div>

                                <div class="label label-small">Col Width</div>
                                <div class="input"><input id="col-width" type="text" class="small" style="width: 28px"/>
                                </div>


                                <div style="clear:both"></div>

                                <input id="generate-grid" type="button" value="Generate"
                                       onclick="GeneratePlotGrid(); return false;" class='button'/>
                            </fieldset>
                            <div style="clear:both"></div>
                        </div>

                        <div id="info">
                            <fieldset style="width: 90%;">
                                <legend>Info Map</legend>
                                <div style="clear:both"></div>
                                <div id="info-map-wrapper" class="info-wrapper"></div>
                            </fieldset>
                            <fieldset style="width: 90%;">
                                <legend>Info Code</legend>
                                <div style="clear:both"></div>
                                <div id="info-code-wrapper"></div>
                            </fieldset>
                            <div style="clear:both"></div>

                        </div>

                        <div id="file">
                            <input type="button" value="Export" onclick="Export(); return false;" class='button'/>
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