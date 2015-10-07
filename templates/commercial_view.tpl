{include file="header.tpl"}
<script type="text/javascript" src="/common/jquery.prettyPhoto.js"></script>
<script>
var city_id={$city_id};
{literal}
$(document).ready(function() {
	$(".gallery a[rel^='prettyPhoto']").prettyPhoto({animationSpeed:'slow',theme:'light_square',slideshow:10000, autoplay_slideshow: false});
	$("#div_history").load('/ajax.html?action=getCommercialListByCity&id='+city_id);	
});
</script>
{/literal}

{$block_html}

{include file="footer.tpl"}