var selectedPage = -1;
var selectedPageName = "";
var selectedGroups = new Array();


$(document).ready(function(){
	//GetPageList();
	//GetGroupList();
	
	$("#page-search").on('keyup',function () {
		$("#page-list li").each(function(){
			var filter = $("#page-search").val().toUpperCase();
			if ($(this).text().toUpperCase().indexOf(filter) > -1) {
				$(this).show();
			} else {
				$(this).hide();
			}
		});
		
	});
	
	$("#group-search").on('keyup',function () {
		$("#group-list li").each(function(){
			var filter = $("#group-search").val().toUpperCase();
			if ($(this).text().toUpperCase().indexOf(filter) > -1) {
				$(this).show();
			} else {
				$(this).hide();
			}
		});
		
	});
	
	$(":button").mouseup(function(){
		$(this).blur();
	})
	
});


// function GetPageList(){
// 	$.ajax({
// 		url: "Resources/PHP/GetPageList.php",
// 		dataType: "text",
// 		success: function(response) {
// 			var data = JSON.parse(response);
// 			var items = "";
// 			//$("#page-list-wrapper").html("");
// 			$("#page-list").html("");
//
// 			if (data.length > 0)
// 			{
//
// 				$.each(data,function(index,item){
// 					var page = "<li id='page-" + index + "' class='page-item' onclick='SelectPage(" + index + ",\"" +  item + "\");'>" +
// 									item +
// 								"</li>";
// 					$('#page-list').append(page);
// 					if (index == 0){
// 						SelectPage(index, item);
// 					}
// 				});
// 			}
// 		}
// 	});
// }


// function GetGroupList(){
//
// 	$.ajax({
// 		url: "Resources/PHP/GetGroupList.php",
// 		dataType: "text",
// 		success: function(response) {
// 			var data = JSON.parse(response);
// 			var items = "";
// 			$("#group-list").html("");
//
// 			if (data.length > 0)
// 			{
// 				$.each(data,function(index,item){
// 					var lock = "";
// 					if (item.role_name.toLowerCase().indexOf("admin") >= 0){
// 						lock = " style = 'background-color:#3297FD ' onclick='this.checked=!this.checked;'";
// 						selectedGroups.push(item.role_name);
// 					}
// 					// var page = "<li id='group-" + item.ID + "' class='page-item' onclick='ToggleGroup(" + item.ID + ",\"" +  item.Name + "\");' " + lock + ">" +
// 					// 				item.Name +
// 					var page = "<li id='group-" + item.role_id + "' class='page-item' onclick='ToggleGroup(" + item.role_id + ",\"" +  item.role_name + "\");' " + lock + ">" +
// 						item.role_name +
// 						"</li>";
// 					$('#group-list').append(page);
// 				});
// 			}
// 		}
// 	});
// }

function SelectPage(index, name){
	//Unselect the previous selected page
	$("#page-" + selectedPage).css("background-color","#F0F0F0");
	
	//Select the current selected new page
	$("#page-" + index).css("background-color","#3297FD");
	selectedPage = index;
	selectedPageName = name;
	ShowPageAccess(name);
}

/*
function TogglePage(index, name){
	
	if ($.inArray(name, selectedPages) == -1){
		selectedPages.push(name);
		$("#page-" + index).css("background-color","#3297FD");
	} else {
		selectedPages = jQuery.grep(selectedPages, function(value) {
			return value != name;
		});
		$("#page-" + index).css("background-color","#F0F0F0");
	}
	
	if (selectedPages.length == 1){
		ShowPageAccess(name);
	} else {
		ResetPageAccess();
	}
}
*/

function ToggleGroup(id, name){
	if (name.toLowerCase().indexOf("admin") >= 0){
		return;
	}

	if ($.inArray(name, selectedGroups) == -1){
		selectedGroups.push(name);
		$("#group-" + id).css("background-color","#3297FD");
	} else {
		selectedGroups = jQuery.grep(selectedGroups, function(value) {
			return value != name;
		});
		$("#group-" + id).css("background-color","#F0F0F0");
	}
}


function Apply(){
	$.ajax({
		url: "Resources/PHP/SetPageAccess.php",
		dataType: "text",
		data:{
			SelectedPage: selectedPageName,
			SelectedGroups: selectedGroups
		},
		success: function(response) {
			if (response == 1){
				alert("Page access has been set successfully.");
			}
			// else {
			// 	alert("Failed to set page access.");
			// }

		}
	});
}

function ShowPageAccess(name){
	ResetPageAccess();
	$.ajax({
		url: "Resources/PHP/GetPageAccess.php",
		dataType: "text",
		data:{
			name: name
		},
		success: function(response) {
			var accessGroups = response.split(";");
			var groupItems = $("#group-list li");
			
			$.each(groupItems,function(index,item){
				var group = $(item);
				if ($.inArray(group.text(), accessGroups) !== -1 && group.text().toLowerCase().indexOf("admin") < 0) {
					group.css("background-color","#3297FD");
					selectedGroups.push(group.text());
				}
			});
				
			
		}
	});
}


function ResetPageAccess(){
	selectedGroups = [];		
	var groupItems = $("#group-list li");
			
	$.each(groupItems,function(index,item){
		var group = $(item);
		if (group.text().toLowerCase().indexOf("admin") >= 0) {
			group.css("background-color","#3297FD");
			selectedGroups.push(group.text());
		} else {
			group.css("background-color","#F0F0F0");
		}
	});
}

/*
function UnselectAllPages(){
	selectedPages = [];		
	var pageItems = $("#page-list li");
			
	$.each(pageItems,function(index,item){
		var page = $(item);
		page.css("background-color","#F0F0F0");
	});
}
*/