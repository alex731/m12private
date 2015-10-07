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
	var minChars=2;
	var matchContains = 10;
	$('#street').autocomplete = undefined;	
	$('#street').autocomplete('/ajax.php', {
		delay:4,
		minChars: minChars,
		selectOnly:false,
		mustMatch:true,
		autoFill: false,
		selectFirst:true,
		matchCase: 0,
		matchContains: matchContains,
		matchSubset: 10,
		maxItemsToShow: 10,			
		extraParams: {action:'streetList','city_id':city_id,rnd:Math.round((Math.random()*10000))}
	});	
	/*
	var arr = $("#city").val().split(' ');			
	var c=arr[1];
	for (var i=2;i<arr.length;i++) c=c+' '+arr[i];				
	var geo_url = 'http://geocode-maps.yandex.ru/1.x/?geocode='+c+'&key='+YANDEX_KEY+'&format=json&ll='+LON_CENTER_REGION+','+LAT_CENTER_REGION+'&spn=4,4&rspn=1';
	$.getJSON(geo_url,function(data){
		var coord = data.response.GeoObjectCollection.featureMember[0].GeoObject.Point.pos.split(' ');
		$("#lon").val(coord[0]);
		$("#lat").val(coord[1]);				
		map.setCenter(new YMaps.GeoPoint($("#lon").val(),$("#lat").val()), 12, YMaps.MapType.PMAP);
		
		var p = createObject("Placemark", new YMaps.GeoPoint($("#lon").val(),$("#lat").val()), "constructor#pmrdmPlacemark", "Дом");			
		map.addOverlay(p);			 
		mapClick.cleanup();
		$('#put_house_txt').html('Дом на карте:');
		loadStreets();
	});
	*/
	if (li.extra[1]>0 && li.extra[2]>0) {
		$("#lat").val(li.extra[1]);
		$("#lon").val(li.extra[2]);	
		map.setCenter(new YMaps.GeoPoint($("#lon").val(),$("#lat").val()), 10, YMaps.MapType.PMAP);		
		var p = createObject("Placemark", new YMaps.GeoPoint($("#lon").val(),$("#lat").val()), "constructor#pmrdmPlacemark", "Дом");			
		map.addOverlay(p);			 
		mapClick.cleanup();
		$('#put_house_txt').html('Дом на карте:');
	}
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

selectStreet = function(li) {
	street_id = li.extra[0];
	if (typeof(KIND)=='undefined' || KIND=='commercial') { 
		$('#street_id').val(street_id);
		$('#tenement_id').val(0);
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
	var minChars=2;
	var matchContains = 2;	
	$('#street').autocomplete = undefined;	
	$('#street').autocomplete('/ajax.php', {
		delay:4,
		minChars: minChars,
		selectOnly:false,
		mustMatch:true,
		autoFill: false,
		selectFirst:true,
		matchCase: 0,
		matchContains: matchContains,
		matchSubset: 10,
		maxItemsToShow: 10,
		defaultVal:val,
		onItemSelect:selectStreet,
		extraParams: {action:'streetList','city_id':city_id,rnd:Math.round((Math.random()*10000))}
	});
}

loadTenement = function(tenement_id) {	
	var q;
	if (typeof(tenement_id)!=undefined && tenement_id>0) {
		q = 'action=getTenementInfo&tenement_id='+tenement_id;		
	}
	else {
		if ($('#street').val()=='' || $('#number').val()=='') return false;
		var num = $('#number').val().replace(' ','').replace('-','').replace('"','').replace("'",'').toLocaleLowerCase();
		$('#number').val(num);	
		q = 'action=getTenementInfo&city_id='+city_id+'&street='+encodeURI($('#street').val())+'&number='+encodeURI($('#number').val());		
	}
	$.get('/ajax.php?'+q,function(data){		
		if (data!='') {
			eval("var o= "+data+";");			
			var html = '<div class="flat_view">Адрес: <b>'+o['address']+'</b></div>';
			if (typeof(KIND)=='undefined' || KIND!='commercial') {
				for (var i in o) {
					if ($.inArray(i,['tenement_id','city_id','street_id','id','lat','lon','address'])==-1) 
						html+='<div class="flat_view">'+i+': <b>'+o[i]+'</b></div>';
					else 
						html+='<input type=hidden name="'+i+'" id="'+i+'" value="'+o[i]+'">';				
				}			
				if (typeof(is_admin)!='undefined') {
					if (is_admin!='') html+='<a href="/tenement.html?action=edit&id='+o['id']+'">Редактировать</a>';
				}
				$('#tenement_desc').html(html);
				if ($("#div_history")) {
					$("#div_history").load('/ajax.html?action=getFlatListByTenement&limit=3&id='+o['id']);	
				}
			}
			else {				
				$('#city_id').val(o['city_id']);
				$('#tenement_id').val(o['id']);
				$('#div_street').hide();
				$('#div_number').hide();
				$('#div_number').after(html);
				//$('#address').html(html);
			}
			var lon = $("#lon").val();
			var lat = $("#lat").val();
			if (o['lon']>0) lon = o['lon'];
			if (o['lat']>0) lat = o['lat'];
			
			if (lon>0 && lat>0) {
				if (typeof(KIND)=='undefined' || KIND!='commercial') $("#YMapsID-3050").css({'height':'300px', 'width':'600px'});
				map.setCenter(new YMaps.GeoPoint(lon,lat), 16, YMaps.MapType.PMAP);
				var p = createObject("Placemark", new YMaps.GeoPoint(lon,lat), "constructor#pmrdmPlacemark", "Дом");			
				map.addOverlay(p);			 
				mapClick.cleanup();
				$('#put_house_txt').html('Дом на карте:');
			}
			else {				
				//$('#put_house_txt').html('Дом пока не отмечен');				
			}			
		}
		else {
			$('#span_type_id').show();
			var arr = $("#city").val().split(' ');			
			var c=arr[1];
			for (var i=2;i<arr.length;i++) c=c+' '+arr[i];			
			var address = c+', '+$('#street').val()+', д. '+$('#number').val();
			var geo_url = 'http://psearch-maps.yandex.ru/1.x/?text='+address+'&key='+YANDEX_KEY+'&format=json&ll='+LON_CENTER_REGION+','+LAT_CENTER_REGION+'&spn=4,4&rspn=1';
			//var geo_url = 'http://geocode-maps.yandex.ru/1.x/?geocode='+address+'&key='+YANDEX_KEY+'&format=json&ll='+LON_CENTER_REGION+','+LAT_CENTER_REGION+'&spn=4,4&rspn=1';
			$.getJSON(geo_url,function(data){
				var coord = data.response.GeoObjectCollection.featureMember[0].GeoObject.Point.pos.split(' ');
				$("#lon").val(coord[0]);
				$("#lat").val(coord[1]);				
				map.setCenter(new YMaps.GeoPoint($("#lon").val(),$("#lat").val()), 16, YMaps.MapType.PMAP);
				var p = createObject("Placemark", new YMaps.GeoPoint($("#lon").val(),$("#lat").val()), "constructor#pmrdmPlacemark", "Дом");			
				map.addOverlay(p);				
			});
		}	
	});
}