var flights = new Array();
var processing = false;
var uploading = false;

$(document).ready(function(){
	
	GetList("project");
	GetList("product-type");
	
	$("#tms-path").on('change', function() {
		InitFiler();
	});
	
	$(".add-button").mouseup(function(){
		$(this).blur();
	});
				
	window.onbeforeunload = function() {
		if (uploading){
			$('#warning').show();
			$('#warning-text').text('Please wait for files to finish uploading.');
			return 'Please wait.';
		}
		else if (processing){
			$('#warning').show();
			$('#warning-text').text('Please wait for files to finish processing.');
			return 'Please wait.';
		}
	}
});


function GetList(type){
	
	var url = 'Resources/PHP/list.php?type=' + type;
	
	switch(type) {
		case 'project':
			{
				
			}break;
		case 'platform':
			{
				url += '&project=' + $('#project').val();
			}break;
		case 'sensor':
			{
				url += '&project=' + $('#project').val() + '&platform=' + $('#platform').val();
			}break;
		case 'date':
			{
				url += '&project=' + $('#project').val() + '&platform=' + $('#platform').val() + '&sensor=' + $('#sensor').val();
			}break;
		case 'flight':
			{
				url += '&project=' + $('#project').val() + '&platform=' + $('#platform').val() + '&sensor=' + $('#sensor').val() + '&date=' + $('#date').val();
			}break;
		case 'product-type':
			{
				
			}break;
	}

	$.ajax({
		url: url,
		dataType: 'text',
		success: function(response) {
			var items="";
			var data = JSON.parse(response);
			if (data.length > 0)
			{
				$.each(data,function(index,item) 
				{
					items+="<option value='" + item.ID + "'>" + item.Name + "</option>";
				});
				
				$("#" + type).html(items); 
	
				switch(type) {
					case 'project':
						{
							GetList("platform");
							$("#" + type).on('change', function() {
								GetList("platform");
							});
						}break;
					case 'platform':
						{
							GetList("sensor");
							$("#" + type).on('change', function() {
								GetList("sensor");
							});
						}break;
					case 'sensor':
						{
							GetList("date");
							$("#" + type).on('change', function() {
								GetList("date");
							});
						}break;
					case 'date':
						{
							GetList("flight");
							$("#" + type).on('change', function() {
								GetList("flight");
							});
						}break;	
					case 'flight':
						{
							flights = data;
							InitFiler();
							SetFlightInfo(data[0].ID);
							$("#" + type).on('change', function() {
								InitFiler();
								SetFlightInfo(data[0].ID);
							});
						}break;
					
					case 'product-type':
						{
							InitFiler();
							$("#" + type).on('change', function() {
								InitFiler();
							});
						}break;
						
				}
			}
		}
	});
}

function SetFlightInfo(id){
	var items = $.grep(flights, function(e){ return e.ID == id; });
	if (items.length > 0 ) {
		$("#altitude").text(items[0].Altitude);
		$("#forward").text(items[0].Forward);
		$("#side").text(items[0].Side);
	}
}

function CreateThumbnails(){
	var project = $("#project option:selected").text();
	var platform = $("#platform option:selected").text();
	var sensor = $("#sensor option:selected").text();
	var date = $("#date").val();
	var flight = $("#flight option:selected").text();
	var flightID = $("#flight").val();
	var type = "Product";
	
	$.ajax({
		url: 'Resources/PHP/CreateThumbnail.php',
		dataType: 'text',
		data: { project: project, platform: platform, sensor: sensor, date: date, flight : flight, flightID: flightID, type: type},     
		success: function(response) {
		}
	});
}

function InitFiler(){
	$("#content").html('<input type="file" name="files[]" id="filer_input" multiple="multiple">');
	var projectID = $("#project").val();
	var projectName = $("#project option:selected").text();
	var platformID = $("#platform").val();
	var platformName = $("#platform option:selected").text();
	var sensorID = $("#sensor").val();
	var sensorName = $("#sensor option:selected").text();
	var flightID = $("#flight").val();
	var flightName = $("#flight option:selected").text();
	var date = $("#date").val();
	var productType = $("#product-type").val();
	var tmsPath = $( "#tms-path").val();
	
	
	$("#filer_input").filer({
		limit: null,
		maxSize: null,
		//extensions: ['jpg', 'png', 'tif','gif','zip', 'dat', 'csv', 'cfg'],
		extensions: null,
		changeInput: '<div class="jFiler-input-dragDrop"><div class="jFiler-input-inner"><div class="jFiler-input-icon"><i class="icon-jfi-cloud-up-o"></i></div><div class="jFiler-input-text"><h3>Drag&Drop files here</h3> <span style="display:inline-block; margin: 15px 0">or</span></div><a class="jFiler-input-choose-btn blue">Browse Files</a></div></div>',
		showThumbs: true,
		theme: "dragdropbox",
		templates: {
			box: '<ul class="jFiler-items-list jFiler-items-grid"></ul>',
			item: '<li class="jFiler-item">\
						<div class="jFiler-item-container">\
							<div class="jFiler-item-inner">\
								<div class="jFiler-item-thumb">\
									<div class="jFiler-item-status"></div>\
									<div class="jFiler-item-thumb-overlay">\
										<div class="jFiler-item-info">\
											<div style="display:table-cell;vertical-align: middle;">\
												<span class="jFiler-item-title"><b title="{{fi-name}}">{{fi-name}}</b></span>\
												<span class="jFiler-item-others">{{fi-size2}}</span>\
											</div>\
										</div>\
									</div>\
									{{fi-image}}\
								</div>\
								<div class="jFiler-item-assets jFiler-row">\
									<ul class="list-inline pull-left">\
										<li>{{fi-progressBar}}</li>\
									</ul>\
									<ul class="list-inline pull-right">\
										<li><a class="icon-jfi-trash jFiler-item-trash-action"></a></li>\
									</ul>\
								</div>\
							</div>\
						</div>\
					</li>',
			itemAppend: '<li class="jFiler-item">\
							<div class="jFiler-item-container">\
								<div class="jFiler-item-inner">\
									<div class="jFiler-item-thumb">\
										<div class="jFiler-item-status"></div>\
										<div class="jFiler-item-thumb-overlay">\
											<div class="jFiler-item-info">\
												<div style="display:table-cell;vertical-align: middle;">\
													<span class="jFiler-item-title"><b title="{{fi-name}}">{{fi-name}}</b></span>\
													<span class="jFiler-item-others">{{fi-size2}}</span>\
												</div>\
											</div>\
										</div>\
										{{fi-image}}\
									</div>\
									<div class="jFiler-item-assets jFiler-row">\
										<ul class="list-inline pull-left">\
											<li><span class="jFiler-item-others">{{fi-icon}}</span></li>\
										</ul>\
										<ul class="list-inline pull-right">\
											<li><a class="icon-jfi-trash jFiler-item-trash-action"></a></li>\
										</ul>\
									</div>\
								</div>\
							</div>\
						</li>',
			progressBar: '<div class="bar"></div>',
			itemAppendToEnd: false,
			canvasImage: true,
			removeConfirmation: true,
			_selectors: {
				list: '.jFiler-items-list',
				item: '.jFiler-item',
				progressBar: '.bar',
				remove: '.jFiler-item-trash-action'
			}
		},
		dragDrop: {
			dragEnter: null,
			dragLeave: null,
			drop: null,
			dragContainer: null,
		},
		uploadFile: {
			url: "Resources/jQuery.filer/php/UploadProduct.php",
			data: 	{
						projectID: projectID, 
						projectName: projectName, 
						platformID: platformID, 
						platformName : platformName,
						sensorID : sensorID,
						sensorName: sensorName, 
						flightID : flightID,
						flightName: flightName, 
						date:date, 
						productType: productType,
						tmsPath: tmsPath
					},
			type: 'POST',
			enctype: 'multipart/form-data',
			synchron: true,
			beforeSend: function(){ 
				uploading = true;
				$("#info *").prop("disabled", true);
			},
			success: function(data, itemEl, listEl, boxEl, newInputEl, inputEl, id){
				var parent = itemEl.find(".jFiler-jProgressBar").parent(),
					new_file_name = JSON.parse(data),
					filerKit = inputEl.prop("jFiler");

        		filerKit.files_list[id].name = new_file_name;

				itemEl.find(".jFiler-jProgressBar").fadeOut("slow", function(){
					$("<div class=\"jFiler-item-others text-success\"><i class=\"icon-jfi-check-circle\"></i> Success</div>").hide().appendTo(parent).fadeIn("slow");
				});
				
				$("#info *").prop("disabled", false);
			},
			error: function(el){
				var parent = el.find(".jFiler-jProgressBar").parent();
				el.find(".jFiler-jProgressBar").fadeOut("slow", function(){
					$("<div class=\"jFiler-item-others text-error\"><i class=\"icon-jfi-minus-circle\"></i> Error</div>").hide().appendTo(parent).fadeIn("slow");
				});
				
				uploading = false;
				$('#warning').hide();
				$("#info *").prop("disabled", false);
			},
			statusCode: null,
			onProgress: function(data){
				
				if (parseInt(data) == 100){
					$('#processing').show();
					processing = true;
					uploading = false;
					
					$('#warning').hide();
				}
				
			},
			onComplete: function(){
			
				$('#processing').hide();
				CreateThumbnails();
				
				processing = false;
				$('#warning').hide();
			}
		},
		files: null,
		addMore: false,
		allowDuplicates: true,
		clipBoardPaste: true,
		excludeName: null,
		beforeRender: null,
		afterRender: null,
		beforeShow: null,
		beforeSelect: null,
		onSelect: null,
		afterShow: null,
		onRemove: function(itemEl, file, id, listEl, boxEl, newInputEl, inputEl){
			var filerKit = inputEl.prop("jFiler"),
		        file_name = filerKit.files_list[id].name;

		    $.post('Resources/jQuery.filer/php/ajax_remove_file.php', {file: file_name});
		},
		onEmpty: null,
		options: null,
		dialogs: {
			alert: function(text) {
				return alert(text);
			},
			confirm: function (text, callback) {
				confirm(text) ? callback() : null;
			}
		},
		captions: {
			button: "Choose Files",
			feedback: "Choose files To Upload",
			feedback2: "Files were chosen",
			drop: "Drop file here to Upload",
			removeConfirmation: "Are you sure you want to remove this file?",
			errors: {
				filesLimit: "Only {{fi-limit}} files are allowed to be uploaded.",
				filesType: "Only Images, '.zip', '.dat', '.csv', '.cfg' are allowed to be uploaded.",
				filesSize: "{{fi-name}} is too large! Please upload file up to {{fi-maxSize}} MB.",
				filesSizeAll: "Files you've choosed are too large! Please upload files up to {{fi-maxSize}} MB."
			}
		}
	});
}
