var plotNumber = 0;
var currentPlot = -1; 
var layerList = new Array();
var plots = new Array();
var plotInfos = new Array();
var zIndex = 50;
var currentLayer = null;

var requestCount = 0;
				
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
							"<input type='checkbox' onclick='ToggleLayer(this,\"" + layerInfo.ID + "\")' style='cursor: pointer; float: left'>" +
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

//Show row info for edit
function ShowPlotInfo(){
	var plotInfo = plotInfos[currentPlot];
	
	$('#top-left-lat').val(plotInfo.lat);
	$('#top-left-lng').val(plotInfo.lng);
	$('#plot-width').val(plotInfo.width);
	$('#plot-height').val(plotInfo.height);
	$('#var-count').val(plotInfo.count);
	$('#rotation-angle').val(plotInfo.angle);
	$('#epsg-code').val(plotInfo.epsg);

	var offsets = plotInfo.offsets.split(",");
	var vshifts = plotInfo.vshifts.split(",");
	
	$("#offsets").html("");
	for(i = 0; i < offsets.length; i++){
		var offsetInput = 	'<div style="float:left; width: 60px; margin: 5px">' +
								'<div style="width:15px; float:left; margin: 2px">' + (parseInt(i) + 1) + '</div>' +
								'<input id="offset-' + i + '" type="text" style="width: 28px; margin:0 2px; float: left" value="' + offsets[i] + '"  disabled/>'
							'</div>';
		$("#offsets").append(offsetInput);
	}	
	
	$("#vshifts").html("");	
	for(i = 0; i < vshifts.length; i++){	
		var shiftInput = 	'<div style="float:left; width: 60px; margin: 5px">' +
								'<div style="width:15px; float:left; margin: 2px">' + (parseInt(i) + 1) + '</div>' +
								'<input id="vshift-' + i + '" type="text" style="width: 28px; margin:0 2px; float: left" value="' + vshifts[i] + '" disabled/>'
							'</div>';
		$("#vshifts").append(shiftInput);
	}
	
	$("#plot-boundary-properties").show();
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

//function AddBoundaryLayer(filePath, name){
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
		$("#grid-properties").show();
	});
}

function Export(){
	
	
	var collection = {
		"type": "FeatureCollection",
		"features": []
	};

	map.eachLayer(function (layer) {
		if (layer instanceof L.Polygon) { //only export polygon for boundary
			var grid = layer.feature.properties;
			
			if(grid.hasOwnProperty("Grid col")){
				var geojson = layer.toGeoJSON(14);
				collection.features.push(geojson);
			}
		}
	});
	//console.log(collection);
	//Download('plot_grid.geojson',JSON.stringify(collection, null, 4));
	
	
	var download = function(content, fileName, mimeType) {
		var a = document.createElement('a');
		mimeType = mimeType || 'application/octet-stream';

		if (navigator.msSaveBlob) { // IE10
			navigator.msSaveBlob(new Blob([content], {
			  type: mimeType
			}), fileName);
		} else if (URL && 'download' in a) { //html5 A[download]
			a.href = URL.createObjectURL(new Blob([content], {
				type: mimeType
			}));
			a.setAttribute('download', fileName);
			document.body.appendChild(a);
			a.click();
			document.body.removeChild(a);
		  } else {
			location.href = 'data:application/octet-stream,' + encodeURIComponent(content); // only this mime type is supported
		  }
	}

	download(JSON.stringify(collection, null, 4), "plot_grid.geojson", "text/csv;encoding:utf-8");
	
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
			if (data.length > 0){
				
				ClearMap();
				
				$.each(data,function(index,item){
				
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
						}
					
					}).done(function() {
						$('#loading').hide();
					
					});
			
				});
			}
			
			CenterMap(data[0].lat + "\," + data[0].lng ,20);
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


function GeneratePlotGrid(){
	$('#loading').show();
	
	var name = plotInfos[currentPlot].name;
	
	var lat =  plotInfos[currentPlot].lat;
	var lng =   plotInfos[currentPlot].lng;
	var width =  plotInfos[currentPlot].width;
	var height =  plotInfos[currentPlot].height;
	var count =  plotInfos[currentPlot].count;
	var angle =  plotInfos[currentPlot].angle;
	var epsg =  plotInfos[currentPlot].epsg;
	
	var offsets = plotInfos[currentPlot].offsets;
	var vshifts = plotInfos[currentPlot].vshifts;
	
	var rowHeight =  $('#row-height').val();
	var rowCount =  $('#row-count').val();
	var colWidth =  $('#col-width').val();
	var colCount =  $('#col-count').val();
	
	$.ajax({
		url: 'Resources/PHP/GenerateBoundaryWithGrid.php',
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
			vshifts: vshifts,
			rowheight: rowHeight,
			rowcount: rowCount,
			colwidth: colWidth,
			colcount: colCount
			
		},                         
		success: function(response) {
			UpdateGridLayer(response);
			$('#loading').hide();
			
		}
	});
}

function UpdateGridLayer(filePath){

	var plotGrid = new L.GeoJSON.AJAX(filePath, {style: gridStyle, zIndex: 995,  onEachFeature: onEachFeature});

	plotGrid.on('data:loaded',function(e){
		var currentGrid = plotInfos[currentPlot].grid;
		if (currentGrid){
			map.removeLayer(currentGrid);
		}
	
		var grid = new L.FeatureGroup();
		map.addLayer(grid);
		grid.addLayer(plotGrid);
		plotInfos[currentPlot].grid = grid;
		
	});
}
