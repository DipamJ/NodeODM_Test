$(document).ready(function(){

	$("#tabs").tabs({
		active: 0,

		activate: function( event, ui ) {
			var tabName = event.currentTarget.innerText.toLowerCase();

			if (tabName.toLowerCase() == "product type"){
				tabName = "type";
			}

			//if ($("#" + tabName + "-wrapper").html() == "" && tabName.toLowerCase() != "flight"){
			if ($("#" + tabName + "-wrapper").html() == ""){
				GetList(tabName);
			}
		}
	});


	$("input[id^='add']").mouseup(function(){
		$(this).blur();
	})

	$('#manage').on('keyup keypress', function(e) {
		var keyCode = e.keyCode || e.which;
		if (keyCode === 13) {
			e.preventDefault();
			return false;
		}
	});

	$('.coordinate').numeric({
		allowThouSep : false
	});

	$('.zoom').numeric({
		allowMinus   : false,
		allowThouSep : false,
		allowDecSep  : false,
		min			 : 1,
	});

	$("#flight-date").datepicker();
	$("#planting-date").datepicker();
	$("#harvest-date").datepicker();

	GetList('crop');
	GetList('project');
	/*
	GetList('platform');
	GetList('sensor');
	GetList('type');
	*/
	/*
	GetList('flight');
	GetList('type');
	*/

	$("#project-search").on('keyup',function () {
		var filter = $("#project-search").val().toUpperCase();
		$("#project-wrapper table").each(function(){
			$(this).find("tr").each(function(){
				var nameCol = $(this).find("td span")[0];
				if(nameCol){
					console.log($(this));
					if (nameCol.innerText.toUpperCase().indexOf(filter) > -1) {
						$(this).show();
					} else {
						$(this).hide();
					}
				}
			});
			$(this).css("height","auto");
		});
		/*
		$("#project-list tr").each(function(){
			var filter = $("#project-search").val().toUpperCase();
			var nameCol = $(this).find("td span");
			console.log(nameCol);
			console.log(nameCol.attr("id"));

			if (nameCol.attr("id").toUpperCase().indexOf(filter) > -1) {
				$(this).show();
			} else {
				$(this).hide();
			}

		});
		*/
	});
});

function Add(type){ // onclick="Add('crop') -> type = crop
	if (CheckInput(type, null)){
		var name = $('#' + type + '-name').val();
		var url = "Resources/PHP/" + type + ".php?name=" + name + "&action=add";

		if (type == 'flight'){
			var project = $('#flight-project').val();
			var platform = $('#flight-platform').val();
			var sensor = $('#flight-sensor').val();
			var date = $('#flight-date').val();
			var altitude = $('#flight-altitude').val();
			var forward = $('#flight-forward').val();
			var side = $('#flight-side').val();
			url = url + "&project=" + project + "&platform=" + platform + "&sensor=" + sensor +
				"&date=" + date + "&altitude=" + altitude + "&forward=" + forward + "&side=" + side;
		} else if (type == 'project'){
			var crop = $('#crop-type').val();
			var plantingDate = $('#planting-date').val();
			var harvestDate = $('#harvest-date').val();
			var description = $('#description').val();
			var centerLat = $('#center-lat').val();
			var centerLng = $('#center-lng').val();
			var minZoom = $('#min-zoom').val();
			var maxZoom = $('#max-zoom').val();
			var defaultZoom = $('#default-zoom').val();
			var visualization = $('#visualization').val();

			url = url + "&crop=" + crop + "&plantingDate=" + plantingDate + "&harvestDate=" + harvestDate +
				"&description=" + description + "&centerLat=" + centerLat + "&centerLng=" + centerLng +
				"&minZoom=" + minZoom + "&maxZoom=" + maxZoom + "&defaultZoom=" + defaultZoom + "&visualization=" + visualization;
		}
		//////
		// else if (type == 'product-type'){
		else if (type == 'type'){
			// var platform = $('#flight-platform').val();
			var type_selected = $('#product_type_select').val();

			url = url + "&type=" + type_selected;

			//alert('product_type_select select has been reached.');

			//alert('The ' + type + ' has been added.');
		}
		/////

		//url += "&type=" + type;
		$.ajax({
			url: url,
			dataType: 'text',
			success: function(response) {
				if (response == "1"){
					alert('The ' + type + ' has been added.');
					GetList(type);

				} else {
					alert('Could not add the ' + type + '. Error: ' + response + '.');
				}
			}
		});
	} else {
		alert('Please fill in all required fields');
	}
}

function GetList(type)
{
	var project = $("#flight-project").val();
	var platform = $("#flight-platform").val();
	var sensor = $("#flight-sensor").val();

	$("#loading").show();
	$.ajax({
		url: "Resources/PHP/" + type + ".php",
		dataType: "text",
		data: {
			action: "list",
			project: project,
			platform: platform,
			sensor: sensor
		},
		success: function(response) {
			var items = "";
			var options = "";
			var data = JSON.parse(response);

			if (data.length > 0)
			{
				var table =	"<table id='" + type + "s' >" +
					"<thead>" +
					"<tr style='background: #555555; color: #ffffff;'>" +
					"<th style='border: none;'>&nbsp;</th>" +
					//"<th>ID</th>" +
					"<th style='border: none;'>Name</th>";
				if (type == "flight"){
					table +=
						"<th style='border: none;'>Project</th>" +
						"<th style='border: none;'>Platform</th>" +
						"<th style='border: none;'>Sensor</th>"	   +
						"<th style='border: none;'>Date</th>"	   +
						"<th style='border: none;'>Flight Altitude</th>" +
						"<th style='border: none;'>Forward Overlap</th>" +
						"<th style='border: none;'>Side Overlap</th>";
				} else if (type == 'project'){
					table +=
                        "<th style='border: none;'>Crop</th>" +
						"<th style='border: none;'>Planting Date</th>" +
						"<th style='border: none;'>Harvest Date</th>"	   +
						"<th style='border: none;'>Description</th>"	   +
						"<th style='border: none;'>Center Lat</th>" +
						"<th style='border: none;'>Center Long</th>" +
						"<th style='border: none;'>Min Zoom</th>" +
						"<th style='border: none;'>Max Zoom</th>" +
						"<th style='border: none;'>Default Zoom</th>" +
						"<th style='border: none;'>Visualization Page</th>"; // add here link
				} else if (type == 'product'){
					table +=
						"<th style='border: none;'>TMS Path</th>";
				}

				table +=
					"</tr>" +
					"</thead>" +
					"<tbody id='" + type + "-list'>" +
					"</tbody>" +
					"</table>";

				$("#" + type + "-wrapper").html(table);

				$.each(data,function(index,item)
				{

					items+="<tr>" +
						"<td>" +
						"<input style='padding: 4px; background: #fbbc05; margin-right: 3px;' id='edit-" + type + "-" + item.ID +"' type='image' class='' src='Resources/Images/edit.png' alt='Edit' onclick='Edit(" + item.ID +", \"" + type + "\"); return false;' title='Edit'>" +
						"<input style='padding: 3px; background: #449d44; margin-right: 3px;display:none' id='confirmEdit-" + type + "-" + item.ID +"' type='image' class='' src='Resources/Images/confirm.png' alt='Confirm' onclick='ConfirmEdit(" + item.ID +", \"" + type + "\"); return false;' title='Confirm'>" +
						"<input style='padding: 3px; background: #ccc; margin-right: 3px;display:none' id='cancelEdit-" + type + "-" + item.ID +"' type='image' class='' src='Resources/Images/cancel.png' alt='Cancel' onclick='CancelEdit(" + item.ID +", \"" + type + "\"); return false;' title='Cancel'>" +
						"<input style='padding: 3px; background: #d9534f;' id='delete-" + type + "-" + item.ID +"' type='image' class='' src='Resources/Images/delete.png' alt='Delete' onclick='Delete(" + item.ID +", \"" + type + "\"); return false;' title='Delete'>" +
						"<input id='confirmDelete-" + type + "-"  + item.ID +"' type='image' class='image-button confirm-delete-button' src='Resources/Images/confirm.png' alt='Confirm' style='display:none; padding: 3px !important;' onclick='ConfirmDelete(" + item.ID +", \"" + type + "\"); return false;' title='Confirm'>" +
						"<input id='cancelDelete-" + type + "-"  + item.ID +"' type='image' class='image-button cancel-delete-button' src='Resources/Images/cancel.png' alt='Cancel' style='display:none; padding: 3px !important;' onclick='CancelDelete(" + item.ID +", \"" + type + "\"); return false;' title='Cancel'>" +
						"</td>" +

						"<td style='overflow:hidden'>" +
						"<span id='" + type + "-" + item.ID +"-display-name'>" + item.Name + "</span>" +
						"<input id='" + type + "-" + item.ID +"-edit-name' class='edit-input' type='text' value='" + item.Name + "' style='display:none'>" +
						"</td>";

					if (type == "flight"){
						items+= "<td style='overflow:hidden'>" +
							"<span id='" + type + "-" + item.ID +"-display-project'>" + item.ProjectName + "</span>" +
							"<select id='" + type + "-" + item.ID +"-edit-project' class='edit-input' style='display:none'>" + $("#flight-project").html() + "</select>" +
							"</td>" +

							"<td style='overflow:hidden'>" +
							"<span id='" + type + "-" + item.ID +"-display-platform'>" + item.PlatformName + "</span>" +
							"<select id='" + type + "-" + item.ID +"-edit-platform' class='edit-input' style='display:none'>" + $("#flight-platform").html() + "</select>" +
							"</td>" +

							"<td style='overflow:hidden'>" +
							"<span id='" + type + "-" + item.ID +"-display-sensor'>" + item.SensorName + "</span>" +
							"<select id='" + type + "-" + item.ID +"-edit-sensor' class='edit-input' style='display:none'>" + $("#flight-sensor").html() + "</select>" +
							"</td>" +
							"<td style='overflow:hidden'>" +
							"<span id='" + type + "-" + item.ID +"-display-date'>" + item.Date.replace(/\-/g, '/') + "</span>" +
							"<input id='" + type + "-" + item.ID +"-edit-date' class='edit-input' type='text' value='" + item.Date.replace(/\-/g, '/') + "' style='display:none'>" +
							"</td>" +
							"<td style='overflow:hidden'>" +
							"<span id='" + type + "-" + item.ID +"-display-altitude'>" + item.Altitude + "</span>" +
							"<input id='" + type + "-" + item.ID +"-edit-altitude' class='edit-input' type='text' value='" + item.Altitude + "' style='display:none'>" +
							"</td>" +
							"<td style='overflow:hidden'>" +
							"<span id='" + type + "-" + item.ID +"-display-forward'>" + item.Forward + "</span>" +
							"<input id='" + type + "-" + item.ID +"-edit-forward' class='edit-input' type='text' value='" + item.Forward + "' style='display:none'>" +
							"</td>" +
							"<td style='overflow:hidden'>" +
							"<span id='" + type + "-" + item.ID +"-display-side'>" + item.Side + "</span>" +
							"<input id='" + type + "-" + item.ID +"-edit-side' class='edit-input' type='text' value='" + item.Side + "' style='display:none'>" +
							"</td>";
					} else if (type == 'product'){
						items+= "<td style='overflow:hidden'>" +
							"<span id='" + type + "-" + item.ID +"-display-tms-path'>" + item.TMSPath + "</span>" +
							"<input id='" + type + "-" + item.ID +"-edit-tms-path' class='edit-input' type='text' value='" + item.TMSPath + "' style='display:none'>" +
							"</td>";


					} else {
						if (type == 'project'){
							items+= "<td style='overflow:hidden'>" +
								"<span id='" + type + "-" + item.ID +"-display-crop'>" + item.CropName + "</span>" +
								"<select id='" + type + "-" + item.ID +"-edit-crop' class='edit-input' style='display:none'>" + $("#crop-type").html() + "</select>" +
								"</td>" +
								"<td style='overflow:hidden'>" +
								"<span id='" + type + "-" + item.ID +"-display-planting-date'>" + item.PlantingDate.replace(/\-/g, '/') + "</span>" +
								"<input id='" + type + "-" + item.ID +"-edit-planting-date' class='edit-input' type='text' value='" + item.PlantingDate.replace(/\-/g, '/') + "' style='display:none'>" +
								"</td>" +
								"<td style='overflow:hidden'>" +
								"<span id='" + type + "-" + item.ID +"-display-harvest-date'>" + item.HarvestDate.replace(/\-/g, '/') + "</span>" +
								"<input id='" + type + "-" + item.ID +"-edit-harvest-date' class='edit-input' type='text' value='" + item.HarvestDate.replace(/\-/g, '/') + "' style='display:none'>" +
								"</td>" +
								"<td style='overflow:hidden'>" +
								"<span id='" + type + "-" + item.ID +"-display-description'>" + item.Description + "</span>" +
								"<input id='" + type + "-" + item.ID +"-edit-description' class='edit-input' type='text' value='" + item.Description + "' style='display:none'>" +
								"</td>" +
								"<td style='overflow:hidden'>" +
								"<span id='" + type + "-" + item.ID +"-display-center-lat'>" + item.CenterLat + "</span>" +
								"<input id='" + type + "-" + item.ID +"-edit-center-lat' class='edit-input' type='text' value='" + item.CenterLat + "' style='display:none'>" +
								"</td>" +
								"<td style='overflow:hidden'>" +
								"<span id='" + type + "-" + item.ID +"-display-center-lng'>" + item.CenterLng + "</span>" +
								"<input id='" + type + "-" + item.ID +"-edit-center-lng' class='edit-input' type='text' value='" + item.CenterLng + "' style='display:none'>" +
								"</td>" +
								"<td style='overflow:hidden'>" +
								"<span id='" + type + "-" + item.ID +"-display-min-zoom'>" + item.MinZoom + "</span>" +
								"<input id='" + type + "-" + item.ID +"-edit-min-zoom' class='edit-input' type='text' value='" + item.MinZoom + "' style='display:none'>" +
								"</td>" +
								"<td style='overflow:hidden'>" +
								"<span id='" + type + "-" + item.ID +"-display-max-zoom'>" + item.MaxZoom + "</span>" +
								"<input id='" + type + "-" + item.ID +"-edit-max-zoom' class='edit-input' type='text' value='" + item.MaxZoom + "' style='display:none'>" +
								"</td>" +
								"<td style='overflow:hidden'>" +
								"<span id='" + type + "-" + item.ID +"-display-default-zoom'>" + item.DefaultZoom + "</span>" +
								"<input id='" + type + "-" + item.ID +"-edit-default-zoom' class='edit-input' type='text' value='" + item.DefaultZoom + "' style='display:none'>" +
								"</td>" +
								"<td style='overflow:hidden'>" +
								"<span id='" + type + "-" + item.ID +"-display-visualization-page'>" + item.VisualizationPage + "</span>" +
								"<input id='" + type + "-" + item.ID +"-edit-visualization-page' class='edit-input' type='text' value='" + item.VisualizationPage + "' style='display:none'>" +
								"</td>";
						}
						options += "<option value='" + item.ID+"'>" + item.Name + "</option>";
					}


					items +=	"</tr>";

				});
				$("#" + type + "-list").html(items);

				/*
				if (type != "flight"){

					$("#flight-"+ type).html(options);
				}
				*/



				var rowHeight = 41;
				var padding = 10;
				var actualHeight = (data.length + 1) * rowHeight + padding;
				var maxHeight = 300;
				var height = actualHeight < maxHeight ? actualHeight : maxHeight;
				//var bigWidth = 1050;
				var bigWidth = 900;
				var smallWidth = 600;


				if (type == "flight"){
					$('#' + type + 's').fxdHdrCol({
						//fixedCols:  2,
						width:     bigWidth,
						height:    height,

						colModal: [

							{ width: 150, align: 'center' },
							{ width: 250, align: 'center' },
							{ width: 300,  align: 'center' },
							{ width: 100,  align: 'center' },
							{ width: 100,  align: 'center' },
							{ width: 100,  align: 'center' },
							{ width: 100,  align: 'center' },
							{ width: 100,  align: 'center' },
							{ width: 100,  align: 'center' },

						],
						sort: false
					});
				} else if (type == 'project') {

					$('#' + type + 's').fxdHdrCol({
						//fixedCols:  2,
						width:     bigWidth,
						height:    height,

						colModal: [

							{ width: 150, align: 'center' },
							{ width: 350, align: 'center' },
							{ width: 200,  align: 'center' },
							{ width: 100,  align: 'center' },
							{ width: 100,  align: 'center' },
							{ width: 300,  align: 'center' },
							{ width: 100,  align: 'center' },
							{ width: 100,  align: 'center' },
							{ width: 100,  align: 'center' },
							{ width: 100,  align: 'center' },
							{ width: 100,  align: 'center' },
							{ width: 600,  align: 'center' },

						],
						sort: false
					});


					$("#flight-" + type).html(options);
					$("#flight-" + type).chosen({
						inherit_select_classes: true,
						width: 450
					});


					var optionWithAll = "<option value='%'>All</option>" + options;
					$("#raw-data-"+ type).html(optionWithAll);
					$("#raw-data-" + type).chosen({
						inherit_select_classes: true,
						width: 350
					});

					var optionWithAll = "<option value='%'>All</option>" + options;
					$("#data-product-"+ type).html(optionWithAll);
					$("#data-product-" + type).chosen({
						inherit_select_classes: true,
						width: 450
					});

					GetList('platform');
					GetList('sensor');
					GetList('type');

				} else if (type == 'product') {

					$('#' + type + 's').fxdHdrCol({
						//fixedCols:  2,
						width:     bigWidth,
						height:    height,

						colModal: [

							{ width: 150, align: 'center' },
							{ width: 250, align: 'center' },
							{ width: 800, align: 'center' },
						],
						sort: false
					});
				} else {
					$('#' + type + 's').fxdHdrCol({
						//fixedCols:  0,
						width:    smallWidth,
						height:   height,

						colModal: [
							{ width: 150, align: 'center' },
							{ align: 'center' }
						],
						sort: false
					});

					if (type != "type"){
						var optionWithAll = "<option value='%'>All</option>" + options;
						$("#flight-"+ type).html(optionWithAll);
						$("#raw-data-"+ type).html(optionWithAll);
						$("#data-product-"+ type).html(optionWithAll);
					} else {
						var optionWithAll = "<option value='%'>All</option>" + options;
						$("#data-product-"+ type).html(optionWithAll);
					}

					if (type == 'crop') {
						$("#crop-type").html(options);
						$("#crop-type").chosen({
							inherit_select_classes: true
						});
					}
				}
			} else {

				var notifyText = "<p style='text-align:center'>No uploaded item found.</p>";
				$("#" + type + "-wrapper").html(notifyText);
			}
			$("#loading").hide();

		},
		error: function(xhr){
			alert('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
			$("#loading").hide();
		}
	});
}

function Edit(id, type){
	//alert(type);
	$('#confirmEdit-' + type + '-' + id).show();
	$('#cancelEdit-' + type + '-'  + id).show();
	$('#edit-' + type + '-' + id).hide();

	$('[id^="' + type + '-'+ id + '-display"]').hide();
	$('[id^="' + type + '-'+ id + '-edit"]').show();

	$('#' + type + '-' + id + '-edit-name').val($('#' + type + '-' + id + '-display-name').html());

	if (type == "flight"){

		$('#' + type + '-' + id + '-edit-project').html( $('#flight-project').html() );
		$('#' + type + '-' + id + '-edit-project option').filter(function() {
			return $(this).text() == $('#' + type + '-' + id + '-display-project').html();
		}).prop('selected', true);

		$('#' + type + '-' + id + '-edit-platform').html( $('#flight-platform').html() );
		$('#' + type + '-' + id + '-edit-platform option').filter(function() {
			return $(this).text() == $('#' + type + '-' + id + '-display-platform').html();
		}).prop('selected', true);

		$('#' + type + '-' + id + '-edit-sensor').html( $('#flight-sensor').html() );
		$('#' + type + '-' + id + '-edit-sensor option').filter(function() {
			return $(this).text() == $('#' + type + '-' + id + '-display-sensor').html();
		}).prop('selected', true);

		$('#' + type + '-' + id + '-edit-date').datepicker();

		$('#' + type + '-' + id + '-edit-altitude').val($('#' + type + '-' + id + '-display-altitude').html());
		$('#' + type + '-' + id + '-edit-forward').val($('#' + type + '-' + id + '-display-forward').html());
		$('#' + type + '-' + id + '-edit-side').val($('#' + type + '-' + id + '-display-side').html());

	} else if (type == 'project'){

		$('#' + type + '-' + id + '-edit-crop').html( $('#crop-type').html() );
		$('#' + type + '-' + id + '-edit-crop option').filter(function() {
			return $(this).text() == $('#' + type + '-' + id + '-display-crop').html();
		}).prop('selected', true);

		$('#' + type + '-' + id + '-edit-planting-date').datepicker();
		$('#' + type + '-' + id + '-edit-harvest-date').datepicker();

		$('#' + type + '-' + id + '-edit-description').val($('#' + type + '-' + id + '-display-description').html());
		$('#' + type + '-' + id + '-edit-center-lat').val($('#' + type + '-' + id + '-display-center-lat').html());
		$('#' + type + '-' + id + '-edit-center-lng').val($('#' + type + '-' + id + '-display-center-lng').html());
		$('#' + type + '-' + id + '-edit-min-zoom').val($('#' + type + '-' + id + '-display-min-zoom').html());
		$('#' + type + '-' + id + '-edit-max-zoom').val($('#' + type + '-' + id + '-display-max-zoom').html());
		$('#' + type + '-' + id + '-edit-default-zoom').val($('#' + type + '-' + id + '-display-default-zoom').html());
		$('#' + type + '-' + id + '-edit-visualization-page').val($('#' + type + '-' + id + '-display-visualization-page').html());

	} else if (type == 'product'){
		$('#' + type + '-' + id + '-edit-tms-path').val($('#' + type + '-' + id + '-display-tms-path').html());
		$('[id^="' + type + '-'+ id + '-display-name"]').show();
		$('[id^="' + type + '-'+ id + '-edit-name"]').hide();

	}


	CancelDelete(id);
}

function CancelEdit(id, type){
	$('#confirmEdit-' + type + '-' + id).hide();
	$('#cancelEdit-' + type + '-' + id).hide();
	$('#edit-' + type + '-' + id).show();

	$('[id^="' + type + '-'+ id + '-display"]').show();
	$('[id^="' + type + '-'+ id + '-edit"]').hide();
}

function ConfirmEdit(id, type){

	if (CheckInput(type, id)){

		var name = $('#' + type + '-' + id + '-edit-name').val();

		var url = "Resources/PHP/" + type + ".php?id=" + id + "&name=" + name + "&action=edit";
		if (type == 'flight'){
			var project = $('#' + type + '-' + id + '-edit-project').val();
			var platform = $('#' + type + '-' + id + '-edit-platform').val();
			var sensor = $('#' + type + '-' + id + '-edit-sensor').val();
			var date = $('#' + type + '-' + id + '-edit-date').val();
			var altitude = $('#' + type + '-' + id + '-edit-altitude').val();
			var forward = $('#' + type + '-' + id + '-edit-forward').val();
			var side = $('#' + type + '-' + id + '-edit-side').val();

			url = 	url + "&project=" + project + "&platform=" + platform + "&sensor=" + sensor +
				"&date=" + date + "&altitude=" + altitude + "&forward=" + forward + "&side=" + side;
		} else if (type == 'project'){
			var crop = $('#' + type + '-' + id + '-edit-crop').val();
			var plantingDate = $('#' + type + '-' + id + '-edit-planting-date').val();
			var harvestDate = $('#' + type + '-' + id + '-edit-harvest-date').val();
			var description = $('#' + type + '-' + id + '-edit-description').val();
			var centerLat = $('#' + type + '-' + id + '-edit-center-lat').val();
			var centerLng = $('#' + type + '-' + id + '-edit-center-lng').val();
			var minZoom = $('#' + type + '-' + id + '-edit-min-zoom').val();
			var maxZoom = $('#' + type + '-' + id + '-edit-max-zoom').val();
			var defaultZoom = $('#' + type + '-' + id + '-edit-default-zoom').val();
			var visualizationPage = $('#' + type + '-' + id + '-edit-visualization-page').val();

			url = url + "&crop=" + crop + "&plantingDate=" + plantingDate + "&harvestDate=" + harvestDate +
				"&description=" + description + "&centerLat=" + centerLat + "&centerLng=" + centerLng +
				"&minZoom=" + minZoom + "&maxZoom=" + maxZoom + "&defaultZoom=" + defaultZoom + "&visualization=" + visualizationPage;

		} else if (type == 'product'){

			var tmsPath = $('#' + type + '-' + id + '-edit-tms-path').val();

			url = url + "&tmsPath=" + tmsPath;

		}

		$.ajax({
			url: url,
			dataType: 'text',
			success: function(response) {
				if (response == "1") {

					$('#confirmEdit-' + type + '-' + id).hide();
					$('#cancelEdit-' + type + '-' + id).hide();
					$('#edit-' + type + '-' + id).show();

					$('[id^="' + type + '-'+ id + '-display"]').show();
					$('[id^="' + type + '-'+ id + '-edit"]').hide();

					$('#' + type + '-' + id + '-display-name').html($('#' + type + '-' + id + '-edit-name').val());

					if (type == "flight"){
						$('#' + type + '-' + id + '-display-project').html($('#' + type + '-' + id + '-edit-project').text());
						$('#' + type + '-' + id + '-display-platform').html($('#' + type + '-' + id + '-edit-platform').text());
						$('#' + type + '-' + id + '-display-sensor').html($('#' + type + '-' + id + '-edit-sensor').text());
						$('#' + type + '-' + id + '-display-date').html($('#' + type + '-' + id + '-edit-date').val());
						$('#' + type + '-' + id + '-display-altitude').html($('#' + type + '-' + id + '-edit-altitude').val());
						$('#' + type + '-' + id + '-display-forward').html($('#' + type + '-' + id + '-edit-forward').val());
						$('#' + type + '-' + id + '-display-side').html($('#' + type + '-' + id + '-edit-side').val());
					} else if (type == 'project'){

						$('#' + type + '-' + id + '-display-crop').html($('#' + type + '-' + id + '-edit-crop').text());
						$('#' + type + '-' + id + '-display-planting-date').html($('#' + type + '-' + id + '-edit-planting-date').val());
						$('#' + type + '-' + id + '-display-harvest-date').html($('#' + type + '-' + id + '-edit-harvest-date').val());

						$('#' + type + '-' + id + '-display-description').html($('#' + type + '-' + id + '-edit-description').val());
						$('#' + type + '-' + id + '-display-center-lat').html($('#' + type + '-' + id + '-edit-center-lat').val());
						$('#' + type + '-' + id + '-display-center-lng').html($('#' + type + '-' + id + '-edit-center-lng').val());

						$('#' + type + '-' + id + '-display-min-zoom').html($('#' + type + '-' + id + '-edit-min-zoom').val());
						$('#' + type + '-' + id + '-display-max-zoom').html($('#' + type + '-' + id + '-edit-max-zoom').val());
						$('#' + type + '-' + id + '-display-default-zoom').html($('#' + type + '-' + id + '-edit-default-zoom').val());

						$('#' + type + '-' + id + '-display-visualization-page').html($('#' + type + '-' + id + '-edit-visualization-page').val());

					} else if (type == 'product'){
						$('#' + type + '-' + id + '-display-tms-path').html($('#' + type + '-' + id + '-edit-tms-path').val());
					}

					alert("The " + type + " has been updated.");

					GetList(type);

				} else {
					alert("Could not update the " + type + " name. Error: " + response + ".");
				}
			}
		});
	} else {
		alert('Please fill in all required fields');
	}
}

function Delete(id, type){
	$('#confirmDelete-' + type + '-'  + id).show();
	$('#cancelDelete-' + type + '-'  + id).show();
	$('#delete-' + type + '-'  + id).hide();
	CancelEdit(id);
}

function CancelDelete(id, type){
	$('#confirmDelete-' + type + '-'  + id).hide();
	$('#cancelDelete-' + type + '-'  + id).hide();
	$('#delete-' + type + '-'  + id).show();
}

function ConfirmDelete(id, type){
	$.ajax({
		url: 'Resources/PHP/' + type + '.php',
		dataType: 'text',
		data: { id: id, action: 'delete'},
		success: function(response) {
			if (response == "1") {
				$('#confirmDelete-' + type + '-'  + id).hide();
				$('#cancelDelete-' + type + '-'  + id).hide();
				$('#delete-' + type + '-'  + id).show();

				alert("The " + type + " has been deleted.");

				GetList(type);

			} else {
				alert("Could not delete the " + type + ". Error: " + response + ".");
			}
		}
	});
}

function CheckInput(type, id){

	var isValid = true;

	var fields = [];

	if (type == 'project'){
		if (id){
			fields = ['name', 'planting-date', 'center-lat', 'center-lng', 'min-zoom', 'max-zoom', 'default-zoom'];

		} else {
			fields = ['project-name', 'planting-date', 'center-lat', 'center-lng', 'min-zoom', 'max-zoom', 'default-zoom'];
		}

	} else if (type == 'flight'){

		if (id){
			fields = ['name', 'date', 'altitude', 'forward', 'side'];

		} else {
			fields = ['flight-name', 'flight-date', 'flight-altitude', 'flight-forward', 'flight-side'];
		}

	} else {
		if (id){
			fields = ['name'];
		} else {
			fields = [type + '-name'];
		}
	}

	if (id){
		$.each(fields, function(index, item) {
			fields[index] = type + '-' + id + '-edit-' + item;
		});
	}

	console.log(fields);

	$.each(fields, function(index, item) {
		if (!$('#' + item).val()){
			$('#' + item).addClass("error");
			isValid = false;
		}else{
			$('#' + item).removeClass("error");
		}
	});

	return isValid;
}

//------------------------------Manage uploaded raw data-----------------------------------------
/*
function SearchRaw(){
	$("#loading").show();
	GetUnfinishedRawList();
	GetFinishedRawList();
}

function GetUnfinishedRawList(){
	var project = $("#raw-data-project").val();

	$.ajax({
		url: "Resources/PHP/CheckUpload.php",
		dataType: "text",
		data: { project: project},
		success: function(response) {
			var data = JSON.parse(response);
			var items = "";
			$("#unfinished-list-wrapper").html("");
			if (data.length > 0)
			{
				var table =	"<table id='unfinished-list-table'>" +
								"<thead>" +
									"<tr>" +
										"<th>&nbsp;</th>" +
										"<th>File Name</th>" +
										"<th>Uploader</th>" +
										"<th>Project</th>" +
										"<th>File Size</th>" +
										"<th>Status</th>" +
										"<th>Progress</th>" +
										"<th>Platform</th>" +
										"<th>Sensor</th>" +
										"<th>Date</th>" +
										"<th>Flight</th>" +
									"</tr>" +
								"</thead>" +
								"<tbody id='unfinished-list'>" +
								"</tbody>" +
							"</table>";

				$("#unfinished-list-wrapper").html(table);
				$.each(data,function(index,item)
				{

					items+= "<tr>" +
								"<td style='overflow:hidden'>" +
									"<input id='delete-" + item.ID +"' type='image' class='image-button delete-button' src='Resources/Images/delete.png' alt='Delete' onclick='Delete(" + item.ID + "); return false;' title='Delete'>" +
									"<input id='confirmDelete-" + item.ID +"' type='image' class='image-button confirm-delete-button' src='Resources/Images/confirm.png' alt='Confirm' style='display:none' onclick='ConfirmDelete(" + item.ID + "); return false;' title='Confirm'>" +
									"<input id='cancelDelete-" + item.ID +"' type='image' class='image-button cancel-edit-button' src='Resources/Images/cancel.png' alt='Cancel' style='display:none' onclick='CancelDelete(" + item.ID +"); return false;' title='Cancel'>" +
								"</td>" +
								"<td style='overflow:hidden'>" +
									"<span>" + item.FileName + "</span>" +
								"</td>" +
								"<td style='overflow:hidden'>" +
									"<span>" + item.Uploader + "</span>" +
								"</td>" +
								"<td style='overflow:hidden'>" +
									"<span>" + item.ProjectName + "</span>" +
								"</td>" +
								"<td style='overflow:hidden'>" +
									"<span>" + item.Size + "</span>" +
								"</td>" +
								"</td>" +
								"<td style='overflow:hidden'>" +
									"<span>" + item.Status + "</span>" +
								"</td>" +
								"<td style='overflow:hidden'>" +
									"<span>" + item.Progress + "%</span>" +
								"</td>" +
								"<td style='overflow:hidden'>" +
									"<span>" + item.PlatformName + "</span>" +
								"</td>" +
									"<td style='overflow:hidden'>" +
									"<span>" + item.SensorName + "</span>" +
								"</td>" +

								"<td style='overflow:hidden'>" +
									"<span>" + item.Date.replace(/\-/g, '/') + "</span>" +
								"</td>" +
								"<td style='overflow:hidden'>" +
									"<span>" + item.FlightName + "</span>" +
								"</td>" +
							"</tr>";



					items +=	"</tr>";
				});

				$("#unfinished-list").html(items);


				var rowHeight = 61;
				var padding = 10;
				var actualHeight = (data.length + 1) * rowHeight + padding;
				var maxHeight = 300;
				var height = actualHeight < maxHeight ? actualHeight : maxHeight;

				$('#unfinished-list-table').fxdHdrCol({
					fixedCols:  4,
					width:     900,
					height:    height,

					colModal: [
						{ width: 75, align: 'center' },
						{ width: 300, align: 'center' },
						{ width: 100, align: 'center' },
						{ width: 250, align: 'center' },
						{ width: 100, align: 'center' },
						{ width: 100, align: 'center' },
						{ width: 100, align: 'center' },
						{ width: 200, align: 'center' },
						{ width: 150,  align: 'center' },
						{ width: 150,  align: 'center' },
						{ width: 200,  align: 'center' },
					],
					sort: false
				});

			}
		}
	});
}

function GetFinishedRawList(){
	var project = $("#raw-data-project").val();

	$.ajax({
		url: "Resources/PHP/GetFinishedList.php",
		dataType: "text",
		data: {project: project},
		success: function(response) {
			var data = JSON.parse(response);
			var items = "";
			$("#finished-list-wrapper").html("");
			if (data.length > 0)
			{
				var table =	"<table id='finished-list-table'>" +
								"<thead>" +
									"<tr>" +
										"<th>&nbsp;</th>" +
										"<th>File Name</th>" +
										"<th>File Size</th>" +
										"<th>Project</th>" +
										"<th>Platform</th>" +
										"<th>Sensor</th>" +
										"<th>Date</th>" +
										"<th>Flight</th>" +
									"</tr>" +
								"</thead>" +
								"<tbody id='finished-list'>" +
								"</tbody>" +
							"</table>";

				$("#finished-list-wrapper").html(table);
				$.each(data,function(index,item)
				{

					items+= "<tr>" +
								"<td style='overflow:hidden'>" +
									"<input id='delete-" + item.ID +"' type='image' class='image-button delete-button' src='Resources/Images/delete.png' alt='Delete' onclick='Delete(" + item.ID + "); return false;' title='Delete'>" +
									"<input id='confirmDelete-" + item.ID +"' type='image' class='image-button confirm-delete-button' src='Resources/Images/confirm.png' alt='Confirm' style='display:none' onclick='ConfirmDelete(" + item.ID + "); return false;' title='Confirm'>" +
									"<input id='cancelDelete-" + item.ID +"' type='image' class='image-button cancel-edit-button' src='Resources/Images/cancel.png' alt='Cancel' style='display:none' onclick='CancelDelete(" + item.ID +"); return false;' title='Cancel'>" +
									"<input id='download-" + item.ID +"' type='image' class='image-button download-button' src='Resources/Images/download.png' alt='Download' onclick='Download(\"" + item.DownloadPath + "\"); return false;' title='Download'>" +
								"</td>" +
								"<td style='overflow:hidden'>" +
									"<span id='" + item.ID +"-file-name'>" + item.FileName + "</span>" +
								"</td>" +
								"<td style='overflow:hidden'>" +
									"<span>" + item.Size + "</span>" +
								"</td>" +
								"<td style='overflow:hidden'>" +
									"<span>" + item.ProjectName + "</span>" +
								"</td>" +
								"<td style='overflow:hidden'>" +
									"<span>" + item.PlatformName + "</span>" +
								"</td>" +
									"<td style='overflow:hidden'>" +
									"<span>" + item.SensorName + "</span>" +
								"</td>" +

								"<td style='overflow:hidden'>" +
									"<span>" + item.Date.replace(/\-/g, '/') + "</span>" +
								"</td>" +
								"<td style='overflow:hidden'>" +
									"<span>" + item.FlightName + "</span>" +
								"</td>" +
							"</tr>";



					items +=	"</tr>";
				});

				$("#finished-list").html(items);



				var rowHeight = 61;
				var padding = 10;
				var actualHeight = (data.length + 1) * rowHeight + padding;
				var maxHeight = 300;
				var height = actualHeight < maxHeight ? actualHeight : maxHeight;

				$('#finished-list-table').fxdHdrCol({
					fixedCols:  2,
					width:     900,
					height:    height,

					colModal: [
						{ width: 150, align: 'center' },
						{ width: 350, align: 'center' },
						{ width: 100, align: 'center' },
						{ width: 300, align: 'center' },
						{ width: 200, align: 'center' },
						{ width: 150,  align: 'center' },
						{ width: 150,  align: 'center' },
						{ width: 200,  align: 'center' },
					],
					sort: false
				});
				$("#loading").hide();
			}
		}
	});
}

function Download(link){
	var win = window.open(link, '_blank');
	if (win) {
		//Browser has allowed it to be opened
		win.focus();
	} else {
		//Browser has blocked it
		alert('Please allow popups for this website');
	}
}

function Delete(id){
	$('#confirmDelete-' + id).show();
	$('#cancelDelete-' + id).show();
	$('#delete-' + id).hide();
}

function CancelDelete(id){
	$('#confirmDelete-' + id).hide();
	$('#cancelDelete-' + id).hide();
	$('#delete-' + id).show();
}

function ConfirmDelete(id){

	$.ajax({
		url: 'Resources/PHP/Delete.php',
		dataType: 'text',
		data: { id: id},
		success: function(response) {
			if (response == "deleted") {
				$('#confirmDelete-' + id).hide();
				$('#cancelDelete-' + id).hide();
				$('#delete-' + id).show();
				alert("The uploaded file has been deleted.");
				SearchRaw();
			} else {
				alert("Could not delete the uploaded file. Error: " + response + ".");
			}
		},
		error: function (request, status, error){
			alert("Could not delete the uploaded file. Error: " + error + ".");
		}
	});

}
*/

function Search(type){
	$("#loading").show();
	if (type == "raw"){
		GetUnfinishedList("raw");
		GetFinishedList("raw");

	} else if (type == "product"){
		GetUnfinishedList("product");
		GetFinishedList("product");

	}
}

function GetUnfinishedList(type){
	var project = "%";
	if (type == "raw"){
		project = $("#raw-data-project").val();
	} else if (type == "product"){
		project = $("#data-product-project").val();
	}

	/*
	var platform = "%";
	if (type == "raw"){
		platform = $("#raw-data-platform").val();
	} else if (type == "product"){
		platform = $("#data-product-platform").val();
	}

	var sensor = "%";
	if (type == "raw"){
		sensor = $("#raw-data-sensor").val();
	} else if (type == "product"){
		sensor = $("#data-product-sensor").val();
	}

	var productType = $("#data-product-type").val();
	*/
	$.ajax({
		url: "Resources/PHP/CheckUpload.php",
		dataType: "text",
		data: { project: project, type: type},
		/*
		data: {
			type: type,
			project: project,
			platform: platform,
			sensor: sensor,
			productType: productType
		},
		*/
		success: function(response) {
			var data = JSON.parse(response);
			var items = "";
			$("#unfinished-" + type + "-list-wrapper").html("");
			if (data.length > 0)
			{
				var productTypeHeader = "";
				var productType = "";

				if (type == "product"){
					productTypeHeader = "<th>Type</th>";
				}
				var table =	"<table id='unfinished-" + type + "-list-table'>" +
					"<thead>" +
					"<tr class='bg-dark text-white'>" +
					"<th>&nbsp;</th>" +
					"<th>File Name</th>" +
					"<th>Uploader</th>" +
					"<th>Project</th>" +
					"<th>File Size</th>" +
					productTypeHeader +
					"<th>Status</th>" +
					"<th>Upload Progress</th>" +
					"<th>Platform</th>" +
					"<th>Sensor</th>" +
					"<th>Date</th>" +
					"<th>Flight</th>" +
					"</tr>" +
					"</thead>" +
					"<tbody id='unfinished-" + type + "-list'>" +
					"</tbody>" +
					"</table>";

				$("#unfinished-" + type + "-list-wrapper").html(table);
				$.each(data,function(index,item)
				{
					if (type == "product"){
						productType = 	"<td style='overflow:hidden'>" +
							"<span>" + item.TypeName + "</span>" +
							"</td>";
					}

					items+= "<tr>" +
						"<td style='overflow:hidden'>" +
						"<input id='delete-" + type + "-" + item.ID +"' type='image' class='image-button delete-button' src='Resources/Images/delete.png' alt='Delete' onclick='DeleteUpload(" + item.ID + ",\"" + type + "\"); return false;' title='Delete'>" +
						"<input id='confirmDelete-" + type + "-" + item.ID +"' type='image' class='image-button confirm-delete-button' src='Resources/Images/confirm.png' alt='Confirm' style='display:none' onclick='ConfirmDeleteUpload(" + item.ID + ",\"" + type +"\"); return false;' title='Confirm'>" +
						"<input id='cancelDelete-" + type + "-" + item.ID +"' type='image' class='image-button cancel-edit-button' src='Resources/Images/cancel.png' alt='Cancel' style='display:none' onclick='CancelDeleteUpload(" + item.ID +",\"" + type + "\"); return false;' title='Cancel'>" +
						"</td>" +
						"<td style='overflow:hidden'>" +
						"<span>" + item.FileName + "</span>" +
						"</td>" +
						"<td style='overflow:hidden'>" +
						"<span>" + item.Uploader + "</span>" +
						"</td>" +
						"<td style='overflow:hidden'>" +
						"<span>" + item.ProjectName + "</span>" +
						"</td>" +
						"<td style='overflow:hidden'>" +
						"<span>" + item.Size + "</span>" +
						"</td>" +
						productType +
						"<td style='overflow:hidden'>" +
						"<span>" + item.Status + "</span>" +
						"</td>" +
						"<td style='overflow:hidden'>" +
						"<span>" + item.Progress + "%</span>" +
						"</td>" +
						"<td style='overflow:hidden'>" +
						"<span>" + item.PlatformName + "</span>" +
						"</td>" +
						"<td style='overflow:hidden'>" +
						"<span>" + item.SensorName + "</span>" +
						"</td>" +

						"<td style='overflow:hidden'>" +
						"<span>" + item.Date.replace(/\-/g, '/') + "</span>" +
						"</td>" +
						"<td style='overflow:hidden'>" +
						"<span>" + item.FlightName + "</span>" +
						"</td>" +
						"</tr>";



					items +=	"</tr>";
				});

				$("#unfinished-" + type + "-list").html(items);


				var rowHeight = 61;
				var padding = 10;
				var actualHeight = (data.length + 1) * rowHeight + padding;
				var maxHeight = 300;
				var height = actualHeight < maxHeight ? actualHeight : maxHeight;
				var cols = [
					{ width: 95, align: 'center' },
					{ width: 300, align: 'center' },
					{ width: 100, align: 'center' },
					{ width: 250, align: 'center' },
					{ width: 100, align: 'center' },
					{ width: 100, align: 'center' },
					{ width: 150, align: 'center' },
					{ width: 200, align: 'center' },
					{ width: 150,  align: 'center' },
					{ width: 150,  align: 'center' },
					{ width: 200,  align: 'center' },
				];

				if (type == "product"){
					cols = [
						{ width: 95, align: 'center' },
						{ width: 300, align: 'center' },
						{ width: 100, align: 'center' },
						{ width: 250, align: 'center' },
						{ width: 100, align: 'center' },
						{ width: 150, align: 'center' },
						{ width: 100, align: 'center' },
						{ width: 150, align: 'center' },
						{ width: 200, align: 'center' },
						{ width: 150,  align: 'center' },
						{ width: 150,  align: 'center' },
						{ width: 200,  align: 'center' },
					];
				}


				$("#unfinished-" + type + "-list-table").fxdHdrCol({
					//fixedCols:  2,
					width:     900,
					height:    height,

					colModal: cols,
					sort: false
				});

			}
		},
		error: function(xhr){
			alert('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
			$("#loading").hide();
		}
	});
}

function GetFinishedList(type){

	var project = "%";
	if (type == "raw"){
		project = $("#raw-data-project").val();
	} else if (type == "product"){
		project = $("#data-product-project").val();
	}

	var platform = "%";
	if (type == "raw"){
		platform = $("#raw-data-platform").val();
	} else if (type == "product"){
		platform = $("#data-product-platform").val();
	}

	var sensor = "%";
	if (type == "raw"){
		sensor = $("#raw-data-sensor").val();
	} else if (type == "product"){
		sensor = $("#data-product-sensor").val();
	}

	var productType = $("#data-product-type").val();


	$.ajax({
		url: "Resources/PHP/GetFinishedList.php",
		dataType: "text",
		data: {
			type: type,
			project: project,
			platform: platform,
			sensor: sensor,
			productType: productType
		},
		success: function(response) {
			var data = JSON.parse(response);
			var items = "";
			$("#finished-" + type + "-list-wrapper").html("");
			if (data.length > 0)
			{
				var productTypeHeader = "";
				var productType = "";
				var tmsButton = "";

				if (type == "product"){
					productTypeHeader = "<th>Type</th>";
				}

				var table =	"<table id='finished-" + type + "-list-table'>" +
					"<thead>" +
					"<tr class='bg-dark text-white'>" +
					"<th>&nbsp;</th>" +
					"<th>File Name</th>" +
					"<th>File Size</th>" +
					productTypeHeader +
					"<th>Project</th>" +
					"<th>Platform</th>" +
					"<th>Sensor</th>" +
					"<th>Date</th>" +
					"<th>Flight</th>" +
					"<th>Uploader</th>" +
					"</tr>" +
					"</thead>" +
					"<tbody id='finished-" + type + "-list'>" +
					"</tbody>" +
					"</table>";

				$("#finished-"+ type + "-list-wrapper").html(table);
				$.each(data,function(index,item)
				{
					if (type == "product"){
						productType = 	"<td style='overflow:hidden'>" +
							"<span>" + item.TypeName + "</span>" +
							"</td>";

						tmsButton = "<input style='padding: 7px !important; background: #fbbc05 !important;' id='tms-" + item.ID +"' type='image' class='image-button tms-button' " +
							"src='Resources/Images/tms.png' alt='TMS' onclick='ViewTMS(\"" + item.TMSPath + "\"); return false;' title='TMS'>";

					}

					items+= "<tr>" +
						"<td style='overflow:hidden'>" +
						"<input id='delete-" + type + "-" + item.ID +"' type='image' class='image-button delete-button' src='Resources/Images/delete.png' alt='Delete' onclick='DeleteUpload(" + item.ID + ",\"" + type + "\"); return false;' title='Delete'>" +
						"<input id='confirmDelete-" + type + "-" + item.ID +"' type='image' class='image-button confirm-delete-button' src='Resources/Images/confirm.png' alt='Confirm' style='display:none' onclick='ConfirmDeleteUpload(" + item.ID + ",\"" + type +"\"); return false;' title='Confirm'>" +
						"<input id='cancelDelete-" + type + "-" + item.ID +"' type='image' class='image-button cancel-edit-button' src='Resources/Images/cancel.png' alt='Cancel' style='display:none' onclick='CancelDeleteUpload(" + item.ID +",\"" + type + "\"); return false;' title='Cancel'>" +
						tmsButton +
						//"<input id='download-" + type + "-" + item.ID +"' type='image' class='image-button download-button' src='Resources/Images/download.png' alt='Download' onclick='Download(\"" + item.DownloadPath + "\"); return false;' title='Download'>" +
						// "<input id='download-" + type + "-" + item.ID +"' type='image' class='image-button download-button' src='Resources/Images/download.png' alt='Download' onclick='ViewDownload(\"" + item.DownloadPath + "\",\"" + item.UploadFolder + "\"); return false;' title='Download'>" +
						// ADDED
						"<input style='padding: 7px !important;' id='download-" + type + "-" + item.ID +"' type='image' class='image-button download-button' src='Resources/Images/download.png' alt='Download' onclick='ViewDownload(\"" + item.DownloadPath + "\",\"" + item.UploadFolder + "\"); return false;' title='Download'>" +

						"</td>" +
						"<td style='overflow:hidden'>" +
						"<span id='" + item.ID +"-file-name'>" + item.FileName + "</span>" +
						"</td>" +
						"<td style='overflow:hidden'>" +
						"<span>" + item.Size + "</span>" +
						"</td>" +
						productType +
						"<td style='overflow:hidden'>" +
						"<span>" + item.ProjectName + "</span>" +
						"</td>" +
						"<td style='overflow:hidden'>" +
						"<span>" + item.PlatformName + "</span>" +
						"</td>" +
						"<td style='overflow:hidden'>" +
						"<span>" + item.SensorName + "</span>" +
						"</td>" +

						"<td style='overflow:hidden'>" +
						"<span>" + item.Date.replace(/\-/g, '/') + "</span>" +
						"</td>" +
						"<td style='overflow:hidden'>" +
						"<span>" + item.FlightName + "</span>" +
						"</td>" +
						"<td style='overflow:hidden'>" +
						"<span>" + item.Uploader + "</span>" +
						"</td>" +
						"</tr>";



					items +=	"</tr>";
				});

				$("#finished-" + type + "-list").html(items);



				var rowHeight = 61;
				var padding = 10;
				var actualHeight = (data.length + 1) * rowHeight + padding;
				var maxHeight = 300;
				var height = actualHeight < maxHeight ? actualHeight : maxHeight;

				var cols = [
					{ width: 150, align: 'center' },
					{ width: 350, align: 'center' },
					{ width: 100, align: 'center' },
					{ width: 300, align: 'center' },
					{ width: 200, align: 'center' },
					{ width: 150,  align: 'center' },
					{ width: 150,  align: 'center' },
					{ width: 200,  align: 'center' },
					{ width: 150,  align: 'center' },
				];

				if (type == "product"){
					cols = [
						{ width: 180, align: 'center' },
						{ width: 350, align: 'center' },
						{ width: 100, align: 'center' },
						{ width: 150,  align: 'center' },
						{ width: 300, align: 'center' },
						{ width: 200, align: 'center' },
						{ width: 150,  align: 'center' },
						{ width: 150,  align: 'center' },
						{ width: 200,  align: 'center' },
						{ width: 150,  align: 'center' },
					];
				}

				$("#finished-" + type + "-list-table").fxdHdrCol({
					//fixedCols:  2,
					width:     900,
					height:    height,
					/*
					colModal: [
						{ width: 150, align: 'center' },
						{ width: 350, align: 'center' },
						{ width: 100, align: 'center' },
						{ width: 300, align: 'center' },
						{ width: 200, align: 'center' },
						{ width: 150,  align: 'center' },
						{ width: 150,  align: 'center' },
						{ width: 200,  align: 'center' },
					],
					*/
					colModal: cols,
					sort: false
				});
				//	$("#loading").hide();
			} else {
				var notifyText = "<p style='text-align:center'>No uploaded item found.</p>";
				$("#finished-"+ type + "-list-wrapper").html(notifyText);
			}
			$("#loading").hide();
		},
		error: function(xhr){
			alert('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
			$("#loading").hide();
		}
	});
}

function DeleteUpload(id, type){
	$("#confirmDelete-" + type + "-" + id).show();
	$("#cancelDelete-" + type + "-" + id).show();
	$("#delete-" + type + "-" + id).hide();
}

function CancelDeleteUpload(id, type){
	$("#confirmDelete-" + type + "-" + id).hide();
	$("#cancelDelete-" + type + "-" + id).hide();
	$("#delete-" + type + "-" + id).show();
}

function ConfirmDeleteUpload(id, type){
	$("#loading").show();
	$.ajax({
		url: 'Resources/PHP/Delete.php',
		dataType: 'text',
		data: { id: id, type: type},
		success: function(response) {
			//if (response == "deleted") {
			if (response == "File has been deleted.") {
				$("#confirmDelete-" + type + "-" + id).hide();
				$("#cancelDelete-" + type + "-" + id).hide();
				$("#delete-" + type + "-" + id).show();
				alert("The uploaded file has been deleted.");
				Search(type);
			} else {
				alert("Could not delete the uploaded file. Error: " + response + ".");
			}

			$("#loading").hide();
		},
		error: function (request, status, error){
			$("#loading").hide();
			alert("Could not delete the uploaded file. Error: " + error + ".");
		}
	});

}

function ViewTMS(link){
	$("#tms-path").val(link);
	$("#dialog-tms").dialog({
		resizable: false,
		height: "auto",
		width: 400,
		modal: true,
	});
}

function Preview(){
	var link = $("#tms-path").val().replace("{z}/{x}/{y}.png","leaflet.html");
	var win = window.open(link, '_blank');
	if (win) {
		//Browser has allowed it to be opened
		win.focus();
	} else {
		//Browser has blocked it
		alert('Please allow popups for this website');
	}
}

function ViewDownload(downloadLink, localPath){
	$("#download-link").val(downloadLink);
	$("#local-path").val(localPath);

	$("#dialog-download").dialog({
		resizable: false,
		height: "auto",
		width: 400,
		modal: true,
	});
}

function CopyToClipBoard(type){
	var textareaName = "";
	var notificationText = "";

	switch(type) {
		case "tms":{
			textareaName = "tms-path";
			notificationText = "The TMS path has been copied to clipboard";
		} break;

		case "link":{
			textareaName = "download-link";
			notificationText = "The download link has been copied to clipboard";
		} break;

		case "path":{
			textareaName = "local-path";
			notificationText = "The local path has been copied to clipboard";
		} break;
	}

	var copyText = document.getElementById(textareaName);
	copyText.select();
	document.execCommand("copy");
	alert(notificationText);
}

function Download(){
	var link = $("#download-link").val();
	var win = window.open(link, '_blank');
	if (win) {
		//Browser has allowed it to be opened
		win.focus();
	} else {
		//Browser has blocked it
		alert('Please allow popups for this website');
	}
}
