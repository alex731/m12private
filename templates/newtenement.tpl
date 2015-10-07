{include file="header.tpl"}
<script type="text/javascript" src="/common/jquery.prettyPhoto.js?fgd=dfgdf"></script>
<script>
{literal}
$(document).ready(function() {
	$(".gallery:first a[rel^='prettyPhoto']").prettyPhoto({animationSpeed:'slow',theme:'light_square',slideshow:10000, autoplay_slideshow: false});
});
{/literal}
</script>
<div class="box2" >
  <div class="block-title">Новостройка в Йошкар-Оле: {$title}</div>
  <div class="box-internal">
  {$page}
  </div>  
</div>
{include file="footer.tpl"}