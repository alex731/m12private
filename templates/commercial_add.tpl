{include file="header.tpl"}
<script language="javascript" src="/common/jquery.autocomplete.js?d45f=dsd6&fg=dsfd7gfg&sds=dfgsdf7sd" type="text/javascript"></script>
<script type="text/javascript" src="/common/swfupload/swfupload.js"></script>
<script type="text/javascript" src="/common/handlers2.js?vxc=xvxc"></script>
<script type="text/javascript" src="/common/tenement.js?dfgc234=dg234sf"></script>
<!-- <script type="text/javascript" src="/common/house.js?4df=dre36xc"></script> -->
<script>
YANDEX_KEY = '{$YANDEX_KEY}';
is_admin = '{$is_admin}';
LAT_CENTER_REGION = '{$LAT_CENTER_REGION}';
LON_CENTER_REGION = '{$LON_CENTER_REGION}';
KIND = 'commercial';
var is_error = '{$is_error}';
var city_id = '{$city_id}';
var street_id = '{$street_id}';
var street = "{$street}";
var tenement_id = '{$tenement_id}';
var lon = '{$lon}';
var lat = '{$lat}';
{literal}
$(document).ready(function() {	
	if (!is_error) {
		setCity(0,'г. Йошкар-Ола');		
		setStreet(street_id,street);
		if (!tenement_id && street!='') {
			$('#street').val(street);
		}				
	}
	else {
		if (tenement_id>0) {
			loadTenement(tenement_id);
		}
		else {
			$('#city_id').val(city_id);
			$('#street_id').val(street_id);
			$("#lon").val(lon);
			$("#lat").val(lat);
			YMaps.jQuery(window).load(function () {
				setTimeout(function(){
					map.setCenter(new YMaps.GeoPoint($("#lon").val(),$("#lat").val()), 12, YMaps.MapType.PMAP);
					var p = createObject("Placemark", new YMaps.GeoPoint($("#lon").val(),$("#lat").val()), "constructor#pmrdmPlacemark", "Дом");			
					map.addOverlay(p);
				},1000);
			});
		}
				
					
	}
	$('#number').change(loadTenement);
	if (tenement_id>0 || street_id>0 || street!='') {
		swfuRealty = new SWFUpload({
			// Backend Settings
			upload_url: "/uploadPhoto.php",
			post_params: {"PHPSESSID": "{/literal}{$session_id}{literal}","kind":"9"},
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
			button_placeholder_id : "spanButtonPlaceholder",
			button_width: 300,
			button_height: 18,
			button_text : '<span class="button">Выберите фотографии объекта <span class="buttonSmall">(6 MB Max)</span></span>',
			button_text_style : '.button { font-family: Helvetica, Arial, sans-serif; font-size: 12pt; } .buttonSmall { font-size: 10pt; }',
			button_text_top_padding: 0,
			button_text_left_padding: 18,
			button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
			button_cursor: SWFUpload.CURSOR.HAND,		
			// Flash Settings
			flash_url : "/common/swfupload/swfupload.swf",
	
			custom_settings : {
				upload_target : "divFileProgressContainerPhoto"
			},
			
			// Debug Settings
			debug: false
		});
	}
	updateSession();
});

delPhoto = function(name) {
	var q = 'action=delPhoto&name='+name;
	$.get('/ajax.php?'+q,function(data){
		if (data!='') {
			$('#table'+data).remove();
		}
		else {
			alert('Произошла ошибка.');
		}
	});
}

updateSession = function() {
	$.get('/ajax.php?action=updateSession', '', function(o) {	    
	});
	var t=setTimeout("updateSession()",20000);
}
</script>
{/literal}
{$block_html}
{include file="footer.tpl"}