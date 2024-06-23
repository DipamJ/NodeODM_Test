// var resumableInstances = new Array();
// var rowColor = "#fff";
// var flights = new Array();
// var processing = false;
// var uploading = false;
// $(document).ready(function(){
// 	$.noConflict();
//
// 	GetList("project");
// 	GetList("platform");
// 	GetList("sensor");
// 	setTimeout(function(){
// 		GetList("date"); //Populate the dropdown list for "date"
// 	}, 500);
//
// 	$(".add-button").mouseup(function(){
// 		$(this).blur();
// 	});
//
// 	$("#flight-date").datepicker();
//
// 	GetUnfinishedList();
// 	GetFinishedList();
//
// 	window.setInterval(function(){
// 		GetUnfinishedList();
// 	}, 10000);
//
//
// 	$("#email-to").on('change', function() {
// 		if (!CheckEmailAddress($("#email-to").val())){
// 			alert("Invalid email address.");
// 		}
// 	});
//
// 	$("#cc-address").on('change', function() {
// 		if (!CheckEmailAddress($("#cc-address").val())){
// 			alert("Invalid email address.");
// 		}
// 	});
//
// 	//GetEmailReceiver();
// });
//
// function CheckUploadCondition(){
// 	if ($("#flight").val() != "" && $("#flight").val() != null){
// 		$("#upload-button").prop("disabled",false);
// 	} else {
// 		$("#upload-button").prop("disabled",true);
// 	}
// }
//
//
// function SetFlight(project, platform, sensor, date, flight){
// 	$.ajax({
// 		url: "Resources/PHP/List.php",
// 		data:{
// 			type: "date",
// 			project: project,
// 			platform: platform,
// 			sensor: sensor,
// 		},
// 		dataType: 'text',
// 		success: function(response) {
// 			var items="";
// 			var data = JSON.parse(response);
// 			if (data.length > 0)
// 			{
// 				$.each(data,function(index,item)
// 				{
// 					items+= "<option value='" + item.ID + "'>" + item.Name + "</option>";
// 				});
//
// 				$("#date").html(items);
//
// 				$("#date option").filter(function() {
// 					return $(this).text() == date;
// 				}).prop('selected', true);
// 				GetList("flight");
// 				setTimeout(function(){
// 					$("#flight").val(flight);
// 				}, 500);
//
// 			}
// 		}
// 	});
//
// }
//
// function ShowAddFlight(){
// 	$("#flight-project").val($("#project option:selected").text());
// 	$("#flight-platform").val($("#platform option:selected").text());
// 	$("#flight-sensor").val($("#sensor option:selected").text());
// 	$("#add-flight").show();
// 	$("#select-flight").hide();
// }
//
// function ShowSelectFlight(){
// 	$("#add-flight").hide();
// 	$("#select-flight").show();
// 	CheckUploadCondition();
// }
//
// function AddFlight(){
// 	var name = $("#flight-name").val();
// 	var project = $("#project").val();
// 	var platform = $("#platform").val();
// 	var sensor = $("#sensor").val();
// 	var date = $("#flight-date").val();
// 	var altitude = $("#flight-altitude").val();
// 	var forward = $("#flight-forward").val();
// 	var side = $("#flight-side").val();
//
// 	$.ajax({
// 		url: "Resources/PHP/Flight.php",
// 		data:{
// 			action: "add",
// 			name: name,
// 			project: project,
// 			platform: platform,
// 			sensor: sensor,
// 			date: date,
// 			altitude: altitude,
// 			forward: forward,
// 			side: side
// 		},
// 		dataType: "text",
// 		success: function(response) {
// 			SetFlight(project, platform, sensor, date, response);
//
// 		}
// 	});
// 	ShowSelectFlight();
// }
//
//
// function GetList(type){
//
// 	var url = "Resources/PHP/List.php?type=" + type;
//
// 	switch(type) {
// 		case 'date':
// 		{
// 			url += '&project=' + $('#project').val() + '&platform=' + $('#platform').val() + '&sensor=' + $('#sensor').val();
// 		}break;
// 		case 'flight':
// 		{
// 			url += '&project=' + $('#project').val() + '&platform=' + $('#platform').val() + '&sensor=' + $('#sensor').val() + '&date=' + $('#date').val();
// 		}break;
// 		case 'product-type':
// 		{
//
// 		}break;
// 	}
//
// 	$.ajax({
// 		url: url,
// 		dataType: 'text',
// 		success: function(response) {
// 			$("#flight").html("");
// 			$("#" + type).html("");
// 			var items="";
// 			var data = JSON.parse(response);
// 			if (data.length > 0)
// 			{
// 				$.each(data,function(index,item)
// 				{
// 					items+="<option value='" + item.ID + "'>" + item.Name + "</option>";
// 				});
//
// 				$("#" + type).html(items);
//
// 				switch(type) {
// 					case 'project':
// 					{
// 						// $("#project").chosen({
// 						// 	inherit_select_classes: true
// 						// });
// 						$("#" + type).on('change', function() {
// 							GetList("date");
// 						});
// 					}break;
// 					case 'platform':
// 					{
// 						$("#" + type).on('change', function() {
// 							GetList("date");
// 						});
// 					}break;
// 					case 'sensor':
// 					{
// 						//GetList("date");
// 						$("#" + type).on('change', function() {
// 							GetList("date");
// 						});
// 					}break;
// 					case 'date':
// 					{
// 						GetList("flight");
// 						$("#" + type).on('change', function() {
// 							GetList("flight");
// 						});
// 					}break;
// 					case 'flight':
// 					{
// 						flights = data;
// 						SetFlightInfo(data[0].ID);
// 						$("#" + type).on('change', function() {
// 							SetFlightInfo(data[0].ID);
// 						});
// 					}break;
//
// 					case 'product-type':
// 					{
// 					}break;
//
// 				}
// 			}
//
// 			CheckUploadCondition();
// 		}
// 	});
// }
//
// function SetFlightInfo(id){
// 	var items = $.grep(flights, function(e){ return e.ID == id; });
// 	if (items.length > 0 ) {
// 		$("#altitude").text(items[0].Altitude);
// 		$("#forward").text(items[0].Forward);
// 		$("#side").text(items[0].Side);
// 	}
// }
//
// function GetProjectList(){
// 	$.ajax({
// 		url: "Resources/PHP/Project.php",
// 		dataType: 'text',
// 		data: { action: 'list'},
// 		success: function(response) {
// 			var project = "<option value='0' lat='0' lng='0'>None</option>";
// 			$('#project').append(project);
//
// 			var data = JSON.parse(response);
// 			if (data.length > 0)
// 			{
//
// 				$.each(data,function(index,item)
// 				{
//
// 					var project = "<option value='" + item.ID + "' lat='" + item.CenterLat + "' lng='" + item.CenterLng + "'>" + item.Name + "</option>";
// 					$('#project').append(project);
//
// 				});
//
// 			}
//
// 			$("#project").change(function(){
// 				var lat = this.options[this.selectedIndex].getAttribute("lat");
// 				var lng = this.options[this.selectedIndex].getAttribute("lng");
// 				if (lat != 0 && lng != 0){
// 					$("#lat").val(lat);
// 					$("#lng").val(lng);
// 					ChangeLocation();
// 				}
//
// 			});
// 		}
// 	});
// }
//
// function CreateResumableInstance(identifier){
//
//
// 	var count = resumableInstances.length;
// 	var resumableStr = "<a href='#' id='browsebutton-" + count + "'><img src='Resources/Images/upload.png'></a>";
//
// 	$("#resumable-list").append(resumableStr);
//
//
// 	var projectName = $("#project option:selected").text();
// 	var platformName = $("#platform option:selected").text();
// 	var sensorName = $("#sensor option:selected").text();
// 	var flightID = $("#flight").val();
// 	var flightName = $("#flight option:selected").text();
// 	var date = $("#date").val();
//
// 	var receiver = $("#email-to").val();
// 	var note = $("#note").val();
// 	var ccAddress = $("#cc-address").val();
//
// 	var targetStr = "Resources/PHP/Upload.php?project=" + projectName +
// 		"&platform=" + platformName +
// 		"&sensor=" + sensorName +
// 		"&date=" + date +
// 		"&flightID=" + flightID +
// 		//"&flightName=" + flightName;
// 		"&flightName=" + flightName +
// 		"&receiver=" + receiver +
// 		"&cc=" + ccAddress +
// 		"&note=" + note;
//
// 	var r = new Resumable({
// 		target: targetStr,
// 		chunkSize: 20*1024*1024
// 		//testChunks: false
// 	});
//
//
// 	r.assignBrowse(document.getElementById("browsebutton-" + count));
//
// 	var currentProgress = 0;
// 	r.on('fileAdded', function(data) {
// 		if ( identifier != "" && r.files[0].uniqueIdentifier != identifier){
// 			$( "#dialog-different-file" ).dialog({
// 				resizable: false,
// 				height: "auto",
// 				position: { my: "top+50", at: "top+50", of: window },
// 				width: 400,
// 				modal: true,
// 				buttons: {
// 					"OK": function() {
// 						r.cancel();
// 						resumableInstances.splice( $.inArray(r, resumableInstances), 1 );
// 						$("#resumable-" + count).remove();
// 						$("#browsebutton-" + count).remove();
// 						$( this ).dialog( "close" );
// 					}
// 				}
// 			});
//
//
// 		} else {
// 			$("#loading").show();
// 			$.ajax({
// 				url: "Resources/PHP/CheckFileStatus.php",
// 				dataType: 'text',
// 				data: { identifier: r.files[0].uniqueIdentifier},
// 				success: function(response) {
//
// 					var file = JSON.parse(response);
// 					//var name = $("#name").val();
//
//
// 					if (file == null || file["Status"] != "Finished"){
// 						if (file){
// 							currentProgress = file["Progress"]/100;
// 							UpdateStatus("Resume", file["Identifier"], currentProgress);
// 							GetUnfinishedList();
// 							//name = file["Name"];
// 						}
// 						var fileName = data.fileName.replace(/[^a-zA-Z0-9_.\s]/gi, '').replace(/[_\s]/g, '_').replace(/_+/gi,'_');
// 						r.upload();
//
// 						var rowStr = 	"<div id='resumable-" + count + "' style='height:30px; lineHeight: 30px'>" +
// 							"<div id='" + count + "-file-name' style='padding-top:5px; height: 30px ;width: 200px;float: left; border: 1px solid #CCCCCC;background:" + rowColor + ";'>" + fileName + "</div>" +
// 							"<div id='" + count + "-status' style='padding-top:5px; height: 30px ;width: 100px;float: left; border: 1px solid #CCCCCC;background:" + rowColor + ";'>Uploading</div>" +
// 							"<div id='" + count + "-progress'  style='padding-top:5px; height: 30px ;width: 350px;float: left; border: 1px solid #CCCCCC;background:" + rowColor + ";'>" +
// 							"<div class='progress-bar'>" +
// 							"<img id='progress-bar-" + count + "' class='progress-bar-image' src='Resources/Images/ProgressBar.jpg'>" +
// 							"<div id='progress-text-" + count + "' class='progress-text'></div>" +
// 							"</div>" +
// 							"</div>" +
// 							"<div id='" + count + "-control'  style='padding-top:5px; height: 30px ;width: 100px;float: left; border: 1px solid #CCCCCC;background:" + rowColor + ";'>" +
// 							"<img id='pause-resume-" + count + "'  style='cursor:pointer; margin: 0 5px' src='Resources/Images/pause.png' alt='Pause' title='Pause' height='24' width='24' "+
// 							"onclick='PauseResume(\""+ count +"\",\"" + r.files[0].uniqueIdentifier + "\"," + currentProgress +")'>" +
// 							"<img id='cancel-" + count + "'  style='cursor:pointer; margin: 0 5px' src='Resources/Images/remove.png' alt='Cancel' title='Cancel' height='24' width='24' "+
// 							"onclick='Cancel(\""+ count +"\",\"" + r.files[0].uniqueIdentifier +"\")'>" +
// 							"</div>" +
//
// 							"</div>";
//
// 						$("#upload-files").append(rowStr);
// 						var totalSize = Math.floor (data.size / 1024) ;
// 						$("#total-size-" + count).text(totalSize + "KB");
// 						$("#loading").hide();
// 					}else {
// 						alert("The same file was already uploaded.");
// 						$("#loading").hide();
// 					}
//
// 				}
// 			});
// 		}
//
// 	});
//
//
// 	r.on('fileProgress', function(data) {
// 		if(!r.files[0]){
// 			return;
// 		}
// 		var type = "Uploading";
// 		var progress = currentProgress;
// 		if (r.progress() >= currentProgress){
// 			progress = r.progress();
// 		} else {
// 			type = "Checking";
// 		}
//
// 		UpdateProgressBar(progress, data.size, count, type);
// 	});
//
// 	r.on('fileSuccess', function(file,message){
// 		SendEmail(file.uniqueIdentifier);
// 		$("#resumable-" + count).remove();
// 		GetFinishedList();
// 		GetUnfinishedList();
// 	});
//
// 	resumableInstances.push(r);
//
// 	$("#browsebutton-" + count)[0].click();
//
// }
//
// function UpdateProgressBar(progress, size, index, type){
// 	if (type == "Uploading"){
// 		var percent = Math.floor(progress*100);
// 		if (percent) {
//
// 			$("#progress-bar-" + index).css("width", percent * 3 + "px"); //Update the upload progress bar
// 			var totalSize = Math.floor (size / 1024) ;
// 			var uploadSize = Math.floor((percent * totalSize ) / 100);
//
// 			var progressText = uploadSize + "KB / " + totalSize + "KB (" + percent + "%)";
//
// 			$("#progress-text-" + index).text(progressText);
//
// 		}
// 	} else {
// 		$("#progress-bar-" + index).css("width","0"); //Update the upload progress bar
// 		$("#progress-text-" + index).text(type);
// 	}
// }
//
// function PauseResume(index, identifier, progress){
// 	var alt = $("#pause-resume-" + index).attr("alt");
// 	if (alt == "Pause"){
// 		$("#pause-resume-" + index).attr("alt", "Resume");
// 		$("#pause-resume-" + index).attr("title","Resume");
// 		$("#pause-resume-" + index).attr("src","Resources/Images/resume.png");
// 		$("#" + index + "-status").html("Paused");
// 		resumableInstances[index].pause();
// 	} else {
// 		$("#pause-resume-" + index).attr("alt", "Pause");
// 		$("#pause-resume-" + index).attr("title","Pause");
// 		$("#pause-resume-" + index).attr("src","Resources/Images/pause.png");
// 		$("#" + index + "-status").html("Uploading");
// 		resumableInstances[index].upload();
// 	}
//
// 	UpdateStatus(alt, identifier, progress);
//
// 	GetUnfinishedList();
// }
//
// function Cancel(index, identifier){
//
// 	$( "#dialog-confirm" ).dialog({
// 		resizable: false,
// 		height: "auto",
// 		position: { my: "top+50", at: "top+50", of: window },
// 		width: 400,
// 		modal: true,
// 		buttons: {
// 			"Yes": function() {
// 				$("#loading").show();
// 				$.ajax({
// 					url: "Resources/PHP/Cancel.php",
// 					dataType: 'text',
// 					data: { identifier: identifier},
// 					success: function(response) {
// 						if (response == "cancelled"){
// 							if(index != -1){
// 								resumableInstances[index].cancel();
// 								//resumableInstances.splice( $.inArray(resumableInstances[index], resumableInstances), 1 );
// 								$("#resumable-" + index).remove();
// 							}
// 							GetUnfinishedList();
// 							alert("The upload has been cancelled.");
// 							$("#dialog-confirm").dialog( "close" );
// 						} else {
// 							alert("Could not cancel the upload. Error: " + response + ". Please try again.");
// 						}
// 						$("#loading").hide();
// 					},
// 					error: function (request, status, error){
// 						alert("Could not cancel the upload. Error: " + error + ". Please try again.");
// 						$("#loading").hide();
// 					}
// 				});
// 			},
// 			"No": function() {
// 				$( this ).dialog( "close" );
// 			}
// 		}
// 	});
//
//
// }
//
// function UpdateStatus(type, identifier, progress){
// 	$.ajax({
// 		url: "Resources/PHP/PauseResume.php",
// 		dataType: 'text',
// 		data: { type: type, identifier: identifier, progress: progress},
// 		success: function(response) {
//
// 		}
// 	});
// }
//
// function Remove(index){
// 	resumableInstances[index].cancel();
// 	$("#resumable-" + index).remove();
// }
//
// function GetUnfinishedList(){
// 	$.ajax({
// 		url: "Resources/PHP/CheckUpload.php",
// 		dataType: 'text',
// 		success: function(response) {
// 			var data = JSON.parse(response);
// 			$("#unfinished-files").html("");
//
// 			if (data.length > 0){
//
// 				$.each(data,function(index,item){
//
// 					//This user is not uploading anything
// 					if (resumableInstances.length == 0){
// 						AppendUploadingFileList(item);
// 					} else { //This user is uploading at least one item, check and skip that item
// 						var valid = 1;
// 						$.each(resumableInstances,function(i,r){
// 							if (r.files[0]){
// 								if (r.files[0].uniqueIdentifier == item["Identifier"]){ // this is the item being uploaded by this user
// 									valid = 0;
// 								}
// 							}
// 						});
//
// 						if (item["Status"] == "Unzip"){
// 							valid = 1;
// 						}
//
// 						if (valid == 1){
// 							AppendUploadingFileList(item);
// 						}
// 					}
// 				});
//
// 			}
// 		}
// 	});
//
// }
//
//
// function AppendUploadingFileList(file){
//
// 	var status = file["Status"];
// 	var reuploadStr = 	"<img id='pause-resume-" + file["Identifier"] + "'  style='cursor:pointer; margin: 0 5px' src='Resources/Images/upload.png' alt='Pause' title='Resume' height='24' width='24'  onclick='ResumeUpload(\"" + file["Identifier"] +"\")'>";
// 	var progressStr = 	"<div class='progress-bar'>" +
// 		"<img id='progress-bar-" + file["Identifier"] + "' class='progress-bar-image' src='Resources/Images/ProgressBar.jpg'>" +
// 		"<div id='progress-text-" + file["Identifier"] + "' class='progress-text'></div>" +
// 		"</div>";
//
// 	var rowStr = 	"<div id='resumable-" + file["Identifier"] + "' style='height:30px; lineHeight: 30px'>" +
// 		"<div id='" + file["Identifier"] + "-file-name' style='padding-top:5px; height: 30px; width: 200px;float: left; border: 1px solid #CCCCCC;background:" + rowColor + ";'>" + file["FileName"] + "</div>" +
// 		"<div id='" + file["Identifier"] + "-status' style='padding-top:5px; height: 30px;width: 100px;float: left; border: 1px solid #CCCCCC;background:" + rowColor + ";'>" + status + "</div>" +
// 		"<div id='" + file["Identifier"] + "-progress'  style='padding-top:5px; height: 30px;width: 350px;float: left; border: 1px solid #CCCCCC;background:" + rowColor + ";'>" +
// 		progressStr +
// 		"</div>" +
//
// 		"<div id='" + file["Identifier"] + "-control'  style='padding-top:5px; height: 30px;width: 100px;float: left; border: 1px solid #CCCCCC;background:" + rowColor + ";'>" +
// 		reuploadStr +
// 		"<img style='cursor:pointer; margin: 0 5px' src='Resources/Images/remove.png' alt='Cancel' title='Cancel' height='24' width='24' "+
// 		"onclick='Cancel(-1,\"" + file["Identifier"] +"\")'>" +
// 		"</div>" +
//
// 		"</div>" +
// 		"<div style='clear:both'></div>";
// 	$("#unfinished-files").append(rowStr);
// 	UpdateProgressBar(file["Progress"]/100, file["Size"], file["Identifier"], "Uploading");
// }
//
// function ResumeUpload(identifier){
// 	CreateResumableInstance(identifier);
// }
//
// function CreateThumbnail(identifier){
// 	$.ajax({
// 		url: "Resources/PHP/CreateThumbnail.php",
// 		dataType: 'text',
// 		data: {
// 			identifier: identifier
// 		},
// 		success: function(response) {
//
// 		}
// 	});
// }
//
// function GetFinishedList(){
//
// 	$.ajax({
// 		url: "Resources/PHP/GetFinishedList.php",
// 		dataType: "text",
// 		success: function(response) {
// 			var data = JSON.parse(response);
// 			var items = "";
// 			$("#finished-list-wrapper").html("");
// 			if (data.length > 0)
// 			{
// 				var table =	"<table id='finished-list-table'>" +
// 					"<thead>" +
// 					"<tr>" +
// 					"<th>&nbsp;</th>" +
// 					"<th>File Name</th>" +
// 					"<th>File Size</th>" +
// 					"<th>Project</th>" +
// 					"<th>Platform</th>" +
// 					"<th>Sensor</th>" +
// 					"<th>Date</th>" +
// 					"<th>Flight</th>" +
// 					"</tr>" +
// 					"</thead>" +
// 					"<tbody id='finished-list'>" +
// 					"</tbody>" +
// 					"</table>";
//
// 				$("#finished-list-wrapper").html(table);
// 				$.each(data,function(index,item)
// 				{
//
// 					items+= "<tr>" +
// 						"<td style='overflow:hidden, background:#555555, height:55px, color:#fff, fontsize:19px, fontWeight:500, padding:13px 0px 0px 0px, text-align:center'>" +
// 						"<input id='download-" + item.ID +"' type='image' class='image-button download-button' src='Resources/Images/download.png' alt='Download' onclick='Download(\"" + item.DownloadPath + "\"); return false;' title='Download'>" +
// 						"</td>" +
// 						"<td style='overflow:hidden, background:#555555, height:55px, color:#fff, fontsize:19px, fontWeight:500, padding:13px 0px 0px 0px, text-align:center'>" +
// 						"<span id='" + item.ID +"-file-name'>" + item.FileName + "</span>" +
// 						"</td>" +
// 						"<td style='overflow:hidden, background:#555555, height:55px, color:#fff, fontsize:19px, fontWeight:500, padding:13px 0px 0px 0px, text-align:center'>" +
// 						"<span>" + item.Size + "</span>" +
// 						"</td>" +
// 						"<td style='overflow:hidden, background:#555555, height:55px, color:#fff, fontsize:19px, fontWeight:500, padding:13px 0px 0px 0px, text-align:center'>" +
// 						"<span>" + item.ProjectName + "</span>" +
// 						"</td>" +
// 						"<td style='overflow:hidden, background:#555555, height:55px, color:#fff, fontsize:19px, fontWeight:500, padding:13px 0px 0px 0px, text-align:center'>" +
// 						"<span>" + item.PlatformName + "</span>" +
// 						"</td>" +
// 						"<td style='overflow:hidden, background:#555555, height:55px, color:#fff, fontsize:19px, fontWeight:500, padding:13px 0px 0px 0px, text-align:center'>" +
// 						"<span>" + item.SensorName + "</span>" +
// 						"</td>" +
//
// 						"<td style='overflow:hidden, background:#555555, height:55px, color:#fff, fontsize:19px, fontWeight:500, padding:13px 0px 0px 0px, text-align:center'>" +
// 						"<span>" + item.Date.replace(/\-/g, '/') + "</span>" +
// 						"</td>" +
// 						"<td style='overflow:hidden, background:#555555, height:55px, color:#fff, fontsize:19px, fontWeight:500, padding:13px 0px 0px 0px, text-align:center'>" +
// 						"<span>" + item.FlightName + "</span>" +
// 						"</td>" +
// 						"</tr>";
//
//
//
// 					items +=	"</tr>";
// 				});
//
// 				$("#finished-list").html(items);
//
//
//
// 				var rowHeight = 61;
// 				var padding = 10;
// 				var actualHeight = (data.length + 1) * rowHeight + padding;
// 				var maxHeight = 300;
// 				var height = actualHeight < maxHeight ? actualHeight : maxHeight;
//
// 				$('#finished-list-table').fxdHdrCol({
// 					// fixedCols:  2,
// 					width:     780,
// 					height:    height,
//
// 					colModal: [
// 						{ width: 75, align: 'center' }, // Download
// 						{ width: 250, align: 'center' }, // File name
// 						{ width: 100, align: 'center' }, // File Size
// 						{ width: 300, align: 'center' }, // Project
// 						{ width: 150, align: 'center' }, // Platform
// 						{ width: 100,  align: 'center' }, // Sensor
// 						{ width: 100,  align: 'center' }, // Date
// 						{ width: 100,  align: 'center' }, // Flight
// 					],
// 					sort: false
// 				});
//
// 			}
// 		}
// 	});
// }
//
// function Download(link){
// 	var win = window.open(link, '_blank');
// 	if (win) {
// 		//Browser has allowed it to be opened
// 		win.focus();
// 	} else {
// 		//Browser has blocked it
// 		alert('Please allow popups for this website');
// 	}
// }
//
// function CheckEmailAddress(address) {
// 	var pattern = /^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i;
// 	return pattern.test(address);
// }
//
// function SendEmail(identifier){
//
// 	$.ajax({
// 		url: "Resources/PHP/Email.php",
// 		data: { identifier: identifier},
// 		dataType: "text",
// 		success: function(response) {
// 		},
// 		error: function(xhr){
// 			alert('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
// 			$("#processing").hide();
// 		}
// 	});
// }
//
//
// // function GetEmailReceiver() {
// // 	$.ajax({
// // 		url: "Resources/PHP/GetEmailReceiver.php",
// // 		dataType: "text",
// // 		success: function(response) {
// // 			var data = JSON.parse(response);
// // 			$("#email-to").val(data.Receiver);
// // 			$("#cc-address").val(data.CC);
// // 		},
// // 		error: function(xhr){
// // 			alert('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
// // 			$("#processing").hide();
// // 		}
// // 	});
// // }
//
// // ALL ABOVE BEFORE UPDATE
//

var resumableInstances = new Array();
var rowColor = "#fff";
var flights = new Array();
var processing = false;
var uploading = false;
$(document).ready(function(){
	$.noConflict();

	GetList("project");
	GetList("platform");
	GetList("sensor");
	setTimeout(function(){
		GetList("date"); //Populate the dropdown list for "date"
	}, 500);

	$(".add-button").mouseup(function(){
		$(this).blur();
	});

	$("#flight-date").datepicker();

	GetUnfinishedList();
	GetFinishedList();

	window.setInterval(function(){
		GetUnfinishedList();
	}, 10000);


	$("#email-to").on('change', function() {
		if (!CheckEmailAddress($("#email-to").val())){
			alert("Invalid email address.");
		}
	});

	$("#cc-address").on('change', function() {
		if (!CheckEmailAddress($("#cc-address").val())){
			alert("Invalid email address.");
		}
	});

	//GetEmailReceiver();
});

function CheckUploadCondition(){
	if ($("#flight").val() != "" && $("#flight").val() != null){
		$("#upload-button").prop("disabled",false);
	} else {
		$("#upload-button").prop("disabled",true);
	}
}


function SetFlight(project, platform, sensor, date, flight){
	$.ajax({
		url: "Resources/PHP/List.php",
		data:{
			type: "date",
			project: project,
			platform: platform,
			sensor: sensor,
		},
		dataType: 'text',
		success: function(response) {
			var items="";
			var data = JSON.parse(response);
			if (data.length > 0)
			{
				$.each(data,function(index,item)
				{
					items+= "<option value='" + item.ID + "'>" + item.Name + "</option>";
				});

				$("#date").html(items);

				$("#date option").filter(function() {
					return $(this).text() == date;
				}).prop('selected', true);
				GetList("flight");
				setTimeout(function(){
					$("#flight").val(flight);
				}, 500);

			}
		}
	});

}

function ShowAddFlight(){
	$("#flight-project").val($("#project option:selected").text());
	$("#flight-platform").val($("#platform option:selected").text());
	$("#flight-sensor").val($("#sensor option:selected").text());
	$("#add-flight").show();
	$("#select-flight").hide();
}

function ShowSelectFlight(){
	$("#add-flight").hide();
	$("#select-flight").show();
	CheckUploadCondition();
}

function AddFlight(){
	var name = $("#flight-name").val();
	var project = $("#project").val();
	var platform = $("#platform").val();
	var sensor = $("#sensor").val();
	var date = $("#flight-date").val();
	var altitude = $("#flight-altitude").val();
	var forward = $("#flight-forward").val();
	var side = $("#flight-side").val();

	$.ajax({
		url: "Resources/PHP/Flight.php",
		data:{
			action: "add",
			name: name,
			project: project,
			platform: platform,
			sensor: sensor,
			date: date,
			altitude: altitude,
			forward: forward,
			side: side
		},
		dataType: "text",
		success: function(response) {
			SetFlight(project, platform, sensor, date, response);

		}
	});
	ShowSelectFlight();
}


function GetList(type){

	var url = "Resources/PHP/List.php?type=" + type;

	switch(type) {
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
			$("#flight").html("");
			$("#" + type).html("");
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
						// $("#project").chosen({
						// 	inherit_select_classes: true
						// });
						// $("#" + type).on('change', function() {
						// 	GetList("date");
						// });
						$("#project").chosen({
							inherit_select_classes: true
						});
						$("#" + type).on('change', function() {
							GetList("date");
						});
					}break;
					case 'platform':
					{
						$("#" + type).on('change', function() {
							GetList("date");
						});
					}break;
					case 'sensor':
					{
						//GetList("date");
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
						SetFlightInfo(data[0].ID);
						$("#" + type).on('change', function() {
							SetFlightInfo(data[0].ID);
						});
					}break;

					case 'product-type':
					{
					}break;

				}
			}

			CheckUploadCondition();
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

function GetProjectList(){
	$.ajax({
		url: "Resources/PHP/Project.php",
		dataType: 'text',
		data: { action: 'list'},
		success: function(response) {
			var project = "<option value='0' lat='0' lng='0'>None</option>";
			$('#project').append(project);

			var data = JSON.parse(response);
			if (data.length > 0)
			{

				$.each(data,function(index,item)
				{

					var project = "<option value='" + item.ID + "' lat='" + item.CenterLat + "' lng='" + item.CenterLng + "'>" + item.Name + "</option>";
					$('#project').append(project);

				});

			}

			$("#project").change(function(){
				var lat = this.options[this.selectedIndex].getAttribute("lat");
				var lng = this.options[this.selectedIndex].getAttribute("lng");
				if (lat != 0 && lng != 0){
					$("#lat").val(lat);
					$("#lng").val(lng);
					ChangeLocation();
				}

			});
		}
	});
}

function CreateResumableInstance(identifier){


	var count = resumableInstances.length;
	var resumableStr = "<a href='#' id='browsebutton-" + count + "'><img src='Resources/Images/upload.png'></a>";

	$("#resumable-list").append(resumableStr);


	var projectName = $("#project option:selected").text();
	var platformName = $("#platform option:selected").text();
	var sensorName = $("#sensor option:selected").text();
	var flightID = $("#flight").val();
	var flightName = $("#flight option:selected").text();
	var date = $("#date").val();

	var receiver = $("#email-to").val();
	var note = $("#note").val();
	var ccAddress = $("#cc-address").val();

	var targetStr = "Resources/PHP/Upload.php?project=" + projectName +
		"&platform=" + platformName +
		"&sensor=" + sensorName +
		"&date=" + date +
		"&flightID=" + flightID +
		//"&flightName=" + flightName;
		"&flightName=" + flightName +
		"&receiver=" + receiver +
		"&cc=" + ccAddress +
		"&note=" + note;

	var r = new Resumable({
		target: targetStr,
		chunkSize: 20*1024*1024
		//testChunks: false
	});


	r.assignBrowse(document.getElementById("browsebutton-" + count));

	var currentProgress = 0;
	r.on('fileAdded', function(data) {
		if ( identifier != "" && r.files[0].uniqueIdentifier != identifier){
			$( "#dialog-different-file" ).dialog({
				resizable: false,
				height: "auto",
				position: { my: "top+50", at: "top+50", of: window },
				width: 400,
				modal: true,
				buttons: {
					"OK": function() {
						r.cancel();
						resumableInstances.splice( $.inArray(r, resumableInstances), 1 );
						$("#resumable-" + count).remove();
						$("#browsebutton-" + count).remove();
						$( this ).dialog( "close" );
					}
				}
			});


		} else {
			$("#loading").show();
			$.ajax({
				url: "Resources/PHP/CheckFileStatus.php",
				dataType: 'text',
				data: { identifier: r.files[0].uniqueIdentifier},
				success: function(response) {

					var file = JSON.parse(response);
					//var name = $("#name").val();


					if (file == null || file["Status"] != "Finished"){
						if (file){
							currentProgress = file["Progress"]/100;
							UpdateStatus("Resume", file["Identifier"], currentProgress);
							GetUnfinishedList();
							//name = file["Name"];
						}
						var fileName = data.fileName.replace(/[^a-zA-Z0-9_.\s]/gi, '').replace(/[_\s]/g, '_').replace(/_+/gi,'_');
						r.upload();

						var rowStr = 	"<div id='resumable-" + count + "' style='height:30px; lineHeight: 30px'>" +
							"<div id='" + count + "-file-name' style='padding-top:5px; height: 30px ;width: 200px;float: left; border: 1px solid #CCCCCC;background:" + rowColor + ";'>" + fileName + "</div>" +
							"<div id='" + count + "-status' style='padding-top:5px; height: 30px ;width: 100px;float: left; border: 1px solid #CCCCCC;background:" + rowColor + ";'>Uploading</div>" +
							"<div id='" + count + "-progress'  style='padding-top:5px; height: 30px ;width: 350px;float: left; border: 1px solid #CCCCCC;background:" + rowColor + ";'>" +
							// "<div class='progress-bar'>" +
							// //"<img id='progress-bar-" + count + "' class='progress-bar-image' src='Resources/Images/ProgressBar.jpg'>" +
							// "<img id='progress-bar-" + count + "' class='progress-bar-image w-100' style='height: 22px;' src='Resources/Images/ProgressBar.jpg'>" +
							// //"<div id='progress-text-" + count + "' class='progress-text'></div>" +
							// // "<div id='progress-text-" + count + "' style='position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);'></div>" +
							// // "</div>" +
							//
							// "<div id='progress-text-" + count + "' style='position: relative; top: 50%; left: 50%; transform: translate(-50%, -50%);'></div>" +
							// "</div>" +
							"<div class='progress-bar'>" +
								// "<img id='progress-bar-" + count + "' class='progress-bar-image' style'ms-flex-direction: column !important;' src='Resources/Images/ProgressBar.jpg'>" +
								"<img id='progress-bar-" + count + "' class='progress-bar-image' style'' src='Resources/Images/ProgressBar.jpg'>" +
								"<div id='progress-text-" + count + "' class='progress-text'></div>" +
							"</div>" +

							"</div>" +
							"<div id='" + count + "-control'  style='padding-top:5px; height: 30px ;width: 100px;float: left; border: 1px solid #CCCCCC;background:" + rowColor + ";'>" +
							"<img id='pause-resume-" + count + "'  style='cursor:pointer; margin: 0 5px' src='Resources/Images/pause.png' alt='Pause' title='Pause' height='24' width='24' "+
							"onclick='PauseResume(\""+ count +"\",\"" + r.files[0].uniqueIdentifier + "\"," + currentProgress +")'>" +
							"<img id='cancel-" + count + "'  style='cursor:pointer; margin: 0 5px' src='Resources/Images/remove.png' alt='Cancel' title='Cancel' height='24' width='24' "+
							"onclick='Cancel(\""+ count +"\",\"" + r.files[0].uniqueIdentifier +"\")'>" +
							"</div>" +

							"</div>";

						$("#upload-files").append(rowStr);
						var totalSize = Math.floor (data.size / 1024) ;
						$("#total-size-" + count).text(totalSize + "KB");
						$("#loading").hide();
					}else {
						alert("The same file was already uploaded.");
						$("#loading").hide();
					}

				}
			});
		}

	});


	r.on('fileProgress', function(data) {
		if(!r.files[0]){
			return;
		}
		var type = "Uploading";
		var progress = currentProgress;
		if (r.progress() >= currentProgress){
			progress = r.progress();
		} else {
			type = "Checking";
		}

		UpdateProgressBar(progress, data.size, count, type);
	});

	r.on('fileSuccess', function(file,message){
		SendEmail(file.uniqueIdentifier);
		$("#resumable-" + count).remove();
		GetFinishedList();
		GetUnfinishedList();
	});

	resumableInstances.push(r);

	$("#browsebutton-" + count)[0].click();

}

function UpdateProgressBar(progress, size, index, type){
	if (type == "Uploading"){
		var percent = Math.floor(progress*100);
		if (percent) {

			$("#progress-bar-" + index).css("width", percent * 3 + "px"); //Update the upload progress bar
			var totalSize = Math.floor (size / 1024) ;
			var uploadSize = Math.floor((percent * totalSize ) / 100);

			var progressText = uploadSize + "KB / " + totalSize + "KB (" + percent + "%)";

			$("#progress-text-" + index).text(progressText);

		}
	} else {
		$("#progress-bar-" + index).css("width","0"); //Update the upload progress bar
		$("#progress-text-" + index).text(type);
	}
}

function PauseResume(index, identifier, progress){
	var alt = $("#pause-resume-" + index).attr("alt");
	if (alt == "Pause"){
		$("#pause-resume-" + index).attr("alt", "Resume");
		$("#pause-resume-" + index).attr("title","Resume");
		$("#pause-resume-" + index).attr("src","Resources/Images/resume.png");
		$("#" + index + "-status").html("Paused");
		resumableInstances[index].pause();
	} else {
		$("#pause-resume-" + index).attr("alt", "Pause");
		$("#pause-resume-" + index).attr("title","Pause");
		$("#pause-resume-" + index).attr("src","Resources/Images/pause.png");
		$("#" + index + "-status").html("Uploading");
		resumableInstances[index].upload();
	}

	UpdateStatus(alt, identifier, progress);

	GetUnfinishedList();
}

function Cancel(index, identifier){

	$( "#dialog-confirm" ).dialog({
		resizable: false,
		height: "auto",
		position: { my: "top+50", at: "top+50", of: window },
		width: 400,
		modal: true,
		buttons: {
			"Yes": function() {
				$("#loading").show();
				$.ajax({
					url: "Resources/PHP/Cancel.php",
					dataType: 'text',
					data: { identifier: identifier},
					success: function(response) {
						if (response == "cancelled"){
							if(index != -1){
								resumableInstances[index].cancel();
								//resumableInstances.splice( $.inArray(resumableInstances[index], resumableInstances), 1 );
								$("#resumable-" + index).remove();
							}
							GetUnfinishedList();
							alert("The upload has been cancelled.");
							$("#dialog-confirm").dialog( "close" );
						} else {
							alert("Could not cancel the upload. Error: " + response + ". Please try again.");
						}
						$("#loading").hide();
					},
					error: function (request, status, error){
						alert("Could not cancel the upload. Error: " + error + ". Please try again.");
						$("#loading").hide();
					}
				});
			},
			"No": function() {
				$( this ).dialog( "close" );
			}
		}
	});


}

function UpdateStatus(type, identifier, progress){
	$.ajax({
		url: "Resources/PHP/PauseResume.php",
		dataType: 'text',
		data: { type: type, identifier: identifier, progress: progress},
		success: function(response) {

		}
	});
}

function Remove(index){
	resumableInstances[index].cancel();
	$("#resumable-" + index).remove();
}

function GetUnfinishedList(){
	$.ajax({
		url: "Resources/PHP/CheckUpload.php",
		dataType: 'text',
		success: function(response) {
			var data = JSON.parse(response);
			$("#unfinished-files").html("");

			if (data.length > 0){

				$.each(data,function(index,item){

					//This user is not uploading anything
					if (resumableInstances.length == 0){
						AppendUploadingFileList(item);
					} else { //This user is uploading at least one item, check and skip that item
						var valid = 1;
						$.each(resumableInstances,function(i,r){
							if (r.files[0]){
								if (r.files[0].uniqueIdentifier == item["Identifier"]){ // this is the item being uploaded by this user
									valid = 0;
								}
							}
						});

						if (item["Status"] == "Unzip"){
							valid = 1;
						}

						if (valid == 1){
							AppendUploadingFileList(item);
						}
					}
				});

			}
		}
	});

}

function AppendUploadingFileList(file){

	var status = file["Status"];
	var reuploadStr = 	"<img id='pause-resume-" + file["Identifier"] + "'  style='cursor:pointer; margin: 0 5px' src='Resources/Images/upload.png' alt='Pause' title='Resume' height='24' width='24' onclick='ResumeUpload(\"" + file["Identifier"] +"\")'>";
	var progressStr = 	"<div class='progress-bar'>" +
		 //"<img id='progress-bar-" + file["Identifier"] + "' class='progress-bar-image' src='Resources/Images/ProgressBar.jpg'>" +
		"<img id='progress-bar-" + file["Identifier"] + "' class='progress-bar-image w-100' style='height: 22px;' src='Resources/Images/ProgressBar.jpg'>" +
		"<div id='progress-text-" + file["Identifier"] + "' class='progress-text'></div>" +
		"</div>";

	var sss = "<div class='table-responsive' id='resumable-" + file["Identifier"] + "'>"+
		"<table className='table table-bordered bg-white'>"+
		"<tbody><tr>"+
		"<td class='text-center' id='" + file["Identifier"] + "-file-name'>"+
		"</td>"+

		"<td id='" + file["Identifier"] + "-status'>"+
		"</td>"+

		"<td id='" + file["Identifier"] + "-progress'>"+
		"</td>"+

		"<td id='" + file["Identifier"] + "-control'>"+
		"</td>"+
		"</tr></tbody>"+
		"</table>"+
	"</div>";


	var rowStr = 	"<div id='resumable-" + file["Identifier"] + "' style='height:30px; lineHeight: 30px'>" +
		"<div id='" + file["Identifier"] + "-file-name' style='padding-top:5px; height: 30px; width: 200px;float: left; border: 1px solid #CCCCCC;background:" + rowColor + ";'>" + file["FileName"] + "</div>" +
		"<div id='" + file["Identifier"] + "-status' style='padding-top:5px; height: 30px;width: 100px;float: left; border: 1px solid #CCCCCC;background:" + rowColor + ";'>" + status + "</div>" +
		"<div id='" + file["Identifier"] + "-progress'  style='padding-top:5px; height: 30px;width: 350px;float: left; border: 1px solid #CCCCCC;background:" + rowColor + ";'>" +
		progressStr +
		"</div>" +

		"<div id='" + file["Identifier"] + "-control'  style='padding-top:5px; height: 30px;width: 100px;float: left; border: 1px solid #CCCCCC;background:" + rowColor + ";'>" +
		reuploadStr +
		"<img style='cursor:pointer; margin: 0 5px' src='Resources/Images/remove.png' alt='Cancel' title='Cancel' height='24' width='24' "+
		"onclick='Cancel(-1,\"" + file["Identifier"] +"\")'>" +
		"</div>" +

		"</div>" +
		"<div style='clear:both'></div>";


	$("#unfinished-files").append(rowStr);
	UpdateProgressBar(file["Progress"]/100, file["Size"], file["Identifier"], "Uploading");
}

function ResumeUpload(identifier){
	CreateResumableInstance(identifier);
}

function CreateThumbnail(identifier){
	$.ajax({
		url: "Resources/PHP/CreateThumbnail.php",
		dataType: 'text',
		data: {
			identifier: identifier
		},
		success: function(response) {

		}
	});
}

function GetFinishedList(){

	$.ajax({
		url: "Resources/PHP/GetFinishedList.php",
		dataType: "text",
		success: function(response) {
			var data = JSON.parse(response);
			var items = "";
			$("#finished-list-wrapper").html("");
			if (data.length > 0)
			{
				var table =	"<table id='finished-list-table' class='table table-bordered' style='white-space: nowrap;'>" +
					"<thead>" +
					"<tr style='background: #555555; color: white;'>" +
					"<th style='border: none !important;'>&nbsp;</th>" +
					"<th style='border: none !important;'>File Name</th>" +
					"<th style='border: none !important;'>File Size</th>" +
					"<th style='border: none !important;'>Project</th>" +
					"<th style='border: none !important;'>Platform</th>" +
					"<th style='border: none !important;'>Sensor</th>" +
					"<th style='border: none !important;'>Date</th>" +
					"<th style='border: none !important;'>Flight</th>" +
					"</tr>" +
					"</thead>" +
					"<tbody id='finished-list'>" +
					"</tbody>" +
					"</table>";

				$("#finished-list-wrapper").html(table);
				$.each(data,function(index,item)
				{

					// 7px !important;

					items+= "<tr>" +
						"<td style=''>" +
						"<input style='background: #5499ff; padding: 7px !important;' id='download-" + item.ID +"' type='image' class='image-button download-button' src='Resources/Images/download.png' alt='Download' onclick='Download(\"" + item.DownloadPath + "\"); return false;' title='Download'>" +
						"</td>" +
						"<td style='overflow:hidden, background:#555555, height:55px, color:#fff, fontsize:19px, fontWeight:500, padding:13px 0px 0px 0px, text-align:center'>" +
						"<span id='" + item.ID +"-file-name'>" + item.FileName + "</span>" +
						"</td>" +
						"<td style='overflow:hidden, background:#555555, height:55px, color:#fff, fontsize:19px, fontWeight:500, padding:13px 0px 0px 0px, text-align:center'>" +
						"<span>" + item.Size + "</span>" +
						"</td>" +
						"<td style='overflow:hidden, background:#555555, height:55px, color:#fff, fontsize:19px, fontWeight:500, padding:13px 0px 0px 0px, text-align:center'>" +
						"<span>" + item.ProjectName + "</span>" +
						"</td>" +
						"<td style='overflow:hidden, background:#555555, height:55px, color:#fff, fontsize:19px, fontWeight:500, padding:13px 0px 0px 0px, text-align:center'>" +
						"<span>" + item.PlatformName + "</span>" +
						"</td>" +
						"<td style='overflow:hidden, background:#555555, height:55px, color:#fff, fontsize:19px, fontWeight:500, padding:13px 0px 0px 0px, text-align:center'>" +
						"<span>" + item.SensorName + "</span>" +
						"</td>" +

						"<td style='overflow:hidden, background:#555555, height:55px, color:#fff, fontsize:19px, fontWeight:500, padding:13px 0px 0px 0px, text-align:center'>" +
						"<span>" + item.Date.replace(/\-/g, '/') + "</span>" +
						"</td>" +
						"<td style='overflow:hidden, background:#555555, height:55px, color:#fff, fontsize:19px, fontWeight:500, padding:13px 0px 0px 0px, text-align:center'>" +
						"<span>" + item.FlightName + "</span>" +
						"</td>" +
						"</tr>";

					items +=	"</tr>";
				});

				$("#finished-list").html(items);



				// var rowHeight = 61;
				// var padding = 10;
				// var actualHeight = (data.length + 1) * rowHeight + padding;
				// var maxHeight = 300;
				// var height = actualHeight < maxHeight ? actualHeight : maxHeight;

			// 	$('#finished-list-table').fxdHdrCol({
			// 		// fixedCols:  2,
			// 		width:     780,
			// 		height:    height,
			//
			// 		colModal: [
			// 	{ width: 75, align: 'center', border: '1px solid rgba(0, 0, 0, .10)', padding: '15px 0px 0px 0px', height: '72px', background: '#5499ff', color: '#fff', borderRadius: '3px', fontsize: '18px' }, // Download
			// 	{ width: 250, align: 'center', border: '1px solid rgba(0, 0, 0, .10)', color: '#090909', fontsize: '16px', fontWeight: 'normal', lineHeight: '71px' }, // File name
			// 	{ width: 100, align: 'center', border: '1px solid rgba(0, 0, 0, .10)', color: '#090909', fontsize: '16px', fontWeight: 'normal', lineHeight: '71px'  }, // File Size
			// 	{ width: 300, align: 'center', border: '1px solid rgba(0, 0, 0, .10)', color: '#090909', fontsize: '16px', fontWeight: 'normal', lineHeight: '71px'  }, // Project
			// 	{ width: 150, align: 'center', border: '1px solid rgba(0, 0, 0, .10)', color: '#090909', fontsize: '16px', fontWeight: 'normal', lineHeight: '71px'  }, // Platform
			// 	{ width: 100,  align: 'center', border: '1px solid rgba(0, 0, 0, .10)', color: '#090909', fontsize: '16px', fontWeight: 'normal', lineHeight: '71px'  }, // Sensor
			// 	{ width: 100,  align: 'center', border: '1px solid rgba(0, 0, 0, .10)', color: '#090909', fontsize: '16px', fontWeight: 'normal', lineHeight: '71px'  }, // Date
			// 	{ width: 100,  align: 'center', border: '1px solid rgba(0, 0, 0, .10)', color: '#090909', fontsize: '16px', fontWeight: 'normal', lineHeight: '71px'  }, // Flight
			// ],
			// 	sort: false
			// });

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

function CheckEmailAddress(address) {
	var pattern = /^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i;
	return pattern.test(address);
}

function SendEmail(identifier){

	$.ajax({
		url: "Resources/PHP/Email.php",
		data: { identifier: identifier},
		dataType: "text",
		success: function(response) {
		},
		error: function(xhr){
			alert('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
			$("#processing").hide();
		}
	});
}


// function GetEmailReceiver() {
// 	$.ajax({
// 		url: "Resources/PHP/GetEmailReceiver.php",
// 		dataType: "text",
// 		success: function(response) {
// 			var data = JSON.parse(response);
// 			$("#email-to").val(data.Receiver);
// 			$("#cc-address").val(data.CC);
// 		},
// 		error: function(xhr){
// 			alert('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
// 			$("#processing").hide();
// 		}
// 	});
// }
