var data = "";
var numPerPage = 20;
var maxPage = 0;
var currentPage = 1;
var startingValueField = "";

$(document).ready(function(){
	//GetImportedData(5);
	$("#imported-file").on('change', function() {
		$("#imported-field-info").hide();
		$("#imported-data-info").hide();
		$("#select").prop('disabled', true);
		$("#import").prop('disabled', true);
		GetDataSetInfo();
	});
	
	$("#crop").on('keyup',function () {
		CheckInput();
	});
	
	$("#type").on('keyup',function () {
		CheckInput();
	});
	
	$("#year").on('keyup',function () {
		CheckInput();
	});
	
	$("#location").on('keyup',function () {
		CheckInput();
	});
	
	$('#page').on('change', function() {
		ChangePage();
	});
	
	$('#row-per-page').on('change', function() {
		ChangeRowPerPage();
	});
			
	$(":button").mouseup(function(){
		$(this).blur();
	})		
	
});

function GetDataSetInfo(){
	var file_data = $("#imported-file").prop("files")[0]; //Get the file for upload   
	var nameParts = file_data.name.replace(".csv", "").split("_");
	
	var year = GetYear(nameParts);
	$("#year").val(year);
	$("#year").prop('disabled', false);
	
	var location = GetLocation(nameParts);
	$("#location").val(location);
	$("#location").prop('disabled', false);
	
	var crop = GetCrop(nameParts);
	$("#crop").val(crop);
	$("#crop").prop('disabled', false);
		
	var type = GetType(nameParts);
	$("#type").val(type);
	$("#type").prop('disabled', false);
	
	var season = GetSeason(nameParts);
	$("#season").val(season);
	$("#season").prop('disabled', false);
	
	var subLocation = GetSubLocation(nameParts);
	$("#sublocation").val(subLocation);
	$("#sublocation").prop('disabled', false);
	
	CheckInput();
}

function GetYear(filenameParts){
	var year = 0;
	$.each(filenameParts,function(index,part){
		if (parseInt(part) > 2000){
			year = part;
		}
	});
	
	return year; //no year found
}

function GetLocation(filenameParts){
	var location = "";
	var fileName = filenameParts.join("_");
	var cc = ["CORPUS CHRISTI", "CORPUS_CHRISTI", "CORPUSCHRISTI", "CORPUS-CHRISTI"];
	$.each(cc,function(index,name){
		if (fileName.toUpperCase().indexOf(name) != -1){
			location = "Corpus Christi";
		}
	});
	
	var cs = ["COLLEGE STATION","COLLEGE_STATION","COLLEGESTATION","COLLEGE-STATION"];
	$.each(cs,function(index,name){
		if (fileName.toUpperCase().indexOf(name) != -1){
			location = "College Station";
		}
	});
	
	var locationList = ["CASTROVILLE", "AMARILLO", "EOFSAC", "WESLACO", "BEEVILLE", "LUBBOCK"];
	$.each(filenameParts,function(index,part){
		var fileLocation = part.toUpperCase();
		fileLocation = fileLocation.replace(/[\+\-\_]/g," ");
		if (locationList.indexOf(fileLocation) != -1){
			location = fileLocation.substr(0,1).toUpperCase() + fileLocation.substr(1).toLowerCase();
		}
	});
	
	if (location == ""){
		$.each(filenameParts,function(index,part){
			if (part.length == 2){
				location = part.toUpperCase();
			}
		});
	}
	
	return location; 
}

function GetCrop(filenameParts){
	var cropList = ["TOMATO", "COTTON", "SORGHUM", "CORN", "WHEAT", "RICE",  "CANOLA", "SPINACH", "POTATO"];
	var crop = "";
	$.each(filenameParts,function(index,part){
		if (cropList.indexOf(part.toUpperCase()) != -1){
			crop = part.substr(0,1).toUpperCase() + part.substr(1).toLowerCase();
		}
	});
	
	return crop ; 
}

function GetType(filenameParts){
	var typeList = ["CANOPY", "COVER", "HEIGHT", "VOLUME", "EXG", "NDVI",  "MASKED", "MEAN", "SUM", "AREA","MAX", "95"];
	var type = "";
	$.each(filenameParts,function(index,part){
		if (typeList.indexOf(part.toUpperCase()) != -1){
			var typePart = part.substr(0,1).toUpperCase() + part.substr(1).toLowerCase();
			if (part.toUpperCase() == "EXG" || part.toUpperCase() == "NDVI"){
				typePart = part.toUpperCase();
			}
			
			type += typePart + " ";
		}
	});
	
	return type.slice(0,-1) ; //remove last space and return type
}

function GetSeason(filenameParts){
	var seasonList = ["SPRING", "WINTER", "SUMMER", "FALL"];
	var season = "";
	$.each(filenameParts,function(index,part){
		if (seasonList.indexOf(part.toUpperCase()) != -1){
			season = part.substr(0,1).toUpperCase() + part.substr(1).toLowerCase();
		}
	});
	
	return season; 
}


function GetSubLocation(filenameParts){
	var subLocationList = ["NORTH", "SOUTH", "WEST", "EAST"];
	var subLocation = "";
	$.each(filenameParts,function(index,part){
		if (subLocationList.indexOf(part.toUpperCase()) != -1){
			subLocation = part.substr(0,1).toUpperCase() + part.substr(1).toLowerCase();
		}
	});
	
	return subLocation; 
}

function CheckInput(){
	var crop = $("#crop").val();
	var type = $("#type").val(); 
	var year = $("#year").val();
	var location = $("#location").val();
	var season = $("#season").val();
	var subLocation = $("#sublocation").val();
	
	if (crop != "" && type != "" && year != "" && location != ""){
		//$("#select").prop('disabled', false);
		//return true;
		
		
		$.ajax({
			url: "Resources/PHP/CheckInput.php",
			dataType: 'text',
			data: {
				crop: crop,
				type: type,
				year: year,
				location: location,
				season: season,
				sublocation: subLocation
			},                         
			success: function(response) {
				if (response == "OK"){
					$("#select").prop('disabled', false);
				} else {
					alert("The same crop data set has already been imported. Please see the imported data below.");
					$("#select").prop('disabled', true);
					var id = parseInt(response);
					if (id){
						GetImportedData(id);
						
					}
				}
			},
			error: function (request, status, error) {
				alert(error + ". Could not check the file");
				$("#select").prop('disabled', true);
			}
		});
		
	} else {
		$("#select").prop('disabled', true);
		return false;
	}
}

function Select(){
	$("#processing").show();
	var file_data = $("#imported-file").prop("files")[0]; //Get the file for upload 
	if (!file_data){ //if there is no file selected, abort
		alert("No file selected.");
		return;
	} 
	
	var form_data = new FormData(); //Create a new form data for uploading                 
	form_data.append("file", file_data);
	
	$("#imported-field-wrapper").html("");
		
	$.ajax({
		url: "Resources/PHP/Select.php",
		dataType: 'text',
		cache: false,
		contentType: false,
		processData: false,
		data: form_data,                         
		type: "post", 
		success: function(response) {
			if (response == "failed"){
				alert("Could not get the fields. Please try again.");
				$("#imported-field-info").hide();
			} else {
			
				var data = JSON.parse(response);
				if (data.length > 0)
				{
					var items = "<div style='width:" + (data.length + 1) * 150 + "px;'>";
				
					$.each(data,function(index,item) 
					{
						items+= "<div id='field-" + FormatString(item) + "' onclick='SelectField(\"" + item + "\"); return false;' class='field-wrapper bg-color-dark text-white'> "  + item + "</div>";
					});
					items += "</div>"; 
					
					$("#imported-field-wrapper").html(items);
					
				}
			
				$("#processing").hide();
				$("#imported-field-info").show();
			}
		},
		error: function (request, status, error) {
			alert(error + ". Please try again.");
			$("#processing").hide();
			$("#imported-field-info").hide();
		}
	});
}

function  FormatString(rawString){
	return rawString.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
} 

function SelectField(field){
	startingValueField = field;
	
	$("[id^=field-]").css("background-color","#000");
	$("#field-"+ FormatString(field)).css("background-color","#a6a5ea");
	
	$("#import").prop('disabled', false);
}

function Import(){
	$("#processing").show();
	var file_data = $("#imported-file").prop("files")[0]; //Get the file for upload 
	if (!file_data){ //if there is no file selected, abort
		alert("No file selected.");
		return;
	} 
	
	var crop = $("#crop").val();
	var type = $("#type").val();
	var year = $("#year").val();
	var season = $("#season").val();
	var location = $("#location").val();
	var subLocation = $("#sublocation").val();
	var url = "Resources/PHP/Import.php?crop=" + crop + "&type=" + type + "&year=" + year + "&startingvaluefield=" + startingValueField + 
									  "&season=" + season + "&location=" + location + "&sublocation=" + subLocation; 	
	
	var form_data = new FormData(); //Create a new form data for uploading                 
	form_data.append("file", file_data);
	
	$.ajax({
		url: url,
		dataType: 'text',
		cache: false,
		contentType: false,
		processData: false,
		data: form_data,                         
		type: "post", 
		success: function(response) {
			if (response.indexOf("Imported") >= 0){
				alert("The data file has been imported successfully.");
				GetImportedData(response.replace("Imported ",""));
				$("#processing").show();
				$("#imported-data-info").show();
				$("#select").prop('disabled', true);
				$("#import").prop('disabled', true);
			} else {
				alert("Could not import data. Please check the file and try again.");
				$("#processing").hide();
				$("#imported-data-info").hide();
			}
		},
		error: function (request, status, error) {
			alert(error + ". Please try again.");
			$("#processing").hide();
			$("#imported-data-info").hide();
		}
	});
}

function GetImportedData(datasetID){
	$("#imported-list-wrapper").html("");
	$("#page-control").hide();
	$.ajax({
		url: "Resources/PHP/GetImportedData.php",
		data: {dataset: datasetID},                         
		success: function(response) {
			
			data = JSON.parse(response);
			
			var items = "";
			if (data.length > 0)
			{
				
				maxPage = Math.ceil( data.length / numPerPage );
				$("#page-num").html(maxPage);
				
				currentPage = 1;
				$("#page").val(currentPage);
				$("#page-control").show();
				if (maxPage > 1){
					$("#next").show();
				} else {
					$("#next").hide();
				}
				ShowTable();
			}
			
		}
	});
}

//Show data table for the current page
function ShowTable(){
	$("#processing").show();
	
	var start = (currentPage - 1) * numPerPage;
	var end = currentPage * numPerPage;
	if (end > data.length){
		end = data.length;
	}
	
	
	var firstRow = data[0];
	var fields = new Array();
	$.each(firstRow, function(index, value){
		var keys = Object.keys(value);
		fields.push(keys[0]);
	});
	
	var rowHeight = 61;
	var padding = 10;
	var actualHeight = (data.length + 1) * rowHeight + padding;
	var maxHeight = 200;
	var height = actualHeight < maxHeight ? actualHeight : maxHeight;  
	
	var cols = new Array();
	var criteriaNum = 0;
	var actualWidth = 75;
	
	var table = 	"<table id='imported-table'>" +
						"<thead>" +
							"<tr class='bg-dark text-white'>";
	
	$.each(fields,function(index,field){
		var colWidth;
		if (field.indexOf("criteria") != -1){
			table+= 				"<th>" + field.replace("criteria_","") + "</th>";
			colWidth = 100;
		} else {
			table+= 				"<th>" + field.replace("data_","") + "</th>";
			colWidth = 150;
		}
		
		actualWidth += colWidth;
		cols.push ({
			width: parseInt(colWidth),
			align: 'center'
		});
	});
	
	table += 				"</tr>" +
						"</thead>" +
						"<tbody id='imported-list'>" +
						"</tbody>" +
					"</table>";
	
	$("#imported-list-wrapper").html(table);
	
	var items = "";
	for (var i = start; i < end; i++){
		items+= "<tr>";
		item = data[i];
		
		$.each(fields,function(i,field){
			items += "<td style='overflow:hidden'><span>" + item[i][field]  + "</span></td>";
		});
		
		items +=	"</tr>";
	}
	$("#imported-list").html(items);
	
	var maxWidth = 1100;
	var width = actualWidth < maxWidth ? actualWidth : maxWidth;  
	
	
	$('#imported-table').fxdHdrCol({
		//fixedCols:  parseInt(criteriaNum),
		fixedCols: 0,
		width:     width,
		height:    height,
		colModal: cols,
		sort: false
	});
	
	$("#page-control").show();
	$("#processing").hide();
	CheckPage();
	$("#page").val(currentPage);
	$("#imported-data-info").show();
}

//Function for displaying the next data list page
function Next(){
	if (currentPage < maxPage) {
		currentPage ++;
		ShowTable();
	} 
}

//Function for displaying the previous data list page
function Prev(){
	if (currentPage > 1) {
		currentPage --;
		ShowTable();
	}
}



//Check current page number display table accordingly
function ChangePage(){
	
	if ($.isNumeric( $("#page").val() )){
		var page = parseInt($("#page").val())
		if (page >= 1 && page <= maxPage){
			currentPage = page;
			ShowTable();
		} else {
			$("#page").val(currentPage);
		}
	} else {
		$("#page").val(currentPage);
	}
}

//Change the number of data row displayed per page
function ChangeRowPerPage(){
	numPerPage = $("#row-per-page").val();
	maxPage = Math.ceil( data.length / numPerPage );
	$("#page-num").html(maxPage);
	currentPage = 1;
	$("#page").val(currentPage);
	ShowTable();
	
	if (maxPage > 1){
		$("#next").show();
		$("#prev").show();
		
	} else {
		$("#next").hide();
		$("#prev").hide();
	}
}

//Check and disable/enable next/prev button according to current page
function CheckPage(){
	if (currentPage == 1){
		$("#prev").prop("disabled",true);
		$("#next").prop("disabled",false);
	} else if (currentPage == maxPage){
		$("#prev").prop("disabled",false);
		$("#next").prop("disabled",true);
	} else {
		$("#prev").prop("disabled",false);
		$("#next").prop("disabled",false);
	}
}
