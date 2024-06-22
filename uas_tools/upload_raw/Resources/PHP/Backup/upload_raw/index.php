<?php 
	session_start();
	$userName = $_SESSION["username"];
	$roles = $_SESSION["userroles"];
	$groups = $_SESSION["groups"];
	unset($_SESSION["page"]);
	
	if (!$roles){
		$_SESSION["page"] =  "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		header("Location: https://uashub.tamucc.edu/uas-tools/");
		exit();
	} else if (in_array("uasadmin", $groups ) || in_array("uasteam", $groups )) {
		
?>
	<!DOCTYPE>
	<html lang="html">

		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<title>Upload Raw Data</title>
			
			<!-- Styles -->
			<link rel="stylesheet" type="text/css" href="Resources/style.css">
			
			<script type="text/javascript" src="Resources/JS/jquery.min.js"></script>
			<script src="Resources/JS/Chosen/chosen.jquery.min.js"></script>
			<link rel="stylesheet" type="text/css" href="Resources/JS/Chosen/chosen.css">
			
			<script src="Resources/JS/JqueryUI/jquery-ui.min.js"></script>
			<link rel="stylesheet" type="text/css" href="Resources/JS/JqueryUI/jquery-ui.css">
			<script type="text/javascript" src="Resources/JS/resumable.js"></script>
			<script type="text/javascript" src="Resources/JS/main.js"></script>
			<script type="text/javascript" src="Resources/JS/spark-md5.min.js"></script>
			
			<script src="Resources/JS/FixedTable/fixed_table_rc.js"></script>
			<link rel="stylesheet" type="text/css" href="Resources/JS/FixedTable/fixed_table_rc.css">
			
			<link rel="stylesheet" href="Resources/JS/Lightbox/css/lightbox.min.css">
			<script src="Resources/JS/Lightbox/js/lightbox-plus-jquery.min.js"></script>
			
			<style>
				/*
				#uploaded-list-container{
					width: 93%;
				}
				*/
				
				.add-button{
					float:right;
					margin: 10px 0 0 0;
					cursor: pointer;
				}
				
				#warning{
					width:100%; 
					margin-top:10px; 
					text-align:center;
					color: red;
					display: none;
				}
			</style>
			
		</head>

		
		
		<body>
			<input type="hidden" id="user-name" value="<?php echo $userName; ?>" />
			<div id="processing"></div>
			<form style="z-index:10" >
				<h2>Upload Raw Data</h2>
				<br>
				<div id="select-flight">
					<fieldset>
						<legend>Select Flight</legend>
						<div>
							<div class="label">Project</div>
							<select id="project" class="select-large">
							</select>
						</div>
						<div style="clear:both; margin-bottom:5px"></div>	
						<div class ="half-width">
							<div>
								<div class="label">Platform</div>
								<select id="platform">
								</select>
							</div>
							
							<div style="clear:both; margin-bottom:5px"></div>	
							
							<div>
								<div class="label">Sensor</div>
								<select id="sensor">
								</select>
							</div>
							
						</div>
						
						<div class ="half-width">
							<div>
								<div class="label">Date</div>
								<select id="date">
								</select>
							</div>
							
							<div style="clear:both; margin-bottom:5px"></div>	
							
							<div>
								<div class="label">Flight</div>
								<select id="flight">
								</select>
								<input type="button" value="Add Flight" onclick="ShowAddFlight(); return false;"/>
							</div>
						</div>
					
						<div style="clear:both"></div>
						
						<div class="full-width">
							<p>Flight Attitude: <span type="text" id="altitude" class="normal"></span>, Forward Overlap: <span type="text" id="forward" class="normal"></span>,  Side Overlap: <span type="text" id="side" class="normal"></span></p>
						</div>
						
						<div style="clear:both"></div>
						<input  id="upload-button" type='button' class='button right-button' value="Upload" onclick='CreateResumableInstance(); return false;' />
					</fieldset>
					<br>
					<div style="clear:both"></div>
					<fieldset >
						<legend>Uploading List</legend>
						<div id="unfinished-files" style="text-align:center;">
						</div>
						<div style="clear:both"></div>
						<div id="upload-files" style="text-align:center;">
						<div id="resumable-list" style="display:none"></div>
					</fieldset>
					<div style="clear:both"></div>
					<br>
					<fieldset >
						<legend>Finished List</legend>
						<div id="finished-list-wrapper">
						</div>
					</fieldset>
					
				</div>
				<div id="add-flight" style="display:none">
					<fieldset>
						<legend>Add Flight</legend>
						<div class ="half-width">
							<div>
								<div class="label">Name</div>
								<div class="input"><input type="text" id="flight-name"></div>
							</div>
							<div style="clear:both"></div>
							<div>
								<div class="label">Project</div>
								<div class="input"><input type="text" id="flight-project" disabled></div>
							</div>
							
							<div style="clear:both"></div>
							
							<div>
								<div class="label">Platform</div>
								<div class="input"><input type="text" id="flight-platform" disabled></div>
							</div>
							
							<div style="clear:both"></div>
							
							<div>
								<div class="label">Sensor</div>
								<div class="input"><input type="text" id="flight-sensor" disabled></div>
							</div>
						</div>	
						<div class ="half-width">
							<div>
								<div class="label">Date</div>
								<div class="input"><input type="text" id="flight-date" class="normal"></div>
							</div>
							
							<div style="clear:both"></div>
							
							<div>
								<div class="label">Flight Altitude</div>
								<div class="input"><input type="text" id="flight-altitude" class="normal"></div>
							</div>
							
							<div style="clear:both"></div>
							
							<div>
								<div class="label">Forward Overlap</div>
								<div class="input"><input type="text" id="flight-forward" class="normal"></div>
							</div>
							
							<div style="clear:both"></div>
							
							<div>
								<div class="label">Side Overlap</div>
								<div class="input"><input type="text" id="flight-side" class="normal"></div>
								<div style="clear:both"></div>
							</div>
						</div>
						<div style="clear:both"></div>
						<div style="text-align:center; margin-top: 10px">
							<input type="button" value="Add" onclick="AddFlight(); return false;"/>
							<input type="button" value="Cancel" onclick="ShowSelectFlight(); return false;"/>
						</div>
					</fieldset>
					
				</div>
			</form>
			<div id="dialog-confirm" title="Cancel the upload?"  style="display:none">
				<p><span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span>The upload will be cancelled. Are you sure?</p>
			</div>
		</body>
	</html>

	
<?php

	} else {
		echo "You do not have the permission to view this page. Please contact admin at long.huynh@tamucc.edu";
	}
?>	