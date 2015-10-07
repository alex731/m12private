{include file="header.tpl"}
{literal}
<style>
img.comp_logo {
max-height:100px;
max-width:150px;
vertical-align:middle;
border: 2px solid #ccc;
border-radius: 5px;
margin:2px;
padding:2px;
}
</style>
<script type="text/javascript" src="/common/jquery.prettyPhoto.js"></script>
<script>
$(document).ready(function() {
	$(".gallery:first a[rel^='prettyPhoto']").prettyPhoto({animationSpeed:'slow',theme:'light_square',slideshow:10000, autoplay_slideshow: false});
	//$(".gallery:gt(0) a[rel^='prettyPhoto']").prettyPhoto({animationSpeed:'fast',slideshow:10000,overlay_gallery:true});		
	updateSession();	
});
function loadTenement() {
	if ($('#street').val()=='' || $('#number').val()=='') return false;
	var q = 'action=getTenementInfo&city_id='+$('#city_id').val()+'&street='+$('#street').val()+'&number='+$('#number').val();
	$.get('/ajax.php?'+q,function(data){
		if (data!='') {
			eval("var o= "+data+";");		
			var html = '';
			for (var i in o) {
				if (i!='tenement_id'&&i!='city_id'&&i!='street_id') html+='<div>'+i+': '+o[i]+'</div>';
				else html+='<input type=hidden name="'+i+'" value="'+o[i]+'">';				
			}
			$('#tenement_desc').html(html);
		}
		else $('#span_type_id').show();
	});
}
updateSession = function() {
	$.get('/ajax.php?action=updateSession', '', function(o) {	    
	});
	var t=setTimeout("updateSession()",60000);
}
</script>
{/literal}

<table height="100%">
<tr>
	<td style="vertical-align: top;">{$block_flat_html}</td>
	<td style="vertical-align: top;">{$block_tenement_html}</td>
</tr>
<tr><td colspan="2">

</td></tr>
</table>
{include file="footer.tpl"}