{include file="header.tpl"}
 <td><table cellpadding="0" cellspacing="0" border="0" height="100%">
 <tr>  
  <td height="100%" valign="top">
   <table cellpadding="0" cellspacing="0" height="100%" border="0">
  <tr><td valign="top" height="100%">
  <!--  =========Blok News================-->
   <table border="0" cellpadding="0" cellspacing="0" height="100%"><tr>
    <td height="100%" align="center" valign="top">
     <table cellpadding="1" cellspacing="0" border="0" class="table_background1" height="100%">
	  <tr height="100%"><td align="center" valign="top" height="100%">
	   <table cellpadding="1" cellspacing="1" border="0" class="table_background" height="100%">
		 <tr class="table_background1">
		  <td align="center" valign="top" height="100%">
		  <table cellpadding="0" cellspacing="1" border="0" class="table_background1" width="160">
		    <tr class="table_background" height="20"><td class="menu_header">{$tip.title1}</td></tr>
			<tr><td class="tip_content" valign="top">{$tip.content1}</td></tr>
 		  </table>
		  </td></tr></table></td></tr>
  </table>
  </td></tr></table>
  <!--  =========END Blok News================-->
  </td></tr>
  </table> 
  </td>
 <!--  =========Blok Main================-->
  <td height="100%" align="center" valign="top" width="100%">
   <table cellpadding="1" cellspacing="0" border="0" class="table_background1" height="100%" width="100%">
	<tr height="100%"><td align="center" valign="top" width="100%">	
	   <table cellpadding="1" cellspacing="1" border="0" class="table_background" width="100%" height="100%">
		 <tr class="table_background1">
		  <td align="center" valign="top" width="100%">
		  <form action="login.html" method="post" enctype="application/x-www-form-urlencoded" name="ClientReg">
		  <table cellpadding="0" cellspacing="1" border="0" class="table_background1" width="100%" height="100%">
		   <tr class="table_background" height="20"><td class="menu_header" align="center" colspan="3"><h2>{$txt.label.7}</h2></td></tr>
			<tr><td colspan="3" class="error_text">{$err}</td></tr>
			<tr><td colspan="3" class="base_text">{$txt.login_page.4}</td></tr>
			<tr><td rowspan="8" height="1">&nbsp;</td><td colspan="2" height="1"></td></tr>
			<tr height="25"><td class="base_text">{$txt.email} ({$txt.login})<font class="require">*</font>:</td><td><input name="Login" type="text" size="25" maxlength="60" value="{$form_value.email}"></td></tr>
			<tr height="25"><td class="base_text">{$txt.password}<font class="require">*</font>:</td><td><input name="Password" type="password" size="25" maxlength="60" value="{$form_value.password}"></td></tr>
			<tr><td colspan="3">&nbsp;</td></tr>
			<tr height="25"><td colspan="3" align="center">
				<input name="Log_in" type="submit" value="{$txt.login}">&nbsp;&nbsp;
				<input name="Reset" type="reset" value="{$txt.reset}">
				<input name="IdClient" type="hidden" value="{$form_value.id_client}">
			 </td></tr>			
			<tr><td colspan="3" height="100%"></td></tr>
 		  </table>
		  </form></td></tr></table></td></tr></table>
	</td>		  
  <td height="100%" valign="top">
   <table cellpadding="0" cellspacing="0" height="100%">
   <tr><td valign="top" height="100%">
   <!--=============Blok Do you know ... ==================-->
   <table cellpadding="0" cellspacing="0" height="100%"><tr>
    <td height="100%" align="center" valign="top">
     <table cellpadding="1" cellspacing="0" border="0" class="table_background1" height="100%">
	  <tr height="100%"><td align="center" valign="top">
	   <table cellpadding="1" cellspacing="1" border="0" class="table_background" width="160" height="100%">
		 <tr class="table_background1">
		  <td align="center" valign="top" height="100%">
		  <table cellpadding="0" cellspacing="1" border="0" class="table_background1" width="160" height="100%">
		   <tr class="table_background" height="20"><td class="menu_header"><h2>{$tip.title2}</h2></td></tr>
			<tr><td class="tip_content" valign="top">{$tip.content2}</td></tr>
			<tr><td></td></tr>				
 		  </table>
		  </td></tr></table></td></tr>
  </table>
  </td></tr></table>
  <!--================END Blok Do you know ... ================-->
  </td></tr>
  </table> 
  </td>
 
  
  <!--End bloks -->
  </tr></table>
 </td>
{include file="footer.tpl"}