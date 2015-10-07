{include file="header.tpl"}
<script type="text/javascript" src="/common/jquery.prettyPhoto.js"></script>
<script language="javascript" src="/common/jquery.autocomplete.js" type="text/javascript"></script>
<script type="text/javascript" src="/common/cal.js"></script>
<link href="calendar.css" rel="stylesheet" type="text/css" />
<script>
{literal}
selectStreet = function(li) {		
	$('#f_street_id').val(li.extra[0]);	
}
$(document).ready(function() {
	$('#f_date').simpleDatepicker({ startdate: 2012, enddate: 2013 });
	$(".gallery:first a[rel^='prettyPhoto']").prettyPhoto({animationSpeed:'slow',theme:'light_square',slideshow:10000, autoplay_slideshow: false});
	var minChars=3;
	var matchContains = 2;
	$('#f_street').autocomplete = undefined;	
	$('#f_street').autocomplete('/ajax.php', {
		delay:4,
		minChars: minChars,
		selectOnly:false,
		mustMatch:true,
		autoFill: false,
		selectFirst:true,
		matchCase: 0,
		matchContains: matchContains,
		matchSubset: 10,
		maxItemsToShow: 10,		
		onItemSelect:selectStreet,
		extraParams: {action:'streetList','city_id':0,rnd:Math.round((Math.random()*10000))}
	});

	if (rooms!='') {
		$('#f_rooms [value='+rooms+']').attr('selected', 'selected');
	}
	if (date!='') {			
		$('#f_date').val(date);
	}
	if (price!='') {
		$('#f_price [value='+price+']').attr('selected', 'selected');
	}
	if (price_sq!='') {
		$('#f_price_sq [value='+price_sq+']').attr('selected', 'selected');
	}
	if (tenement!='') {
		$('#f_tenement [value='+tenement+']').attr('selected', 'selected');
	}
	if (kitchen!='') {
		$('#f_kitchen [value='+kitchen+']').attr('selected', 'selected');
	}
	if (total_area!='') {
		$('#f_total_area [value='+total_area+']').attr('selected', 'selected');
	}
	if (balcon!='') {
		$('#f_balcon').attr('checked', 'true');
	}
	if (no_corner!='') {
		$('#f_no_corner').attr('checked', 'true');
	}
	if (storey_no_first!='') {
		$('#f_storey_no_first').attr('checked', 'true');
	}
	if (storey_no_last!='') {
		$('#f_storey_no_last').attr('checked', 'true');
	}
	if (bath!='') {
		$('#f_bath').attr('checked', 'true');
	}
	if (photo!='') {
		$('#f_photo').attr('checked', 'true');
	}
	if (regions!='') {
		$('#f_regions').attr('checked', 'true');
	}
	if (newt!='') {
		$('#f_newt [value='+newt+']').attr('selected', 'selected');
	}
	if (street!='') {			
		$('#f_street').val(street);
		$('#f_street_id').val(street_id);
	}
	if (heating!='') {
		$('#f_heating [value='+heating+']').attr('selected', 'selected');
	}
	if (is_owner!='') {
		$('#f_is_owner').attr('checked', 'true');
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
<div class="box2" >
  <div class="block-title">{$block_name}</div>
  <div class="box-internal">
{$block_html}
</div></div>
<script>
{if $_SESSION.admin}
{literal}
delFlat = function(id) {
	if (confirm('Вы уверены удалить эту квартиру?')) {
		location.href = '/flat.html?action=delete&id='+id;
	}
	return 0;			
}
{/literal}
{/if}
{if $_SESSION.user_id>0}
	{literal}
	update = function(id) {
		if (confirm('Вы уверены что хотитет обновить дату этого объявления?')) {
			location.href = '/flat.html?action=updateDate&id='+id;
		}
		return 0;			
	}
	remove = function(id) {
		if (confirm('Вы уверены что хотитет снять это объявление?')) {
			location.href = '/flat.html?action=remove&id='+id;
		}
		return 0;			
	}
	sold = function(id) {
		if (confirm('Подтверждаете продажу квартиры?')) {
			location.href = '/flat.html?action=sold&id='+id;
		}
		return 0;			
	}
	{/literal}
{/if}
</script>

{include file="footer.tpl"}