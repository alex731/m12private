{include file="header.tpl"}
<script type="text/javascript" src="/common/jquery.prettyPhoto.js"></script>
<script language="javascript" src="/common/jquery.autocomplete.js" type="text/javascript"></script>
<script>
{literal}
liFormatCity = function(row, i, num) {
	var result = row[0];
	return result;
}
selectCity = function(li) {		
	$('#f_city_id').val(li.extra[0]);	
}
$(document).ready(function() {
	$(".gallery:first a[rel^='prettyPhoto']").prettyPhoto({animationSpeed:'slow',theme:'light_square',slideshow:10000, autoplay_slideshow: false});
	$('#f_city').autocomplete = undefined;
	$('#f_city').autocomplete('/ajax.php', {
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
		extraParams: {action:'cityList',rnd:Math.round((Math.random()*10000))}
	});

	if (price!='') {
		$('#f_price [value='+price+']').attr('selected', 'selected');
	}
	if (price_sq!='') {
		$('#f_price_sq [value='+price_sq+']').attr('selected', 'selected');
	}
	if (total_area!='') {
		$('#f_total_area [value='+total_area+']').attr('selected', 'selected');
	}
	if (photo!='') {
		$('#f_photo').attr('checked', 'true');
	}
	if (regions!='') {
		$('#f_regions').attr('checked', 'true');
	}
	if (house!='') {
		$('#f_house [value='+house+']').attr('selected', 'selected');
	}
	if (radius!='') {
		$('#f_radius').val(radius);
	}
	if (city!='') {			
		$('#f_city').val(city);
		$('#f_city_id').val(city_id);
	}	
	if (heating!='') {
		$('#f_heating [value='+heating+']').attr('selected', 'selected');
	}
	var priceControl = function(){
		if ($('#price_min').val()>0 || $('#price_max').val()>0) {
			 $('#f_price').attr('disabled','disabled');
		}
		else if ($('#price_min').val()=='' && $('#price_max').val()=='') {
			$('#f_price').removeAttr('disabled');				
		}
	}
	$('#price_min').blur(priceControl);
	$('#price_max').blur(priceControl);

	$('#close_filter').click(function(){
		$('#filter').hide();
		$('#show_filter_div').show();			
	});
	$('#show_filter').click(function(){
		$('#show_filter_div').hide();	
		$('#filter').show();						
	});
	$('#btn_map').click(function(){
		$('#action').val('map');
		document.filter_form.submit();									
	});
			
});
</script>
{/literal}
{$block_html}
<script>
{if $_SESSION.admin}
{literal}
delObject = function(id) {
	if (confirm('Вы уверены удалить это объявление?')) {
		location.href = '/house.html?action=delete&id='+id;
	}
	return 0;			
}
{/literal}
{/if}
{if $_SESSION.user_id>0}
	{literal}
	update = function(id) {
		if (confirm('Вы уверены что хотитет обновить дату этого объявления?')) {
			location.href = '/house.html?action=updateDate&id='+id;
		}
		return 0;			
	}
	remove = function(id) {
		if (confirm('Вы уверены что хотитет снять это объявление?')) {
			location.href = '/house.html?action=remove&id='+id;
		}
		return 0;			
	}
	sold = function(id) {
		if (confirm('Подтверждаете продажу объекта?')) {
			location.href = '/house.html?action=sold&id='+id;
		}
		return 0;			
	}
	{/literal}
{/if}
</script>

{include file="footer.tpl"}