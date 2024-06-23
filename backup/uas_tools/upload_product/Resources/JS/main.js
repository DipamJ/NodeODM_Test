var resumableInstances = new Array();
var rowColor = "#fff";
var flights = new Array();
var processing = false;
var uploading = false;

$(document).ready(function () {
    $.noConflict();

    GetList("project"); //Populate the dropdown list for "project"
    GetList("platform"); //Populate the dropdown list for "platform"
    GetList("sensor"); //Populate the dropdown list for "sensor"
    GetList("product-type"); //Populate the dropdown list for "product type"
    setTimeout(function () {
        GetList("date"); //Populate the dropdown list for "date"
    }, 500);

    GetUnfinishedList(); //Check and get the list of unfinished uploads
    GetFinishedList();


    window.setInterval(function () {
        GetUnfinishedList(); //Check and get the list of unfinished uploads every 10 seconds
    }, 10000);


    $("#product-type").on('change', function () {
        if ($("#product-type option:selected").text() == "MULTI Ortho") { //show bands selection when product type is "MULTI Ortho"
            $("#bands").show();
        } else {
            $("#bands").hide(); //hide bands selection when product type is not "MULTI Ortho"
        }
    });
    $("#flight-date").datepicker();

    $(":button").mouseup(function () {
        $(this).blur();
    })

});

function CheckUploadCondition() {
    if ($("#flight").val() != "" && $("#flight").val() != null) {
        $("#upload-button").prop("disabled", false);
    } else {
        $("#upload-button").prop("disabled", true);
    }
}

function GetList(type) {

    var url = "Resources/PHP/List.php?type=" + type;

    switch (type) {
        case 'date': {
            url += '&project=' + $('#project').val() + '&platform=' + $('#platform').val() + '&sensor=' + $('#sensor').val();
        }
        break;
    case 'flight': {
        url += '&project=' + $('#project').val() + '&platform=' + $('#platform').val() + '&sensor=' + $('#sensor').val() + '&date=' + $('#date').val();
    }
    break;
    case 'product-type': {

    }
    break;
    }

    $.ajax({
        url: url,
        dataType: 'text',
        success: function (response) {
            $("#flight").html("");
            $("#" + type).html("");
            var items = "";
            var data = JSON.parse(response);
            if (data.length > 0) {
                $.each(data, function (index, item) {
                    items += "<option value='" + item.ID + "'>" + item.Name + "</option>";
                });

                $("#" + type).html(items);

                switch (type) {
                    case 'project': {
                        $("#project").chosen({
                            inherit_select_classes: true
                        });

                        $("#" + type).on('change', function () {
                            GetList("date");
                        });

                    }
                    break;
                case 'platform': {
                    //GetList("date");

                    $("#" + type).on('change', function () {
                        GetList("date");
                    });

                }
                break;
                case 'sensor': {
                    //GetList("date");
                    $("#" + type).on('change', function () {
                        GetList("date");
                    });
                }
                break;
                case 'date': {
                    GetList("flight");
                    $("#" + type).on('change', function () {
                        GetList("flight");
                    });
                }
                break;
                case 'flight': {
                    flights = data;
                    SetFlightInfo(data[0].ID);
                    $("#" + type).on('change', function () {
                        SetFlightInfo(data[0].ID);
                    });
                }
                break;

                case 'product-type': {}
                break;

                }
            }

            CheckUploadCondition();
        }
    });
}


//Get and display flight info from "flight" table
function SetFlightInfo(id) {
    var items = $.grep(flights, function (e) {
        return e.ID == id;
    });
    if (items.length > 0) {
        $("#altitude").text(items[0].Altitude);
        $("#forward").text(items[0].Forward);
        $("#side").text(items[0].Side);
    }
}

//Create a new resumable instance for upload
function CreateResumableInstance(identifier) {


    var count = resumableInstances.length;
    var resumableStr = "<a href='#' id='browsebutton-" + count + "'><img src='Resources/Images/upload.png'></a>";

    $("#resumable-list").append(resumableStr);

    var projectName = $("#project option:selected").text();
    var platformName = $("#platform option:selected").text();
    var sensorName = $("#sensor option:selected").text();
    var flightID = $("#flight").val();
    var flightName = $("#flight option:selected").text();
    var date = $("#date").val();
    var typeID = $("#product-type").val();

    var typeName = $("#product-type option:selected").text();
    //var tmsPath = $( "#tms-path").val();

    var minZoom = $("#min-zoom").val();
    var maxZoom = $("#max-zoom").val();
    var zoom = $("#zoom").val();
    var epsg = $("#epsg").val();
    var bands = $("#b1").val() + "," + $("#b2").val() + "," + $("#b3").val() + "," + $("#alpha").val();


    var targetStr = "Resources/PHP/Upload.php?project=" + projectName +
        "&platform=" + platformName +
        "&sensor=" + sensorName +
        "&date=" + date +
        "&flightID=" + flightID +
        "&flightName=" + flightName +
        "&typeID=" + typeID +
        "&typeName=" + typeName +
        "&bands=" + bands +
        "&minZoom=" + minZoom +
        "&maxZoom=" + maxZoom +
        "&zoom=" + zoom +
        "&epsg=" + epsg;
    var r = new Resumable({
        target: targetStr,
        chunkSize: 20 * 1024 * 1024,
        simultaneousUploads: 10

    });


    r.assignBrowse(document.getElementById("browsebutton-" + count));

    var currentProgress = 0;
    r.on('fileAdded', function (data) {

        if (identifier != "" && r.files[0].uniqueIdentifier != identifier) {
            $("#dialog-different-file").dialog({
                resizable: false,
                height: "auto",
                position: {
                    my: "top+50",
                    at: "top+50",
                    of: window
                },
                width: 400,
                modal: true,
                buttons: {
                    "OK": function () {
                        r.cancel();
                        resumableInstances.splice($.inArray(r, resumableInstances), 1);
                        $("#resumable-" + count).remove();
                        $("#browsebutton-" + count).remove();
                        $(this).dialog("close");
                    }
                }
            });


        } else {
            $("#loading").show();

            $.ajax({
                url: "Resources/PHP/CheckFileStatus.php", //check to see if the same file has been/is being uploaded
                dataType: 'text',
                data: {
                    identifier: r.files[0].uniqueIdentifier
                },
                success: function (response) {

                    var file = JSON.parse(response);
                    if (file == null || file["Status"] != "Finished") {
                        if (file) {
                            currentProgress = file["Progress"] / 100;
                            UpdateStatus("Resume", file["Identifier"], currentProgress);
                            GetUnfinishedList();

                        }
                        var fileName = data.fileName.replace(/[^a-zA-Z0-9_.\s]/gi, '').replace(/[_\s]/g, '_').replace(/_+/gi, '_');
                        r.upload();
                        //GetUnfinishedList();

                        var rowStr = "<div id='resumable-" + count + "' style='height:30px; line-height: 30px'>" +
                            "<div id='" + count + "-file-name' style='padding-top:5px; height: 30px ;width: 380px;float: left; border: 1px solid #CCCCCC;background:" + rowColor + ";'>" + fileName + "</div>" +
                            "<div id='" + count + "-status' style='padding-top:5px; height: 30px ;width: 150px;float: left; border: 1px solid #CCCCCC;background:" + rowColor + ";'>Uploading</div>" +
                            "<div id='" + count + "-progress'  style='padding-top:5px; height: 30px ;width: 400px;float: left; border: 1px solid #CCCCCC;background:" + rowColor + ";'>" +
                            "<div class='progress-bar'>" +
                            "<img id='progress-bar-" + count + "' class='progress-bar-image' src='Resources/Images/ProgressBar.jpg'>" +
                            "<div id='progress-text-" + count + "' class='progress-text'></div>" +
                            "</div>" +
                            "</div>" +
                            "<div id='" + count + "-control'  style='padding-top:5px; height: 30px ;width: 100px;float: left; border: 1px solid #CCCCCC;background:" + rowColor + ";'>" +
                            "<img id='pause-resume-" + count + "'  style='cursor:pointer; margin: 0 5px' src='Resources/Images/pause.png' alt='Pause' title='Pause' height='24' width='24' " +
                            "onclick='PauseResume(\"" + count + "\",\"" + r.files[0].uniqueIdentifier + "\"," + currentProgress + ")'>" +
                            "<img id='cancel-" + count + "'  style='cursor:pointer; margin: 0 5px' src='Resources/Images/remove.png' alt='Cancel' title='Cancel' height='24' width='24' " +
                            "onclick='Cancel(\"" + count + "\",\"" + r.files[0].uniqueIdentifier + "\")'>" +
                            "</div>" +

                            "</div>";

                        $("#upload-files").append(rowStr);
                        var totalSize = Math.floor(data.size / 1024);
                        $("#total-size-" + count).text(totalSize + "KB");
                        $("#loading").hide();

                    } else {
                        alert("The same file was already uploaded.");
                        $("#loading").hide();
                    }
                }
            });
        }
    });


    r.on('fileProgress', function (data) {
        if (!r.files[0]) {
            return;
        }

        var type = "Uploading";
        var progress = currentProgress;
        if (r.progress() >= currentProgress) {
            progress = r.progress();
        } else {
            type = "Checking";
        }

        UpdateProgressBar(progress, data.size, count, type);
        /*
        if (r.progress() == 1) {
        	$("#resumable-" + count).remove();
        	ConvertProduct(data.fileName, count, r.files[0].uniqueIdentifier);
        	GetUnfinishedList();
        } else {
        	UpdateProgressBar(progress, data.size, r.files[0].uniqueIdentifier, type);

        }
        */
    });

    r.on('fileSuccess', function (file, message) {
        //SendEmail(file.uniqueIdentifier);
        $("#resumable-" + count).remove();
        ConvertProduct(file.fileName, count, file.uniqueIdentifier);
        GetFinishedList();
        GetUnfinishedList();
    });

    resumableInstances.push(r);

    $("#browsebutton-" + count)[0].click();

}

//Function for displaying the upload progress bar
function UpdateProgressBar(progress, size, index, type) {
    if (type == "Uploading") {
        var percent = Math.floor(progress * 100);
        if (percent) {

            $("#progress-bar-" + index).css("width", percent * 3 + "px"); //Update the upload progress bar
            var totalSize = Math.floor(size / 1024);
            var uploadSize = Math.floor((percent * totalSize) / 100);

            var progressText = uploadSize + "KB / " + totalSize + "KB (" + percent + "%)";

            $("#progress-text-" + index).text(progressText);

        }
    } else {
        $("#progress-bar-" + index).css("width", "0"); //Update the upload progress bar
        $("#progress-text-" + index).text(type);
    }
}

function MakeID() {
    var text = "";
    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

    for (var i = 0; i < 5; i++)
        text += possible.charAt(Math.floor(Math.random() * possible.length));

    return text;
}

// Pause/resume an upload
function PauseResume(index, identifier, progress) {
    var alt = $("#pause-resume-" + index).attr("alt");
    if (alt == "Pause") {
        $("#pause-resume-" + index).attr("alt", "Resume");
        $("#pause-resume-" + index).attr("title", "Resume");
        $("#pause-resume-" + index).attr("src", "Resources/Images/resume.png");
        $("#" + index + "-status").html("Paused");
        resumableInstances[index].pause();
    } else {
        $("#pause-resume-" + index).attr("alt", "Pause");
        $("#pause-resume-" + index).attr("title", "Pause");
        $("#pause-resume-" + index).attr("src", "Resources/Images/pause.png");
        $("#" + index + "-status").html("Uploading");
        resumableInstances[index].upload();
    }

    UpdateStatus(alt, identifier, progress);

    //GetUnfinishedList();
}

//Cancel an unfinished upload
function Cancel(index, identifier) {

    $("#dialog-confirm").dialog({
        resizable: false,
        height: "auto",
        position: {
            my: "top+50",
            at: "top+50",
            of: window
        },
        width: 400,
        modal: true,
        buttons: {
            "Yes": function () {
                $("#loading").show();
                /*
                $.ajax({
                	url: "Resources/PHP/Cancel.php",
                	dataType: 'text',
                	data: { identifier: identifier},
                	success: function(response) {
                		if (response == "cancelled"){
                			if(index != -1){
                				resumableInstances[index].cancel();
                				resumableInstances.splice( $.inArray(resumableInstances[index], resumableInstances), 1 );
                				$("#resumable-" + index).remove();
                			}
                			GetUnfinishedList();
                			alert("The upload has been cancelled.");
                			$("#dialog-confirm").dialog( "close" );
                		} else {
                			alert("Could not cancel the upload. Error: " + response + ". Please try again.");
                		}
                	},
                	error: function (request, status, error){
                		alert("Could not cancel the upload. Error: " + error + ". Please try again.");
                	}
                });
                */
                ConfirmCancel(index, identifier);
            },
            "No": function () {
                $(this).dialog("close");
            }
        }
    });


}

function ConfirmCancel(index, identifier) {
    $.ajax({
        url: "Resources/PHP/Cancel.php",
        dataType: 'text',
        data: {
            identifier: identifier
        },
        success: function (response) {
            if (response == "cancelled") {
                if (index != -1) {
                    /*
                    $.each(resumableInstances,function(i,r){
                    	if (r.files[0].uniqueIdentifier == identifier){
                    		r.cancel();

                    		//resumableInstances[index].cancel();
                    		resumableInstances.splice( $.inArray(r, resumableInstances), 1 );
                    	}
                    });
                    */
                    resumableInstances[index].cancel();
                    //resumableInstances.splice( $.inArray(r, resumableInstances), 1 );
                    $("#resumable-" + index).remove();
                }
                GetUnfinishedList();
                alert("The upload has been cancelled.");
                $("#dialog-confirm").dialog("close");
            } else {
                alert("Could not cancel the upload. Error: " + response + ". Please try again.");
            }
            $("#loading").hide();
        },
        error: function (request, status, error) {
            alert("Could not cancel the upload. Error: " + error + ". Please try again.");
            $("#loading").hide();
        }
    });
}

function UpdateStatus(type, identifier, progress) {
    $.ajax({
        url: "Resources/PHP/PauseResume.php",
        dataType: 'text',
        data: {
            type: type,
            identifier: identifier,
            progress: progress
        },
        success: function (response) {

        }
    });
}

//Function for getting the list of unfinished upload
function GetUnfinishedList() {
    $.ajax({
        url: "Resources/PHP/CheckUpload.php",
        dataType: 'text',
        success: function (response) {
            var data = JSON.parse(response);
            $("#unfinished-files").html("");

            if (data.length > 0) {

                $.each(data, function (index, item) {

                    //This user is not uploading anything
                    if (resumableInstances.length == 0) {
                        AppendUploadingFileList(item);
                    } else { //This user is uploading at least one item, check and skip that item
                        var valid = 1;
                        $.each(resumableInstances, function (i, r) {
                            if (r.files[0]) {
                                if (r.files[0].uniqueIdentifier == item["Identifier"]) { // this is the item being uploaded by this user
                                    //if (r.files[0].uniqueIdentifier == item.Identifier && item.Status == "Uploading"){ // this is the item being uploaded by this user
                                    if (item.Status != "Converting" && item.Status != "Pending") {
                                        valid = 0;
                                    }
                                }
                            }
                        });

                        if (valid == 1) {
                            AppendUploadingFileList(item);
                        }
                    }
                });
            } else {
                $("#uploading-list").hide();


            }
        }
    });

}

function AppendUploadingFileList(file) {
    var status = file["Status"];
    var reuploadStr = "<img id='pause-resume-" + file["Identifier"] + "'  style='cursor:pointer; margin: 0 5px' src='Resources/Images/upload.png' alt='Upload' title='Upload' height='24' width='24'  onclick='ResumeUpload(\"" + file["Identifier"] + "\")'>";
    var progressStr = "<div class='progress-bar'>" +
        "<img id='progress-bar-" + file["Identifier"] + "' class='progress-bar-image' src='Resources/Images/ProgressBar.jpg'>" +
        "<div id='progress-text-" + file["Identifier"] + "' class='progress-text'></div>" +
        "</div>";
    var cancelStr = "<img style='cursor:pointer; margin: 0 5px' src='Resources/Images/remove.png' alt='Cancel' title='Cancel' height='24' width='24' " +
        "onclick='Cancel(-1,\"" + file["Identifier"] + "\")'>";

    if (status == "Converting") {

        reuploadStr = "";
        if (!file["ConvertProgress"]) {
            file["ConvertProgress"] = "";
        }
        progressStr = "<span style='font-size: x-small; line-height: 14px'>" + file["ConvertProgress"] + "</span>";

        cancelStr = "";
    } else if (status == "Pending") {
        reuploadStr = "";
        progressStr = "";
    }

    var rowStr =
        "<div class='table-responsive'>" +
        "<table class='table table-bordered bg-white'>" +
        "<tbody><tr id='resumable-" + file["Identifier"] + "'>" +
        "<td class='text-center' id='" + file["Identifier"] + "-file-name'>" + file["FileName"] + "</td>" +
        "<td id='" + file["Identifier"] + "-status'>" + status + "</td>" +
        "<td id='" + file["Identifier"] + "-progress'>" + progressStr+  "</td>" +
        "<td class='td-actions text-right' id='" + file["Identifier"] + "-control'>" + reuploadStr + cancelStr + "</td>" +
        "</tr></tbody>" +
        "</table>" +
        "</div>";

    /*var rowStr = "<div id='resumable-" + file["Identifier"] + "' style='height:30px; line-height: 30px'>" +
        "<div id='" + file["Identifier"] + "-file-name' style='padding-top:5px; height: 30px; width: 420px;float: left; border: 1px solid #CCCCCC;background:" + rowColor + ";'>" + file["FileName"] + "</div>" +
        "<div id='" + file["Identifier"] + "-status' style='padding-top:5px; height: 30px;width: 150px;float: left; border: 1px solid #CCCCCC;background:" + rowColor + ";'>" + status + "</div>" +
        "<div id='" + file["Identifier"] + "-progress'  style='padding-top:5px; height: 30px;width: 400px;float: left; border: 1px solid #CCCCCC;background:" + rowColor + ";'>" +
        progressStr +
        "</div>" +

        "<div id='" + file["Identifier"] + "-control'  style='padding-top:5px; height: 30px;width: 100px;float: left; border: 1px solid #CCCCCC;background:" + rowColor + ";'>" +
        reuploadStr +
        cancelStr +
        "</div>" +
        "</div>" +
        "<div style='clear:both'></div>";*/
    $("#unfinished-files").append(rowStr);
    UpdateProgressBar(file["Progress"] / 100, file["Size"], file["Identifier"], "Uploading");
}

function ResumeUpload(identifier) {
    CreateResumableInstance(identifier);
}

function ConvertProduct(fileName, index, identifier) {

    $.ajax({
        url: "Resources/PHP/Convert.php",
        dataType: 'text',
        data: {
            filename: fileName,
            identifier: identifier
        },
        success: function (response) {
            if (response == "converted") {

                alert("Finished processing the file '" + fileName + "'");
            } else if (response == "pending") {
                alert("There is another file being processed. The file '" + fileName + "' was put into queue. Please check back later.");
            } else {
                alert(response);
            }


            GetFinishedList();
            GetUnfinishedList();

        }
    });
}

function GetFinishedList() {
    $.ajax({
        url: "Resources/PHP/GetFinishedList.php",
        dataType: 'text',
        success: function (response) {
            var data = JSON.parse(response);
            var items = "";
            $("#product-wrapper").html("");
            if (data.length > 0) {
                var table = "<table id='product-table' style='width: 100%; overflow-x: scroll;'>" +
                    "<thead>" +
                    "<tr style='background: #555555; color: #ffffff;'>" +
                    "<th style='border: none;'>&nbsp;</th>" +
                    "<th style='border: none;'>&nbsp;</th>" +
                    "<th style='border: none;'>File Name</th>" +
                    "<th style='border: none;'>Type</th>" +
                    "<th style='border: none;'>Project</th>" +
                    "<th style='border: none;'>Platform</th>" +
                    "<th style='border: none;'>Sensor</th>" +
                    "<th style='border: none;'>Date</th>" +
                    "<th style='border: none;'>Flight</th>" +
                    "<th style='border: none;'>Min Zoom</th>" +
                    "<th style='border: none;'>Max Zoom</th>" +
                    "<th style='border: none;'>Default Zoom</th>" +
                    "<th style='border: none;'>EPSG</th>" +
                    "</tr>" +
                    "</thead>" +
                    "<tbody id='product-list'>" +
                    "</tbody>" +
                    "</table>";

                $("#product-wrapper").html(table);
                $.each(data, function (index, item) {

                  // style='padding: 7px !important;'

                    items += "<tr>" +
                        "<td>" +
                        "<input style='padding: 7px !important; background: #fbbc05;' id='tms-" + item.ID + "' type='image' class='' src='Resources/Images/tms.png' alt='TMS' onclick='ViewTMS(\"" + item.TMSPath + "\"); return false;' title='TMS'>" +
                        "&nbsp;" +
                        "<input style='background: #5499ff; padding: 7px !important;' id='download-" + item.ID + "' type='image' class='' src='Resources/Images/download.png' alt='Download' onclick='ViewDownload(\"" + item.DownloadPath + "\",\"" + item.UploadFolder + "\"); return false;' title='Download'>" +
                        "</td>" +
                        "<td>" +
                        "<a class='example-image-link' href='" + item.ThumbPath + "' data-lightbox='" + item.ID + "'>" +
                        "<img class='example-image' src='" + item.ThumbPath + "' alt='" + item.Name + "' style='width:60px; height: 60px'/>" +
                        "</a>" +
                        "</td>" +
                        "<td style='overflow:hidden'>" +
                        "<span id='" + item.ID + "-display-name'>" + item.FileName + "</span>" +
                        "</td>" +
                        "<td style='overflow:hidden'>" +
                        "<span id='" + item.ID + "-display-type'>" + item.TypeName + "</span>" +
                        "</td>" +
                        "<td style='overflow:hidden'>" +
                        "<span id='" + item.ID + "-display-project'>" + item.ProjectName + "</span>" +
                        "</td>" +
                        "<td style='overflow:hidden'>" +
                        "<span id='" + item.ID + "-display-platform'>" + item.PlatformName + "</span>" +
                        "</td>" +
                        "<td style='overflow:hidden'>" +
                        "<span id='" + item.ID + "-display-sensor'>" + item.SensorName + "</span>" +
                        "</td>" +

                        "<td style='overflow:hidden'>" +
                        "<span id='" + item.ID + "-display-date'>" + item.Date.replace(/\-/g, '/') + "</span>" +
                        "<input id='" + item.ID + "-edit-date' class='edit-input' type='text' value='" + item.Name + "' style='display:none'>" +
                        "</td>" +
                        "<td style='overflow:hidden'>" +
                        "<span id='" + item.ID + "-display-flight'>" + item.FlightName + "</span>" +
                        "</td>" +
                        "<td style='overflow:hidden'>" +
                        "<span id='" + item.ID + "-display-min-zoom'>" + item.MinZoom + "</span>" +
                        "</td>" +
                        "<td style='overflow:hidden'>" +
                        "<span id='" + item.ID + "-display-max-zoom'>" + item.MaxZoom + "</span>" +
                        "</td>" +
                        "<td style='overflow:hidden'>" +
                        "<span id='" + item.ID + "-display-zoom'>" + item.Zoom + "</span>" +
                        "</td>" +
                        "<td style='overflow:hidden'>" +
                        "<span id='" + item.ID + "-display-epsg'>" + item.EPSG + "</span>" +
                        "</td>" +

                        "</tr>";

                    items += "</tr>";
                });

                $("#product-list").html(items);

                var rowHeight = 61;
                var padding = 10;
                var actualHeight = (data.length + 1) * rowHeight + padding;
                var maxHeight = 300;
                var height = actualHeight < maxHeight ? actualHeight : maxHeight;

                $('#product-table').fxdHdrCol({
                    // fixedCols:  3,
                    width: 970,
                    height: height,

                    colModal: [

                        {
                            width: 100,
                            align: 'center'
                        }, // Link & Download
                        {
                            width: 100,
                            align: 'center'
                        }, // Thumbnail
                        {
                            width: 350,
                            align: 'center'
                        }, // File Name
                        {
                            width: 100,
                            align: 'center'
                        }, // Type
                        {
                            width: 300,
                            align: 'center'
                        }, // Project
                        {
                            width: 150,
                            align: 'center'
                        }, // Platform
                        {
                            width: 100,
                            align: 'center'
                        }, // Sensor
                        {
                            width: 100,
                            align: 'center'
                        }, // Date
                        {
                            width: 100,
                            align: 'center'
                        }, // Flight
                        {
                            width: 90,
                            align: 'center'
                        }, // Min Zoom
                        {
                            width: 90,
                            align: 'center'
                        }, // Max Zoom
                        {
                            width: 90,
                            align: 'center'
                        }, // Default Zoom
                        {
                            width: 75,
                            align: 'center'
                        }, // EPSG

					],
                    sort: false
                });

            } else {
                var notifyText = "<p style='text-align:center'>No uploaded product found.</p>";
                $("#product-wrapper").html(notifyText);
            }
        }
    });
}

function CheckInput(id) {

    var isValid = true;

    var fields = [];

    fields = ['name', 'date', 'lat', 'lng'];

    if (id) {
        $.each(fields, function (index, item) {
            fields[index] = id + '-edit-' + item;
        });
    }

    $.each(fields, function (index, item) {
        if (!$('#' + item).val()) {
            $('#' + item).addClass("error");
            isValid = false;
        } else {
            $('#' + item).removeClass("error");
        }
    });

    return isValid;
}


function ViewTMS(link) {
    $("#tms-path").val(link);
    $("#dialog-tms").dialog({
        resizable: false,
        height: "auto",
        width: 400,
        modal: true,
    });
}

function Preview() {
    var link = $("#tms-path").val().replace("{z}/{x}/{y}.png", "leaflet.html");
    var win = window.open(link, '_blank');
    if (win) {
        win.focus();
    } else {
        //Browser has blocked it
        alert('Please allow popups for this website');
    }
}

function ViewDownload(downloadLink, localPath) {
    $("#download-link").val(downloadLink);
    $("#local-path").val(localPath);

    $("#dialog-download").dialog({
        resizable: false,
        height: "auto",
        width: 400,
        modal: true,
    });
}

/*
function CopyTMS(){
	var copyText = document.getElementById("tms-path");
	copyText.select();
	document.execCommand("copy");
	alert("The TMS path has been copied to clipboard");
}
*/

function CopyToClipBoard(type) {
    var textareaName = "";
    var notificationText = "";

    switch (type) {
        case "tms": {
            textareaName = "tms-path";
            notificationText = "The TMS path has been copied to clipboard";
        }
        break;

    case "link": {
        textareaName = "download-link";
        notificationText = "The download link has been copied to clipboard";
    }
    break;

    case "path": {
        textareaName = "local-path";
        notificationText = "The local path has been copied to clipboard";
    }
    break;
    }

    var copyText = document.getElementById(textareaName);
    copyText.select();
    document.execCommand("copy");
    alert(notificationText);
}

/*
function Download(link){
	var win = window.open(link, '_blank');
	if (win) {
		//Browser has allowed it to be opened
		win.focus();
	} else {
		//Browser has blocked it
		alert('Please allow popups for this website');
	}
}*/

function Download() {
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

function CenterMap(position, zoom) {
    var loc = position.split(',');
    var z = parseInt(zoom);
    map.setView(loc, z, {
        animation: true
    });
}

function ChangeLocation() {
    var lat = $('#lat').val();
    var lng = $('#lng').val();
    var latlng = new L.LatLng(lat, lng);
    marker.setLatLng(latlng);
    CenterMap(lat + "\," + lng, map.getZoom());
}


function SetFlight(project, platform, sensor, date, flight) {
    $.ajax({
        url: "Resources/PHP/List.php",
        data: {
            type: "date",
            project: project,
            platform: platform,
            sensor: sensor,
        },
        dataType: 'text',
        success: function (response) {
            var items = "";
            var data = JSON.parse(response);
            if (data.length > 0) {
                $.each(data, function (index, item) {
                    items += "<option value='" + item.ID + "'>" + item.Name + "</option>";
                });

                $("#date").html(items);

                $("#date option").filter(function () {
                    return $(this).text() == date;
                }).prop('selected', true);
                GetList("flight");
                setTimeout(function () {
                    $("#flight").val(flight);
                }, 500);

            }
        }
    });

}


function ShowAddFlight() {
    $("#flight-project").val($("#project option:selected").text());
    $("#flight-platform").val($("#platform option:selected").text());
    $("#flight-sensor").val($("#sensor option:selected").text());
    $("#add-flight").show();
    $("#upload").hide();
}

function ShowUpload() {
    $("#add-flight").hide();
    $("#upload").show();
    CheckUploadCondition();
}

function AddFlight() {
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
        data: {
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
        success: function (response) {
            SetFlight(project, platform, sensor, date, response);

        }
    });
    ShowUpload();

}


function SendEmail(identifier) {

    $.ajax({
        url: "Resources/PHP/Email.php",
        data: {
            identifier: identifier
        },
        dataType: "text",
        success: function (response) {},
        error: function (xhr) {
            alert('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
            $("#processing").hide();
        }
    });
}
