<?php /* Smarty version 2.6.26, created on 2013-06-09 23:43:58
         compiled from header.tpl */ ?>
<html>
<head>
<title><?php echo $this->_tpl_vars['title_page']; ?>
</title>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $this->_tpl_vars['charset_page']; ?>
">
<link href="../style2.css?sdf=dfgdf&dfg=dfgdf" rel="stylesheet" type="text/css">
<script language="javascript" src="../common/calendar.js" type="text/javascript"></script>
<script language="javascript" src="../common/js.js" type="text/javascript"></script>
<script language="javascript" src="../common/checkform.js" type="text/javascript"></script>
<script language="javascript" src="../common/menu.js" type="text/javascript"></script>
<script language="javascript" src="../common/jquery-1.4.3.min.js" type="text/javascript"></script>
<script language="javascript" src="../common/jquery.autocomplete.js" type="text/javascript"></script>
</head>
<!-- 
<tr height="20"><td style="padding-left:5px;">&#8226;</td><td><a class="header" href="flat.php?action=list">Квартиры</a></td></tr>
<tr height="20"><td style="padding-left:5px;">&#8226;</td><td></td></tr>
 -->
<body>
<table width="100%">
<tr>
	<td width="200px" height="100%" valign="top">
	<table class="tborder" cellpadding="5" cellspacing="1" border="0" width="100%" height="100%" align="center"> 
<tr><td class="theader">Menu</td></tr>
<tr><td class="tcontent">
<div class="admin-menu">&#149; <a class="header" href="/tenement.php?action=add">Добавить дом</a></div>
<div class="admin-menu">&#149; <a class="header" href="/flat.php?action=add">Добавить квартиру</a></div>
<div class="admin-menu">&#149; <a class="header" href="flat.php?action=newSales">Новые кв.-продажа</a></div>
<div class="admin-menu">&#149; <a class="header" href="flat.php?action=newRent">Новые кв.-аренда</a></div>
<div class="admin-menu">&#149; <a class="header" href="tenement.php?action=listNew">Новые дома</a></div>
<div class="admin-menu">&#149; <a class="header" href="house.php?action=listNew">Новые коттеджи</a></div>
<div class="admin-menu">&#149; <a class="header" href="land.php?action=listNew">Новые участки</a></div>
<div class="admin-menu">&#149; <a class="header" href="tenement.php?action=listActive">Активные дома</a></div>
<div class="admin-menu">&#149; <a class="header" href="company.php?action=add">Добавить АН</a></div>
<div class="admin-menu">&#149; <a class="header" href="commercial.php?action=newSales">Комм. недвижимость - продажа</a></div>
<div class="admin-menu">&#149; <a class="header" href="commercial.php?action=newRent">Комм. недвижимость - аренда</a></div>
<hr></hr>
<div><a class="header" href="index.php?action=logout">Выход</a></div>
</td></tr></table>
	</td>
	<td valign="top">
	<table class="tborder" cellpadding="5" cellspacing="1" border="0" width="100%" height="100%" align="center"> 
<tr><td class="theader"><?php echo $this->_tpl_vars['title_content']; ?>
</td></tr>
<tr><td class="tcontent">