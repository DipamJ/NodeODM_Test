var markers = L.markerClusterGroup({
	showCoverageOnHover: false,
	iconCreateFunction: function(cluster) {
		//var count = cluster.getChildCount();
		var projectCount = 0;
		var pointcloudCount = 0;
		var children = cluster.getAllChildMarkers();
		$.each(children,function(index,child){
			if (child.options.icon.options.iconUrl == "Resources/Images/project-marker.png"){
				projectCount++;
			} else {
				pointcloudCount++;
			}

		});

		if (projectCount > 0 && pointcloudCount > 0){
			return L.divIcon({ html: '<b>' + cluster.getChildCount() + '</b>', className: 'mixed-cluster', iconSize: L.point(52, 52) });
		}

		if (projectCount == 0 && pointcloudCount > 0){
			return L.divIcon({ html: '<b>' + cluster.getChildCount() + '</b>', className: 'pointcloud-cluster', iconSize: L.point(52, 52) });
		}

		if (projectCount > 0 && pointcloudCount == 0){
			return L.divIcon({ html: '<b>' + cluster.getChildCount() + '</b>', className: 'project-cluster', iconSize: L.point(52, 52) });
		}
	}
});

var projectMarkers = L.markerClusterGroup({
	showCoverageOnHover: false,
	iconCreateFunction: function(cluster) {
		return L.divIcon({ html: '<b>' + cluster.getChildCount() + '</b>', className: 'project-cluster', iconSize: L.point(52, 52) });
	}
});

var projectIcon = L.icon({
    iconUrl: "Resources/Images/project-marker.png"
});

var pointcloudMarkers = L.markerClusterGroup({
	showCoverageOnHover: false,
	iconCreateFunction: function(cluster) {
		return L.divIcon({ html: '<b>' + cluster.getChildCount() + '</b>', className: 'pointcloud-cluster', iconSize: L.point(52, 52) });
	}
});

var pointcloudIcon = L.icon({
    iconUrl: "Resources/Images/pointcloud-marker.png"
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
					var marker = new L.marker(latlng,{icon: projectIcon});
					marker.bindPopup(htmlString);

					marker.on('mouseover', function (e) {
						this.openPopup();
					});

					var project = "<li id='project-" + item.ID + "' onclick='CenterMap(&#39;" + item.CenterLat + "," + item.CenterLng + "&#39;,19); return false;' >" +
									item.Name +
								"</li>";
					$('#project-list').append(project);
					//projectMarkers.addLayer(marker);
					markers.addLayer(marker);

				});

			}
		}
	});
}

function GetPointcloudList(){
	$.ajax({
		url: "Resources/PHP/Pointcloud.php",
		dataType: 'text',
		data: { action: 'list'},
		success: function(response) {
			var data = JSON.parse(response);
			if (data.length > 0)
			{
				$.each(data,function(index,item)
				{
					var latlng = new L.LatLng(item.Lat, item.Lng);
					var htmlString = "<div style='text-align:center'>" +
							"<p style='font-weight:bold; '>" + item.Name + "</p>" +
							"<p>" +item.Description	+ "</p>" +
							"<a href='" + item.DisplayPath + "' target='_blank' style='margin:5px'>Visualization Tool</a>" +
						"</div>";
					var marker = new L.marker(latlng,{icon: pointcloudIcon});
					marker.bindPopup(htmlString);

					marker.on('mouseover', function (e) {
						this.openPopup();
					});

					var pointcloud = "<li id='pointcloud-" + item.ID + "' onclick='CenterMap(&#39;" + item.Lat + "," + item.Lng + "&#39;,19); return false;' >" +
									item.Name +
								"</li>";
					$('#pointcloud-list').append(pointcloud);
					//pointcloudMarkers.addLayer(marker);
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
