{include file="header.tpl"}
{literal}
<script>

$(document).ready(function() {
	$('#street').autocomplete('/ajax.php', {
		delay: 10,
		minChars: 1,
		matchSubset: 1,
		autoFill: true,
		maxItemsToShow: 10,
		extraParams: {action:'street_list'}
	});
});

</script>

{/literal}

<table class="tborder" cellpadding="5" cellspacing="1" border="0" width="100%" align="center"> 
<tr><td class="theader">{$title_content}</td></tr>
<tr><td class="tcontent">

<form method="post" enctype="application/x-www-form-urlencoded" name="EditObject">
   <fieldset><legend>Описание дома</legend>         
   <div class="form_convert">
   {$tenement_html_form}
   <div align=center>
   <br>
   <input type="hidden" name='action' value='add_save'>
   <input type='submit' value='Сохранить'>
   </div>
   
   </div>
</fieldset>
<fieldset><legend>Описание квартиры</legend>
{$flat_html_form}
</fieldset>
</form>   
</td></tr></table>
  
   
{include file="footer.tpl"}