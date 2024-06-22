var plotNumber = 0;
var currentPlot = -1; 
var layerList = new Array();
var plots = new Array();
var plotInfos = new Array();
var zIndex = 50;
var currentLayer = null;
var marker;
var infoMap = new Array();
var infoCode = new Array();
var requestNum = 0;

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
	
	$('#imported-info-map').change(function(e){
		var fileName = $('#imported-info-map').val().split('\\').pop();
		if (fileName !== ""){
			ImportInfoMap();
		}
	});

	$('#imported-info-code').change(function(e){
		var fileName = $('#imported-info-code').val().split('\\').pop();
		if (fileName !== ""){
			ImportInfoCode();
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
				
function GetProjectList(){
	$.ajax({
		url: 'Resources/PHP/GetProjectList.php',
		dataType: 'text',
		success: function(response) {
			var items="";
			var data = JSON.parse(response);
			$.each(data,function(index,item) 
			{
				items+="<option value='" + item.ID+"' data-centerlat='" + item.CenterLat +"' " +
						"data-centerlng='" + item.CenterLng +"' data-minzoom='" + item.MinZoom +"' data-maxzoom='"+ item.MaxZoom +"' " +
						"data-defaultzoom='"+ item.DefaultZoom +"'>" + item.Name + "</option>";
			});
			$("#project-list").html(items); 
			GetProductTypeList($("#project-list").val());
			
			var selectedProject = $('#project-list option:selected');
			CenterMap(selectedProject.attr('data-centerlat') + "\," + selectedProject.attr('data-centerlng') ,selectedProject.attr('data-defaultzoom'));
			
			$("#project-list").chosen({
				inherit_select_classes: true
			});	
		}
	});
}

function GetProductTypeList(projectID){
	$.ajax({
		url: 'Resources/PHP/GetProductTypeList.php',
		dataType: 'text',
		data: { project: projectID},                         
		success: function(response) {
			var items="";
			
			var data = JSON.parse(response);
			$.each(data,function(index,item) 
			{
				items+="<option value='" + item.ID+"'>" + item.Name + "</option>";
			});
			$("#product-type-list").html(items); 
			GetLayerList($("#project-list").val(), $("#product-type-list").val());
		}
	});
}

function GetLayerList(projectID, type){
	$.ajax({
		url: 'Resources/PHP/GetLayerList.php',
		dataType: 'text',
		data: { project: projectID, type: type},                         
		success: function(response) {
			
			var data = JSON.parse(response);
			RemoveCurrentLayer();
					
			$.each(data,function(index,item) 
			{
				if (item.TMSPath != ""){
					AddLayer(item);
				}
			});
			
		}
	});
}

function AddLayer(layerInfo){
	
	var selectedProject = $('#project-list option:selected');
	var name = layerInfo.Name.replace(/-/g, '/');;
	var layer = "<li id='" + layerInfo.ID +"'>" +
					"<div class='added-layer'>"+
						"<div class='added-layer-name'>" + 
							"<input id='use-layer-"+ layerInfo.ID + "' name='use-layer' type='checkbox' onclick='ToggleLayer(this,\"" + layerInfo.ID + "\")' style='cursor: pointer; float: left'>" +
							name + 
							"<input type='image' class='image-button-small set-view-button' src='Resources/Images/view.png' alt='center' title='center' style='float:right; margin-right: 5px;' "+
							"onclick='CenterMap(&#39;" + selectedProject.attr('data-centerlat') + "\," + selectedProject.attr('data-centerlng') + "&#39;," + selectedProject.attr('data-defaultzoom') +"); return false;' >" + 
						
						"</div>" + 
					"</div>"+
				"</li>";
	$('#added-layer-list').append(layer);
	
	var layerName = layerInfo.Name;
	var tmsPath = layerInfo.TMSPath;
	var bounds = layerInfo.Boundary.split(";");
	var boundary = new Array();
	$.each(bounds,function(index,bound){
		var point = bound.split(",");
		boundary.push(L.latLng(point[0], point[1]));
	});
	
	AddMapLayer(layerName, tmsPath, "layer-" + layerInfo.ID, boundary);
	$('#layer-' + layerInfo.ID).hide();
	
	layerList.push(layerInfo.ID);
	
	$( "#added-layer-list" ).sortable({
		update: function(event, ui) {
			SortLayer();
		}
	});
}


function RemoveCurrentLayer(){
	
	$.each( layerList, function( index, layer ) {
		RemoveMapLayer(layer);
	});
	$('#added-layer-list').html("");
	layerList = [];
	
}

function ToggleLayer(obj, id)
{
	var $input = $(obj);
	if (!$input.prop('checked')) $('#layer-' + id).hide();
	else {
		$('#layer-' + id).show();
		var selectedProject = $('#project-list option:selected');
	}
}

function SortLayer(){
	var zIndex = 50;
	$("#added-layer-list li").each(function(li) {
		$('#layer-' + $(this).attr('id')).css('z-index', zIndex);
		zIndex++;
	});
}

//Get offet / vshift values
function GetTransformArrayString(type){
	var tArray = "";
	
	$('[id^="' + type + '-"]').each(function() {
		tArray += $(this).val() + ",";
	});
	
	tArray = tArray.slice(0,-1); //remove last ','
	return tArray;
}

//Generate inputs accordingly to the number of plots
function SetVarNumber(mod){
	
	if(mod != ''){
		mod += "-";
	}
	
	var varNum = $("#" + mod + "var-count").val();
	$("#" + mod + "offsets").html("");
	$("#" + mod + "vshifts").html("");
	
	for(var i = 0; i < varNum; i++){
		var offsetInput = 	'<div style="float:left; width: 60px; margin: 5px">' +
								'<div style="width:15px; float:left; margin: 2px">' + (parseInt(i) + 1) + '</div>' +
								'<input id="' + mod + 'offset-' + i + '" type="text" style="width: 28px; margin:0 2px; float: left" value="0" />'
							'</div>';
		$("#" + mod + "offsets").append(offsetInput);
		
		var shiftInput = 	'<div style="float:left; width: 60px; margin: 5px">' +
								'<div style="width:15px; float:left; margin: 2px">' + (parseInt(i) + 1) + '</div>' +
								'<input id="' + mod + 'vshift-' + i + '" type="text" style="width: 28px; margin:0 2px; float: left" value="0" />'
							'</div>';
		$("#" + mod + "vshifts").append(shiftInput);
		$(".apply-all").show();
	}
}

//Show row info for edit
function ShowPlotInfo(){
	var plotInfo = plotInfos[currentPlot];
	
	$('#mod-row-number').val(plotInfo.name);
	$('#mod-top-left-lat').val(plotInfo.lat);
	$('#mod-top-left-lng').val(plotInfo.lng);
	$('#mod-plot-width').val(plotInfo.width);
	$('#mod-plot-height').val(plotInfo.height);
	$('#mod-var-count').val(plotInfo.count);
	$('#mod-rotation-angle').val(plotInfo.angle);
	$('#mod-epsg-code').val(plotInfo.epsg);

	var offsets = plotInfo.offsets.split(",");
	var vshifts = plotInfo.vshifts.split(",");
	
	$("#mod-offsets").html("");
	for(i = 0; i < offsets.length; i++){
		var offsetInput = 	'<div style="float:left; width: 60px; margin: 5px">' +
								'<div style="width:15px; float:left; margin: 2px">' + (parseInt(i) + 1) + '</div>' +
								'<input id="mod-offset-' + i + '" type="text" style="width: 28px; margin:0 2px; float: left" value="' + offsets[i] + '" />'
							'</div>';
		$("#mod-offsets").append(offsetInput);
	}	
	
	$("#mod-vshifts").html("");	
	for(i = 0; i < vshifts.length; i++){	
		var shiftInput = 	'<div style="float:left; width: 60px; margin: 5px">' +
								'<div style="width:15px; float:left; margin: 2px">' + (parseInt(i) + 1) + '</div>' +
								'<input id="mod-vshift-' + i + '" type="text" style="width: 28px; margin:0 2px; float: left" value="' + vshifts[i] + '" />'
							'</div>';
		$("#mod-vshifts").append(shiftInput);
	}
	
	$("#mod-plot-boundary-properties").show();
}

function GeneratePlotBoundary() {
	$('#loading').show();
	
	var name = $('#row-number').val();
	var lat =  $('#top-left-lat').val();
	var lng =  $('#top-left-lng').val();
	var width =  $('#plot-width').val();
	var height =  $('#plot-height').val();
	var count =  $('#var-count').val();
	var angle =  $('#rotation-angle').val();
	var epsg = $('#epsg-code').val();
	
	var offsets = GetTransformArrayString('offset');
	var vshifts = GetTransformArrayString('vshift');
		
	var plotInfo = new Object();
	plotInfo.name = name;
	plotInfo.lat = lat;
	plotInfo.lng = lng;
	plotInfo.width = width;
	plotInfo.height = height;
	plotInfo.count = count;
	plotInfo.angle = angle;
	plotInfo.epsg = epsg;
	plotInfo.offsets = offsets;
	plotInfo.vshifts = vshifts;
	//plotInfos.push(plotInfo);
	
	//plotNames.push ($('#row-number').val());
	
	$.ajax({
		url: 'Resources/PHP/GenerateBoundary.php',
		dataType: 'text',
		data: { 
			name: name,
			lat: lat,
			lng:lng,
			width: width,
			height: height,
			count: count,
			angle: angle,
			epsg: epsg,
			offsets: offsets,
			vshifts: vshifts
		},                         
		success: function(response) {
			$('#loading').hide();
			AddBoundaryLayer(response, plotInfo);
		}
	});
	
}

function UpdatePlotBoundary(){
	$('#loading').show();
	
	//var name = $('#plot-list option:selected').text();
	var name = $('#mod-row-number').val();
	var lat =  $('#mod-top-left-lat').val();
	var lng =  $('#mod-top-left-lng').val();
	var width =  $('#mod-plot-width').val();
	var height =  $('#mod-plot-height').val();
	var count =  $('#mod-var-count').val();
	var angle =  $('#mod-rotation-angle').val();
	var epsg =  $('#mod-epsg-code').val();
		
	var offsets = GetTransformArrayString('mod-offset');
	var vshifts = GetTransformArrayString('mod-vshift');
	
	plotInfos[currentPlot].lat = lat;
	plotInfos[currentPlot].lng = lng;
	plotInfos[currentPlot].width = width;
	plotInfos[currentPlot].height = height;
	plotInfos[currentPlot].count = count;
	plotInfos[currentPlot].angle = angle;
	plotInfos[currentPlot].epsg = epsg;
	plotInfos[currentPlot].offsets = offsets;
	plotInfos[currentPlot].vshifts = vshifts;
	
	$.ajax({
		url: 'Resources/PHP/GenerateBoundary.php',
		dataType: 'text',
		data: { 
			name: name,
			lat: lat,
			lng:lng,
			width: width,
			height: height,
			count: count,
			angle: angle,
			epsg: epsg,
			offsets: offsets,
			vshifts: vshifts
		},                         
		success: function(response) {
			plotInfos[currentPlot].name = name;
			$('#plot-list option:selected').text(name);
			
			UpdateBoundaryLayer(response);
			$('#loading').hide();
			
		}
	});

}

function DeletePlotBoundary(){
	map.removeLayer(plots[currentPlot]);
	plots.splice(currentPlot, 1);
	plotInfos.splice(currentPlot, 1);
	
	$("#plot-list option[value='" + currentPlot +"']").remove();
	currentPlot = -1;
	plotNumber = 0;
	SetPlotList();
}

//Update plot drop down list
function SetPlotList(){
	$('#plot-list').html("");
	for (i = 0; i < plots.length; i++ ){
		var plotOption = "<option value='" + plotNumber +"'>" + plotInfos[i].name + "</option>";
		$('#plot-list').append(plotOption);
		plotNumber++;
	}
	
	if (plotNumber > 0){
		currentPlot = 0;
	
		UnHighlighAll();
		HighlightPlot(currentPlot);
		ShowPlotInfo();
	} else {
		$("#mod-plot-boundary-properties").hide();
	}
}

function HighlightPlot(index){
	var plot = plots[index];
	
	plot.eachLayer( function(layer){
		layer.setStyle({fillOpacity: 0.3, fillColor: 'white'}); 
	});
}

function UnHighlighAll(){
	$.each(plots, function (index, plot) {
		plot.eachLayer( function(layer){
			layer.setStyle({fillOpacity: 0, fillColor: 'white'});
		});
	}); 
}

//Apply entered value to all offets/vshifts
function ApplyAll(type, mod){
	if (mod != ""){
		mod += "-";
	}
	$("[id^=" + mod + type + "]").each(function() {
		$(this).val($("#applied-" + mod + type).val());
	});
	
}

function UnSelectAllPlot(){
	plotItems.eachLayer(function (layer) {
		layer.setStyle({fillOpacity: 0, fillColor: 'white'});
	});
}
				
function onEachFeature(feature, layer) {
	var infoList = [];
	infoList.push("Plot boundary")
	if (feature.properties) {
		for (key in feature.properties) {
			infoList.push(key + ": " + feature.properties[key]);
		}
		layer.bindPopup(infoList.join("<br />"));
	}
}	

function onModifyBoundary(feature, layer) {
	layer.on('click', function (e) {
		UnSelectAllPlot();
		layer.setStyle({fillOpacity: 0.3, fillColor: 'white'});
		currentLayer = layer;
		var coordinates = layer.getLatLngs();
		$('#selected-top-left-lat').val(coordinates[0]['lat']);
		$('#selected-top-left-lng').val(coordinates[0]['lng']);
		$('#selected-plot-width').val(coordinates[0].distanceTo(coordinates[1]));
		$('#selected-plot-height').val(coordinates[0].distanceTo(coordinates[3]));
	});

	map.removeLayer(currentLayer);
	
	plotItems.addLayer(layer);
	currentLayer = layer;
	currentLayer.setStyle({fillOpacity: 0.3, fillColor: 'white'});
}	

function ImportBoundaryLayer(filePath){
	var boundary = new L.GeoJSON.AJAX(filePath, {style: boundaryStyle, zIndex: 995,  onEachFeature: onEachFeature});
	map.addLayer(boundary);
}

function AddBoundaryLayer(filePath, plotInfo){
	var boundary = new L.GeoJSON.AJAX(filePath, {style: boundaryStyle, zIndex: 995,  onEachFeature: onEachFeature});
	boundary.on('data:loaded',function(e){
		var plot = new L.FeatureGroup();
		map.addLayer(plot);
		
		plot.addLayer(boundary);
		plots.push(plot);
		plotInfos.push(plotInfo);
		
		var plotOption = "<option value='" + plotNumber +"'>" + plotInfo.name + "</option>";
		plotNumber ++;
		$('#plot-list').append(plotOption);
	
		
		$('#plot-list').val(0);
		currentPlot = 0;
		UnHighlighAll();
		HighlightPlot(currentPlot);
		ShowPlotInfo();
		
		$('#row-number').val((parseInt(plotNumber) + 1));
		
		if (requestNum > 0){
			requestNum--;
			if(requestNum == 0){
				Apply();
			}
		}
	});
}


function UpdateBoundaryLayer(filePath){

	var boundary = new L.GeoJSON.AJAX(filePath, {style: boundaryStyle, zIndex: 995,  onEachFeature: onEachFeature});

	boundary.on('data:loaded',function(e){
	
		map.removeLayer(plots[currentPlot]);
		var plot = new L.FeatureGroup();
		map.addLayer(plot);
		plot.addLayer(boundary);
		plots[currentPlot] = plot;
		
		UnHighlighAll();
		HighlightPlot($('#plot-list').val());
		
		Apply();
	});
}

function Export(){
	
	var format = $('#export-format').val();
	if (format == "geojson"){
		var collection = {
			"type": "FeatureCollection",
			"features": []
		};

		map.eachLayer(function (layer) {
			if (layer instanceof L.Polygon) { //only export polygon for boundary
				var geojson = layer.toGeoJSON(14);
				collection.features.push(geojson);
			}
		});
		Download('plot_boundary.geojson',JSON.stringify(collection, null, 4));
	} else if (format == "plot"){
		var exportInfo = {"plots":plotInfos, "infoMap": infoMap, "infoCode": infoCode};
		
		//Download('plot_boundary.plot',JSON.stringify(plotInfos));
		Download('plot_boundary.plot',JSON.stringify(exportInfo));
	}
}

function Import(){
	$('#loading').show();
		
	var file_data = $('#importedFile').prop('files')[0]; //Get the file for upload   
	var form_data = new FormData(); //Create a new form data for uploading                 
	form_data.append('file', file_data);
	
	
	$.ajax({
		url: 'Resources/PHP/Import.php',
		dataType: 'text',
		cache: false,
		contentType: false,
		processData: false,
		data: form_data,                         
		type: 'post',						
		success: function(response) {
				
			var data = JSON.parse(response);
			
			var importedInfoMap = data.infoMap;
			if (importedInfoMap.length > 0){
				ShowInfoMap(importedInfoMap);
			}
			
			var importedInfoCode = data.infoCode;
			if (importedInfoCode.length > 0){
				ShowInfoCode(importedInfoCode);
			}
			
			var plots = data.plots;
			
			//if (data.length > 0){
			if (plots.length > 0){	
				ClearMap();
				
				//$.each(data,function(index,item){
				requestNum = plots.length;	
					
				$.each(plots,function(index,item){
					//setTimeout(function(){
					var name = item.name;
					var lat =  item.lat;
					var lng =  item.lng;
					var width =  item.width;
					var height =  item.height;
					var count =  item.count;
					var angle =  item.angle;
					var epsg = item.epsg;
					
					var offsets = item.offsets;
					var vshifts = item.vshifts;
						
					var plotInfo = new Object();
					plotInfo.name = name;
					plotInfo.lat = lat;
					plotInfo.lng = lng;
					plotInfo.width = width;
					plotInfo.height = height;
					plotInfo.count = count;
					plotInfo.angle = angle;
					plotInfo.epsg = epsg;
					plotInfo.offsets = offsets;
					plotInfo.vshifts = vshifts;
					plotInfo.grid = null;
					
					$.ajax({
						url: 'Resources/PHP/GenerateBoundary.php',
						dataType: 'text',
						//async: false,
						data: { 
							name: name,
							lat: lat,
							lng:lng,
							width: width,
							height: height,
							count: count,
							angle: angle,
							epsg: epsg,
							offsets: offsets,
							vshifts: vshifts
						},                         
						success: function(response) {
							AddBoundaryLayer(response, plotInfo);
							
							/*
							requestNum--;
							if(requestNum == 0){
								ApplyInfo();
							}
							*/
						}
					
					}).done(function() {
						$('#loading').hide();
					});
				});
				
				
				CenterMap(data[0].lat + "\," + data[0].lng ,20);
			
			}
			
		}
	});
}

function ClearMap(){
	map.eachLayer(function (layer) {
		if (layer instanceof L.Polygon) {
			map.removeLayer(layer);
		}
	});
	
	plotNumber = 0;
	currentPlot = -1; 
	plots = new Array();
	plotInfos = new Array();
	currentLayer = null;
	$("#plot-list").html("");
}

function AddMapLayer(name, tms, id, boundary){
	var layer = L.tileLayer(tms, {tms: true , zIndex: zIndex++, bounds: boundary});
	map.addLayer(layer);
	$('.leaflet-layer').filter(function() {
		return $(this).css('z-index') == zIndex - 1;
	}).each(function() {
		this.setAttribute("id", id);   
	});
}

function RemoveMapLayer(id) {
	$('#layer-' + id).remove();
}

function CenterMap(position, zoom){
	var loc = position.split(',');
	var z = parseInt(zoom);
	map.setView(loc, z, {animation: true});
}

function CalculateOffets(){
	var tlLat = $("#top-left-lat").val();
	var tlLng = $("#top-left-lng").val();
	var width = $("#plot-width").val();
	var height = $("#plot-height").val();
	
}

/*
function UnSelectAll(){
	drawnItems.eachLayer(function (layer) {
		layer.setStyle({dashArray: null});
	});
	
	$('#geojson').val('');
	CheckCalculateConditions();
}
*/

function CheckDrawBoundary(layer){
	
	$("#dialog-text").text("Do you want to calculate the angle and offsets?");
	$("#dialog-icon").hide();
	$("#dialog-confirm").dialog({
		resizable: false,
		height: "auto",
		width: 400,
		modal: true,
		buttons: {
			
			"Calculate": function() {
				$( this ).dialog( "close" );
				Calculate();
			},
			Cancel: function() {
				$( this ).dialog( "close" );
			}
		}
	});
	
}

/*
function CheckCalculateConditions(area, layer){
	
	var isPass = true;
	
	if ($('#geojson').val() == ''){
		isPass = false;
	}
	
	
	var activeLayers = GetActiveLayerList();
	if (activeLayers.length === 0) {
		isPass = false;
	}
	
	if (area > 100){
		isPass = false;
		$("#dialog-text").text("The selected area (" + area.toFixed(2) + "m\u00B2) is too large, please make the area smaller than 100m\u00B2.");
		$("#dialog-icon").show();
		$("#dialog-confirm").dialog({
			resizable: false,
			height: "auto",
			width: 400,
			modal: true,
			buttons: {
				Cancel: function() {
					$( this ).dialog( "close" );
					drawnItems.removeLayer(layer);
				}
			}
		});
	}else {
		if (isPass){
			$("#dialog-text").text("The selected area is " + area.toFixed(2) + "m\u00B2. You can go ahead and perform growth analysis.");
			$("#dialog-icon").hide();
			$("#dialog-confirm").dialog({
				resizable: false,
				height: "auto",
				width: 400,
				modal: true,
				buttons: {
					
					"Analysis": function() {
						$( this ).dialog( "close" );
						Calculate();
					},
					Cancel: function() {
						$( this ).dialog( "close" );
					}
				}
			});
		}
	}
	
}
*/


function Calculate(){
	
	var count = parseFloat($("#var-count").val());
	var width = parseFloat($("#boundary-width").val());
	var plotWidth = parseFloat($("#plot-width").val());
	var avgOffset = (width - plotWidth * count ) / (count - 1);
	SetVarNumber('');
	$('#applied-offset').val(avgOffset);
	$('#plot-height').val($('#boundary-height').val());
	ApplyAll('offset','');
	$('#offset-0').val(0);
}

function GetActiveLayerList(){
	var activeLayers = [];
	
	$('input[name=use-layer]:checked').each(function(index, layer){
		var id = layer.id.replace("use-layer-","");
		activeLayers.push(id);
	});
	
	
	return activeLayers;
	
}


function ToRadian(degree) {
    return degree*Math.PI/180;
}

function GetAngle(origin, destination) {
	var lng2 = origin.lng;
	var lat2 = origin.lat;
	var lng1 = destination.lng;
	var lat1 = destination.lat;
	
	var dLon = (lng2-lng1);
	var y = Math.sin(dLon) * Math.cos(lat2);
	var x = Math.cos(lat1)*Math.sin(lat2) - Math.sin(lat1)*Math.cos(lat2)*Math.cos(dLon);
	return Math.atan2(y, x);
}



function ImportInfoCode(){
	$("#loading").show();
		
	var file_data = $('#imported-info-code').prop('files')[0]; //Get the file for upload   
	var form_data = new FormData(); //Create a new form data for uploading                 
	form_data.append('file', file_data);
	
	$.ajax({
		url: 'Resources/PHP/ImportInfoCode.php',
		dataType: 'text',
		cache: false,
		contentType: false,
		processData: false,
		data: form_data,                         
		type: 'post',						
		success: function(response) {
				
			var data = JSON.parse(response);
			if (data.length > 0){
				ShowInfoCode(data);
				/*
				infoCode = data;
				
				var firstRow = data[0];
				
				$("#info-code-wrapper").html("");
				$("#info-code-header").html("");
				var keys = Object.keys(firstRow);
				
				$.each(keys, function(index, key){
					var headerField = 	"<div class='info-code-block'>" +
											Add"<span>" +  key + "</span>" +
										"</div>";
					$("#info-code-header").append(headerField);
					
					
				});
				
				$("#info-code-header").append("<div style='clear:both'></div>");
			
				$.each(keys, function(index, key){
					//var inputField = 	"<div style='float:left; width: 120px; margin: 5px'>" +
					var inputField = 	"<div class='info-code-block'>" +
											"<input id='info-code-add-" + index + "' type='text' class='info-input' />" +
										"</div>";
				
					$("#info-code-header").append(inputField);
				});
				
					
				var addButton =	"<input type='button' value='Add' onclick='AddCode(); return false;' class='button' style='float:left; margin: 2px'/>";
				
				$("#info-code-header").append(addButton);
				
			
				
				$.each(data, function(i, line){
					
					var lineData = 	"";
					
					$.each(keys, function(j, key){
						lineData += 	"<div class='info-code-block'>" +
											"<input id='info-code-" + i + "-" + j + "' type='text' class='info-input' value='" + line[key] + "' />" +
										"</div>";
						
					});
							
					$("#info-code-wrapper").append(lineData);
					$("#info-code-wrapper").append("<div style='clear:both'></div>");
				});
				*/
				
				
			}
			
			
			$("#loading").hide();
		}
	});
}

function ShowInfoCode(data){
	infoCode = data;
				
	var firstRow = data[0];
	
	$("#info-code-wrapper").html("");
	$("#info-code-header").html("");
	var keys = Object.keys(firstRow);
	
	$.each(keys, function(index, key){
		var headerField = 	"<div class='info-code-block'>" +
								"<span>" +  key + "</span>" +
							"</div>";
		$("#info-code-header").append(headerField);
		
		
	});
	
	
	$("#info-code-header").append("<div style='clear:both'></div>");

	$.each(keys, function(index, key){
		//var inputField = 	"<div style='float:left; width: 120px; margin: 5px'>" +
		var inputField = 	"<div class='info-code-block'>" +
								"<input id='info-code-add-" + index + "' type='text' class='info-input' />" +
							"</div>";
	
		$("#info-code-header").append(inputField);
	});
	
		
	//var addButton =	"<input type='button' value='Add' onclick='AddCode(); return false;' class='button' style='float:left; margin: 2px'/>";
	var addButton =	"<input type='button' value='Add' onclick='AddCode(); return false;' class='button' style='display:inline-block; margin: 2px'/>";
	
	$("#info-code-header").append(addButton);
	
	
	
	$.each(data, function(i, line){
		
		var lineData = 	"";
		
		$.each(keys, function(j, key){
			lineData += 	"<div class='info-code-block'>" +
								"<input id='info-code-" + i + "-" + j + "' type='text' class='info-input' value='" + line[key] + "' />" +
							"</div>";
			
		});
		
		/*
		for (var i= 0; i < keys.length; i ++){
			lineData += 	"<div class='info-code-block'>" +
								"<input id='info-code-" + index+ "' type='text' class='info-input' value='" + value[keys[i]] + "' />" +
							"</div>";
		}				
			*/			
		$("#info-code-wrapper").append(lineData);
		$("#info-code-wrapper").append("<div style='clear:both'></div>");
	});
			
}

function ImportInfoMap(){
	$("#loading").show();
		
	var file_data = $('#imported-info-map').prop('files')[0]; //Get the file for upload   
	var form_data = new FormData(); //Create a new form data for uploading                 
	form_data.append('file', file_data);
	
	$.ajax({
		url: 'Resources/PHP/ImportInfoMap.php',
		dataType: 'text',
		cache: false,
		contentType: false,
		processData: false,
		data: form_data,                         
		type: 'post',						
		success: function(response) {
				
			var data = JSON.parse(response);
			if (data.length > 0){
				ShowInfoMap(data);
				/*
				infoMap = data;
				$("#info-map-wrapper").html("");
				
				$.each(data, function(i, line){
					var lineData = 	"<div>";
					$.each(line, function(j, grid){
						lineData += 	"<div class='info-map-block'>" +
											"<input id='info-map-" + i + "-" + j + "' type='text' class='info-input' value='" + grid + "' />" +
										"</div>";
					});
					lineData += "</div>";				
					$("#info-map-wrapper").append(lineData);
					//$("#info-map-wrapper").append("<div style='clear:both'></div>");
				});
				*/
			}
			
			
			$("#loading").hide();
		}
	});
}

function ShowInfoMap(data){
	infoMap = data;
	$("#info-map-wrapper").html("");
	
	$.each(data, function(i, line){
		var lineData = 	"<div>";
		$.each(line, function(j, grid){
			lineData += 	"<div class='info-map-block'>" +
								"<input id='info-map-" + i + "-" + j + "' type='text' class='info-input' value='" + grid + "' />" +
							"</div>";
		});
		lineData += "</div>";				
		$("#info-map-wrapper").append(lineData);
		//$("#info-map-wrapper").append("<div style='clear:both'></div>");
	});
}

function ApplyInfo(){
	
	var checkInfoMapResult = CheckInfoMap();
	if (checkInfoMapResult == -1){
		alert("Please import both info map and info code.");
	} else if (checkInfoMapResult == 0){
		Apply();
		alert("Info has beed applied.");
	} else if (checkInfoMapResult > 0){
		$( "#dialog-confirm" ).dialog({
			resizable: false,
			height: "auto",
			width: 400,
			modal: true,
			buttons: {
				"Proceed": function() {
					$( this ).dialog( "close" );
					Apply();
					alert("Info has beed applied.");

				},
				Cancel: function() {
					$( this ).dialog( "close" );
				}
			}
		});
	}

}


function Apply(){
	
	map.eachLayer(function (layer) {
		if (layer instanceof L.Polygon) { //only export polygon for boundary
			
			var plotProps = layer.feature.properties;
			if(plotProps.hasOwnProperty("Row") && plotProps.hasOwnProperty("Col")){
				
				var info = GetInfo(parseInt(layer.feature.properties["Row"]), parseInt(layer.feature.properties["Col"]));
				
				var keys = Object.keys(info);
				
				$.each(keys, function(index, key){
					layer.feature.properties[key] = info[key];
				});
				
				
				var infoList = [];
				infoList.push("Plot boundary");
				if (layer.feature.properties) {
					for (key in layer.feature.properties) {
						infoList.push(key + ": " + layer.feature.properties[key]);
					}
					layer.bindPopup(infoList.join("<br />"));
				}
				
				/*
				var plotGeoJSON = layer.toGeoJSON(14);
				//collection.features.push(geojson);
				var boundary = new L.geoJSON(plotGeoJSON, {style: boundaryStyle, zIndex: 995,  onEachFeature: onEachFeature}).addTo(map);
				boundary.on('data:loaded',function(e){
	
					map.removeLayer(layer);
					var plot = new L.FeatureGroup();
					map.addLayer(plot);
					plot.addLayer(boundary);
					plots[currentPlot] = plot;
					
					UnHighlighAll();
					HighlightPlot($('#plot-list').val());
				});
			*/
			}
		}
	});
	
}

function GetInfo(row, col){
	var info = {};
	var keys = Object.keys(infoCode[0]);
	for(var j = 0; j< keys.length; j++){
		info[keys[j]] = "NA";
	}
	
	
	if ($("#info-map-" + (row - 1) + "-" + (col - 1)).length > 0){
		var entry = $("#info-map-" + (row - 1) + "-" + (col - 1)).val();
		for(var i = 0; i < infoCode.length; i++){
			info["Entry"] = entry;
			
			if ($("#info-code-" + i + "-0").val() == entry){
				for(var j = 0; j< keys.length; j++){
					if (keys[j] != "Entry"){
						info[keys[j]] = $("#info-code-" + i + "-" + j).val();
					}
				}

			}
		}
	}
	return info;
}

function CheckInfoMap(){
	
	var errorNum = 0; 
	
	if (infoMap == null || infoCode == null){
		errorNum = -1;
	} else {
		var rowNum = infoMap.length;
		var colNum = infoMap[0].length;
		for(var row = 0; row < rowNum; row++){
			for(var col = 0; col < rowNum; col++){
				var entry = $("#info-map-" + row + "-" + col).val();
				var found = false;
				for(var i = 0; i < infoCode.length; i++){
					if ($("#info-code-" + i + "-0").val() == entry){
						found = true;
					}
				}
				
				if (found){
					$("#info-map-" + row + "-" + col).removeAttr("style");
				}else {
					$("#info-map-" + row + "-" + col).css("border","1px solid red");
					errorNum ++;
				}
			}
		}
	}
	return errorNum;
}

function AddCode(){
	
	var keys = Object.keys(infoCode[0]);
	var lineData = "";
	var code = {};
	
	$.each(keys, function(i, key){
		var value = $("#info-code-add-" + i).val();
		lineData += 	"<div class='info-code-block'>" +
							"<input id='info-code-" + infoCode.length + "-" + i + "' type='text' class='info-input' value='" + value + "' />" +
						"</div>";
		code[key] = value;
	});
		
	infoCode.push(code);	
	$("#info-code-wrapper").append(lineData);
	$("#info-code-wrapper").append("<div style='clear:both'></div>");
	alert("New code has been added");
}