<td width="100%" align="left">   
<table cellpadding="0" cellspacing="0" width="100%">
<tr>
 <td align="left">
  <table cellpadding="0" cellspacing="0" border="0">
	<tr>
	 <td><a href="index.html" class="menu">{$txt.menu.0}</a></td>
	 <td><a href="domains-for-sale.html" class="menu">{$txt.menu.2}</a></td>
	 <td><a href="domains-news.html" class="menu">{$txt.menu.13}</a></td>		 
	 <td><a href="domains-services.html" class="menu">{$txt.menu.1}</a></td>
	 <td><a href="buy-domainname-help.html" class="menu">{$txt.menu.18}</a></td>		 		     	 
	 <td><a href="about-us.html" class="menu">{$txt.menu.12}</a></td>	 		 		 
	 <td><a href="contact-us.html" class="menu">{$txt.menu.10}</a></td>	 		 		 		 
	 <td align="right" style="padding-left:50px;"><a href="profile_domains.html" class="menu">{$txt.menu.19}</a></td>
	 <td><a href="logoff.html" class="menu" title="{$txt.atitle.14}">{$txt.menu.7}</a></td>	 		 		 		 
	 </tr>
	 <td></td>
  </table>
 </td>
 <td></td>
 <td align="right">
  <table cellpadding="0" cellspacing="0">
	    <tr>
	 <td><a href="?language=english" title="USA (english version)"><img border="0" src="{$config.site}images/usa.gif"></a></td>
	 <td><a href="?language=english" title="Canada (english version)"><img border="0" src="{$config.site}images/canada.gif"></a></td>
	 <td><a href="?language=english" title="Australia (english version)"><img border="0" src="{$config.site}images/australia.gif"></a></td>
	 <td><a href="?language=english" title="New Zeland (english version)"><img border="0" src="{$config.site}images/new_zealand.gif"></a></td>	  
	 {section name=g loop=$lang}
     <td><a href="?language={$lang[g].lang}" title="{$lang[g].lang_title}"><img border="0" src="{$config.site}show_pic.php?id_lang={$lang[g].id_lang}"></a></td>
    {/section}
	</tr>
  </table>	
 </td>
</tr>
</table>   
</td>
