<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<title>{$title}</title>
<meta http-equiv="Content-Type" content="text/html; charset="UTF-8">
<meta name="keywords" content="{$keywords}">
<meta name="description" content="{$meta_description}">
<meta name="author" content="{$meta_author}">
<meta name="revisit-after" content="{$meta_revisit} days">
<meta name="copyright" content="&copy; 2011 {$config.company}">
<meta name="robots" content="all" />
<link href="style2.css?xcv=gdfgdg" rel="stylesheet" type="text/css" />
<link href="bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="bootstrap-responsive.min.css" rel="stylesheet" type="text/css" /> 
<link href="style_upload.css" rel="stylesheet" type="text/css" />
<link href="prettyPhoto.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="menu_style.css" type="text/css" />
<link rel="stylesheet" href="boxy.css" type="text/css" />
{$rss}

<script language="javascript" src="/common/jquery-1.4.3.min.js" type="text/javascript"></script>
<script type="text/javascript">  
  {literal}
  $_site_url = 'http://mari12.ru/';  
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-25226117-1']);
  _gaq.push(['_trackPageview']);
  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();  
  {/literal}
</script>
<link rel="icon" href="/favicon.ico" type="image/x-icon">
<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
{if $subdomain!=''}
<base href="http://{$MAIN_DOMAIN}/" target="_blank" />
{/if}
</head>
<body>
<table cellpadding="0" cellspacing="0" border="0" width="100%">
 <tr>
  <td valign="middle" width="100%" height="100%" colspan=2>
   <table cellpadding="0" cellspacing="0" border="0" width="100%" height="100%">
    <tr height="100">
	 <td class="head_top">
	  <table cellpadding="0" cellspacing="0" border="0" width="100%">
	   <tr>
	    <td valign="middle" align="left">
	    <a href="index.html" title="Главная">
		<font class="site_title">Мой Новый <font color="#615e60">Дом</font></font></a><br><font class="slogan">Найди, <font color="#d50707">где тебе жить</font></font></td>
	    <td valign="middle" align="left"><p style="padding-top:10px;"><h1 class="site_subtitle">Недвижимость в Йошкар-Оле и Марий Эл<br><font color="#d50707">Каталог</font> квартир, домов, земельных участков</h1></p>
		
<!--		<p>
		<h1 style="color:#d50707">Поздравляем с Рождеством Христовым!</h1>
		</p>
		<p>
		<h1 style="color:#d50707">Поздравляем с Крещением Господним!</h1>
		</p>
        <p>
		<h1 style="color:#d50707">Поздравляем со Светлым Христовым Воскресением!</h1>
		</p>
        -->
		</td>
	    <td align="right"><img src="/images/top_{$top_img_num}.jpg"></td>	    	   
	   </tr>  
	   <tr><td class="menu_header white_text" colspan="3">
	   {$hello}
	   </td></tr>
	  </table>
	 </td>
	 <td align="right" width="160" rowspan="2"></td>
	</tr> 	
	<tr style="vertical-align:middle;">	
		<td class="menu2">
		<table width="100%" cellpadding="0" cellspacing="0">
		<tr><td align="left" style="vertical-align:middle;">
			   {if !$_SESSION.user_id}
		<form action="user.html?action=login" method="post" class="white_text form-inline" style="padding:0px; margin:0px;">
			 Вход для агентств &rarr; Логин: <input type="text" name="login" id='login' class="input-mini">
	   Пароль: <input type="password" name="pass" id='pass' class="input-mini">
	   <input type="submit" value="Войти" class="btn btn-mini"></input>
	   <a href="/get_account.html" class="white_text">Получить логин и пароль</a>	   
	  </form>
	  
	  {else}
	   {$hello}
	   {/if}	    		
		</td>
		<td align="right">
		<a href="company.html?action=companies" class="white_text">Компании по недвижимости</a> 
		<a href="about.html" class="white_text">О каталоге недвижимости</a> 
		<a href="partners.html" class="white_text">Наши партнеры</a>		
		</td>
		</tr>
		</table>	   
		</td>		
	</tr> 		
   </table>
  </td>
 </tr> 
 <tr>
  <td colspan=2 width="100%" height="10px;" align="center" valign="top" style="padding-top:0px;padding-bottom:0px;">
   {include file="main_menu.tpl"}
<!-- <div class="box_mini"></div> -->
{if !$subdomain && !$_SESSION.user_id}
<!--<div style="padding:0; margin: 8px 0 -10px 0;">adv</div>-->
{/if}
  </td> 
 </tr>
 {if $_SESSION.user_id>0}
    {include file="user_menu.tpl"}
 {/if}
 <tr>
  <td height="100%" valign="top" style="padding: 1 1 1 1;"></td>
  <td height="100%" style="padding: 1 0 1 0; width:100%">
