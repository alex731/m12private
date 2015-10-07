{include file="header.tpl"}
{literal}
<style>
{include file="../style_upload.css"}
</style>
<script type="text/javascript" src="/common/swfupload/swfupload.js"></script>
<script type="text/javascript" src="/common/handlers2.js?vxc=xvxc"></script>
<script>
$(document).ready(function() {
	$('#street').autocomplete('/ajax.php', {
		delay:4,
		minChars: 2,
		matchSubset: 1,
		matchContains: 1,
		autoFill: false,
		maxItemsToShow: 10,
		extraParams: {action:'streetList',city_id:$('#city_id').val(),rnd:Math.round((Math.random()*10000))}
	});
	//$('#span_type_id').hide();
	$('#number').change(loadTenement);
	$('#city_id').change(function(){
		$('#street').autocomplete('/ajax.php', {
			delay:4,
			minChars: 2,
			matchSubset: 1,
			matchContains: 1,
			autoFill: false,
			maxItemsToShow: 10,
			extraParams: {action:'streetList',city_id:$('#city_id').val()}
		});
	});

	/*
	swfuFlat = new SWFUpload({
		// Backend Settings
		upload_url: "/uploadFlat.php",
		post_params: {"PHPSESSID": "{/literal}{$session_id}{literal}"},

		// File Upload Settings
		file_size_limit : "2 MB",	// 2MB
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
		button_placeholder_id : "spanButtonPlaceholderFlat",
		button_width: 180,
		button_height: 18,
		button_text : '<span class="button">Выберите фотографии квартиры <span class="buttonSmall">(2 MB Max)</span></span>',
		button_text_style : '.button { font-family: Helvetica, Arial, sans-serif; font-size: 12pt; } .buttonSmall { font-size: 10pt; }',
		button_text_top_padding: 0,
		button_text_left_padding: 18,
		button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
		button_cursor: SWFUpload.CURSOR.HAND,
		
		// Flash Settings
		flash_url : "/common/swfupload/swfupload.swf",

		custom_settings : {
			upload_target : "divFileProgressContainerFlat"
		},
		
		// Debug Settings
		debug: false
	});
	*/
	updateSession();
	//$('#div_flat__contacts').hide();
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
loadTenement = function() {
	if ($('#street').val()=='' || $('#number').val()=='') return false;
	var q = 'action=getTenementInfo&city_id='+$('#city_id').val()+'&street='+$('#street').val()+'&number='+$('#number').val();
	$.get('/ajax.php?'+q,function(data){
		if (data!='') {
			eval("var o= "+data+";");		
			var html = '';
			for (var i in o) {
				//i!='tenement_id'&&i!='city_id'&&i!='street_id'&&i!='id'&&
				if ($.inArray(i,['tenement_id','city_id','street_id','id','lat','lon'])==-1) 
					html+='<div class="flat_view">'+i+': <b>'+o[i]+'</b></div>';
				else 
					html+='<input type=hidden name="'+i+'" id="'+i+'" value="'+o[i]+'">';								
			}
			html = '<div class="flat_view">Адрес: <b>'+o['adress']+'</b></div>'+html;
			$('#tenement_desc').html(html);
			var lon = $("#lon").val();
			var lat = $("#lat").val();
			if (lon>0 && lat>0) {
				map.setCenter(new YMaps.GeoPoint($("#lon").val(),$("#lat").val()), 16, YMaps.MapType.PMAP);
				var p = createObject("Placemark", new YMaps.GeoPoint($("#lon").val(),$("#lat").val()), "constructor#pmrdmPlacemark", "Дом");			
				map.addOverlay(p);			 
				mapClick.cleanup();
				$('#put_house_txt').html('Дом на карте:');
			}
			else {
				$('#put_house_txt').html('Дом пока не отмечен');
			}		
		}
		else $('#span_type_id').show();
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
updateSession = function() {
	$.get('/ajax.php?action=updateSession', '', function(o) {	    
	});
	var t=setTimeout("updateSession()",60000);
}

$('#use_our_company').live('click',function(){
	if ($('#use_our_company').attr('checked')) {
		$('#div_flat__contacts').hide();
	}
	else {
		$('#div_flat__contacts').show();
	}
});
</script>
{/literal}
{$block_html}
{include file="footer.tpl"}