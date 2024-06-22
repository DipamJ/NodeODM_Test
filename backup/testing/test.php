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
var table = "
<table id='imported-set-table'>" +

    "
    <thead>" +
    "
    <tr>" +
        //added
        "
        <th>Delete</th>
        " +
        //added
        "
        <th>Year</th>
        " +
        "
        <th>Season</th>
        " +
        "
        <th>Crop</th>
        " +
        "
        <th>Type</th>
        " +
        "
        <th>Location</th>
        " +
        "
        <th>Sub-location</th>
        " +
        "
    </tr>
    " +
    "
    </thead>
    " +
    "
    <tbody id='imported-set'>" +
    "
    </tbody>
    " +
    "
</table>";

$("#imported-set-wrapper").html(table);
$.each(data,function(index,item)
{
if (index == 0){
firstSet = item.ID;
}

items+= "
<tr id='data-set-" + item.ID + "' onclick='SelectDataSet(\"" + item.ID + "\"); return false;' style='cursor:pointer'>" +
    //added
    "
    <td style='overflow:hidden'>" +
        "<span>" + item.Year + "</span>" +
        "
    </td>
    " +
    //added

    "
    <td style='overflow:hidden'>" +
        "<span>" + item.Year + "</span>" +
        "
    </td>
    " +

    "
    <td style='overflow:hidden'>" +
        "<span>" + item.Season + "</span>" +
        "
    </td>
    " +

    "
    <td style='overflow:hidden'>" +
        "<span>" + item.Crop + "</span>" +
        "
    </td>
    " +

    "
    <td style='overflow:hidden'>" +
        "<span>" + item.Type + "</span>" +
        "
    </td>
    " +

    "
    <td style='overflow:hidden'>" +
        "<span>" + item.Location + "</span>" +
        "
    </td>
    " +

    "
    <td style='overflow:hidden'>" +
        "<span>" + item.SubLocation + "</span>" +
        "
    </td>
    " +

    "
</tr>";
});

$("#imported-set").html(items);
var rowHeight = 61;
var padding = 10;
var actualHeight = (data.length + 1) * rowHeight + padding;
var maxHeight = 300;
var height = actualHeight < maxHeight ? actualHeight : maxHeight;

$('#imported-set-table').fxdHdrCol({
fixedCols: 0,
width: 1100,
height: height,

colModal: [
//added
{ width: 150, align: 'center' },
//added
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