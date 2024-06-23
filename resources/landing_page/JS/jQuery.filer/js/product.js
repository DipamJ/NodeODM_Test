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
	
	var d = new Date();
	var month = d.getMonth()+1;
	var day = d.getDate();
	var date= 	(month<10 ? '0' : '') + month + '/' +
					(day<10 ? '0' : '') + day + '/' +
					d.getFullYear();
	$("#date").val(date);
	$("#date").datepicker();
	GetSiteList();
});	

//Get and display product information (used for "edit-product.html")
function GetProductInfo(id){
	$.ajax({
		url: "Resources/PHP/Product.php",
		dataType: 'text',
		type: "GET",
		data: { 
			action: "get",
			id: id
		},  
		success: function(response) {
			var data = JSON.parse(response);
			
			$("#site").val(data.site);
			$("#title").val(data.title);
			$("#date").val(data.date);
			$("#time").val(data.time);
			$("#survey-type").val(data.survey_type);
			$("#product-type").val(data.product_type);
			$("#description").val(data.description);
			
			//Check if product data has already been uploaded (green if yes, red if no)
			if (data.data_download_path == ""){
				$("#uploaded-product fieldset").css("border","2px groove red");
			} else {
				$("#uploaded-product fieldset").css("border","2px groove limegreen");
			}
			
			//Check if metadata has already been uploaded (green if yes, red if no)
			if (data.meta_download_path == ""){
				$("#uploaded-metadata fieldset").css("border","2px groove red");
			} else {
				$("#uploaded-metadata fieldset").css("border","2px groove limegreen");
			}
		}
	});
}

//Get list of site and populate the drop down list
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
				
				//If the current page is "edit-product.html", get and display the product information
				if ($(document).find("title").text() == "Edit Product"){
				
					GetProductInfo(id);
				}
			}
		}
	});
}

//Init filer for upload
function InitFiler(type){
	
	$("#" + type  + "-content").html('<input type="file" name="files[]" id="' + type + '-input" multiple="multiple">');
		
	$("#" + type + "-input").filer({
		limit: 1,
		maxSize: null,
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
			type: 'POST',
			enctype: 'multipart/form-data',
			synchron: true,
			beforeSend: function(){ 
				$('#processing').show();
			},
			success: function(data, itemEl, listEl, boxEl, newInputEl, inputEl, id){
				var result = JSON.parse(data);
				
				if (result.Result == "Fail"){
					alert("Failed to upload.");
					$('#processing').hide();
				} else {
					
					$("#" + type + "-path").val(result.FilePath);
				
					if (type == "metadata"){
						$("#title").val(result.Metadata.Title);
						$("#description").val(result.Metadata.Abstract);
						var date  = result.Metadata.StartDate;
						$("#date").val(date.substring(4, 6) + "/" + date.substring(6)  + "/" +  date.substring(0, 4));
				
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
				
			},
			statusCode: null,
			onProgress: function(data){
				
			},
			onComplete: function(response){
				
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

//Add/update product
function ManageProduct(type){
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
	var id = GetUrlParameter("id");
	var title = $("#title").val();
	
	$('#processing').show();
	
	$.ajax({
		url: "Resources/PHP/ManageProduct.php",
		dataType: 'text',
		type: "POST",
		data: { 
			site: site,
			title: title,
			siteName: siteName,
			date: date,
			time: time,
			surveyType: surveyType,
			productType: productType,
			productTypeName: productTypeName,
			description: description,
			productPath: productPath,
			metadataPath: metaPath,
			type: type,
			id: id
		},  
		success: function(response) {
			if (response.indexOf("Error") >= 0){
				alert("Failed to " + type + "product.");
			} else {
				if (type == "add"){
					alert("Product added successfully.");
				} else {
					alert("Product updated successfully.");
					GetProductInfo(id);
				}
				
				InitFiler("metadata");
				InitFiler("product");
			}
			
			$('#processing').hide();
		},
		error: function(xhr){
			alert("Failed to add product. Error:" + xhr);
			$('#processing').hide();
		}
	});
}