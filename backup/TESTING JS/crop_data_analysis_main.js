var data = "";
var keys = new Array();
var numPerPage = 100;
var maxPage = 0;
var currentPage = 1;
var maxList = new Array();
var minList = new Array();
var avgList = new Array();

//Get list (by name) and populate dropdownlist with the result
function GetList(name, crop)
{
    var table = $("#table").val();
    var year = $("#year").val();
    $.ajax({
        url: 'Resources/PHP/GetList.php',
        dataType: 'text',
        data: {
            name: name,
            year: year,
            crop: crop,
            table: table
        },
        success: function(response) {
            var items= "<option value='%' >All</option>";
            var data = JSON.parse(response);

            $.each(data,function(index,item)
            {
                items+="<option value='" + item.ID+"'>" + item.Name + "</option>";
            });
            $("#" + name).html(items);
        }
    });
}

//Get search data list and show the table
function GetDataList(crop){
    var row = $('#row').val();
    var col = $('#col').val();
    var grid = $('#grid').val();
    var name = $('#name').val();
    var plotid = $('#plotid').val();

    var table = $('#table').val();
    /*
    if ( table == "ndvi" || table == "exg"){
        return;
    }
    */
    var year = $('#year').val();

    $('#loading').show();

    $.ajax({
        url: 'Resources/PHP/GetDataList.php',
        dataType: 'text',
        data: {
            row: row,
            col: col,
            grid: grid,
            name: name,
            plotid: plotid,
            table: table,
            year: year,
            crop: crop
        },
        success: function(response) {
            data = JSON.parse(response);
            $('#loading').hide();
            if (data.length > 0){
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
                GetSelectedValues();
                ShowValueTable();
                $("#values").show();
            } else{
                $("#data-list-wrapper").html("No data found.");
                $("#export-data").hide();
            }
        },
        error: function (request, status, error){

            $('#loading').hide();
            alert(request.responseText);
        }
    });

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

//Show data table for the current page
function ShowTable(){
    $('#loading').show();

    var start = (currentPage - 1) * numPerPage;
    var end = currentPage * numPerPage;
    if (end > data.length){
        end = data.length;
    }

    var items = "";

    var table =	"<table id='data' >" +
        "<thead>" +
        "<tr>" +
        "<th><input id='check-all' type='checkbox' checked onchange='ToggleAllRowData();'></th>";
    $.each(keys,function(index,key) {
        if($.isNumeric(key)){
            //key = [key.slice(0, 2), '/', key.slice(2)].join('');
            key = key.substring(4, 6) + '/' + key.substring(6, 8);
        }

        switch (key){
            case "row": {
                key = "tier";
            } break;
            case "col": {
                key = "row";
            } break;
            case "name": {
                key = "genotype";
            } break;
        }

        table += 			"<th>" + key + "</th>";
    });

    table +=			"</tr>" +
        "</thead>" +
        "<tbody id='data-list'>" +
        "</tbody>" +
        "</table>";

    $("#data-list-wrapper").html(table);

    for (var i = start; i < end; i++){
        item = data[i];
        items += "<tr>";
        items += "<td><input id='check-data-" + i + "' name ='check-data' type='checkbox' checked onchange='ToggleRowData();'></td>";
        $.each(keys,function(index,key) {
            if(index >= 5){
                items += 	"<td>" + parseFloat(item[key]).toFixed(2) + "</td>";
            } else {
                items += 	"<td>" + item[key] + "</td>";
            }
        });
        items += "</tr>";
    }

    $("#data-list").html(items);

    var rowHeight = 41;
    var padding = 10;
    var actualHeight = (data.length + 1) * rowHeight + padding;
    var maxHeight = 250;
    var height = actualHeight < maxHeight ? actualHeight : maxHeight;
    var width = $("form").width() * 0.9;

    var cols = new Array();

    cols.push ({

        width: 50,
        align: 'center'
    });

    $.each(keys,function(index,key) {
        var w;
        if (index < 3){
            w = 100;
        } else if (index == 3 ){
            w = 200;
        } else if (index == 4) {
            w = 100;
        } else {
            w = 150;
        }
        cols.push ({
            width: w,
            align: 'center'
        });
    });

    $('#data').fxdHdrCol({
        fixedCols:  2,
        width:     width,
        height:    height,
        colModal: cols,
        sort: false
    });

    $("#page").val(currentPage);
    $("#loading").hide();
    $("#export-data").show();
}

//Check current page number display table accordingly
function CheckPage(){

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
    } else {
        $("#next").hide();
    }
}



//Get column names of the table
function GetColumnNames (crop){
    var table = $("#table").val();
    var year = $("#year").val();

    $.ajax({
        url: 'Resources/PHP/GetColumn.php',
        dataType: 'text',
        data: {
            table: table,
            year: year,
            crop: crop
        },
        success: function(response) {
            keys = JSON.parse(response);
            //$("#starting-date").val(keys[9].substring(0,2) + "/" + keys[9].substring(2,4) + "/" + $('#year').val());
            //$("#last-date").val(keys[keys.length - 1].substring(0,2) + "/" + keys[keys.length - 1].substring(2,4) + "/" + $('#year').val());
            $("#starting-date").val(keys[5].substring(4,6) + "/" + keys[5].substring(6,8) + "/" + $('#year').val());
            $("#last-date").val(keys[keys.length - 1].substring(4,6) + "/" + keys[keys.length - 1].substring(6,8) + "/" + $('#year').val());
        }
    });
}


//Function for getting max, mix, avg lists of the selected data
function GetSelectedValues(){
    maxList = new Array();
    minList = new Array();
    avgList = new Array();

    $.each(keys,function(i,key) {
        if($.isNumeric(key)){
            var max = 0;
            var min = 1000;//number that is greater than any data value
            var total = 0;
            var count = 0;

            $.each(data,function(j,item) {
                if ( $("#check-data-"+ j).is(':checked')){
                    var val = parseFloat(item[key]);

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

            var avg = total/count;

            maxList.push(max);
            minList.push(min);
            avgList.push(avg);
        }
    });
}

//Show the selected value list (max/min/avg)
function ShowValueTable(){

    var table =	"<table id='value' >" +
        "<thead>" +
        "<tr>" +
        "<th><input id='check-all-value' type='checkbox' checked onchange='ToggleAllSelectValues();'></th>";
    $.each(keys,function(index,key) {
        if($.isNumeric(key)){
            //var date = [key.slice(0, 2), '/', key.slice(2)].join('');
            var date = key.substring(4, 6) + '/' + key.substring(6, 8);
            table += 		"<th><input id='check-value-" + key.substring(4, 8) + "' name ='check-value' type='checkbox' checked onchange='ToggleValue();'>" + date + "</th>";
        }
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
        width: 100,
        align: 'center'
    });

    $.each(keys,function(index,key) {
        if($.isNumeric(key)){
            cols.push ({
                width: 200,
                align: 'center'
            });
        }
    });

    for (var i =0; i< 28; i++){
        cols.push ({
            width: 100,
            align: 'center'
        });
    }

    $('#value').fxdHdrCol({
        fixedCols:  1,
        width:     width,
        height:    height,
        colModal: cols,
        sort: false
    });

    $('#loading').hide();
    $("#export-value").show();

    UpdateInitParameters();
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

function Search(){
    GetDataList("sorghum");
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
        var date = new Date ([dates[i].slice(0, 2), "/", dates[i].slice(2)].join('') + "/" + $("#year").val());
        if (date >=  startDate){
            startIndex = i;
        }
        i++;
    }

    var j = dates.length - 1;
    while (j >= 0 && endIndex == -1){
        var date = new Date ([dates[j].slice(0, 2), "/", dates[j].slice(2)].join('') + "/" + $("#year").val());
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

//Call python program to fit the chart, generate the values and display the result chart if success
function GenerateCharts(){

    var startingDate = $("#starting-date").val();
    var lastDate = $("#last-date").val();
    var lastDay = $("#last-day").val();

    var allValues = GetValueListString($("#value-type").val()).split(",");
    var values = new Array();

    var dates = "";
    for(var i = 5; i < keys.length; i++){
        if ($("#check-value-" + keys[i].substring(4,8)).is(":checked")){
            dates = dates + keys[i].substring(4,8) + ",";
            values.push(allValues[i - 5]);
        }
    }

    $('#loading').show();

    dates = dates.slice(0, -1).split(",");


    //var values = GetValueListString($("#value-type").val()).split(",");
    //var values = GetValueListString(valueList).split(",");
    var data = GetDateValueList(startingDate, lastDate, dates, values);

    dates = data.dates;
    values = data.values;

    var fittingType = $("#fitting-type").val(); //symmetric/asymmetric/richard4/richard5

    var parameters = "";

    $("[id^=" + fittingType + "]").each(function() {
        parameters += $(this).val() + ",";
    });

    parameters = parameters.slice(0, -1);

    var year = $("#year").val();

    $.ajax({
        url: 'Resources/PHP/GenerateChartValues.php',
        dataType: 'text',
        data: {
            dates: dates,
            values: values,
            type: fittingType,
            parameters: parameters,
            startdate: startingDate,
            lastday: lastDay,
            year: year

        },
        success: function(response) {
            var data = JSON.parse(response);
            $('#loading').hide();

            $("#canopy-cover-chart").html("");
            $("#growth-rate-chart").html("");

            if (!data.ChartResult && !data.GrChartResult){
                alert("Failed to generate charts.");
            } else {
                var chartName = $("#table option:selected").text().toLowerCase();

                if(data.ChartResult) {
                    GenerateChart();
                } else {
                    alert("Failed to generate " + chartName + "chart.");
                }

                if(data.ChartResult && data.GrChartFeatureResult) {
                    GenerateGrowthRateChart();
                } else {
                    alert("Failed to generate " + chartName + " grow rate chart.");
                }

                $("#charts").show();
                $('html, body').animate({
                    scrollTop: $("#canopy-cover-chart").offset().top
                }, 200);

                ShowOptimizedParameters();
            }
        }
    });

}

//Display data chart
function GenerateChart(){

    var unitWidth = 5;
    var h = 300;
    var margin = {top: 40, right: 35, bottom: 50, left: 60};

    //$("#canopy-cover-chart").html("");

    d3.json("Resources/PHP/GenerateChart.php",function(error, data){

        if(error){
            alert("Failed to generate canopy cover chart.")
            return;
        }

        var w = data.length * unitWidth;

        var yLabel = $("#table option:selected").text();

        switch (yLabel){
            case "Canopy Cover": {
                yLabel += ' (%)';
            } break;
            case "Canopy Height": {
                yLabel += ' (m)';
            } break;
            case "Canopy Volume": {
                yLabel += ' (m\u00B3)';
            } break;
        }


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


        var svg = d3.select("#canopy-cover-chart").append("svg")
            .attr("width", width + margin.left + margin.right)
            .attr("height", height + margin.top + margin.bottom)
            .attr("id", name + "-growth-chart")
            .append("g")
            .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

        var vals = GetValueListString($("#value-type").val()).split(",");
        var max = Math.max.apply(Math,vals);

        x.domain(d3.extent(data, function(d) { return d.dae; }));
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
            .attr("d", valueline(data));


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
                i = bisectDate(data, x0, 1),
                d0 = data[i - 1],
                d1 = data[i],
                d = x0 - d0.dae > d1.dae - x0 ? d1 : d0;
            var text = "dae:" + d.dae + ", value:" + d.value.toFixed(2);

            focus.attr("transform", "translate(" + x(d.dae) + "," + y(d.value) + ")");
            focus.select("text").text(text);
            $('#vertical-line').attr('y2', (height - y(d.value)).toString() );
            $('#horizontal-line').attr('x1', -(width - x( d3.max(data, function(d) { return d.dae; }) - d.dae) - 10 ).toString() );
        }

        //----------------------------Show value points--------------------------------
        var startingDate = $("#starting-date").val();
        var lastDate = $("#last-date").val();

        var allValues = GetValueListString($("#value-type").val()).split(",");
        var values = new Array();

        var dates = "";
        var allDates = "";
        for(var i = 5; i < keys.length; i++){

            if ($("#check-value-" + keys[i].substring(4,8)).is(":checked")){
                dates = dates + keys[i].substring(4,8) + ",";
                values.push(allValues[i - 5]);
            }

            allDates = allDates + keys[i].substring(4,8) + ",";
        }
        dates = dates.slice(0, -1).split(",");
        //console.log(dates);
        allDates = allDates.slice(0, -1).split(",");

        //var values = GetValueListString($("#value-type").val()).split(",");

        var usedData = GetDateValueList(startingDate, lastDate, dates, values);
        //console.log(usedData);

        var usedValues = usedData.values.split(',');
        var usedDates = usedData.dates.split(',');

        var start = new Date($("#starting-date").val());

        var lastDAP = parseInt($("#last-day").val());
        console.log(max);
        console.log(height);
        //for (var i = 0; i < dates.length; i++){
        for (var i = 0; i < allDates.length; i++){

            var date = allDates[i].slice(0, 2) + '/' + allDates[i].slice(2) + "/" + $("#year").val();
            var end = new Date(date);
            var diff = new Date(end - start);
            var dae = diff/1000/60/60/24;
            console.log(allValues[i]);
            console.log(height * allValues[i] / max);

            if (dae >= 0) {
                var color = "red";

                if ($.inArray(allDates[i], usedDates ) >= 0){
                    color = "green";
                }

                svg.append("circle")
                    .attr("class", "fitting-point")
                    .attr("cx", dae * width / lastDAP)
                    //.attr("cy", height - (height * values[i] / 100))
                    //	.attr("cy", height - (height * values[i] / max))
                    .attr("cy", height - (height * allValues[i] / max))
                    .attr("r", "2px")
                    .attr("fill", color)
            }
        }

        $("#canopy-cover-chart").show();

    });
}

//Display growthrate chart
function GenerateGrowthRateChart(){

    var unitWidth = 5;
    var h = 300;
    var margin = {top: 40, right: 35, bottom: 50, left: 60};

    d3.json("Resources/PHP/GenerateGrowthRateChart.php",function(error, data){

        if(error){
            alert("Failed to generate canopy cover growth rate chart.")
            return;
        }

        var w = data.length * unitWidth;

        var table = $("#table option:selected").text();
        var unit = "";
        switch (table){
            case "Canopy Cover": {
                unit += 'm\u00B2/day';
            } break;
            case "Canopy Height": {
                unit += 'm/day';
            } break;
            case "Canopy Volume": {
                unit += 'm\u00B3/day';
            } break;
        }

        var yLabel = '';

        yLabel = 'Growth Rate (' + unit + ')';



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
            .attr("id", name + "-growth-chart")
            .append("g")
            .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

        x.domain(d3.extent(data, function(d) { return d.dae; }));
        y.domain([0, d3.max(data, function(d) { return d.value; })]);

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
            .attr("d", valueline(data));

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
                i = bisectDate(data, x0, 1),
                d0 = data[i - 1],
                d1 = data[i],
                d = x0 - d0.dae > d1.dae - x0 ? d1 : d0;
            var text = "dae:" + d.dae + ", value:" + d.value.toFixed(2);

            focus.attr("transform", "translate(" + x(d.dae) + "," + y(d.value) + ")");
            focus.select("text").text(text);
            $('#gr-vertical-line').attr('y2', (height - y(d.value)).toString() );
            $('#gr-horizontal-line').attr('x1', -(width - x( d3.max(data, function(d) { return d.dae; }) - d.dae) - 10 ).toString() );
        }


        //--------------------Draw lines and add text for max and half max growth rates--------------------------
        var lastDAP = $("#last-day").val();
        $.ajax({
            url: "Resources/PHP/GetGrowthRateFeatures.php",
            dataType: 'text',
            success: function(response) {
                var data = JSON.parse(response);
                var maxGrowth = data.maxGrowth;
                var maxGrowthDAP = data.maxGrowthDAP;
                var d1 = data.halfMaxGrowthD1;
                var d2 = data.halfMaxGrowthD2;
                var deltaD = data.deltaD;

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
    });
}

function ShowOptimizedParameters(){
    $.ajax({
        url: 'Resources/PHP/GetOptimizedParameters.php',
        dataType: 'text',
        success: function(response) {
            var data = JSON.parse(response);
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
                    parameters = ["β", "L", "t<sub>m","k","T"];
                } break;
            }

            var parameterString = "<span>( Optimized Parameters: ";
            for (var i =0; i < parameters.length; i++){
                parameterString += parameters[i] + " = " + data[i];
                if (i != parameters.length - 1){
                    parameterString += " , ";
                }
            }
            parameterString += " )</span>";
            $("#optimized-parameters").html(parameterString);
        }
    });
}

//Select/un-select row(s) to use for fitting charts
function ToggleRowData(){
    GetSelectedValues();
    ShowValueTable();

    if($('input[name=check-data]:not(:checked)').length > 0){
        $("#check-all").prop( "checked", false );
    } else {
        $("#check-all").prop( "checked", true );
    }

    if($('input[name=check-data]:checked').length > 0){
        $("#values").show();
    } else {
        $("#values").hide();
        $("#charts").hide();
    }
}

//Select all row to fit the charts
function ToggleAllRowData(){
    if ($("#check-all").is(':checked')){
        $("#values").show();
        $('[id^=check-data]').prop( "checked", true );
    } else {
        $("#values").hide();
        $("#charts").hide();
        $('[id^=check-data]').prop( "checked", false );
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

//Update the initial parameters base on the fitting type
function UpdateInitParameters(){
    var table = $('#table').val();
    switch(table){
        case "canopy_cover":{
            var max = 0;
            var min = 0;
            switch($("#value-type").val()){
                case "max":{
                    max = Math.trunc(Math.max.apply(Math,maxList));
                    min = Math.trunc(Math.min.apply(Math,maxList));
                } break;
                case "min":{
                    max = Math.trunc(Math.max.apply(Math,minList));
                    min = Math.trunc(Math.min.apply(Math,minList));
                } break;
                case "avg":{
                    max = Math.trunc(Math.max.apply(Math,avgList));
                    min = Math.trunc(Math.min.apply(Math,avgList));
                } break;
            }

            //Parameters for sigmoid
            $("#sigmoid-a").val("100");
            $("#sigmoid-a").val("3");
            $("#sigmoid-a").val("25");

            //Parameters for logistic
            $("#logistic-v").val(min);
            $("#logistic-tau").val(max);
            $("#logistic-mu").val("30");
            $("#logistic-sigma").val("3");
            $("#logistic-rho").val("1");

            //Parameters for Richard 4
            $("#richard4-li").val(max);
            $("#richard4-k").val("3");
            $("#richard4-gamma").val("60");
            $("#richard4-delta").val("1");

            //Parameters for Richard 5
            $("#richard5-beta").val(min);
            $("#richard5-li").val(max);
            $("#richard5-tm").val("60");
            $("#richard5-k").val("3");
            $("#richard5-T").val("1");

        } break;
        case "canopy_height":{
            //Parameters for sigmoid
            $("#sigmoid-a").val("1.2");
            $("#sigmoid-a").val("10.5");
            $("#sigmoid-a").val("57.5");

            //Parameters for logistic
            $("#logistic-v").val("1.3");
            $("#logistic-tau").val("-1.3");
            $("#logistic-mu").val("172.5");
            $("#logistic-sigma").val("16.3");
            $("#logistic-rho").val("726.7");

            //Parameters for Richard 4
            $("#richard4-li").val("1.3");
            $("#richard4-k").val("0.2");
            $("#richard4-gamma").val("69.5");
            $("#richard4-delta").val("6.8");

            //Parameters for Richard 5
            $("#richard5-beta").val("-0.2");
            $("#richard5-li").val("1.5");
            $("#richard5-tm").val("78");
            $("#richard5-k").val("1");
            $("#richard5-T").val("41");

        } break;
        case "canopy_volume":{
            //Parameters for sigmoid
            $("#sigmoid-a").val("0.8");
            $("#sigmoid-a").val("6.4");
            $("#sigmoid-a").val("58.3");

            //Parameters for logistic
            $("#logistic-v").val("0.8");
            $("#logistic-tau").val("-0.8");
            $("#logistic-mu").val("131.5");
            $("#logistic-sigma").val("10.4");
            $("#logistic-rho").val("692.6");

            //Parameters for Richard 4
            $("#richard4-li").val("0.8");
            $("#richard4-k").val("0.3");
            $("#richard4-gamma").val("63");
            $("#richard4-delta").val("4");

            //Parameters for Richard 5
            $("#richard5-beta").val("0");
            $("#richard5-li").val("0.8");
            $("#richard5-tm").val("61.5");
            $("#richard5-k").val("0.2");
            $("#richard5-T").val("2");
        } break;
    }

}

function ExportData(type){
    var csv = "";
    var fileName = "";
    if (type == "data"){
        csv = Data2CSV(data);
        fileName = "data.csv";
    } else if (type == "value"){
        csv = Values2CSV(data);
        fileName = "values.csv";
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

function Data2CSV(objArray) {
    var array = typeof objArray != "object" ? JSON.parse(objArray) : objArray;
    var str = "";
    var line = "";

    var header = keys.join(",");
    str += header  + "\r\n";

    for (var i = 0; i < array.length; i++) {
        var line = "";

        for (var index in array[i]) {
            line += array[i][index] + ",";
        }
        line = line.slice(0, -1);
        str += line + "\r\n";
    }
    return str;
}

function Values2CSV() {
    var str = "";
    var header = "type,";

    $.each(keys,function(index,key) {
        if($.isNumeric(key)){
            header += key + ",";
        }
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