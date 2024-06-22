var processing = false;
var uploading = false;

function GetUrlParameter(sParam) {
	var sPageURL = window.location.search.substring(1);
	var sURLVariables = sPageURL.split("&");
	for (var i = 0; i < sURLVariables.length; i++)
	{
		var sParameterName = sURLVariables[i].split("=");
		if (sParameterName[0] == sParam)
		{
			return sParameterName[1];
		}
	}
}

$(document).ready(function(){
	
	GetSiteList();
	
	$("#site").on('change', function() {
		InitFiler("metadata");
		InitFiler("product");
	});
});

function GetSiteList(){
	
	$.ajax({
		url: "Resources/PHP/Site.php",
		dataType: 'text',
		data: { action: 'list'},  
		success: function(response) {
			var items="";
			var data = JSON.parse(response);
			if (data.length > 0)
			{
				$.each(data,function(index,item) 
				{
					items+="<option value='" + item.id + "'>" + item.name + "</option>";
				});
				
				$("#site").html(items); 
				InitFiler("metadata");
				InitFiler("product");
				
				var id = GetUrlParameter("id");
				if(id){
					$("#site").val(id);
				}
			}
		}
	});
}

//Init filer for upload
function InitFiler(type){
	
	$("#" + type  + "-content").html('<input type="file" name="files[]" id="' + type + '-input" multiple="multiple">');
	
	
	/*
	var site = $("#site").val();
	var name = $("#name").val();
	var date = $("#date").val();
	var description = $("#description").val();
	*/
		
	$("#" + type + "-input").filer({
		limit: 1,
		maxSize: null,
		//extensions: ['las'],
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
			url: "Resources/JS/jQuery.filer/php/Upload.php",
			/*
			data: 	{
						site: site,
						name: name,
						date: date,
						description: description
					},
			*/
			type: 'POST',
			enctype: 'multipart/form-data',
			synchron: true,
			beforeSend: function(){ 
				$('#processing').show();
	
				/*
				uploading = true;
				$("#info *").prop("disabled", true);
				*/
			},
			success: function(data, itemEl, listEl, boxEl, newInputEl, inputEl, id){
				var result = JSON.parse(data);
				
				//if (result.indexOf("Error") >= 0){
				if (result.Result == "Fail"){
					alert("Failed to upload.");
					$('#processing').hide();
				} else {
					
					//$("#" + type + "-path").val(result);
					$("#" + type + "-path").val(result.FilePath);
				
					if (type == "metadata"){
						console.log(result.Metadata);
						$("#site option").each(function() {
							if($(this).text() == result.Metadata.site) {
								$(this).attr('selected', 'selected');            
							}                        
						});
						$("#date").val(result.Metadata.date);
						$("#time").val(result.Metadata.time);
						
						$("#survey-type option").each(function() {
							if($(this).text() == result.Metadata.surveyType) {
								$(this).attr('selected', 'selected');            
							}                        
						});
						
						$("#product-type option").each(function() {
							if($(this).text() == result.Metadata.productType) {
								$(this).attr('selected', 'selected');            
							}                        
						});
						$("#description").val(result.Metadata.description);
						
					}
				
					var parent = itemEl.find(".jFiler-jProgressBar").parent(),
						new_file_name = JSON.parse(data),
						filerKit = inputEl.prop("jFiler");

					filerKit.files_list[id].name = new_file_name;

					itemEl.find(".jFiler-jProgressBar").fadeOut("slow", function(){
						$("<div class=\"jFiler-item-others text-success\"><i class=\"icon-jfi-check-circle\"></i> Success</div>").hide().appendTo(parent).fadeIn("slow");
					});
					
					$('#processing').hide();
					alert("Uploaded successfully.");
				}
			},
			error: function(el){
				console.log("error");
				
				var parent = el.find(".jFiler-jProgressBar").parent();
				el.find(".jFiler-jProgressBar").fadeOut("slow", function(){
					$("<div class=\"jFiler-item-others text-error\"><i class=\"icon-jfi-minus-circle\"></i> Error</div>").hide().appendTo(parent).fadeIn("slow");
				});
				
				alert("Failed to upload.");
				$('#processing').hide();
				/*
				uploading = false;
				$('#warning').hide();
				$("#info *").prop("disabled", false);
				*/
				
			},
			statusCode: null,
			onProgress: function(data){
				/*
				if (parseInt(data) == 100){
					$('#processing').show();
					processing = true;
					uploading = false;
					
					$('#warning').hide();
				}
				*/
			},
			onComplete: function(response){
				//$('#processing').hide();
				//alert("Uploaded successfully.");
				/*
				console.log("complete");
				console.log(response);
				var result = JSON.parse(response);
				
				if (result.indexOf("Error") >= 0){
					alert("Failed to upload.");
					$('#processing').hide();
					
				} else {
				
					//var path = JSON.parse(response);
					$('#processing').hide();
					alert("Uploaded successfully.");
					//$("#" + type + "-path").val(path);
					$("#" + type + "-path").val(result);
				}
				*/
				/*
				processing = false;
				$('#warning').hide();
				$("#info *").prop("disabled", false);
				*/
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

		    $.post('Resources/JS/jQuery.filer/php/ajax_remove_file.php', {file: file_name});
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
			feedback2: "files were chosen",
			drop: "Drop file here to Upload",
			removeConfirmation: "Are you sure you want to remove this file?",
			errors: {
				filesLimit: "Only {{fi-limit}} files are allowed to be uploaded.",
				filesType: "Only '.las' files are allowed to be uploaded.",
				filesSize: "{{fi-name}} is too large! Please upload file up to {{fi-maxSize}} MB.",
				filesSizeAll: "Files you've choosed are too large! Please upload files up to {{fi-maxSize}} MB."
			}
		}
	});
}


function AddProduct(){
	var site = $("#site").val();
	var siteName = $("#site option:selected").text();
	var date = $("#date").val();
	var time = $("#time").val();
	var surveyType = $("#survey-type").val();
	var productType = $("#product-type").val();
	var productTypeName = $("#product-type option:selected").text();
	var description = $("#description").val();
	var productPath = $("#product-path").val();
	var metaPath = $("#metadata-path").val();
	
	$('#processing').show();
	
	$.ajax({
		url: "Resources/PHP/AddProduct.php",
		dataType: 'text',
		type: "POST",
		data: { 
			site: site,
			siteName: siteName,
			date: date,
			time: time,
			surveyType: surveyType,
			productType: productType,
			productTypeName: productTypeName,
			description: description,
			productPath: productPath,
			metadataPath: metaPath
		},  
		success: function(response) {
			if (response.indexOf("Error") >= 0){
				alert("Failed to add product.");
			} else {
				alert("Product added successfully.");
				InitFiler("metadata");
				InitFiler("product");
				$("#description").val("");
			}
			
			$('#processing').hide();
		}
	});
}