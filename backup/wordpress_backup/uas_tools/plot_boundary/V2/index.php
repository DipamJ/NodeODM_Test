<?php 
	
	require_once("Resources/PHP/SetDBConnection.php");

// Log Document
function _log($str)
{
    // log to the output
    $log_str = date('d.m.Y') . ": {$str}\r\n";
    echo $log_str;

    // log to file
    if (($fp = fopen('upload_log.txt', 'a+')) !== false) {
        fputs($fp, $log_str);
        fclose($fp);
    }
}

	$con = SetDBConnection();
	
	if(mysqli_connect_errno())
	{
		echo "Failed to connect to database server: ".mysqli_connect_error();
	}
	else
	{
		session_start();
		$userName = $_SESSION["username"];
		$roles = $_SESSION["userroles"];
		$groups = $_SESSION["groups"];
		unset($_SESSION["page"]);
	
		if (!$roles){
			$_SESSION["page"] =  "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
			header("Location: https://uashub.tamucc.edu/uas-tools/");
			exit();
		} else  {
			$pageName = basename(__DIR__);
			if ($pageName == "V2"){
				$pageName = basename(realpath(__DIR__ . "/.."));

				_log('$pageName: ');
			}
            //echo '$pageName: ';

			$sql = "SELECT * FROM page_access WHERE Page = '$pageName'";
			$allowedGroups = array();
			if ($result = mysqli_query($con,$sql)){
				if ($row = mysqli_fetch_assoc($result)){
					$allowedGroups = explode(";", $row["Groups"]);
					$accessGroupsStr = $row["Groups"];
				}
			}
			
			$intersect = array_intersect($groups,$allowedGroups);
			if (sizeof($intersect) > 0){
				
		
?>

				<!DOCTYPE html>
				<html>
					<head>
						<title>Plot Boundary</title>
						<meta charset="utf-8" />
						<link rel="stylesheet" type="text/css" href="Resources/style.css">
						<script src="Resources/JS/jquery.min.js"></script>
						
						<script src="Resources/JS/Chosen/chosen.jquery.min.js"></script>
						<link rel="stylesheet" type="text/css" href="Resources/JS/Chosen/chosen.css">
						
						<link rel="stylesheet" href="Resources/JS/Leaflet/leaflet.css" />
						<script src="Resources/JS/Leaflet/leaflet-src.js"></script>
						<link rel="stylesheet" type="text/css" href="Resources/JS/JqueryUI/jquery-ui.css">
						<script src="Resources/JS/JqueryUI/jquery-ui.min.js"></script>
						
						<script src="Resources/JS/plot-boundary.js"></script>
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
						<script>
							$(document).ready(function(){

								$('#loading').hide();
								
								
								GetProjectList();
					
								$(":button").mouseup(function(){
									$(this).blur();
								});
					
								$('#project-list').on('change', function() {
									GetLayerList($("#project-list").val(), $("#product-type-list").val());
									var selectedProject = $('#project-list option:selected');
									CenterMap(selectedProject.attr('data-centerlat') + "\," + selectedProject.attr('data-centerlng') ,selectedProject.attr('data-defaultzoom'));
								});
								$('#product-type-list').on('change', function() {

									GetLayerList($("#project-list").val(), $("#product-type-list").val());

								});
						
								$('#plot-list').on('change', function() {
									currentPlot = $(this).val();
									UnHighlighAll();
									HighlightPlot($(this).val());
									ShowPlotInfo();
								});
								
								$("#tabs").tabs({
									active: 0
								});
								
								$('#importedFile').change(function(e){
									var fileName = $('#importedFile').val().split('\\').pop();
									if (fileName !== ""){
										Import();
									}
								});
							});	
							function Download(filename, text) {
								var pom = document.createElement('a');
								pom.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(text));
								pom.setAttribute('download', filename);
								pom.style.display = 'none';
								document.body.appendChild(pom);
								pom.click();
								document.body.removeChild(pom);
							}
						</script>
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
										<li><a href="#export">Export</a></li>
									</ul>
									<div id="layer">
									
										<div style="text-align:left; margin: 5px auto; width: 98%">
											<div class="label" >Project</div>
											<select id="project-list" class="select-large"></select>
										</div>
										
										<div style="clear:both"></div>
									
										<div style="text-align:left; margin: 5px auto; width: 98%">
											<div class="label" >Data Product</div>
											<select id="product-type-list" class="select-small"></select>
										</div>

										<div style="clear:both"></div>

										<fieldset style="width: 80%;">
											<legend>Layer List</legend>
											<ul id="added-layer-list" >
											</ul>
										</fieldset>
									</div>
									<div id="gen-boundary">
										<fieldset  id ="gen-plot-boundary-properties"  style="width: 90%">

											<legend>Plot Properties</legend>
											<input id='rectangle-id' type="hidden"/> 

											<div class="label label-small">Name</div>
											<div class="input"><input id="row-name" type="text" class="medium" value='row-1' /></div>
											
											<div style="clear:both"></div>
											
											<div class="label label-small">Top Left Lat</div>
											<div class="input"><input id="top-left-lat" type="text" class="small" value='27.77485'/></div>

											<div class="label label-small">Top Left Lng</div>
											<div class="input"><input id="top-left-lng" type="text" class="small" value='-97.5608'/></div>

											<div style="clear:both"></div>

											<div class="label label-small">Plot Width</div>
											<div class="input"><input id="plot-width" type="text" class="small" value='1'/></div>
											
											<div class="label label-small">Plot Height</div>
											<div class="input"><input id="plot-height" type="text" class="small" value='10'/></div>

											<div style="clear:both"></div>

											<div class="label label-small">Angle</div>
											<div class="input"><input id="rotation-angle" type="text" class="small" value='0'/></div>

											<div class="label label-small">EPSG</div>
											<div class="input"><input id="epsg-code" type="text" class="small" value='32614'/></div>
											
											<div style="clear:both"></div>
											
											<div class="label label-small">Var Per Col</div>
											<div class="input"><input id="var-count" type="text" class="small" value='70' /></div>
											<div class="input"><input type="button" value="Set" onclick="SetVarNumber(''); return false;" class='button' style='width: 50px; margin:0'/></div>

											<div class="input"><input type="button" value="Calculate" onclick="Calculate(); return false;" class='button' style='width: 75px; margin:0'/></div>

											
											<div style="clear:both"></div>
											
											<div class="full-width" style="margin: 5px">Offsets</div>
											<div class='apply-all'> 
												<div class="label label-small">Apply to all</div>
												<div class="input"><input id="applied-offset" type="text" style="width: 33px" /></div>
												<div class="input"><input type="button" value="Apply" onclick="ApplyAll('offset',''); return false;" class='button' style='width: 50px; margin:0'/></div>
											</div>
											<div style="clear:both"></div>
											<div class="full-width" id='offsets' style="height: 90px; overflow-y: overlay; border: 1px solid gray;padding: 2px; margin: 10px 0"></div>
											
											<div style="clear:both"></div>
											
											<div class="full-width" style="margin: 5px">Vertical Shift</div>
											<div class='apply-all'> 
												<div class="label label-small">Apply to all</div>
												<div class="input"><input id="applied-vshift" type="text" style="width: 33px" /></div>
												<div class="input"><input type="button" value="Apply" onclick="ApplyAll('vshift',''); return false;" class='button' style='width: 50px; margin:0'/></div>
											</div>
											<div style="clear:both"></div>
											<div class="full-width" id='vshifts'  style="height: 90px; overflow-y: overlay; border: 1px solid gray;padding: 2px; margin: 10px 0"></div>
											
											<div style="clear:both"></div>

											<input id="generate-boundary" type="button" value="Generate" onclick="GeneratePlotBoundary(); return false;" class='button'/>
									
										</fieldset>
									</div>
									
									<div id="mod-boundary">
										<div class="label label-small">Plot</div>
										<select id ="plot-list" class="small"></select>
										<fieldset  id ="mod-plot-boundary-properties"  style="width: 90%; display:none">
											<legend>Selected Boundary Properties</legend>
											
											<div class="label label-small">Top Left Lat</div>
											<div class="input"><input id="mod-top-left-lat" type="text" class="small" value='27.77485'/></div>

											<div class="label label-small">Top Left Lng</div>
											<div class="input"><input id="mod-top-left-lng" type="text" class="small" value='-97.5608'/></div>

											<div style="clear:both"></div>

											<div class="label label-small">Plot Width</div>
											<div class="input"><input id="mod-plot-width" type="text" class="small" value='1'/></div>
											
											<div class="label label-small">Plot Height</div>
											<div class="input"><input id="mod-plot-height" type="text" class="small" value='10'/></div>

											<div style="clear:both"></div>

											<div class="label label-small">Angle</div>
											<div class="input"><input id="mod-rotation-angle" type="text" class="small" value='0'/></div>

											<div class="label label-small">EPSG</div>
											<div class="input"><input id="mod-epsg-code" type="text" class="small" value='32614'/></div>
											
											<div style="clear:both"></div>
											
											<div class="label label-small">Var Per Col</div>
											<div class="input"><input id="mod-var-count" type="text" class="small" value='70' /></div>
											<div class="input"><input type="button" value="Set" onclick="SetVarNumber('mod'); return false;" class='button' style='width: 50px; margin:0'/></div>

											<div style="clear:both"></div>
											
											<div class="full-width" style="margin: 5px">Offsets</div>
											<div> 
												<div class="label label-small">Apply to all</div>
												<div class="input"><input id="applied-mod-offset" type="text" style="width: 33px" /></div>
												<div class="input"><input type="button" value="Apply" onclick="ApplyAll('offset','mod'); return false;" class='button' style='width: 50px; margin:0'/></div>
											</div>
											<div style="clear:both"></div>
											<div class="full-width" id='mod-offsets' style="height: 90px; overflow-y: overlay; border: 1px solid gray;padding: 2px; margin: 10px 0"></div>
											
											<div style="clear:both"></div>
											
											<div class="full-width" style="margin: 5px">Vertical Shift</div>
											<div> 
												<div class="label label-small">Apply to all</div>
												<div class="input"><input id="applied-mod-vshift" type="text" style="width: 33px" /></div>
												<div class="input"><input type="button" value="Apply" onclick="ApplyAll('vshift','mod'); return false;" class='button' style='width: 50px; margin:0'/></div>
											</div>
											<div style="clear:both"></div>
											<div class="full-width" id='mod-vshifts'  style="height: 90px; overflow-y: overlay; border: 1px solid gray;padding: 2px; margin: 10px 0"></div>
											
											<div style="clear:both"></div>
											
											<input type="button" value="Update" onclick="UpdatePlotBoundary(); return false;" class='button'/>
											<input type="button" value="Delete" onclick="DeletePlotBoundary(); return false;" class='button'/>
											
											<div style="clear:both"></div>
										</fieldset>
										<div style="clear:both"></div>
									</div>
									
									<div id="import"> 
										<input type="file" id="importedFile" accept=".plot" style="display: none;" />
										<input type="button" value="Import" onclick="$('#importedFile').click(); return false;" class='button'/>
									</div>
									<div id="export"> 
										<div class="label">Format</div>
										<select id="export-format" class="select-small">
											<option value="plot">.plot</option>
											<option value="geojson">.geojson</option>
										</select>
										<input type="button" value="Export" onclick="Export(); return false;" class='button' style="float:left; margin: 2px;"/>
										<div style="clear:both"></div>
									</div>
								</div>
								<br>
								<input type="text" id="geojson" style='display: none'>
								<input type="text" id="boundary-width" style='display: none'>
								<input type="text" id="boundary-height" style='display: none'>
								<div id="dialog-confirm" title="Area selected" style="display:none">
									<p><span id="dialog-icon" class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span><span id="dialog-text"></span></p>
								</div>
								
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
								
								var lat = $('#top-left-lat').val();
								var lng = $('#top-left-lng').val();
								var latlng = new L.LatLng(lat, lng);
								marker = new L.marker(latlng).addTo(map);
								
								
								map.on('click', function(e){
									marker.setLatLng(e.latlng); 
									$('#top-left-lat').val(e.latlng.lat);
									$('#top-left-lng').val(e.latlng.lng);
								});
								
							</script>
						</form>
					</body>
				</html>
	
<?php

			} else {
				$memberOf = (implode("; ", $groups));
			
?>
				<!DOCTYPE>
				<html lang="html">
				<head>
					<title><?php echo $page; ?></title>
				</head>
				<body>
					<p>Hello, <?php echo $userName; ?>!</p>
					<p>You do not currently have permission to access this page</p>
					<p>Groups that you are a member of: <?php echo $memberOf; ?></p>
					<p>Groups allowed to access the content of this page: <?php echo $accessGroupsStr; ?></p>
					<p>Please contact admin at <a href="mailto:long.huynh@tamucc.edu">long.huynh@tamucc.edu</a></p>
				</body>
				</html>
				
<?php
			}
		}
		
	}
?>		