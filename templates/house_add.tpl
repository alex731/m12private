{include file="header.tpl"}
<script language="javascript" src="/common/jquery.autocomplete.js?d45f=dsd6&fg=dsfd7gfg&sds=dfgsdf7sd" type="text/javascript"></script>
<script type="text/javascript" src="/common/swfupload/swfupload.js"></script>
<script type="text/javascript" src="/common/handlers2.js?vxc=xvxc"></script>
<script type="text/javascript" src="/common/house.js?4df=dre36xc"></script>
<script>
YANDEX_KEY = '{$YANDEX_KEY}';
is_admin = '{$is_admin}';
LAT_CENTER_REGION = '{$LAT_CENTER_REGION}';
LON_CENTER_REGION = '{$LON_CENTER_REGION}';
{literal}
$(document).ready(function() {	
	setCity(0,'г. Йошкар-Ола');		
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