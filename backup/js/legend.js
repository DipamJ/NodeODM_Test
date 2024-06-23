var legendList = new Array();

function GetInfo(type, attribute){
	$.ajax({
		type: "GET" ,
		url: type + "_style.qml" ,
		dataType: "xml" ,
		success: function(xml) {
			var result = new Array();
			$(xml).find('item').each(function() {
				result.push($(this.attributes[attribute])[0].nodeValue);
			});
			return result;
		}
	});
}

function AddLegend(type){
	var legend = L.control({position: 'bottomleft'});
	var div = L.DomUtil.create('div', 'info legend ' + type);
	var grades = new Array();
	var colors = new Array();
	
	legendList.push(type.toLowerCase());
	
	$.ajax({
		type: "GET" ,
		url: type + "_style.qml" ,
		dataType: "xml" ,
		success: function(xml) {
			$(xml).find('item').each(function() {
				grades.push($(this.attributes["value"])[0].nodeValue);
				colors.push($(this.attributes["color"])[0].nodeValue);
			});
			
			for (var i = 0; i < grades.length; i++) {
				div.innerHTML += '<label>' + grades[i] + '</label>';
			}

			div.innerHTML += '<br>';

			for (var i = 0; i < colors.length; i++) {
				div.innerHTML += '<span style="background:' + colors[i] + '"></span> ';
			}
			
			legend.onAdd = function (map) { return div; };
			legend.addTo(map);
		}
	});
}

function HideAllLegens(){
	for(var i = 0; i < legendList.length; i++){
		$('.' + legendList[i]).hide();
	}
}

function AddLayerCheck(){
	map.on('overlayadd', function (eventLayer) {
		
		HideAllLegens();
		
		var i = legendList.length - 1;
		var stop = false;
		while (i >= 0 && stop == false){
			if(CheckGroupActive(legendList[i])){
				stop = true;
				$('.' + legendList[i]).show();
			} else {
				i--;
			}
		}
		
		/*
		if (i >= 0){
			$('.' + legendList[i]).show();
		}
		*/
		
		/*
		//Find the index of the group that the added layer belongs to
		var index = -1;
		for(var i = 0; i < legendList.length; i++){
			if (eventLayer.group['name'].toLowerCase() == legendList[i].toLowerCase()){
				index = i;
			}
			
			$('.' + legendList[i]).hide();
		}
		
		var j = legendList.length - 1;
		var stop = false;
		
		//Find the active group with highest priority
		while (j > index && stop == false){
			if(CheckGroupActive(legendList[j])){
				stop = true;
			} else {
				j--;
			}
		}
		
		$('.' + legendList[j]).show();
		*/
		
		/*
		var otherHigherPriority = false;
		for(var j = legendList.length - 1; j > index; j--){
			
			if(CheckGroupActive(legendList[j])){
				otherHigherPriority = true;
			}
			
		}
	
		if(!otherHigherPriority) {
			$('.' + legendList[index]).show();
		}
		*/
	
		/*
		switch(eventLayer.group['name']){
			
			case "ExG":{
					if (!CheckGroupActive('CHM') && !CheckGroupActive('NDVI')){
						$('.exg').show();
					}
				} break;
			
			case "CHM": {
					if (!CheckGroupActive('NDVI')){
						$('.chm').show();
						$('.exg').hide();
					}
				} break;
			
			case "NDVI": {
					$('.exg').hide();
					$('.chm').hide();
					$('.ndvi').show();
				} break;
		}
		*/
	});

	map.on('overlayremove', function (eventLayer) {
		
		HideAllLegens();
		
		var i = legendList.length - 1;
		var stop = false;
		while (i >= 0 && stop == false){
			if(CheckGroupActive(legendList[i])){
				stop = true;
				$('.' + legendList[i]).show();
			} else {
				i--;
			}
		}
		
		/*
		switch(eventLayer.group['name']){
			case "ExG":{
					if (!CheckGroupActive('ExG')){
						$('.exg').hide();
					}
				} break;
			
			case "CHM": {
					if (!CheckGroupActive('CHM')){
						$('.chm').hide();
						if (CheckGroupActive('ExG')){
							$('.exg').show();
						}
					}
				} break;
			
			case "NDVI": {
					if (!CheckGroupActive('NDVI')){
						$('.ndvi').hide();
						if (CheckGroupActive('CHM')){
							$('.chm').show();
						} else if (CheckGroupActive('ExG')){
							$('.exg').show();
						}
					}
				} break;
			
		}
		*/
		
		/*
		var index = -1;
		for(i = 0; i < legendList.length; i++){
			//console.log(eventLayer.group['name']);
			//console.log(legendList[i]);
			if (eventLayer.group['name'].toLowerCase() == legendList[i].toLowerCase()){
				index = i;
			}
			
			//$('.' + legendList[i]).hide();
		}
		
		var otherHigherPriority = false;
		for(j = legendList.length - 1; j > index; j--){
			if(CheckGroupActive(legendList[j])){
				otherHigherPriority = true;
			}
			
			console.log("name:" + legendList[j] + ";result:" + otherHigherPriority);
			
		}
	
		if(!otherHigherPriority) {
			$('.' + legendList[index]).show();
		}
		*/
		
		/*
		var layerGroupName = eventLayer.group['name'].toLowerCase();
		
		if(!CheckGroupActive(layerGroupName)){
			$('.' + layerGroupName).hide();
		}
		
		var i = legendList.length - 1;
		var stop = false;
		
		while (i > 0 && stop == false){
			if(CheckGroupActive(legendList[i])){
				$('.' + legendList[i]).show();
				stop = true;
			}
			i--;
		}*/
	});
}

function CheckGroupActive(name){
	
	for (i = 0; i < overLayers.length; i++){
		if (overLayers[i].group.toLowerCase() == name){
			for (j = 0; j < overLayers[i].layers.length; j++){
				if (map.hasLayer(overLayers[i].layers[j]['layer'])){
					return true;
				}
			}
		}
	}
	
	return false;
}