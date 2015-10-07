{include file="header.tpl"}
<style>
{include file="../style_upload.css"}
</style>
<script language="javascript" src="/common/jquery.autocomplete.js?df=dsf" type="text/javascript"></script>
<script type="text/javascript" src="/common/swfupload/swfupload.js"></script>
<script type="text/javascript" src="/common/handlers2.js?vxc=xvxc"></script>
<script type="text/javascript" src="/common/tenement.js?vxc=xvwerwexc"></script>
<script>
YANDEX_KEY = '{$YANDEX_KEY}';
is_admin = '{$is_admin}';
{literal}	
$(document).ready(function() {
	setCity(0,'г. Йошкар-Ола');
	setStreet();
	$('#number').change(loadTenement);

	swfuRealty = new SWFUpload({
		// Backend Settings
		upload_url: "/uploadPhoto.php",
		post_params: {"PHPSESSID": "{/literal}{$session_id}{literal}","kind":"1"},
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
		button_text : '<span class="button">Выберите фотографии дома <span class="buttonSmall">(6 MB Max)</span></span>',
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

updateSession = function() {
	$.get('/ajax.php?action=updateSession', '', function(o) {	    
	});
	var t=setTimeout("updateSession()",60000);
}
/*
$('#use_our_company').live('click',function(){
	if ($('#use_our_company').attr('checked')) {
		$('#div_flat__contacts').hide();
	}
	else {
		$('#div_flat__contacts').show();
	}
});
*/
</script>
{/literal}
{$block_html}
{include file="footer.tpl"}