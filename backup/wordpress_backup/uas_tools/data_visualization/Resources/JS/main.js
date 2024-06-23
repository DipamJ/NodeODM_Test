var markers = L.markerClusterGroup({
	showCoverageOnHover: false,
	iconCreateFunction: function(cluster) {
		return L.divIcon({ html: '<b>' + cluster.getChildCount() + '</b>', className: 'cluster', iconSize: L.point(52, 52) });
	}
});


var isClusterPopupShow = false;

function GetProjectList(){
	$.ajax({
		url: "Resources/PHP/Project.php",
		dataType: 'text',
		data: { action: 'list'},  
		success: function(response) {
			var data = JSON.parse(response);
			if (data.length > 0)
			{
				$.each(data,function(index,item) 
				{
					var latlng = new L.LatLng(item.CenterLat, item.CenterLng);
					var htmlString = "<div style='text-align:center'>" +
							"<p style='font-weight:bold; '>" + item.Name + "</p>" +
							"<p>" +item.Description	+ "</p>" +
							"<a href='" + item.VisualizationPage + "' target='_blank' style='margin:5px'>Visualization Tool</a>" +
						"</div>";
					var marker = new L.marker(latlng);
					marker.bindPopup(htmlString);
					
					marker.on('mouseover', function (e) {
						this.openPopup();
					});
					
					var project = "<li id='project-" + item.ID + "' onclick='CenterMap(&#39;" + item.CenterLat + "," + item.CenterLng + "&#39;,19); return false;' >" + 
									item.Name +  
								"</li>";
					$('#project-list').append(project);
					markers.addLayer(marker);
					
				});
				
			}
		}
	});
}

//Center and zoom map at given location and zoom level
function CenterMap(position, zoom){
	var loc = position.split(',');
	var z = parseInt(zoom);
	map.setView(loc, z, {animation: true});
}


function ShowClusterPopup(e){
	if (!isClusterPopupShow){
		var left  = e.clientX - 50;
		var top  = e.clientY + 10;
		
		var div = document.getElementById("cluster-popup");
		
		$("#cluster-popup").css({top: top, left: left});
		$("#cluster-popup").show();
		
		isClusterPopupShow = true;
	}
	
}