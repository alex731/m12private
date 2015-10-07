<?
include_once("./include/common.php");
$s->assign("title",'О каталоге недвижимости "Мой Новый Дом"');
$s->assign("top_img_num",rand(1,3));
$s->display("about.tpl");
?>