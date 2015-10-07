{include file="header.tpl"}
<script type="text/javascript" src="/common/jquery.prettyPhoto.js"></script>
<script>
var id={$id};
{literal}
$(document).ready(function() {
	$(".gallery:first a[rel^='prettyPhoto']").prettyPhoto({animationSpeed:'slow',theme:'light_square',slideshow:10000, autoplay_slideshow: false});
	//$(".gallery:gt(0) a[rel^='prettyPhoto']").prettyPhoto({animationSpeed:'fast',slideshow:10000,overlay_gallery:true});	
	$("#div_history").load('/ajax.html?action=getFlatListByTenement&id='+id);	
});
</script>
{/literal}
{$block_html}
{if ($url_edit)}
<a href="{$url_edit}">Редактировать</a>
{/if}
{if ($url_approve)} 
- <a href="{$url_approve}">Утвердить информацию о доме</a>
{/if}
  
{include file="footer.tpl"}