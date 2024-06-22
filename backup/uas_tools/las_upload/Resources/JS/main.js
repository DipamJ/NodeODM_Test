var resumableInstances = new Array();
//var uploadingList = "";
var rowColor = "#fff";
var marker;

$(document).ready(function () {
    GetProjectList();
    GetUnfinishedList("Update");

    setTimeout(function () {

        GetLASList();

    }, 500); //7.5 seconds

    //	GetLASList();
    $("#date").datepicker();
    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth() + 1; //January is 0!

    var yyyy = today.getFullYear();
    if (dd < 10) {
        dd = '0' + dd
    }
    if (mm < 10) {
        mm = '0' + mm
    }
    today = mm + '/' + dd + '/' + yyyy;

    $('#date').val(today);


    window.setInterval(function () {
        GetUnfinishedList("Update");
    }, 10000);



});

function GetProjectList() {
    $.ajax({
        url: "Resources/PHP/Project.php",
        dataType: 'text',
        data: {
            action: 'list'
        },
        success: function (response) {
            var project = "<option value='0' lat='0' lng='0'>--Select Project--</option>";
            $('#project').append(project);

            var data = JSON.parse(response);
            if (data.length > 0) {

                $.each(data, function (index, item) {

                    //var project = "<option value='" + item.ID + "' >" + item.Name + "</option>";
                    var project = "<option value='" + item.ID + "' lat='" + item.CenterLat + "' lng='" + item.CenterLng + "'>" + item.Name + "</option>";
                    $('#project').append(project);

                });

            }

            $("#project").change(function () {
                var lat = this.options[this.selectedIndex].getAttribute("lat");
                var lng = this.options[this.selectedIndex].getAttribute("lng");
                if (lat != 0 && lng != 0) {
                    $("#lat").val(lat);
                    $("#lng").val(lng);
                    ChangeLocation();
                }

            });
        }
    });
}


//function CreateResumableInstance(){
function CreateResumableInstance() {


    var count = resumableInstances.length;
    var resumableStr = "<a href='#' id='browsebutton-" + count + "'><img src='Resources/Images/upload.png'></a>";

    $("#resumable-list").append(resumableStr);

    var targetStr = "Resources/PHP/Upload.php?name=" + $("#name").val() +
        "&project=" + $("#project").val() +
        "&description=" + $("#description").val() +
        "&date=" + $("#date").val() +
        "&lat=" + $("#lat").val() +
        "&lng=" + $("#lng").val();
    var r = new Resumable({
        target: targetStr,
    });


    r.assignBrowse(document.getElementById("browsebutton-" + count));
    $("#browsebutton-" + count + " > input[type=file]").attr("accept", ".las");

    var currentProgress = 0;
    r.on('fileAdded', function (data) {
        $.ajax({
            url: "Resources/PHP/CheckFileStatus.php",
            dataType: 'text',
            data: {
                identifier: r.files[0].uniqueIdentifier
            },
            success: function (response) {

                var file = JSON.parse(response);
                var name = $("#name").val();


                if (file == null || file["Status"] != "Finished") {
                    if (file) {
                        currentProgress = file["Progress"] / 100;
                        UpdateStatus("Resume", file["Identifier"], currentProgress);
                        GetUnfinishedList("List");
                        name = file["Name"];
                        if (file["Status"] == "Uploading") {
                            alert("The file is being uploaded by another user.");
                            return;
                        }
                    }
                    var fileName = data.fileName.replace(/[^a-z0-9.\s]/gi, '').replace(/[_\s]/g, '_').replace(/_+/gi, '_');
                    r.upload();

                    var rowStr = "<div id='resumable-" + count + "' style='height:30px; line-height: 30px'>" +
                        "<div id='" + count + "-name' style='padding-top:5px; height: 30px ;width: 200px;float: left; border: 1px solid #CCCCCC;background:" + rowColor + ";'>" + name + "</div>" +
                        "<div id='" + count + "-file-name' style='padding-top:5px; height: 30px ;width: 200px;float: left; border: 1px solid #CCCCCC;background:" + rowColor + ";'>" + fileName + "</div>" +
                        "<div id='" + count + "-status' style='padding-top:5px; height: 30px ;width: 200px;float: left; border: 1px solid #CCCCCC;background:" + rowColor + ";'>Uploading</div>" +
                        "<div id='" + count + "-progress'  style='padding-top:5px; height: 30px ;width: 320px;float: left; border: 1px solid #CCCCCC;background:" + rowColor + ";'>" +
                        "<div class='progress-bar'>" +
                        "<img id='progress-bar-" + count + "' class='progress-bar-image' src='Resources/Images/ProgressBar.jpg'>" +
                        "<div id='progress-text-" + count + "' class='progress-text'></div>" +
                        "</div>" +
                        "</div>" +
                        "<div id='" + count + "-control'  style='padding-top:5px; height: 30px ;width: 50px;float: left; border: 1px solid #CCCCCC;background:" + rowColor + ";'>" +
                        "<img id='pause-resume-" + count + "'  style='cursor:pointer; margin: 0 5px' src='Resources/Images/pause.png' alt='Pause' title='Pause' height='24' width='24' " +
                        "onclick='PauseResume(\"" + count + "\",\"" + r.files[0].uniqueIdentifier + "\"," + currentProgress + ")'>" +
                        "<img id='processing-" + count + "'  src='Resources/Images/processing.gif' alt='Processing' style='display:none' title='Pause' height='24' width='24' >" +
                        "<a id='display-" + count + "' href='#' style='display:none' target='_blank'>View</a>" +
                        "</div>" +

                        "</div>";

                    $("#upload-files").append(rowStr);
                    var totalSize = Math.floor(data.size / 1024);
                    $("#total-size-" + count).text(totalSize + "KB");
                } else {
                    alert("The same file was already uploaded.");
                    // Added
                    $("#loading").hide();
                }

            }
        });



    });


    r.on('fileProgress', function (data) {
        var type = "Uploading";
        var progress = currentProgress;
        if (r.progress() > currentProgress) {
            progress = r.progress();
        }
        console.log(data);
        UpdateProgressBar(progress, data.size, count, type);

        if (r.progress() == 1) {
            $("#pause-resume-" + count).hide();
            $("#processing-" + count).show();
            $("#" + count + "-status").html("Processing");
            //ConvertPointcloud(data.fileName,$("#name").val(), count, r.files[0].uniqueIdentifier);
            ConvertPointcloud(r.files[0].uniqueIdentifier, count);
        }
    });

    resumableInstances.push(r);

    $("#browsebutton-" + count)[0].click();

}

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

    GetUnfinishedList("List");
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

function Remove(index) {
    resumableInstances[index].cancel();
    $("#resumable-" + index).remove();
}

function GetUnfinishedList(type) {
    $.ajax({
        url: "Resources/PHP/CheckUpload.php",
        dataType: 'text',
        data: {
            type: type
        },
        success: function (response) {
            var data = JSON.parse(response);
            if (data.length > 0) {
                $("#unfinished-files").html("");

                $.each(data, function (index, item) {

                    if (resumableInstances.length == 0) {
                        AppendUploadingFileList(item);
                    } else {
                        var valid = 1;
                        $.each(resumableInstances, function (i, r) {
                            if (r.files[0]) {
                                if (r.files[0].uniqueIdentifier == item["Identifier"]) {
                                    valid = 0;
                                }
                            }
                        });

                        if (valid == 1) {
                            AppendUploadingFileList(item);
                        }
                    }
                });

            }
        }
    });

}

function AppendUploadingFileList(file) {

    var status = file["Status"];
    var reuploadStr = "<img id='pause-resume-" + file["Identifier"] + "'  style='cursor:pointer; margin: 0 5px' src='Resources/Images/upload.png' alt='Pause' title='Pause' height='24' width='24'  onclick='ResumeUpload(\"" + file["Identifier"] + "\")'>";
    if (status == "Uploading") {
        status += " (other)";
        reuploadStr = "";
    }
    var rowStr = "<div id='resumable-" + file["Identifier"] + "' style='height:30px; line-height: 30px'>" +
        "<div id='" + file["Identifier"] + "-name' style='padding-top:5px; height: 30px ;width: 200px;float: left; border: 1px solid #CCCCCC;background:" + rowColor + ";'>" + file["Name"] + "</div>" +
        "<div id='" + file["Identifier"] + "-file-name' style='padding-top:5px; height: 30px; width: 200px;float: left; border: 1px solid #CCCCCC;background:" + rowColor + ";'>" + file["FileName"] + "</div>" +
        "<div id='" + file["Identifier"] + "-status' style='padding-top:5px; height: 30px;width: 200px;float: left; border: 1px solid #CCCCCC;background:" + rowColor + ";'>" + status + "</div>" +
        "<div id='" + file["Identifier"] + "-progress'  style='padding-top:5px; height: 30px;width: 320px;float: left; border: 1px solid #CCCCCC;background:" + rowColor + ";'>" +
        "<div class='progress-bar'>" +
        "<img id='progress-bar-" + file["Identifier"] + "' class='progress-bar-image' src='Resources/Images/ProgressBar.jpg'>" +
        "<div id='progress-text-" + file["Identifier"] + "' class='progress-text'></div>" +
        "</div>" +
        "</div>" +

        "<div id='" + file["Identifier"] + "-control'  style='padding-top:5px; height: 30px;width: 50px;float: left; border: 1px solid #CCCCCC;background:" + rowColor + ";'>" +
        reuploadStr +
        "</div>" +

        "</div>" +
        "<div style='clear:both'></div>";
    $("#unfinished-files").append(rowStr);
    UpdateProgressBar(file["Progress"] / 100, file["Size"], file["Identifier"], "Uploading");
}

function ResumeUpload(identifier) {
    CreateResumableInstance();

    /*
    $.ajax({
    	url: "Resources/PHP/CheckFileStatus.php",
    	dataType: 'text',
    	data: { identifier: identifier},
    	success: function(response) {
    		var file = JSON.parse(response);
    		if (file["Status"] == "Uploading"){
    			alert("The file is being uploaded by another user.");
    		} else if (file["Status"] == "Unfinished"){
    			CreateResumableInstance();
    			//$("#browsebutton-0")[0].click();
    		}
    	}
    });
    */
}

//function ConvertPointcloud(fileName, displayName, index, identifier){
function ConvertPointcloud(identifier, index) {
    $.ajax({
        url: "Resources/PHP/Convert.php",
        dataType: 'text',
        data: {
            //filename: fileName,
            //displayname: displayName,
            identifier: identifier
        },
        success: function (response) {
            //if (response == "1"){
            // 	$("#resumable-" + index).remove();
            // 	GetLASList();
            //}
            // Reload the page after converting
            //location.reload();
            alert('Conversion has been completed');
            //GetLASList();
            location.reload();
        }

    });
}

function GetLASList() {

    $.ajax({
        url: "Resources/PHP/Pointcloud.php",
        dataType: 'text',
        data: {
            action: 'list'
        },
        success: function (response) {
            var data = JSON.parse(response);
            var items = "";
            if (data.length > 0) {
                var table = "<table id='las-table'>" +
                    "<thead>" +
                    "<tr>" +
                    "<th>&nbsp;</th>" +
                    "<th>Name</th>" +
                    "<th>Project</th>" +
                    "<th>Date</th>" +
                    "<th>Description</th>" +
                    "<th>Lat</th>" +
                    "<th>Lng</th>" +
                    "</tr>" +
                    "</thead>" +
                    "<tbody id='las-list'>" +
                    "</tbody>" +
                    "</table>";

                $("#las-wrapper").html(table);
                $.each(data, function (index, item) {
                    var projectName = $("#project option[value='" + item.Project + "']").text();
                    /*image-button edit-button
                    image-button confirm-edit-button
                    image-button cancel-edit-button
                    image-button delete-button
                    image-button confirm-delete-button
                    image-button cancel-delete-button
                    image-button view-button
                    image-button download-button
                    */
                    items += "<tr>" +
                        "<td>" +
                        "<input style='padding: 7px !important; background: #f0ad4e; margin-right: 3px;' id='edit-" + item.ID + "' type='image' class='' src='Resources/Images/edit.png' alt='Edit' onclick='Edit(" + item.ID + "); return false;' title='Edit'>" +

                        "<input style='padding: 6px; background: #449d44; margin-right: 3px;display:none' id='confirmEdit-" + item.ID + "' type='image' class='' src='Resources/Images/confirm.png' alt='Confirm' onclick='ConfirmEdit(" + item.ID + "); return false;' title='Confirm'>" +

                        "<input style='padding: 6px; background: #ccc; margin-right: 3px;display:none' id='cancelEdit-" + item.ID + "' type='image' class='' src='Resources/Images/cancel.png' alt='Cancel' onclick='CancelEdit(" + item.ID + "); return false;' title='Cancel'>" +

                        "<input style='padding: 6px !important; background: #d9534f; margin-right: 3px;' id='delete-" + item.ID + "' type='image' class='' src='Resources/Images/delete.png' alt='Delete' onclick='Delete(" + item.ID + "); return false;' title='Delete'>" +

                        "<input style='padding: 6px; background: #d9534f; margin-right: 3px;display:none' id='confirmDelete-" + item.ID + "' type='image' class='' src='Resources/Images/confirm.png' alt='Confirm' onclick='ConfirmDelete(" + item.ID + "); return false;' title='Confirm'>" +

                        "<input style='padding: 6px; background: #ccc; margin-right: 3px; display:none' id='cancelDelete-" + item.ID + "' type='image' class='' src='Resources/Images/cancel.png' alt='Cancel' onclick='CancelDelete(" + item.ID + "); return false;' title='Cancel'>" +

                        "<input style='padding: 6.5px !important; background: #449d44; margin-right: 3px;' id='view-" + item.ID + "' type='image' class='' src='Resources/Images/view.png' alt='View' onclick='View(\"" + item.DisplayPath + "\"); return false;' title='View'>" +

                        "<input style='padding: 6.5px !important; background: #5599ff;' id='download-" + item.ID + "' type='image' class='' src='Resources/Images/download.png' alt='View' onclick='Download(\"" + item.DownloadPath + "\"); return false;' title='Download'>" +
                        "</td>" +

                        "<td style='overflow:hidden'>" +
                        "<span id='" + item.ID + "-display-name'>" + item.Name + "</span>" +
                        "<input id='" + item.ID + "-edit-name' class='edit-input' type='text' value='" + item.Name + "' style='display:none'>" +
                        "</td>" +
                        "<td style='overflow:hidden'>" +
                        //"<span id='" + item.ID +"-display-project'>" + item.ProjectName + "</span>" +
                        "<span id='" + item.ID + "-display-project'>" + projectName + "</span>" +
                        "<select id='" + item.ID + "-edit-project' class='edit-input' style='display:none'></select>" +
                        "</td>" +
                        "<td style='overflow:hidden'>" +
                        "<span id='" + item.ID + "-display-date'>" + item.Date.replace(/\-/g, '/') + "</span>" +
                        "<input id='" + item.ID + "-edit-date' class='edit-input' type='text' value='" + item.Name + "' style='display:none'>" +
                        "</td>" +
                        "<td style='overflow:hidden'>" +
                        "<span id='" + item.ID + "-display-description'>" + item.Description + "</span>" +
                        "<input id='" + item.ID + "-edit-description' class='edit-input' type='text' value='" + item.Name + "' style='display:none'>" +
                        "</td>" +
                        "<td style='overflow:hidden'>" +
                        "<span id='" + item.ID + "-display-lat'>" + item.Lat + "</span>" +
                        "<input id='" + item.ID + "-edit-lat' class='edit-input' type='text' value='" + item.Name + "' style='display:none'>" +
                        "</td>" +
                        "<td style='overflow:hidden'>" +
                        "<span id='" + item.ID + "-display-lng'>" + item.Lng + "</span>" +
                        "<input id='" + item.ID + "-edit-lng' class='edit-input' type='text' value='" + item.Name + "' style='display:none'>" +
                        "</td>" +

                        "</tr>";



                    items += "</tr>";
                });

                $("#las-list").html(items);

                var rowHeight = 41;
                var padding = 10;
                var actualHeight = (data.length + 1) * rowHeight + padding;
                var maxHeight = 300;
                var height = actualHeight < maxHeight ? actualHeight : maxHeight;

                $('#las-table').fxdHdrCol({
                    // fixedCols: 2,
                    width: 1000,
                    height: height,

                    colModal: [

                        {
                            width: 200,
                            align: 'center'
                        }, // Edit & Delete & Link & Download
                        {
                            width: 200,
                            align: 'center'
                        }, // Name
                        {
                            width: 300,
                            align: 'center'
                        }, // Project
                        {
                            width: 100,
                            align: 'center'
                        }, // Date
                        {
                            width: 300,
                            align: 'center'
                        }, // Description
                        {
                            width: 75,
                            align: 'center'
                        }, // Lat
                        {
                            width: 75,
                            align: 'center'
                        }, // Long

					],
                    sort: false
                });

            }
        }
    });
}

function Edit(id) {
    //alert(type);
    $('#confirmEdit-' + id).show();
    $('#cancelEdit-' + id).show();
    $('#edit-' + id).hide();

    $('[id^="' + id + '-display"]').hide();
    $('[id^="' + id + '-edit"]').show();

    $('#' + id + '-edit-name').val($('#' + id + '-display-name').html());
    $('#' + id + '-edit-project').html($('#project').html());
    $('#' + id + '-edit-project option').filter(function () {
        return $(this).text() == $('#' + id + '-display-project').html();
    }).prop('selected', true);
    $('#' + id + '-edit-date').val($('#' + id + '-display-date').html());
    $('#' + id + '-edit-date').datepicker();
    $('#' + id + '-edit-description').val($('#' + id + '-display-description').html());
    $('#' + id + '-edit-lat').val($('#' + id + '-display-lat').html());
    $('#' + id + '-edit-lng').val($('#' + id + '-display-lng').html());

    CancelDelete(id);
}

function CancelEdit(id) {
    $('#confirmEdit-' + id).hide();
    $('#cancelEdit-' + id).hide();
    $('#edit-' + id).show();

    $('[id^="' + id + '-display"]').show();
    $('[id^="' + id + '-edit"]').hide();

}

function ConfirmEdit(id) {

    if (CheckInput(id)) {

        var name = $('#' + id + '-edit-name').val();
        var project = $('#' + id + '-edit-project').val();
        var date = $('#' + id + '-edit-date').val();
        var description = $('#' + id + '-edit-description').val();
        var lat = $('#' + id + '-edit-lat').val();
        var lng = $('#' + id + '-edit-lng').val();



        $.ajax({
            url: "Resources/PHP/Pointcloud.php",
            dataType: 'text',
            data: {
                id: id,
                name: name,
                project: project,
                date: date,
                description: description,
                lat: lat,
                lng: lng,
                action: "edit"
            },
            success: function (response) {
                if (response == "1") {

                    $('#confirmEdit-' + id).hide();
                    $('#cancelEdit-' + id).hide();
                    $('#edit-' + id).show();

                    $('[id^="' + id + '-display"]').show();
                    $('[id^="' + id + '-edit"]').hide();

                    $('#' + id + '-display-name').html($('#' + id + '-edit-name').val());
                    $('#' + id + '-display-project').html($('#' + id + '-edit-project').text());
                    $('#' + id + '-display-date').html($('#' + id + '-edit-date').val());
                    $('#' + id + '-display-description').html($('#' + id + '-edit-description').val());
                    $('#' + id + '-display-lat').html($('#' + id + '-edit-lat').val());
                    $('#' + id + '-display-lng').html($('#' + id + '-edit-lng').val());

                    alert("The pointcloud has been updated.");
                    GetLASList();

                } else {
                    alert("Could not update the " + type + " name. Error: " + response + ".");
                }
            }
        });
    } else {
        alert('Please fill in all required fields');
    }
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

function Delete(id) {
    $('#confirmDelete-' + id).show();
    $('#cancelDelete-' + id).show();
    $('#delete-' + id).hide();
    CancelEdit(id);
}

function CancelDelete(id) {
    $('#confirmDelete-' + id).hide();
    $('#cancelDelete-' + id).hide();
    $('#delete-' + id).show();
}

function ConfirmDelete(id) {
    $.ajax({
        url: "Resources/PHP/Pointcloud.php",
        dataType: 'text',
        data: {
            id: id,
            action: 'delete'
        },
        success: function (response) {
            if (response == "1") {
                $('#confirmDelete-' + id).hide();
                $('#cancelDelete-' + id).hide();
                $('#delete-' + id).show();

                alert("The pointcloud has been deleted.");

                GetLASList();

            } else {
                alert("Could not delete the pointcloud. Error: " + response + ".");
            }
        }
    });
}

function View(link) {
    var win = window.open(link, '_blank');
    if (win) {
        //Browser has allowed it to be opened
        win.focus();
    } else {
        //Browser has blocked it
        alert('Please allow popups for this website');
    }
}

function Download(link) {
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
