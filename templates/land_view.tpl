{include file="header.tpl"}
<script type="text/javascript" src="/common/jquery.prettyPhoto.js"></script>
<script>
var id={$id};
{literal}
$(document).ready(function() {
	$(".gallery:first a[rel^='prettyPhoto']").prettyPhoto({animationSpeed:'slow',theme:'light_square',slideshow:10000, autoplay_slideshow: false});	
});
</script>
{/literal}

{$block_html}

{include file="footer.tpl"}