var lastSelectedRow = -1;

var lastSelectedRowColor = "";

var maxList = new Array();

var minList = new Array();

var avgList = new Array();

var importedData = "";

var dateList = new Array();

var criteriaList = new Array();

var currentDatasetID = 0;

var chartType;

var fields = new Array();

var requestNum = 0;

var numPerPage = 20;

var maxPage = 0;

var currentPage = 1;

var selectedData = new Array();



function GetUrlParameter(sParam) {

    var sPageURL = window.location.search.substring(1);

    var sURLVariables = sPageURL.split('&');

    for (var i = 0; i < sURLVariables.length; i++)

    {

        var sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] == sParam)

        {

            //return sParameterName[1];

            return sParameterName[1].replace(/%20/g, " ");

        }

    }

}



function GetPresetDataSetParameters(){

    var crop = GetUrlParameter("crop");

    if (crop){

        $("#crop option").filter(function() {

            return $(this).text() == crop;

        }).prop('selected', true);

    }



    var year = GetUrlParameter("year");

    if (year){

        $("#year option").filter(function() {

            return $(this).text() == year;

        }).prop('selected', true);

    }



    var season = GetUrlParameter("season");

    if (season){

        $("#season option").filter(function() {

            return $(this).text() == season;

        }).prop('selected', true);

    }



    var location = GetUrlParameter("location");

    if (location){

        $("#location option").filter(function() {

            return $(this).text() == location;

        }).prop('selected', true);

    }



    var sublocation = GetUrlParameter("sublocation");

    if (sublocation){

        $("#sublocation option").filter(function() {

            return $(this).text() == sublocation;

        }).prop('selected', true);

    }



    if (crop && year && location){

        Search();



    }

}



$(document).ready(function(){

    requestNum = 6;

    GetList("Crop");

    GetList("Type");

    GetList("Year");

    GetList("Season");

    GetList("Location");

    GetList("SubLocation");

    ShowFittingOption($("#fitting-type").val());





    $("#value-type").on("change", function() {

        ShowValueTable();

    });



    $("#fitting-type").on("change", function() {



        var fittingType = $(this).val();

        ShowFittingOption(fittingType);

    });





    $(":button").mouseup(function(){

        $(this).blur();

    })



    $("#starting-date").datepicker();

    $("#last-date").datepicker();



    $("#page").on("change", function() {

        ChangePage();

    });



    $("#row-per-page").on("change", function() {

        ChangeRowPerPage();

    });



});



function ShowFittingOption(type){

    var ndviFittingTypes = ["svr","polysimple","polyzero","polysklearn","rbf"];



    if(jQuery.inArray(type, ndviFittingTypes) !== -1){

        switch (type){

            case "svr":{

                $("#ndvi-svr").show();

                $("#ndvi-poly").hide();

                $("#ndvi-rbf").hide();

            } break;

            case "polysimple":{

                $("#ndvi-svr").hide();

                $("#ndvi-poly").show();

                $("#ndvi-rbf").hide();

            } break;

            case "polyzero":{

                $("#ndvi-svr").hide();

                $("#ndvi-poly").show();

                $("#ndvi-rbf").hide();

            } break;

            case "polysklearn":{

                $("#ndvi-svr").hide();

                $("#ndvi-poly").show();

                $("#ndvi-rbf").hide();

            } break;

            case "rbf":{

                $("#ndvi-svr").hide();

                $("#ndvi-poly").hide();

                $("#ndvi-rbf").show();

            } break;



        }



        $("#ndvi-parameters").show();

        $("#canopy-parameters").hide();

    } else {

        $("[id^=init]").hide();

        $("#init-" + type).show();

        $("#optimized-parameters").html("");



        $("#ndvi-parameters").hide();

        $("#canopy-parameters").show();



    }





}





function GetList(name){



    var url = "Resources/PHP/List.php?name=" + name;



    $.ajax({

        url: url,

        dataType: "text",

        success: function(response) {

            var items="";

            var data = JSON.parse(response);

            items+="<option value='%'>All</option>";

            if (data.length > 0)

            {



                $.each(data,function(index,item)

                {

                    items+="<option value='" + item.Name + "'>" + item.Name + "</option>";

                });

            }

            $("#" + name.toLowerCase()).html(items);



            requestNum--;

            if (requestNum == 0){

                GetPresetDataSetParameters();

            }

        }

    });

}



function Search(){



    var crop = $("#crop").val();

    var type = $("#type").val();

    var year = $("#year").val();

    var season = $("#season").val();

    var location = $("#location").val();

    var subLocation = $("#sublocation").val();

    $("#imported-set-wrapper").html("");

    $("#imported-list-wrapper").html("");

    $("#processing").show();



    $.ajax({

        url: "Resources/PHP/Search.php",

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

            var data = JSON.parse(response);

            var items = "";

            var firstSet = 0;

            if (data.length > 0)

            {



                var table = 	"<table id='imported-set-table'>" +

                    "<thead>" +

                    "<tr>" +

                    "<th>Year</th>" +

                    "<th>Season</th>" +

                    "<th>Crop</th>" +

                    "<th>Type</th>" +

                    "<th>Location</th>" +

                    "<th>Sub-location</th>" +



                    "</tr>" +

                    "</thead>" +

                    "<tbody id='imported-set'>" +

                    "</tbody>" +

                    "</table>";



                $("#imported-set-wrapper").html(table);

                $.each(data,function(index,item)

                {

                    if (index == 0){

                        firstSet = item.ID;

                    }



                    items+= "<tr id='data-set-" + item.ID + "' onclick='SelectDataSet(\"" + item.ID + "\"); return false;' style='cursor:pointer'>" +

                        "<td style='overflow:hidden'>" +

                        "<span>" + item.Year + "</span>" +

                        "</td>" +

                        "<td style='overflow:hidden'>" +

                        "<span>" + item.Season + "</span>" +

                        "</td>" +

                        "<td style='overflow:hidden'>" +

                        "<span>" + item.Crop + "</span>" +

                        "</td>" +

                        "<td style='overflow:hidden'>" +

                        "<span>" + item.Type + "</span>" +

                        "</td>" +

                        "<td style='overflow:hidden'>" +

                        "<span>" + item.Location + "</span>" +

                        "</td>" +

                        "<td style='overflow:hidden'>" +

                        "<span>" + item.SubLocation + "</span>" +

                        "</td>" +

                        "</tr>";

                });



                $("#imported-set").html(items);





                var rowHeight = 61;

                var padding = 10;

                var actualHeight = (data.length + 1) * rowHeight + padding;

                var maxHeight = 300;

                var height = actualHeight < maxHeight ? actualHeight : maxHeight;





                $('#imported-set-table').fxdHdrCol({

                    fixedCols:  0,

                    width:     1100,

                    height:    height,

                    colModal: [



                        { width: 150, align: 'center' },

                        { width: 150, align: 'center' },

                        { width: 150, align: 'center' },

                        { width: 250, align: 'center' },

                        { width: 175, align: 'center' },

                        { width: 150, align: 'center' },

                    ],

                    sort: false

                });



                if (firstSet > 0){

                    $("#next").prop("disabled", false);

                    SelectDataSet(firstSet);

                    $("#data-set-fs").show();

                } else {

                    alert("No dataset found");

                }

            }

            $("#processing").hide();





        },

        error: function(xhr){

            alert('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);

            $("#processing").hide();

        }

    });

}



function SelectDataSet(datasetID){

    if (lastSelectedRow != -1){

        $("#data-set-" + lastSelectedRow).children('td,th').css('background-color',lastSelectedRowColor);

    }



    lastSelectedRowColor = $("#data-set-" + datasetID).children('td,th').css('background-color');

    lastSelectedRow = datasetID;

    $("#selected-data-set").html($("#data-set-" + datasetID).html());



    var type = $("#data-set-" + datasetID).children('td')[3].children[0].textContent;

    if (type.toLowerCase().indexOf("cover") >= 0){

        chartType =	"Canopy Cover";

    } else if (type.toLowerCase().indexOf("height") >= 0) {

        chartType =	"Canopy Height";

    } else if (type.toLowerCase().indexOf("volume") >= 0) {

        chartType =	"Canopy Volume";

    } else if (type.toLowerCase().indexOf("ndvi") >= 0) {

        chartType =	"NDVI";

    } else if (type.toLowerCase().indexOf("exg") >= 0) {

        chartType =	"EXG";

    }



    $("#data-set-" + datasetID).children('td,th').css('background-color','#3297FD');

    currentDatasetID = datasetID;



}





/*

function GetDatasetCriteria(datasetID){



	$("#imported-list-wrapper").html("");

	$("#search-criteria-wrapper").html("");

	$("#processing").show();



	$.ajax({

		url: "Resources/PHP/GetDatasetCriteria.php",

		data: {dataset: datasetID},

		success: function(response) {

			$("#processing").hide();

			var data = JSON.parse(response);

			if (data.length > 0)

			{



				var firstRow = data[0];

				criteriaList = new Array();



				$.each(firstRow, function(index, value){

					var keys = Object.keys(value);

					criteriaList.push(keys[0]);

				});



				var count = 0;

				var isPreset = false;

				$.each(criteriaList,function(index,criteria) {

					var list =  "<div class='label label-small'>" + criteria + "</div>" +

								"<div class='input'><select id ='search-" + criteria + "' class='select-small'>" +

									"<option value='%' >All</option>";

					var data = JSON.parse(response);



					var valueList = new Array();

					$.each(data,function(i,item)

					{

						for (var i = 0; i < item.length; i++){

							if (Object.keys(item[i])[0] == criteria){//check to see if this is the value of the current criteria

								if(jQuery.inArray(item[i][criteria], valueList) == -1) {// not added yet -> add to list

									valueList.push(item[i][criteria]);

								}



							}

						}





					});

					if (!valueList.some(isNaN)){ //array contains only numbers

						valueList.sort((a, b) => (a - b));



					} else {

						valueList.sort();



					}



					$.each(valueList,function(j,value){

						list += "<option value='" + value + "' >" + value + "</option>";

					});



					list += 	"</select></div>";

					count++;

					if (count >= 5){

						count = 0;

						list += "<div style='clear:both'></div>";

					}



					$("#search-criteria-wrapper").append(list);



					if (GetCriteriaParameter(criteria)){

						isPreset = true;

					}

				});

				if (isPreset){

					GetImportedData();

				}



			}

		},

		error: function(xhr){

			alert("Could not find the data. Please try again.");

			$("#processing").hide();

		}

	});

}

*/

function GetDatasetCriteria(datasetID){



    $("#imported-list-wrapper").html("");

    $("#search-criteria-wrapper").html("");

    $("#processing").show();



    $.ajax({

        url: "Resources/PHP/GetDatasetCriteria.php",

        data: {dataset: datasetID},

        //data: {datasetID: datasetID},

        success: function(response) {

            $("#processing").hide();

            console.log(response);

            var data = JSON.parse(response);

            if (data.length > 0)

            {

                var count = 0;

                var isPreset = false;

                criteriaList = new Array();

                $.each(data,function(index,criteria) {

                    var name = criteria.Name;

                    criteriaList.push(name);

                    var valueList = criteria.ValueList;



                    var list =  "<div class='label label-small'>" + name + "</div>" +

                        "<div class='input'><select id ='search-" + name + "' class='select-small'>" +

                        "<option value='%' >All</option>";



                    if (!valueList.some(isNaN)){ //array contains only numbers

                        valueList.sort((a, b) => (a - b));



                    } else {

                        valueList.sort();



                    }



                    $.each(valueList,function(j,value){

                        list += "<option value='" + value + "' >" + value + "</option>";

                    });



                    list += 	"</select></div>";

                    count++;

                    if (count >= 5){

                        count = 0;

                        list += "<div style='clear:both'></div>";

                    }



                    $("#search-criteria-wrapper").append(list);



                    if (GetCriteriaParameter(name)){

                        isPreset = true;

                    }

                });



                if (isPreset){

                    GetImportedData();

                }

            }

        },

        error: function(xhr){

            alert("Could not find the data. Please try again.");

            $("#processing").hide();

        }

    });

}



function GetCriteriaParameter(criteria){

    var parameter = GetUrlParameter(criteria);

    if (parameter){

        if (!$.isNumeric(parameter) && Date.parse(parameter)){ //check if parameter is a date

            $("#search-" + criteria + " option").filter(function() {

                return Date.parse($(this).text()) == Date.parse(parameter);

            }).prop('selected', true);



        } else {



            $("#search-" + criteria + " option").filter(function() {

                return $(this).text() == parameter;

            }).prop('selected', true);

        }

        return true;

    }



    return false;

}





function GetCriteriaValueList(criteia){

    $.ajax({

        url: "Resources/PHP/GetDatasetCriteria.php",

        //data: {dataset: datasetID},

        datasetID: {dataset: datasetID},

        success: function(response) {

            var data = JSON.parse(response);

            if (data.length > 0)

            {

                $("#processing").hide();



                var firstRow = data[0];

                criteriaList = new Array();



                $.each(firstRow, function(index, value){

                    var keys = Object.keys(value);

                    criteriaList.push(keys[0]);

                });

            }

        }

    });

}



function GetDateList(){

    dateList = [];

    var firstRow = importedData[0];

    //var fields = new Array();

    fields = new Array();

    $.each(firstRow, function(index, value){

        var keys = Object.keys(value);

        fields.push(keys[0]);

    });



    $.each(fields,function(index,field){

        if (field.indexOf("criteria") == -1){

            var dateItem = {"index" : index, "value" : field.replace("data_","")};

            dateList.push(dateItem);

        }

    });

}



function GetImportedData(){



    $("#imported-list-wrapper").html("");

    $("#processing").show();

    $("#data-for-analysis").hide();

    var selectedCriteriaValues = new Array();

    $.each(criteriaList,function(index,criteria) {

        var item = {"Name" : criteria, "Value" : $("#search-" + criteria).val()};


        var name = criteria.Name;

        criteriaList.push(name);

        selectedCriteriaValues.push(item);

    });

    dateList = new Array();



    $.ajax({

        url: "Resources/PHP/GetImportedData.php",

        data: {

            dataset: currentDatasetID,

            //datasetID: currentDatasetID,

            conditions: selectedCriteriaValues,

        },

        success: function(response) {

            $("#processing").hide();

            console.log(response);

            var data = JSON.parse(response);

            importedData = data;

            var items = "";

            if (data.length > 0)

            {



                GetDateList();



                $.each(data,function(index,item) {

                    selectedData.push(index);

                });



                maxPage = Math.ceil( data.length / numPerPage );

                $("#page-num").html(maxPage);



                currentPage = 1;

                $("#page").val(currentPage);

                $("#page-control").show();

                if (maxPage > 1){

                    $("#nextPage").show();

                } else {

                    $("#nextPage").hide();

                }



                ShowTable();

                GetSelectedValues();

                ShowValueTable();

                $("#data-for-analysis").show();

                $("#processing").hide();

                HideCharts();



                //$("#growth-chart-result").hide();





                $("#starting-date").val(dateList[0].value.substring(4,6) + "/" + dateList[0].value.substring(6,8) + "/" + dateList[0].value.substring(0,4));

                $("#last-date").val(dateList[dateList.length - 1].value.substring(4,6) + "/" + dateList[dateList.length - 1].value.substring(6,8) + "/" + dateList[dateList.length - 1].value.substring(0,4));



            } else {

                alert("No data found");

            }



        },

        error: function(xhr){

            alert('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);

            $("#processing").hide();

            $("#data-for-analysis").show();

        }

    });

}





function ToggleRowData(i){

    if($('input[name=check-data]:not(:checked)').length > 0){

        $("#check-all").prop( "checked", false );

    } else {

        $("#check-all").prop( "checked", true );

    }



    if ($("#check-data-" + i).is(":checked")) {

        AddRemoveData(i, "Add");

    } else {

        AddRemoveData(i, "Remove");

    }



    GetSelectedValues();

    ShowValueTable();



}



//Select all row in current page

function ToggleAllRowInPage(){

    if ($("#check-all").is(':checked')){

        $('[id^=check-data]').prop( "checked", true );

    } else {

        $('[id^=check-data]').prop( "checked", false );

    }



    $("[id^=check-data]").each(function(index, item){

        var i = parseInt($(this).attr("id").replace("check-data-",""));

        if ($(this).is(":checked")) {

            AddRemoveData(i, "Add");

        } else {

            AddRemoveData(i, "Remove");

        }

    });



    GetSelectedValues();

    ShowValueTable();

}



//Select all row to fit the charts

function ToggleAllRowData(){

    if ($("#all-row-data").is(":checked")) {

        $.each(importedData,function(index,item) {

            selectedData.push(index);

        });

    } else {

        selectedData = [];

    }

    ShowTable();

    GetSelectedValues();

    ShowValueTable();

}



function AddRemoveData(i, action){

    if (action == "Add"){

        if($.inArray(i, selectedData) == -1){ //only add if not in array yet

            selectedData.push(i);

        }

    } else {

        selectedData = jQuery.grep(selectedData, function(value) {

            return value != i;

        });

    }

}



function ToggleAllSelectValues(){

    if ($("#check-all-value").is(':checked')){

        $('[id^=check-value]').prop( "checked", true );

    } else {

        $('[id^=check-value]').prop( "checked", false );

    }

}



function ToggleValue(date){



    var count = $('input[name=check-value]').length;



    if($('input[name=check-value]:checked').length < count){

        $("#check-all-value").prop( "checked", false );

    } else {

        $("#check-all-value").prop( "checked", true );

    }

}



//Function for getting max, mix, avg lists of the selected data

function GetSelectedValues(){

    if (selectedData.length > 0){



        maxList = new Array();

        minList = new Array();

        avgList = new Array();



        $.each(dateList,function(index,date) {

            var max = 0;

            var min = 100000;//number that is greater than any data value

            var total = 0;

            var count = 0;



            $.each(importedData,function(i,item) {



                if($.inArray(i, selectedData) !== -1){

                    var val = parseFloat(item[date.index]["data_" + date.value]);



                    if (val > max){

                        max = val;

                    }



                    if(val < min) {

                        min = val;

                    }



                    total += val;

                    count ++;

                }

            });



            var avg = (total/count).toFixed(2);



            maxList.push(max.toFixed(2));

            minList.push(min.toFixed(2));

            avgList.push(avg);



        });

        $("#values").show();

        //	$("#charts").show();

    } else {

        $("#values").hide();

        //$("#growth-chart-result").hide();

        $("#growth-chart-result").hide();

    }

}





//Show the selected value list (max/min/avg)

function ShowValueTable(){



    var table =	"<table id='value' >" +

        "<thead>" +

        "<tr>" +

        "<th><input id='check-all-value' type='checkbox' checked onchange='ToggleAllSelectValues();'></th>";

    $.each(dateList,function(index,date) {

        table += 			"<th><input id='check-value-" + date.index + "' name ='check-value' type='checkbox' checked onchange='ToggleValue();'>" + date.value + "</th>";

    });



    table +=			"</tr>" +

        "</thead>" +

        "<tbody id='value-list'>" +

        "</tbody>" +

        "</table>";



    $("#value-wrapper").html(table);



    switch($("#value-type").val()){

        case "max":{

            $("#value-list").append(GetValueHtml("max", maxList));

        } break;

        case "min":{

            $("#value-list").append(GetValueHtml("min", minList));

        } break;

        case "avg":{

            $("#value-list").append(GetValueHtml("avg", avgList));

        } break;

    }



    var rowHeight = 41;

    var padding = 10;

    var actualHeight = rowHeight * 2 + padding;

    var maxHeight = 175;

    var height = actualHeight < maxHeight ? actualHeight : maxHeight;

    var width = $("form").width() * 0.9;



    var cols = new Array();





    cols.push ({

        width: 50,

        align: 'center'

    });





    $.each(dateList,function(index,date) {

        cols.push ({

            width: 200,

            align: 'center'

        });

    });



    $('#value').fxdHdrCol({

        fixedCols:  1,

        width:     width,

        height:    height,

        colModal: cols,

        sort: false

    });



    $("#processing").hide();

    $("#export-value").show();

    $("#data-for-analysis").show();

    //UpdateInitParameters();

}



//Generate the list of selected values (max/min/avg) in html format for display

function GetValueHtml(type, values){



    var items = "";

    $.each(values,function(index,value) {

        items += 	"<td>" + parseFloat(value).toFixed(2) + "</td>";

    });



    var valueHTML = "";

    switch (type) {

        case "max": {

            valueHTML = "<tr><td>Max</td>" + items + "</tr>";

        } break;

        case "min": {

            valueHTML = "<tr><td>Min</td>" + items + "</tr>";

        } break;

        case "avg": {

            valueHTML = "<tr><td>Avg</td>" + items + "</tr>";

        } break;

    }





    return 	valueHTML;



}



//Generate the list of selected values (max/min/avg) as string separeted by ','

function GetValueListString(type){

    var values = "";



    switch(type){

        case "max":{

            values = maxList;

        }break;

        case "min":{

            values = minList;

        }break;

        case "avg":{

            values = avgList;

        }break;

    }



    var valueString = values.join();



    return valueString;



}



//Get the lists of dates and values from starting date to last date

function GetDateValueList(startingDate, lastDate, dates, values){

    var startDate = new Date(startingDate);

    var endDate = new Date(lastDate);

    var startIndex = -1;

    var endIndex = - 1;



    var i = 0;

    while (i < dates.length && startIndex == -1){

        var date = new Date (dates[i].substring(4,6) + "/" + dates[i].substring(6,8) + "/" + dates[i].substring(0,4));

        if (date >=  startDate){

            startIndex = i;

        }

        i++;

    }



    var j = dates.length - 1;

    while (j >= 0 && endIndex == -1){

        var date = new Date (dates[j].substring(4,6) + "/" + dates[j].substring(6,8) + "/" + dates[j].substring(0,4));

        if (date <=  endDate){

            endIndex = j;

        }

        j--;

    }



    var newDates = new Array();

    var newValues= new Array();

    for (var index = 0; index < dates.length; index ++){

        if (index >= startIndex && index <= endIndex){

            newDates.push(dates[index]);

            newValues.push(values[index]);

        }

    }



    return {dates: newDates.join(','), values: newValues.join(',')};

}





function ShowSelectDataValues(){



    $("#select-data-set").hide();

    $("#data-for-analysis").hide();

    $("#select-data-values").show();



    /*

    if (chartType == "NDVI"){

        $("#fit-ndvi-options").show();

        $("#fit-canopy-options").hide();



    } else {

        $("#fit-ndvi-options").hide();

        $("#fit-canopy-options").show();

    }

    */



    GetDatasetCriteria(currentDatasetID);



}



function ShowSelectDataSet(){

    $("#select-data-set").show();

    $("#select-data-values").hide();

    //$("#growth-chart-result").hide();

    HideCharts();

}



function GenerateChartValues(){

    var ndviFittingTypes = ["svr","polysimple","polyzero","polysklearn","rbf"];



    var fittingType = $("#fitting-type").val();



    if(jQuery.inArray(fittingType, ndviFittingTypes) !== -1){

        GenerateNDVIChartValues();

        $("#growth-chart-result").hide();

    } else {

        GenerateGrowthChartValues();

        $("#ndvi-chart-result").hide();

    }



    /*

    if (chartType == "NDVI"){

        GenerateNDVIChartValues();



    } else {

        GenerateGrowthChartValues();

    }

    */

}





function GenerateNDVIChartValues(){

    var startingDate = $("#starting-date").val();

    var lastDate = $("#last-date").val();

    var lastDay = $("#last-day").val();



    var allValues = GetValueListString($("#value-type").val()).split(",");

    var values = new Array();



    var dates = "";



    if ($("#ndvi-first-zero").prop('checked')){

        var firstSelectDate = new Date(startingDate);

        var firstValueDate = new Date (dateList[0].value.substring(4,6) + "/" + dateList[0].value.substring(6,8) + "/" + dateList[0].value.substring(0,4));



        if (firstSelectDate < firstValueDate){

            dates = startingDate.substring(6,10) + startingDate.substring(0,2) + startingDate.substring(3,5) + ",";

            values.push("0");



        }

    }





    for(var i = 0; i < dateList.length; i++){

        if ($("#check-value-" + dateList[i].index).is(":checked")){

            dates = dates + dateList[i].value.replace(/\-/g,"") + ",";

            values.push(allValues[i]);

        }

    }



    /*

    if ($("#ndvi-last-zero").prop('checked')){

        var lastSelectDate = new Date(lastDate);

        var lastValueDate = new Date (dateList[dateList.length - 1].value.substring(4,6) + "/" + dateList[dateList.length - 1].value.substring(6,8) + "/" + dateList[dateList.length - 1].value.substring(0,4));



        if (lastSelectDate > lastValueDate){

            dates = dates +  lastDate.substring(6,10) + lastDate.substring(0,2) + lastDate.substring(3,5);

            values.push("0");



        }

        dates = dates.split(",");

    } else {

        dates = dates.slice(0, -1).split(",");

    }

    */



    dates = dates.slice(0, -1).split(",");





    $("#processing").show();





    //dates = dates.split(",");





    var valueData = GetDateValueList(startingDate, lastDate, dates, values);

    dates = valueData.dates;

    values = valueData.values;



    var origin = $("#ndvi-through-origin").prop('checked');





    var url = "Resources/PHP/GenerateNDVIChartValues.php?dates=" + dates + "&values=" + values  + "&startDate=" + startingDate  + "&lastDay=" + lastDay  + "&origin=" + origin;

    var fittingType = $("#fitting-type").val();



    switch (fittingType){

        case "svr":{

            var c = $("#ndvi-c").val();

            var gamma = $("#ndvi-gamma").val();

            url += "&type=" + fittingType + "&c=" + c + "&gamma=" + gamma;

        } break;

        case "polysimple":{

            var degree = $("#ndvi-degree").val();

            url += "&type=" + fittingType + "&degree=" + degree;

        } break;

        case "polyzero":{

            var degree = $("#ndvi-degree").val();

            url += "&type=" + fittingType + "&degree=" + degree;

        } break;

        case "polysklearn":{

            var degree = $("#ndvi-degree").val();

            url += "&type=" + fittingType + "&degree=" + degree;

        } break;

        case "rbf":{

            var epsilon = $("#ndvi-epsilon").val();

            var smooth = $("#ndvi-smooth").val();

            url += "&type=" + fittingType + "&epsilon=" + epsilon + "&smooth=" + smooth;

        } break;



    }



    var c = $("#ndvi-c").val();

    var gamma = $("#ndvi-gamma").val();





    $.ajax({

        url: url,

        dataType: 'text',

        success: function(response) {

            $("#ndvi-chart").html("");



            if (response == "1"){

                GenerateNDVIChart();

                $("#ndvi-chart-result").show();

                $("html, body").animate({

                    scrollTop: $("#ndvi-chart").offset().top

                }, 200);

            } else {

                alert("Failed to generate charts.");

                $("#processing").hide();

                $("#ndvi-chart-result").hide();



            }

        }

    });

}



function GenerateNDVIChartPolyValues(){



    var startingDate = $("#starting-date").val();

    var lastDate = $("#last-date").val();

    var lastDay = $("#last-day").val();



    var allValues = GetValueListString($("#value-type").val()).split(",");

    var values = new Array();







    var dates = "";





    if ($("#ndvi-first-zero").prop('checked')){

        var firstSelectDate = new Date(startingDate);

        var firstValueDate = new Date (dateList[0].value.substring(4,6) + "/" + dateList[0].value.substring(6,8) + "/" + dateList[0].value.substring(0,4));



        if (firstSelectDate < firstValueDate){

            dates = startingDate.substring(6,10) + startingDate.substring(0,2) + startingDate.substring(3,5) + ",";

            values.push("0");



        }

    }







    for(var i = 0; i < dateList.length; i++){

        if ($("#check-value-" + dateList[i].index).is(":checked")){

            dates = dates + dateList[i].value.replace(/\-/g,"") + ",";

            values.push(allValues[i]);

        }

    }



    if ($("#ndvi-last-zero").prop('checked')){

        var lastSelectDate = new Date(lastDate);

        var lastValueDate = new Date (dateList[dateList.length - 1].value.substring(4,6) + "/" + dateList[dateList.length - 1].value.substring(6,8) + "/" + dateList[dateList.length - 1].value.substring(0,4));



        if (lastSelectDate > lastValueDate){

            dates = dates +  lastDate.substring(6,10) + lastDate.substring(0,2) + lastDate.substring(3,5);

            values.push("0");



        }

        dates = dates.split(",");

    } else {

        dates = dates.slice(0, -1).split(",");

    }



    $("#processing").show();





    var valueData = GetDateValueList(startingDate, lastDate, dates, values);

    dates = valueData.dates;

    values = valueData.values;



    var degree = $("#ndvi-degree").val();

    var gamma = $("#ndvi-gamma").val();





    $.ajax({

        url: 'Resources/PHP/GenerateNDVIChartPolyValues.php',

        dataType: 'text',

        data: {

            dates: dates,

            values: values,

            startDate: startingDate,

            lastDay: lastDay,

            degree: degree,

            gamma: gamma,

            //max: maxValue,

            //mdate: maxDate

        },

        success: function(response) {

            $("#ndvi-chart").html("");



            if (response == "1"){

                GenerateNDVIChart();

                $("#ndvi-chart-result").show();

                $("html, body").animate({

                    scrollTop: $("#ndvi-chart").offset().top

                }, 200);

            } else {

                alert("Failed to generate charts.");

                $("#processing").hide();

                $("#ndvi-chart-result").hide();



            }

        }

    });





    /*

    var startingDate = new Date($("#starting-date").val());

    var firstValue = 0;



    //var lastDate = $("#last-date").val();



    var lastDay = $("#last-day").val();



    var allValues = GetValueListString($("#value-type").val()).split(",");



    var maxValue = 0;

    var maxDate = "";



    for(var i = 0; i < dateList.length; i++){

        if ($("#check-value-" + dateList[i].index).is(":checked")){

            if (maxValue < allValues[i]){

                maxValue = allValues[i];

                maxDate = new Date (dateList[i].value.substring(4,6) + "/" + dateList[i].value.substring(6,8) + "/" + dateList[i].value.substring(0,4));



            }

        }

    }



    var lastDate = new Date(dateList[dateList.length - 1].value.substring(4,6) + "/" + dateList[dateList.length - 1].value.substring(6,8) + "/" + dateList[dateList.length -1].value.substring(0,4));

    var lastValue = allValues[dateList.length - 1];



    $("#processing").show();



    var firstDap = 0;



    var diffMax = new Date(maxDate - startingDate);

    var maxDap = diffMax/1000/60/60/24;



    var diffLast = new Date(lastDate - startingDate);

    var lastDap = diffLast/1000/60/60/24;



    $.ajax({

        url: 'Resources/PHP/GenerateNDVIChartPolyValues.php',

        dataType: 'text',

        data: {

            firstDap : firstDap,

            firstValue : firstValue,

            maxDap: maxDap,

            maxValue: maxValue,

            lastDap : lastDap,

            lastValue : lastValue,

            lastDay : lastDay

        },

        success: function(response) {

            $("#ndvi-chart").html("");



            if (response == "1"){

                GenerateNDVIChart();

                $("#ndvi-chart-result").show();

                $("html, body").animate({

                    scrollTop: $("#ndvi-chart").offset().top

                }, 200);

            } else {

                alert("Failed to generate charts.");

                $("#processing").hide();

                $("#ndvi-chart-result").hide();



            }

        }

    });

    */

}



function GenerateNDVIChartPolySKLValues(){



    var startingDate = $("#starting-date").val();

    var lastDate = $("#last-date").val();

    var lastDay = $("#last-day").val();



    var allValues = GetValueListString($("#value-type").val()).split(",");

    var values = new Array();







    var dates = "";





    if ($("#ndvi-first-zero").prop('checked')){

        var firstSelectDate = new Date(startingDate);

        var firstValueDate = new Date (dateList[0].value.substring(4,6) + "/" + dateList[0].value.substring(6,8) + "/" + dateList[0].value.substring(0,4));



        if (firstSelectDate < firstValueDate){

            dates = startingDate.substring(6,10) + startingDate.substring(0,2) + startingDate.substring(3,5) + ",";

            values.push("0");



        }

    }







    for(var i = 0; i < dateList.length; i++){

        if ($("#check-value-" + dateList[i].index).is(":checked")){

            dates = dates + dateList[i].value.replace(/\-/g,"") + ",";

            values.push(allValues[i]);

        }

    }



    if ($("#ndvi-last-zero").prop('checked')){

        var lastSelectDate = new Date(lastDate);

        var lastValueDate = new Date (dateList[dateList.length - 1].value.substring(4,6) + "/" + dateList[dateList.length - 1].value.substring(6,8) + "/" + dateList[dateList.length - 1].value.substring(0,4));



        if (lastSelectDate > lastValueDate){

            dates = dates +  lastDate.substring(6,10) + lastDate.substring(0,2) + lastDate.substring(3,5);

            values.push("0");



        }

        dates = dates.split(",");

    } else {

        dates = dates.slice(0, -1).split(",");

    }



    $("#processing").show();





    var valueData = GetDateValueList(startingDate, lastDate, dates, values);

    dates = valueData.dates;

    values = valueData.values;



    var degree = $("#ndvi-degree").val();

    var gamma = $("#ndvi-gamma").val();





    $.ajax({

        url: 'Resources/PHP/GenerateNDVIChartPolySKLValues.php',

        dataType: 'text',

        data: {

            dates: dates,

            values: values,

            startDate: startingDate,

            lastDay: lastDay,

            degree: degree,

            gamma: gamma,

            //max: maxValue,

            //mdate: maxDate

        },

        success: function(response) {

            $("#ndvi-chart").html("");



            if (response == "1"){

                GenerateNDVIChart();

                $("#ndvi-chart-result").show();

                $("html, body").animate({

                    scrollTop: $("#ndvi-chart").offset().top

                }, 200);

            } else {

                alert("Failed to generate charts.");

                $("#processing").hide();

                $("#ndvi-chart-result").hide();



            }

        }

    });





    /*

    var startingDate = new Date($("#starting-date").val());

    var firstValue = 0;



    //var lastDate = $("#last-date").val();



    var lastDay = $("#last-day").val();



    var allValues = GetValueListString($("#value-type").val()).split(",");



    var maxValue = 0;

    var maxDate = "";



    for(var i = 0; i < dateList.length; i++){

        if ($("#check-value-" + dateList[i].index).is(":checked")){

            if (maxValue < allValues[i]){

                maxValue = allValues[i];

                maxDate = new Date (dateList[i].value.substring(4,6) + "/" + dateList[i].value.substring(6,8) + "/" + dateList[i].value.substring(0,4));



            }

        }

    }



    var lastDate = new Date(dateList[dateList.length - 1].value.substring(4,6) + "/" + dateList[dateList.length - 1].value.substring(6,8) + "/" + dateList[dateList.length -1].value.substring(0,4));

    var lastValue = allValues[dateList.length - 1];



    $("#processing").show();



    var firstDap = 0;



    var diffMax = new Date(maxDate - startingDate);

    var maxDap = diffMax/1000/60/60/24;



    var diffLast = new Date(lastDate - startingDate);

    var lastDap = diffLast/1000/60/60/24;



    $.ajax({

        url: 'Resources/PHP/GenerateNDVIChartPolyValues.php',

        dataType: 'text',

        data: {

            firstDap : firstDap,

            firstValue : firstValue,

            maxDap: maxDap,

            maxValue: maxValue,

            lastDap : lastDap,

            lastValue : lastValue,

            lastDay : lastDay

        },

        success: function(response) {

            $("#ndvi-chart").html("");



            if (response == "1"){

                GenerateNDVIChart();

                $("#ndvi-chart-result").show();

                $("html, body").animate({

                    scrollTop: $("#ndvi-chart").offset().top

                }, 200);

            } else {

                alert("Failed to generate charts.");

                $("#processing").hide();

                $("#ndvi-chart-result").hide();



            }

        }

    });

    */

}



function GenerateNDVIChartRBFValues(){



    var startingDate = $("#starting-date").val();

    var lastDate = $("#last-date").val();

    var lastDay = $("#last-day").val();



    var allValues = GetValueListString($("#value-type").val()).split(",");

    var values = new Array();







    var dates = "";





    if ($("#ndvi-first-zero").prop('checked')){

        var firstSelectDate = new Date(startingDate);

        var firstValueDate = new Date (dateList[0].value.substring(4,6) + "/" + dateList[0].value.substring(6,8) + "/" + dateList[0].value.substring(0,4));



        if (firstSelectDate < firstValueDate){

            dates = startingDate.substring(6,10) + startingDate.substring(0,2) + startingDate.substring(3,5) + ",";

            values.push("0");



        }

    }







    for(var i = 0; i < dateList.length; i++){

        if ($("#check-value-" + dateList[i].index).is(":checked")){

            dates = dates + dateList[i].value.replace(/\-/g,"") + ",";

            values.push(allValues[i]);

        }

    }



    if ($("#ndvi-last-zero").prop('checked')){

        var lastSelectDate = new Date(lastDate);

        var lastValueDate = new Date (dateList[dateList.length - 1].value.substring(4,6) + "/" + dateList[dateList.length - 1].value.substring(6,8) + "/" + dateList[dateList.length - 1].value.substring(0,4));



        if (lastSelectDate > lastValueDate){

            dates = dates +  lastDate.substring(6,10) + lastDate.substring(0,2) + lastDate.substring(3,5);

            values.push("0");



        }

        dates = dates.split(",");

    } else {

        dates = dates.slice(0, -1).split(",");

    }



    $("#processing").show();





    var valueData = GetDateValueList(startingDate, lastDate, dates, values);

    dates = valueData.dates;

    values = valueData.values;



    var degree = $("#ndvi-degree").val();

    var gamma = $("#ndvi-gamma").val();





    $.ajax({

        url: 'Resources/PHP/GenerateNDVIChartRBFValues.php',

        dataType: 'text',

        data: {

            dates: dates,

            values: values,

            startDate: startingDate,

            lastDay: lastDay,

            degree: degree,

            gamma: gamma,

            //max: maxValue,

            //mdate: maxDate

        },

        success: function(response) {

            $("#ndvi-chart").html("");



            if (response == "1"){

                GenerateNDVIChart();

                $("#ndvi-chart-result").show();

                $("html, body").animate({

                    scrollTop: $("#ndvi-chart").offset().top

                }, 200);

            } else {

                alert("Failed to generate charts.");

                $("#processing").hide();

                $("#ndvi-chart-result").hide();



            }

        }

    });







}



function GenerateNDVIChartLinearValues(){

    //var startingDate = $("#starting-date").val();

    var startingDate = new Date($("#starting-date").val());

    var firstValue = 0;



    //var lastDate = $("#last-date").val();



    var lastDay = $("#last-day").val();



    var allValues = GetValueListString($("#value-type").val()).split(",");



    var maxValue = 0;

    var maxDate = "";



    for(var i = 0; i < dateList.length; i++){

        if ($("#check-value-" + dateList[i].index).is(":checked")){

            if (maxValue < allValues[i]){

                maxValue = allValues[i];

                maxDate = new Date (dateList[i].value.substring(4,6) + "/" + dateList[i].value.substring(6,8) + "/" + dateList[i].value.substring(0,4));



            }

        }

    }



    var lastDate = new Date(dateList[dateList.length - 1].value.substring(4,6) + "/" + dateList[dateList.length - 1].value.substring(6,8) + "/" + dateList[dateList.length -1].value.substring(0,4));

    var lastValue = allValues[dateList.length - 1];



    $("#processing").show();



    var firstDap = 0;



    var diffMax = new Date(maxDate - startingDate);

    var maxDap = diffMax/1000/60/60/24;



    var diffLast = new Date(lastDate - startingDate);

    var lastDap = diffLast/1000/60/60/24;







    $.ajax({

        url: 'Resources/PHP/GenerateNDVIChartLinearValues.php',

        dataType: 'text',

        data: {

            firstDap : firstDap,

            firstValue : firstValue,

            maxDap: maxDap,

            maxValue: maxValue,

            lastDap : lastDap,

            lastValue : lastValue,

            lastDay : lastDay

        },

        success: function(response) {

            $("#ndvi-chart").html("");



            if (response == "1"){

                GenerateNDVIChart();

                $("#ndvi-chart-result").show();

                $("html, body").animate({

                    scrollTop: $("#ndvi-chart").offset().top

                }, 200);

            } else {

                alert("Failed to generate charts.");

                $("#processing").hide();

                $("#ndvi-chart-result").hide();



            }

        }

    });



}



//Call python program to fit the chart, generate the values and display the result chart if success

function GenerateGrowthChartValues(){



    var startingDate = $("#starting-date").val();

    var lastDate = $("#last-date").val();

    var lastDay = $("#last-day").val();



    var allValues = GetValueListString($("#value-type").val()).split(",");

    var values = new Array();



    var dates = "";

    for(var i = 0; i < dateList.length; i++){

        if ($("#check-value-" + dateList[i].index).is(":checked")){

            dates = dates + dateList[i].value.replace(/\-/g,"") + ",";

            values.push(allValues[i]);

        }

    }



    $("#processing").show();



    dates = dates.slice(0, -1).split(",");





    var valueData = GetDateValueList(startingDate, lastDate, dates, values);

    dates = valueData.dates;

    values = valueData.values;



    var fittingType = $("#fitting-type").val();



    var parameters = "";



    $("[id^=" + fittingType + "]").each(function() {

        parameters += $(this).val() + ",";

    });



    parameters = parameters.slice(0, -1);





    $.ajax({

        url: 'Resources/PHP/GenerateChartValues.php',

        dataType: 'text',

        data: {

            dates: dates,

            values: values,

            type: fittingType,

            parameters: parameters,

            startDate: startingDate,

            lastDay: lastDay,

            //year: year



        },

        success: function(response) {

            var resultData = JSON.parse(response);



            $("#growth-chart").html("");

            $("#growth-rate-chart").html("");



            if (!resultData.ChartResult && !resultData.GrChartResult){

                alert("Failed to generate charts.");

                $("#processing").hide();

                //$("#growth-chart-result").hide();

                HideCharts();

            } else {

                var chartName = $("#table option:selected").text().toLowerCase();



                if(resultData.ChartResult) {

                    GenerateGrowthChart();

                } else {

                    alert("Failed to generate " + chartName + "chart.");

                }



                if(resultData.ChartResult && resultData.GrChartFeatureResult) {

                    GenerateGrowthRateChart();

                } else {

                    alert("Failed to generate " + chartName + " grow rate chart.");

                }



                $("#growth-chart-result").show();

                $('html, body').animate({

                    scrollTop: $("#growth-chart").offset().top

                }, 200);



                ShowOptimizedParameters();

                $("#growth-chart-result").show();

            }

        }

    });





}



//Display data chart

function GenerateNDVIChart(){



    var unitWidth = 5;

    var h = 300;

    var margin = {top: 40, right: 35, bottom: 50, left: 60};



    $("#processing").show();

    //$("#growth-chart").html("");



    d3.json("Resources/PHP/GenerateNDVIChart.php",function(error, chartData){



        if(error){

            alert("Failed to generate canopy cover chart.");

            $("#processing").hide();

            return;

        }



        var w = chartData.length * unitWidth;



        var yLabel = "NDVI";



        var width = w - margin.left - margin.right;

        var height = h - margin.top - margin.bottom;





        var x = d3.scale.linear().range([0, width]);



        var y = d3.scale.linear()

            .range([height, 0]);



        var xAxis = d3.svg.axis().scale(x)

            .orient("bottom").ticks(10);



        var yAxis = d3.svg.axis()

            .scale(y)

            .orient("left")



        var bisectDate = d3.bisector(function(d) { return d.dae; }).left;





        var svg = d3.select("#ndvi-chart").append("svg")

            .attr("width", width + margin.left + margin.right)

            .attr("height", height + margin.top + margin.bottom)

            .append("g")

            .attr("transform", "translate(" + margin.left + "," + margin.top + ")");



        var vals = GetValueListString($("#value-type").val()).split(",");

        var max = Math.max.apply(Math,vals);

        if ( max < d3.max(chartData, function(d) { return d.value; })){

            max = d3.max(chartData, function(d) { return d.value; });

        }

        //var max = d3.max(chartData, function(d) { return d.value; });



        x.domain(d3.extent(chartData, function(d) { return d.dae; }));

        //y.domain([0, d3.max(chartData, function(d) { return d.value; })]);

        y.domain([0, max]);



        svg.append("g")

            .attr("class", "x axis")

            .attr("transform", "translate(0," + height + ")")

            .call(xAxis)

            .append("text")

            .attr("x", width /2 )

            .attr("y", 30)

            .attr("dy", ".71em")

            .style("text-anchor", "middle")

            .attr("class", "axis-label")

            .text("Days After Emerging (days)");



        svg.append("g")

            .attr("class", "y axis")

            .call(yAxis)

            .append("text")

            .attr("x", -height/2)

            .attr("y", -50)

            .style("text-anchor", "middle")

            .attr("class", "axis-label")

            .attr("transform", "rotate(-90)")

            .text(yLabel);







        var valueline = d3.svg.line()

            .x(function(d) { return x(d.dae); })

            .y(function(d) { return y(d.value); });



        svg.append("path")

            .attr("class", "chart-line")

            .attr("d", valueline(chartData));





        //--------------------------add mousemove event---------------------------------------



        var focus = svg.append("g")

            .attr("class", "focus")

            .style("display", "none");



        focus.append("circle")

            .attr("r", 4.5);



        focus.append("text")

            .attr("x", 9)

            .attr("dy", ".35em")

            .attr("class", "focus-text");



        focus.append("line")

            .attr("y1", 0)

            .attr("x1", 0)

            .attr("x2", 0)

            .attr('id','vertical-line')

            .attr('stroke', 'red' )

            .attr('stroke-width', '1')

            .attr('stroke-dasharray', '5.5');





        focus.append("line")

            .attr("y1", 0)

            .attr("y2", 0)

            .attr("x2", 0)

            .attr('id','horizontal-line')

            .attr('stroke', 'red' )

            .attr('stroke-width', '1')

            .attr('stroke-dasharray', '5.5');



        svg.append("rect")

            .attr("class", "overlay")

            .attr("width", width)

            .attr("height", height)

            .on("mouseover", function() { focus.style("display", null); })

            .on("mouseout", function() { focus.style("display", "none"); })

            .on("mousemove", mousemove);







        function mousemove() {

            var x0 = x.invert(d3.mouse(this)[0]),

                i = bisectDate(chartData, x0, 1),

                d0 = chartData[i - 1],

                d1 = chartData[i],

                d = x0 - d0.dae > d1.dae - x0 ? d1 : d0;

            var text = "dae:" + d.dae + ", value:" + d.value.toFixed(2);



            focus.attr("transform", "translate(" + x(d.dae) + "," + y(d.value) + ")");

            focus.select("text").text(text);

            $('#vertical-line').attr('y2', (height - y(d.value)).toString() );

            $('#horizontal-line').attr('x1', -(width - x( d3.max(chartData, function(d) { return d.dae; }) - d.dae) - 10 ).toString() );

        }



        //----------------------------Show value points--------------------------------

        var startingDate = $("#starting-date").val();

        var lastDate = $("#last-date").val();



        var allValues = GetValueListString($("#value-type").val()).split(",");

        var values = new Array();



        var dates = "";

        var allDates = "";



        for(var i = 0; i < dateList.length; i++){

            if ($("#check-value-" + dateList[i].index).is(":checked")){

                dates = dates + dateList[i].value.replace(/\-/g,"") + ",";

                values.push(allValues[i]);

            }

            allDates = allDates + dateList[i].value.replace(/\-/g,"") + ",";

        }



        dates = dates.slice(0, -1).split(",");

        allDates = allDates.slice(0, -1).split(",");



        var usedData = GetDateValueList(startingDate, lastDate, dates, values);



        var usedValues = usedData.values.split(',');

        var usedDates = usedData.dates.split(',');



        var start = new Date($("#starting-date").val());



        var lastDAP = parseInt($("#last-day").val());

        //var lastDAP = new Date(new Date(lastDate) - start)/1000/60/60/24;



        for (var i = 0; i < allDates.length; i++){



            var date = allDates[i].substring(4, 6) + '/' + allDates[i].substring(6,8) + "/" + allDates[i].substring(0,4) ;

            var end = new Date(date);

            var diff = new Date(end - start);

            var dae = diff/1000/60/60/24;



            if (dae >= 0) {

                var color = "red";



                if ($.inArray(allDates[i], usedDates ) >= 0){

                    color = "green";

                }



                svg.append("circle")

                    .attr("class", "fitting-point")

                    //.attr("cx", dae * width / lastDAP)



                    .attr("cx", dae * width / lastDAP)

                    .attr("cy", height - (height * allValues[i] / max))

                    .attr("r", "2px")

                    .attr("fill", color)

            }

        }



        //----------------------------Line fitting--------------------------------



        /*

        var startPoint = 0;



        d3.json("Resources/PHP/GenerateNDVILineChart.php?part=first&startPoint=" + startPoint,function(error, lineChartData1){

            var valLine1 = d3.svg.line()

                        .x(function(d) { return x(d.dae); })

                        .y(function(d) { return y(d.value); });



            startPoint = lineChartData1.length - 1;

            //startPoint = 0;

            svg.append("path")

                .attr("class", "second-chart-line")

                .attr("d", valLine1(lineChartData1));



            d3.json("Resources/PHP/GenerateNDVILineChart.php?part=second&startPoint=" + startPoint,function(error, lineChartData2){

                var valLine2 = d3.svg.line()

                        .x(function(d) { return x(d.dae); })

                        .y(function(d) { return y(d.value); });



                svg.append("path")

                    .attr("class", "second-chart-line")

                    .attr("d", valLine2(lineChartData2));

            });

        });

        */

        /*

        startPoint = 0;



        d3.json("Resources/PHP/GenerateNDVILineChart.php?part=first&startPoint=" + startPoint,function(error, lineChartData1){

            var valLine1 = d3.svg.line()

                        .x(function(d) { return x(d.dae); })

                        .y(function(d) { return y(d.value); });



            svg.append("path")

                .attr("class", "second-chart-line")

                .attr("d", valLine1(lineChartData1));





        });



        d3.json("Resources/PHP/GenerateNDVILineChart.php?part=second&startPoint=" + startPoint,function(error, lineChartData2){

            var valLine2 = d3.svg.line()

                    .x(function(d) { return x(d.dae); })

                    .y(function(d) { return y(d.value); });



            svg.append("path")

                .attr("class", "second-chart-line")

                .attr("d", valLine2(lineChartData2));

        });

        */

        var yFirst;

        var yLast;



        d3.json("Resources/PHP/GenerateNDVILineChartIntersection.php?lastDay=" + lastDAP,function(error, lineChartData){

            var valLine = d3.svg.line()

                .x(function(d) { return x(d.dae); })

                .y(function(d) { return y(d.value); });



            svg.append("path")

                .attr("class", "second-chart-line")

                //.attr("class", "second-chart-line")

                .attr("d", valLine(lineChartData));



            yFirst =  parseFloat(lineChartData[0].value.toFixed(2));

            yLast =  parseFloat(lineChartData[lineChartData.length - 1].value.toFixed(2));

        });



        /*

        var maxVal = d3.max(chartData, function(d) { return d.value; });

        var maxDap = 0;

        $.each(chartData, function(index, point){

            if (point.value == maxVal){

                maxDap = point.dae;

            }

        });





        var maxDapX = CalculateXPosition(maxDap, lastDAP, width);

        var maxValY = CalculateYPosition(maxVal, max, height);





        var firstDapX = CalculateXPosition(chartData[0].dae, lastDAP, width);

        var firstValY = CalculateYPosition(chartData[0].value, max, height);



        var strokeWidth = "1";

        var strokeDashArray = "5,5";

        var textSize = "13px";

        var lineColor = "blue";



        svg.append("line")

                //.attr("y1", height)

                .attr("y1", firstValY)

                .attr("y2", maxValY)

                //.attr("x1", 0)

                .attr("x1", firstDapX)

                .attr("x2", maxDapX)

                .attr( "stroke", lineColor )

                .attr( "stroke-width", strokeWidth)

                .attr("stroke-dasharray", strokeDashArray);



        var lastDapX = CalculateXPosition(chartData[chartData.length - 1].dae, lastDAP, width);

        var lastValY = CalculateYPosition(chartData[chartData.length - 1].value, max, height);



        svg.append("line")

                .attr("y1", maxValY)

                .attr("y2", lastValY)

                .attr("x1", maxDapX)

                .attr("x2", lastDapX)

                .attr( "stroke", lineColor )

                .attr( "stroke-width", strokeWidth)

                .attr("stroke-dasharray", strokeDashArray);



        */



        //----------------------------Show intersection--------------------------------

        $.ajax({

            url: "Resources/PHP/GetPoint.php",

            dataType: 'text',

            data: { type: "intersection"},

            success: function(response) {

                var p = JSON.parse(response);

                /*

                svg.append("circle")

                    .attr("class", "intersection-point")



                    .attr("cx", p.x * width / lastDAP)

                    .attr("cy", height - (height * p.y / max))

                    .attr("r", "4px")

                    .attr("fill", "#FF7F50")

                    */

                //		.on("mouseover", ShowPoint("intersection"))

                //		.on("mouseout", HidePoint("intersection"))



                var iVal = parseFloat(p.y.toFixed(2));

                var iDay = parseInt(p.x);

                var d1 = iDay;

                var d2 = parseInt(lastDAP) - iDay;

                var a1 = (yFirst + iVal) * d1 / 2;

                var a2 = (yLast + iVal) * (d2) / 2;

                $("#ndvi-table-int").html(iVal);

                $("#ndvi-table-iday").html(iDay);

                $("#ndvi-table-d1").html(d1);

                $("#ndvi-table-d2").html(d2);

                $("#ndvi-table-a1").html(a1.toFixed(2));

                $("#ndvi-table-a2").html(a2.toFixed(2));



                CreatePoint(p, "intersection");

            },

            error: function(xhr){

                alert('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);

                $("#processing").hide();

            }

        });





        //----------------------------Show max--------------------------------

        $.ajax({

            url: "Resources/PHP/GetPoint.php",

            dataType: 'text',

            data: { type: "max"},

            success: function(response) {

                var p = JSON.parse(response);

                /*

                svg.append("circle")

                    .attr("class", "intersection-point")



                    .attr("cx", p.x * width / lastDAP)

                    .attr("cy", height - (height * p.y / max))

                    .attr("r", "4px")

                    .attr("fill", "steelblue")

                //	.on("mouseover", ShowPoint("max"))

                //	.on("mouseout", HidePoint("max"))

                    */

                $("#ndvi-table-max").html(p.y.toFixed(2));

                $("#ndvi-table-mday").html(p.x);





                CreatePoint(p, "max");

            },

            error: function(xhr){

                alert('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);

                $("#processing").hide();

            }

        });



        function CreatePoint(p, type){



            //var text = type + "(dae:" + p.x + ", val:" + p.y.toFixed(2) + ")";

            var text = "(dae:" + p.x + ", val:" + p.y.toFixed(2) + ")";



            var X = CalculateXPosition(p.x, lastDAP, width);

            var Y = CalculateYPosition(p.y, max, height);





            var strokeWidth = "1";

            var strokeDashArray = "5,5";

            var textSize = "13px";

            var color;

            if (type == "max"){

                color = "red";

                //color = "navy";

            } else {

                //	color = "red";

                color = "navy";



            }



            svg.append("circle")

                //.attr("class", "intersection-point")

                .attr("cx", p.x * width / lastDAP)

                .attr("cy", height - (height * p.y / max))

                .attr("r", "4px")

                .attr("fill", color)



            svg.append("line")

                .attr("y1", Y)

                .attr("y2", height)

                .attr("x1", X)

                .attr("x2", X)

                .attr( "stroke", color )

                .attr( "stroke-width", strokeWidth)

                .attr("stroke-dasharray", strokeDashArray);





            svg.append("line")

                .attr("y1", Y)

                .attr("y2", Y)

                .attr("x1", 0)

                .attr("x2", X)

                .attr( "stroke", color )

                .attr( "stroke-width", strokeWidth)

                .attr("stroke-dasharray", strokeDashArray);



            svg.append("text")

                .attr("id",type)

                .attr("x", X)

                .attr("y", Y)

                .style("text-anchor", "start")

                .style("font-size", textSize)

                .style("fill", color)

                //.style("display", "none")

                //.text("Max Growth Rate = " + maxGrowth.toFixed(3) + " m\u00B2/day @" + maxGrowthDAP + " days");

                .text(text);

        }





        //----------------------------Show slope--------------------------------

        $.ajax({

            url: "Resources/PHP/GetSlopes.php", // this page has no data type: "max"

            dataType: 'text',

            data: { type: "max"},

            success: function(response) {

                var slopes = JSON.parse(response);



                $("#ndvi-table-slope1").html(slopes[0].toFixed(4));

                $("#ndvi-table-slope2").html(slopes[1].toFixed(4));



            },

            error: function(xhr){

                alert('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);

                $("#processing").hide();

            }

        });





        /*

        function ShowPoint(id){

            //$("#"+ id ).show();

            console.log(id);

        }



        function HidePoint(id){

            //$("#"+ id ).hide();

            console.log(id);

        }

        */



        /*

        var mVal;

        var mDay;

        var iVal;

        var iDay;

        var d1;

        var d2;

        var A1;

        var A2;

        */



        var ndviTable = "<table id='ndvi-table'> "+

            "<thead>" +

            "<tr>" +

            "<th>Max</th>" +

            "<th>Mday</th>" +

            "<th>Int</th>" +

            "<th>Iday</th>" +

            "<th>D1</th>" +

            "<th>Slope1</th>" +

            "<th>Area1</th>" +

            "<th>D2</th>" +

            "<th>Slope2</th>" +

            "<th>Area2</th>" +

            "</tr>" +

            "</thead>" +

            "<tbody id='ndvi-table-values'>" +

            "</tbody>" +

            "</table>";



        $("#ndvi-table-wrapper").html(ndviTable);

        var growthValues = 	"<tr>" +

            /*

            "<td id='ndvi-table-max'>" + mVal +"</td>" +

            "<td id='ndvi-table-mday'>" + mDay +"</td>" +

            "<td id='ndvi-table-int'>" + intersection +"</td>" +

            "<td id='ndvi-table-iday'>" + intersectionDay +"</td>" +

            "<td id='ndvi-table-d1'>" + d1 +"</td>" +

            "<td id='ndvi-table-d2'>" + d2 +"</td>" +

            "<td id='ndvi-table-a1'>" + A1 +"</td>" +

            "<td id='ndvi-table-a2'>" + A2 +"</td>" +

            */

            "<td id='ndvi-table-max'></td>" +

            "<td id='ndvi-table-mday'></td>" +

            "<td id='ndvi-table-int'></td>" +

            "<td id='ndvi-table-iday'></td>" +

            "<td id='ndvi-table-d1'></td>" +

            "<td id='ndvi-table-slope1'></td>" +

            "<td id='ndvi-table-a1'></td>" +

            "<td id='ndvi-table-d2'></td>" +

            "<td id='ndvi-table-slope2'></td>" +

            "<td id='ndvi-table-a2'></td>" +

            "</tr>";



        $("#ndvi-table-values").html(growthValues);





        var colWidth = 60;

        $('#ndvi-table').fxdHdrCol({

            fixedCols:  0,

            width:     750,

            height:    100,



            colModal: [



                { width: colWidth, align: 'center' },

                { width: colWidth, align: 'center' },

                { width: colWidth, align: 'center' },

                { width: colWidth, align: 'center' },

                { width: colWidth, align: 'center' },

                { width: colWidth, align: 'center' },

                { width: colWidth, align: 'center' },

                { width: colWidth, align: 'center' },

                { width: colWidth, align: 'center' },

                { width: colWidth, align: 'center' },



            ],

            sort: false

        });





        //$("#growth-chart").show();

        $("#processing").hide();

    });

}



//Display data chart

function GenerateGrowthChart(){



    var unitWidth = 5;

    var h = 300;

    var margin = {top: 40, right: 35, bottom: 50, left: 60};



    $("#processing").show();

    //$("#growth-chart").html("");



    d3.json("Resources/PHP/GenerateChart.php",function(error, chartData){



        if(error){

            alert("Failed to generate canopy cover chart.");

            $("#processing").hide();

            return;

        }



        var w = chartData.length * unitWidth;



        var yLabel = "";



        switch(chartType) {

            case "Canopy Cover":

                yLabel = "Canopy Cover (%)";

                break;

            case "Canopy Height":

                yLabel = "Canopy Height (m)";

                break;

            case "Canopy Volume":

                yLabel += "Canopy Volume (m\u00B3)";

                break;

            case "NDVI":

                yLabel = "NDVI";

                break;

        }



        /*

        if (chartType == 0){

            yLabel = "Canopy Cover (%)";

        } else if (yLabel.toLowerCase().indexOf("height") >= 0) {

            yLabel = "Canopy Height (m)";

        } else if (yLabel.toLowerCase().indexOf("height") >= 0) {

            yLabel += "Canopy Volume (m\u00B3)";

        }

        */



        var width = w - margin.left - margin.right;

        var height = h - margin.top - margin.bottom;





        var x = d3.scale.linear().range([0, width]);



        var y = d3.scale.linear()

            .range([height, 0]);



        var xAxis = d3.svg.axis().scale(x)

            .orient("bottom").ticks(10);



        var yAxis = d3.svg.axis()

            .scale(y)

            .orient("left")



        var bisectDate = d3.bisector(function(d) { return d.dae; }).left;





        var svg = d3.select("#growth-chart").append("svg")

            .attr("width", width + margin.left + margin.right)

            .attr("height", height + margin.top + margin.bottom)

            //.attr("id", "growth-chart")

            .append("g")

            .attr("transform", "translate(" + margin.left + "," + margin.top + ")");



        var vals = GetValueListString($("#value-type").val()).split(",");

        var max = Math.max.apply(Math,vals);

        if ( max < d3.max(chartData, function(d) { return d.value; })){

            max = d3.max(chartData, function(d) { return d.value; });

        }

        //var max = d3.max(chartData, function(d) { return d.value; });



        x.domain(d3.extent(chartData, function(d) { return d.dae; }));

        //y.domain([0, d3.max(chartData, function(d) { return d.value; })]);

        y.domain([0, max]);



        svg.append("g")

            .attr("class", "x axis")

            .attr("transform", "translate(0," + height + ")")

            .call(xAxis)

            .append("text")

            .attr("x", width /2 )

            .attr("y", 30)

            .attr("dy", ".71em")

            .style("text-anchor", "middle")

            .attr("class", "axis-label")

            .text("Days After Emerging (days)");



        svg.append("g")

            .attr("class", "y axis")

            .call(yAxis)

            .append("text")

            .attr("x", -height/2)

            .attr("y", -50)

            .style("text-anchor", "middle")

            .attr("class", "axis-label")

            .attr("transform", "rotate(-90)")

            .text(yLabel);







        var valueline = d3.svg.line()

            .x(function(d) { return x(d.dae); })

            .y(function(d) { return y(d.value); });



        svg.append("path")

            .attr("class", "chart-line")

            .attr("d", valueline(chartData));





        //--------------------------add mousemove event---------------------------------------



        var focus = svg.append("g")

            .attr("class", "focus")

            .style("display", "none");



        focus.append("circle")

            .attr("r", 4.5);



        focus.append("text")

            .attr("x", 9)

            .attr("dy", ".35em")

            .attr("class", "focus-text");



        focus.append("line")

            .attr("y1", 0)

            .attr("x1", 0)

            .attr("x2", 0)

            .attr('id','vertical-line')

            .attr('stroke', 'red' )

            .attr('stroke-width', '1')

            .attr('stroke-dasharray', '5.5');





        focus.append("line")

            .attr("y1", 0)

            .attr("y2", 0)

            .attr("x2", 0)

            .attr('id','horizontal-line')

            .attr('stroke', 'red' )

            .attr('stroke-width', '1')

            .attr('stroke-dasharray', '5.5');



        svg.append("rect")

            .attr("class", "overlay")

            .attr("width", width)

            .attr("height", height)

            .on("mouseover", function() { focus.style("display", null); })

            .on("mouseout", function() { focus.style("display", "none"); })

            .on("mousemove", mousemove);







        function mousemove() {

            var x0 = x.invert(d3.mouse(this)[0]),

                i = bisectDate(chartData, x0, 1),

                d0 = chartData[i - 1],

                d1 = chartData[i],

                d = x0 - d0.dae > d1.dae - x0 ? d1 : d0;

            var text = "dae:" + d.dae + ", value:" + d.value.toFixed(2);



            focus.attr("transform", "translate(" + x(d.dae) + "," + y(d.value) + ")");

            focus.select("text").text(text);

            $('#vertical-line').attr('y2', (height - y(d.value)).toString() );

            $('#horizontal-line').attr('x1', -(width - x( d3.max(chartData, function(d) { return d.dae; }) - d.dae) - 10 ).toString() );

        }



        //----------------------------Show value points--------------------------------

        var startingDate = $("#starting-date").val();

        var lastDate = $("#last-date").val();



        var allValues = GetValueListString($("#value-type").val()).split(",");

        var values = new Array();



        var dates = "";

        var allDates = "";



        for(var i = 0; i < dateList.length; i++){

            if ($("#check-value-" + dateList[i].index).is(":checked")){

                dates = dates + dateList[i].value.replace(/\-/g,"") + ",";

                values.push(allValues[i]);

            }

            allDates = allDates + dateList[i].value.replace(/\-/g,"") + ",";

        }



        dates = dates.slice(0, -1).split(",");

        allDates = allDates.slice(0, -1).split(",");



        var usedData = GetDateValueList(startingDate, lastDate, dates, values);



        var usedValues = usedData.values.split(',');

        var usedDates = usedData.dates.split(',');



        var start = new Date($("#starting-date").val());



        var lastDAP = parseInt($("#last-day").val());

        //var lastDap = new Date(new Date(startDate) - start)/1000/60/60/24;

        //console.log(lastDap);



        for (var i = 0; i < allDates.length; i++){



            var date = allDates[i].substring(4, 6) + '/' + allDates[i].substring(6,8) + "/" + allDates[i].substring(0,4) ;

            var end = new Date(date);

            var diff = new Date(end - start);

            var dae = diff/1000/60/60/24;



            if (dae >= 0) {

                var color = "red";



                if ($.inArray(allDates[i], usedDates ) >= 0){

                    color = "green";

                }



                svg.append("circle")

                    .attr("class", "fitting-point")

                    .attr("cx", dae * width / lastDAP)

                    //.attr("cx", dae * w / lastDAP)

                    .attr("cy", height - (height * allValues[i] / max))

                    .attr("r", "2px")

                    .attr("fill", color)

            }

        }



        $("#growth-chart").show();

        $("#processing").hide();

    });

}



//Display growthrate chart

function GenerateGrowthRateChart(){



    var unitWidth = 5;

    var h = 300;

    var margin = {top: 40, right: 35, bottom: 50, left: 60};

    $("#processing").show();





    d3.json("Resources/PHP/GenerateGrowthRateChart.php",function(error, chartData){



        if(error){

            alert("Failed to generate canopy cover growth rate chart.");

            $("#processing").hide();

            return;

        }



        var w = chartData.length * unitWidth;



        var unit = "";



        switch(chartType) {

            case "Canopy Cover":

                unit = "(%/day)";

                break;

            case "Canopy Height":

                unit += "(m/day)";

                break;

            case "Canopy Volume":

                unit += "(m\u00B3/day)";

        }



        /*

        if (chartType.toLowerCase().indexOf("cover") >= 0){

            unit = "(%/day)";

        } else if (chartType.toLowerCase().indexOf("height") >= 0) {

            unit += "(m/day)";

        } else if (chartType.toLowerCase().indexOf("height") >= 0) {

            unit += "(m\u00B3/day)";

        }

        */



        var yLabel = "Growth Rate " + unit;







        var width = w - margin.left - margin.right;

        var height = h - margin.top - margin.bottom;





        var x = d3.scale.linear().range([0, width]);



        var y = d3.scale.linear()

            .range([height, 0]);



        var xAxis = d3.svg.axis().scale(x)

            .orient("bottom").ticks(10);



        var yAxis = d3.svg.axis()

            .scale(y)

            .orient("left")



        var bisectDate = d3.bisector(function(d) { return d.dae; }).left;





        var svg = d3.select("#growth-rate-chart").append("svg")

            .attr("width", width + margin.left + margin.right)

            .attr("height", height + margin.top + margin.bottom)

            //.attr("id", "growth-rate-chart")

            .append("g")

            .attr("transform", "translate(" + margin.left + "," + margin.top + ")");



        x.domain(d3.extent(chartData, function(d) { return d.dae; }));

        y.domain([0, d3.max(chartData, function(d) { return d.value; })]);



        svg.append("g")

            .attr("class", "x axis")

            .attr("transform", "translate(0," + height + ")")

            .call(xAxis)

            .append("text")

            .attr("x", width /2 )

            .attr("y", 30)

            .attr("dy", ".71em")

            .style("text-anchor", "middle")

            .attr("class", "axis-label")

            .text("Days After Emerging (days)");



        svg.append("g")

            .attr("class", "y axis")

            .call(yAxis)

            .append("text")

            .attr("x", -height/2)

            .attr("y", -50)

            .style("text-anchor", "middle")

            .attr("class", "axis-label")

            .attr("transform", "rotate(-90)")

            .text(yLabel);



        var valueline = d3.svg.line()

            .x(function(d) { return x(d.dae); })

            .y(function(d) { return y(d.value); });



        svg.append("path")

            .attr("class", "chart-line")

            .attr("d", valueline(chartData));



        //--------------------------add mousemove event---------------------------------------



        var focus = svg.append("g")

            .attr("class", "focus")

            .style("display", "none");



        focus.append("circle")

            .attr("r", 4.5);



        focus.append("text")

            .attr("x", 9)

            .attr("dy", ".35em")

            .attr("class", "focus-text");



        focus.append("line")

            .attr("y1", 0)

            .attr("x1", 0)

            .attr("x2", 0)

            .attr('id','gr-vertical-line')

            .attr('stroke', 'red' )

            .attr('stroke-width', '1')

            .attr('stroke-dasharray', '5.5');



        focus.append("line")

            .attr("y1", 0)

            .attr("y2", 0)

            .attr("x2", 0)

            .attr('id','gr-horizontal-line')

            .attr('stroke', 'red' )

            .attr('stroke-width', '1')

            .attr('stroke-dasharray', '5.5');



        svg.append("rect")

            .attr("class", "overlay")

            .attr("width", width)

            .attr("height", height)

            .on("mouseover", function() { focus.style("display", null); })

            .on("mouseout", function() { focus.style("display", "none"); })

            .on("mousemove", mousemove);





        function mousemove() {

            var x0 = x.invert(d3.mouse(this)[0]),

                i = bisectDate(chartData, x0, 1),

                d0 = chartData[i - 1],

                d1 = chartData[i],

                d = x0 - d0.dae > d1.dae - x0 ? d1 : d0;

            var text = "dae:" + d.dae + ", value:" + d.value.toFixed(2);



            focus.attr("transform", "translate(" + x(d.dae) + "," + y(d.value) + ")");

            focus.select("text").text(text);

            $('#gr-vertical-line').attr('y2', (height - y(d.value)).toString() );

            $('#gr-horizontal-line').attr('x1', -(width - x( d3.max(chartData, function(d) { return d.dae; }) - d.dae) - 10 ).toString() );

        }





        //--------------------Draw lines and add text for max and half max growth rates--------------------------

        var lastDAP = $("#last-day").val();

        $.ajax({

            url: "Resources/PHP/GetGrowthRateFeatures.php",

            dataType: 'text',

            success: function(response) {

                var growthData = JSON.parse(response);

                var maxGrowth = growthData.maxGrowth;

                var maxGrowthDAP = growthData.maxGrowthDAP;

                var d1 = growthData.halfMaxGrowthD1;

                var d2 = growthData.halfMaxGrowthD2;

                var eHalf = maxGrowth / 2;

                var lHalf = maxGrowth / 2;

                var deltaD = growthData.deltaD;

                var deltaD1 = maxGrowthDAP - d1;

                var deltaD2 = d2 - maxGrowthDAP;

                var earea = CalculateArea(chartData, Math.floor(d1), Math.ceil(maxGrowthDAP), deltaD1, eHalf);

                var larea = CalculateArea(chartData, Math.floor(maxGrowthDAP), Math.ceil(d2), deltaD2, lHalf);



                var growthValues = "<tr>" +

                    "<td>" + maxGrowth.toFixed(2) +"</td>" +

                    "<td>" + maxGrowthDAP.toFixed(2) +"</td>" +

                    "<td>" + eHalf.toFixed(2) +"</td>" +

                    "<td>" + d1.toFixed(2) +"</td>" +

                    "<td>" + lHalf.toFixed(2) +"</td>" +

                    "<td>" + d2.toFixed(2) +"</td>" +

                    "<td>" + deltaD.toFixed(2) +"</td>" +

                    "<td>" + deltaD1.toFixed(2) +"</td>" +

                    "<td>" + deltaD2.toFixed(2) +"</td>" +

                    "<td>" + ((((maxGrowth - eHalf) / deltaD1) * 100) / eHalf).toFixed(2)  +"</td>" +

                    "<td>" + ((((maxGrowth - lHalf) / deltaD2) * 100) / lHalf).toFixed(2)  +"</td>" +

                    "<td>" + (((maxGrowth - eHalf) / 2)*deltaD1).toFixed(2)  +"</td>" + //Earea_tri

                    "<td>" + (((maxGrowth - lHalf) / 2)*deltaD2).toFixed(2)  +"</td>" + //Larea_tri

                    "<td>" + earea.toFixed(2)  +"</td>" +

                    "<td>" + larea.toFixed(2)  +"</td>" +

                    "</tr>";



                $("#growth-values").html(growthValues);





                var colWidth = 60;

                $('#growth-table').fxdHdrCol({

                    fixedCols:  0,

                    width:     1100,

                    height:    100,



                    colModal: [



                        { width: colWidth, align: 'center' },

                        { width: colWidth, align: 'center' },

                        { width: colWidth, align: 'center' },

                        { width: colWidth, align: 'center' },

                        { width: colWidth, align: 'center' },

                        { width: colWidth, align: 'center' },

                        { width: colWidth, align: 'center' },

                        { width: colWidth, align: 'center' },

                        { width: colWidth, align: 'center' },

                        { width: colWidth, align: 'center' },

                        { width: colWidth, align: 'center' },

                        { width: colWidth, align: 'center' },

                        { width: colWidth, align: 'center' },

                        { width: colWidth, align: 'center' },

                        { width: colWidth, align: 'center' },



                    ],

                    sort: false

                });



                var d1X = CalculateXPosition(d1, lastDAP, width);

                var d2X = CalculateXPosition(d2, lastDAP, width);

                var halfGrowthY = CalculateYPosition(maxGrowth.toFixed(3)/2, maxGrowth, height);



                var maxGrowthX = CalculateXPosition(maxGrowthDAP, lastDAP, width);

                var maxGrowthY = CalculateYPosition(maxGrowth, maxGrowth, height);



                var strokeWidth = "1";

                var strokeDashArray = "5,5";

                var textSize = "13px";

                var maxGrowthColor = "red";

                var halfGrowthColor = "blue";

                var deltaDColor = "green";



                svg.append("line")

                    .attr("y1", 0)

                    .attr("y2", height)

                    .attr("x1", maxGrowthX)

                    .attr("x2", maxGrowthX)

                    .attr( "stroke", maxGrowthColor )

                    .attr( "stroke-width", strokeWidth)

                    .attr("stroke-dasharray", strokeDashArray);





                svg.append("line")

                    .attr("y1", maxGrowthY)

                    .attr("y2", maxGrowthY)

                    .attr("x1", 0)

                    .attr("x2", maxGrowthX)

                    .attr( "stroke", maxGrowthColor )

                    .attr( "stroke-width", strokeWidth)

                    .attr("stroke-dasharray", strokeDashArray);



                svg.append("text")

                    .attr("x", maxGrowthX)

                    .attr("y", maxGrowthY)

                    .style("text-anchor", "start")

                    .style("font-size", textSize)

                    .style("fill", maxGrowthColor)

                    //.text("Max Growth Rate = " + maxGrowth.toFixed(3) + " m\u00B2/day @" + maxGrowthDAP + " days");

                    .text("Max Growth Rate = " + maxGrowth.toFixed(3) + " " + unit + " @" + maxGrowthDAP + " days");



                svg.append("line")

                    .attr("y1", height)

                    .attr("y2", halfGrowthY)

                    .attr("x1", d1X)

                    .attr("x2", d1X)

                    .attr( "stroke", halfGrowthColor )

                    .attr( "stroke-width", strokeWidth)

                    .attr("stroke-dasharray", strokeDashArray);



                svg.append("line")

                    .attr("y1", height)

                    .attr("y2", halfGrowthY)

                    .attr("x1", d2X)

                    .attr("x2", d2X)

                    .attr( "stroke", halfGrowthColor )

                    .attr( "stroke-width", strokeWidth)

                    .attr("stroke-dasharray", strokeDashArray);



                svg.append("line")

                    .attr("y1", halfGrowthY)

                    .attr("y2", halfGrowthY)

                    .attr("x1", d1X)

                    .attr("x2", d2X)

                    .attr( "stroke", halfGrowthColor )

                    .attr( "stroke-width", strokeWidth)

                    .attr("stroke-dasharray", strokeDashArray);



                svg.append("text")

                    .attr("x", d1X)

                    .attr("y", halfGrowthY)

                    .style("text-anchor", "end")

                    .style("font-size", textSize)

                    .style("fill", halfGrowthColor)

                    .text("Half Max @D1 = " + d1.toFixed(2));



                svg.append("text")

                    .attr("x", d2X)

                    .attr("y", halfGrowthY)

                    .style("text-anchor", "start")

                    .style("font-size", textSize)

                    .style("fill", halfGrowthColor)

                    .text("Half Max @D2 = " + d2.toFixed(2));



                svg.append("text")

                    .attr("x", d1X + 200)

                    .attr("y", halfGrowthY - 50)

                    .style("text-anchor", "end")

                    .style("font-size", textSize)

                    .style("fill", deltaDColor)

                    .text("D2 - D1 = " + deltaD.toFixed(2) + " days");

            }

        });



        //-----------------------------------------------------------------------------









        $("#growth-rate-chart").show();

        $("#growth-chart-result").show();

        $("#processing").hide();

    });

}



function ShowOptimizedParameters(){

    $.ajax({

        url: 'Resources/PHP/GetOptimizedParameters.php',

        dataType: 'text',

        success: function(response) {

            var resultData = JSON.parse(response);

            var parameters = new Array();

            switch($("#fitting-type").val()){

                case "sigmoid": {

                    parameters = ["a", "b", "c"];

                } break;

                case "logistic":{

                    parameters = ["v", "τ", "μ","σ","ρ"];

                } break;

                case "richard4":{

                    parameters = ["L<sub>∞</sub>", "k", "γ","δ"];

                } break;

                case "richard5":{

                    parameters = ["β", "L", "t<sub>m</sub>","k","T"];

                } break;

            }



            var parameterString = "<span>( Optimized Parameters: ";

            for (var i =0; i < parameters.length; i++){

                parameterString += parameters[i] + " = " + resultData[i];

                if (i != parameters.length - 1){

                    parameterString += " , ";

                }

            }

            parameterString += " )</span>";

            $("#optimized-parameters").html(parameterString);

        }

    });

}



//Calculate the x position on the chart given the x value, last day of planting, and chart width

function CalculateXPosition(x, maxDap, width)

{

    return x * width / maxDap;

}



//Calculate the x position on the chart given the y value, max growth rate, and chart height

function CalculateYPosition(y, maxGrowth, height)

{

    return (maxGrowth - y) * height / maxGrowth;

}



function CalculateArea(chartData, d1, d2, deltaD, half){

    if (d2 < 0){

        return 0;

    }



    var area = 0;

    if (d1 < 0){

        d1 = 0;

    }



    for (var i = d1; i < d2; i++){

        area += (chartData[i].value + chartData[i + 1].value)/2;

    }



    return (area - (deltaD * half));

}



function ExportData(type){

    var csv = "";

    var fileName = "";

    if (type == "data"){

        csv = Data2CSV();

        fileName = "data.csv";

    } else if (type == "value"){

        csv = Values2CSV();

        fileName = "values.csv";

    } else if (type == "growth"){

        csv = GrowthRate2CSV();

        fileName = "growth_rate.csv";

    } else if (type == "ndvi"){

        csv = NDVI2CSV();

        fileName = "ndvi.csv";

    }



    var downloadLink = document.createElement("a");

    var blob = new Blob(["\ufeff", csv]);

    var url = URL.createObjectURL(blob);

    downloadLink.href = url;

    downloadLink.download = fileName;



    document.body.appendChild(downloadLink);

    downloadLink.click();

    document.body.removeChild(downloadLink);

}



function Data2CSV() {

    var str = "";

    var line = "";



    var header = fields.join(",");

    header = header.replace(/criteria_/g,"");

    header = header.replace(/data_/g,"");

    str += header  + "\r\n";



    $.each(importedData,function(index,item){

        var line = "";

        $.each(fields,function(i,field){

            line += item[i][field] + ",";

        });



        line = line.slice(0, -1);

        str += line + "\r\n";

    });

    return str;

}



function Values2CSV() {

    var str = "";

    var header = "type,";



    $.each(dateList,function(index,date) {

        header += date.value + ",";

    });



    str += header.slice(0, -1) + "\r\n";



    var max = "max," + maxList.join(",");

    str += max + "\r\n";



    var min = "min," + minList.join(",");

    str += min + "\r\n";



    var avg = "avg," + avgList.join(",");

    str += avg + "\r\n";



    return str;

}



function GrowthRate2CSV() {

    var str = "";

    var header = "Max,Mday,Ehalf,Eday,Lhalf,Lday,Mdur,Edur,Ldur,ERGR,LRGR,Earea_tri,Larea_tri,Earea,Larea";



    var values = "";

    $('#growth-values td').each(function() {

        values += $(this).html() + ",";

    });



    str += header + "\r\n" + values.slice(0, -1);

    return str;

}



function NDVI2CSV() {

    var str = "";

    var header = "Max,Mday,Int,Iday,D1,Slopes1,Area1,D2,Slopes2,Area2";



    var values = "";

    $('#ndvi-table-values td').each(function() {

        values += $(this).html() + ",";

    });



    str += header + "\r\n" + values.slice(0, -1);

    return str;

}



function ExportCharts(type){

    $("#canvas-list").html("");

    $("#processing").show();

    var folder = Math.random().toString(36).substr(2) + Math.random().toString(36).substr(2);

    SaveCharts(folder, type);

}



function SaveCharts(folder, type){

    $.ajax({

        url: "Resources/chart-style.css",

        dataType: "text",

        success: function(response) {

            $("#processing").hide();

            var style = "\n" + response + "\n";

            if (type == "growth"){

                requestNum = 2;

                SaveSVG(style, "growth", folder,"growth_chart.png");

                SaveSVG(style, "growth-rate", folder, "growth_rate_chart.png");

            } else if (type == "ndvi"){

                requestNum = 1;

                SaveSVG(style, "ndvi", folder,"ndvi_chart.png");

            }

        }

    });



}





//Save chart to image file

//Inputs: index (chart index), type ("growth", "growth-rate"), folder (name of the folder the image will be stored)

function SaveSVG(style, type, folder, filename){

    var svg = d3.select("#" + type + "-chart").select("svg");



    if (svg.node() == null){

        requestNum--;

        return;

    }



    var serializer = new XMLSerializer();



    svg.insert("defs",":first-child")



    svg.select("defs")

        .append('style')

        .attr('type','text/css')

        .html(style);



    var svgStr = serializer.serializeToString(svg.node());

    svg.select("defs").remove();



    var canvasStr = "<canvas id='" + type + "-chart-canvas' width='1000px' height='600px' style='display:none'></canvas>";

    $("#canvas-list").append(canvasStr);

    canvg(document.getElementById(type + "-chart-canvas"), svgStr);

    var canvas = document.getElementById(type + "-chart-canvas");





    var img = canvas.toDataURL("image/png"); //img is data:image/png;base64

    img = img.replace('data:image/png;base64,', '');

    var data = img;

    $.ajax({

        url: "Resources/PHP/SaveChart.php",

        dataType: "text",

        type: "POST",

        data: {

            data: data,

            folder: folder,

            name: filename

        },

        success: function(response) {

            requestNum--;

            if (requestNum == 0){

                $("#processing").hide();

                DownloadCharts(folder);

            }

        },

        error: function (request, status, error){

            alert(request.responseText);

        }

    });



}



function DownloadCharts(folder){

    $.ajax({

        url: "Resources/PHP/CompressCharts.php",

        dataType: "text",

        type: "POST",

        data: {

            folder: folder

        },

        success: function(response) {

            var downloadLink = document.createElement("a");

            var url = response;

            downloadLink.href = url;

            downloadLink.download = "charts.zip";



            document.body.appendChild(downloadLink);

            downloadLink.click();

            document.body.removeChild(downloadLink);

            $("#processing").hide();

        },

        error: function (request, status, error){

            alert(request.responseText);

        }

    });

}



//Show data table for the current page

function ShowTable(){

    $("#processing").show();



    var start = (currentPage - 1) * numPerPage;

    var end = currentPage * numPerPage;

    if (end > importedData.length){

        end = importedData.length;

    }



    var rowHeight = 61;

    var padding = 10;

    var actualHeight = (importedData.length + 1) * rowHeight + padding;

    var maxHeight = 200;

    var height = actualHeight < maxHeight ? actualHeight : maxHeight;



    var cols = new Array();

    var criteriaNum = 0;

    var actualWidth = 75;



    cols.push ({

        width: 50,

        align: 'center'

    });



    var table = 	"<table id='imported-table'>" +

        "<thead>" +

        "<tr>" +

        "<th style='background-color:#ffe6e6'><input id='check-all' type='checkbox' checked onchange='ToggleAllRowInPage();'></th>"	;



    $.each(fields,function(index,field){

        var colWidth;

        if (field.indexOf("criteria") != -1){

            table+= 				"<th style='background-color:#ffe6e6'>" + field.replace("criteria_","") + "</th>";

            colWidth = 100;

        } else {

            table+= 				"<th style='background-color:#e6ffe6'>" + field.replace("data_","") + "</th>";

            colWidth = 150;

            //var dateItem = {"index" : index, "value" : field.replace("data_","")};

            //dateList.push(dateItem);

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

        var isChecked = "";

        if($.inArray(i, selectedData) !== -1){

            isChecked = "checked";

            //checkedNum ++;



        } else {

            allChecked = false;

        }



        item = importedData[i];



        items+= "<tr>" +

            "<td><input id='check-data-" + i + "' name ='check-data' type='checkbox' " + isChecked +" onchange='ToggleRowData(" + i + ");'></td>";

        item = importedData[i];



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

    numPerPage = parseInt($("#row-per-page").val());

    maxPage = Math.ceil( importedData.length / numPerPage );

    $("#page-num").html(maxPage);

    currentPage = 1;

    $("#page").val(currentPage);

    ShowTable();



    if (maxPage > 1){

        $("#nextPage").show();

        $("#prevPage").show();



    } else {

        $("#nextPage").hide();

        $("#prevPage").hide();

    }

}



//Check and disable/enable next/prev button according to current page

function CheckPage(){

    if (currentPage == 1){

        $("#prevPage").prop("disabled",true);

        $("#nextPage").prop("disabled",false);

    } else if (currentPage == maxPage){

        $("#prevPage").prop("disabled",false);

        $("#nextPage").prop("disabled",true);

    } else {

        $("#prevPage").prop("disabled",false);

        $("#nextPage").prop("disabled",false);

    }

}





function HideCharts(){

    $("#growth-chart-result").hide();

    $("#ndvi-chart-result").hide();

}
