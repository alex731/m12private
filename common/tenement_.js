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

loadTenement = function() {	
	if ($('#street').val()=='' || $('#number').val()=='') return false;
	var num = $('#number').val().replace(' ','').replace('-','').replace('"','').replace("'",'').toLocaleLowerCase();
	$('#number').val(num);	
	var q = 'action=getTenementInfo&city_id='+city_id+'&street='+encodeURI($('#street').val())+'&number='+encodeURI($('#number').val());
	$.get('/ajax.php?'+q,function(data){		
		if (data!='') {
			eval("var o= "+data+";");			
			var html = '<div class="flat_view">Адрес: <b>'+o['address']+'</b></div>';
			for (var i in o) {
				if ($.inArray(i,['tenement_id','city_id','street_id','id','lat','lon','address'])==-1) 
					html+='<div class="flat_view">'+i+': <b>'+o[i]+'</b></div>';
				else 
					html+='<input type=hidden name="'+i+'" id="'+i+'" value="'+o[i]+'">';
				
			}
			
			if (typeof(is_admin)!=undefined) {
				html+='<a href="/tenement.html?action=edit&id='+o['id']+'">Редактировать</a>';
			}
						
			$('#tenement_desc').html(html);			
			var lon = $("#lon").val();
			var lat = $("#lat").val();
			if (lon>0 && lat>0) {
				$("#YMapsID-3050").css({'height':'300px', 'width':'600px'});
				map.setCenter(new YMaps.GeoPoint($("#lon").val(),$("#lat").val()), 16, YMaps.MapType.PMAP);
				var p = createObject("Placemark", new YMaps.GeoPoint($("#lon").val(),$("#lat").val()), "constructor#pmrdmPlacemark", "Дом");			
				map.addOverlay(p);			 
				mapClick.cleanup();
				$('#put_house_txt').html('Дом на карте:');
			}
			else {				
				//$('#put_house_txt').html('Дом пока не отмечен');				
			}			
			if ($("#div_history")) {
					$("#div_history").load('/ajax.html?action=getFlatListByTenement&limit=3&id='+o['id']);	
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
		/*
		if (data=='') {
			swfuTenement = new SWFUpload({
				// Backend Settings
				upload_url: "/uploadTenement.php",
				post_params: {"PHPSESSID": "{/literal}{$session_id}{literal}"},
	
				// File Upload Settings
				file_size_limit : "6 MB",
				file_types : "*.jpg",
				file_types_description : "JPG Images",
				file_upload_limit : "0",
	
				// Event Handler Settings - these functions as defined in Handlers.js
				//  The handlers are not part of SWFUpload but are part of my website and control how
				//  my website reacts to the SWFUpload events.
				file_queue_error_handler : fileQueueError,
				file_dialog_complete_handler : fileDialogComplete,
				upload_progress_handler : uploadProgress,
				upload_error_handler : uploadError,
				upload_success_handler : uploadSuccess,
				upload_complete_handler : uploadComplete,
	
				// Button Settings
				button_image_url : "/images/SmallSpyGlassWithTransperancy_17x18.png",
				button_placeholder_id : "spanButtonPlaceholderTenement",
				button_width: 400,
				button_height: 18,
				button_text : '<span class="button">Выберите фотографии дома <span class="buttonSmall">(2 MB Max)</span></span>',
				button_text_style : '.button { font-family: Helvetica, Arial, sans-serif; font-size: 12pt; } .buttonSmall { font-size: 10pt; }',
				button_text_top_padding: 0,
				button_text_left_padding: 18,
				button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
				button_cursor: SWFUpload.CURSOR.HAND,
				
				// Flash Settings
				flash_url : "/common/swfupload/swfupload.swf",
	
				custom_settings : {
					upload_target : "divFileProgressContainerTenement"
				},
				
				// Debug Settings
				debug: false
			});
		}
		*/	
	});
}