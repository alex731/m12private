loadStreets = function() {
	$.get('/ajax.php?action=getAnyStreet&id='+city_id,function(data){		
		if (data!='') {
			var a = data.split('|');
			if (a[0]>0) {
				//$('#street').val(a[1]);
				setStreet(a[0],a[1]);
			}					
		}
	});
}
liFormatCity = function(row, i, num) {
	var result = row[0];
	return result;
}
selectCity = function(li) {	
	city_id = li.extra[0];	
	//li.selectValue;
	if (typeof(city_id)=='undefined') city_id=0;
	$('#city_id').val(city_id);	
	$('#street').autocomplete('/ajax.php', {
		delay:4,
		minChars: 3,
		selectOnly:false,
		mustMatch:true,
		autoFill: false,
		selectFirst:true,
		matchCase: 1,
		matchContains: 2,
		matchSubset: 1,
		maxItemsToShow: 10,			
		extraParams: {action:'streetList','city_id':city_id,rnd:Math.round((Math.random()*10000))}
	});
	if (li.extra[1]>0 && li.extra[2]>0) {
		$("#lat").val(li.extra[1]);
		$("#lon").val(li.extra[2]);	
		map.setCenter(new YMaps.GeoPoint($("#lon").val(),$("#lat").val()), 10, YMaps.MapType.PMAP);		
		var p = createObject("Placemark", new YMaps.GeoPoint($("#lon").val(),$("#lat").val()), "constructor#pmrdmPlacemark", "Дом");			
		map.addOverlay(p);			 
		mapClick.cleanup();
		if (typeof(KIND)=='undefined') obj = 'Дом';
		else obj = 'Земельный участок';
		$('#put_house_txt').html(obj+' на карте:');
	}
	//house
	if (typeof(KIND)=='undefined') {
		$("#div_history").load('/ajax.html?action=getHouseListByCity&id='+city_id);
		loadStreets();
		if (city_id>1) {
			$('#div_street > label > font.require').hide();
			$('#div_number > label > font.require').hide();
		}
		else {
			$('#div_street > label > font.require').show();
			$('#div_number > label > font.require').show();
		}
	}
	else if (KIND == 'commercial') {
		$("#div_history").load('/ajax.html?action=getCommercialListByCity&id='+city_id);
	}
	else {
		$("#div_history").load('/ajax.html?action=getLandListByCity&id='+city_id);
	}
}

setCity  = function(id,val){
	$('#city').autocomplete('/ajax.php', {
		delay:4,
		minChars: 2,
		selectOnly:false,
		mustMatch:true,
		autoFill: false,
		selectFirst:true,
		matchCase: 0,
		matchContains: 1,
		matchSubset: 1,
		maxItemsToShow: 10,
		formatItem:liFormatCity,	
		onItemSelect:selectCity,
		defaultVal:val,
		extraParams: {action:'cityList',rnd:Math.round((Math.random()*10000))}
	});
	$('#city').val(val);
	$('#city_id').val(id);	
	city_id=id;
}
setStreet = function(id, val) {
	var minChars=3;
	var matchContains = 2;
	$('#street').autocomplete = undefined;	
	$('#street').autocomplete('/ajax.php', {
		delay:4,
		minChars: minChars,
		selectOnly:false,
		mustMatch:true,
		autoFill: true,
		selectFirst:true,
		matchCase: 1,
		matchContains: matchContains,
		matchSubset: 1,
		maxItemsToShow: 10,
		defaultVal:val,
		extraParams: {action:'streetList','city_id':city_id,rnd:Math.round((Math.random()*10000))}
	});
}