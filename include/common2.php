<?php
include_once("config.php");

//ini_set("memory_limit","32M");
session_start();
ini_set ("default_charset", "utf-8");

include_once($config['smarty']);
include_once("functions_general.php");

function __autoload($class_name) {
  require_once(SITE_PATH."classes/$class_name.php");  
}

$s = new Smarty();
$s->compile_check = true;
$s->debugging = false;
$s->force_compile = true;
$s->template_dir = $config["template_dir"];
$s->compile_dir = $config["compile_dir"];
$s->cache_dir = $config["cache_dir"];
$s->config_dir = $config["config_dir"];

$s->assign("config",$config);
$s->assign("CONTACTS",CONTACTS);

                 
$db = new DB($config['dbserver'],$config['dbname'],$config['dbuser'],$config['dbpassword']);

$s->assign("title","Недвижимость в Йошкар-Оле - продажа квартир, домов, земельных участков");
$s->assign("keywords","Недвижимость в Йошкар-Оле, Продажа квартир,Йошкар-Ола, недвижимость, дома");
$s->assign("meta_description","Мой Новый Дом - каталог квартир Йошкар-Олы с подробным описанием, удобным фильтром и актуальной базой.");
$s->assign("author","Мой Новый Дом, Йошкар-Ола");
$s->assign("meta_revisit","1");

if (isset($_COOKIE['hx']) && $_COOKIE['hx']!='') {
	$hash = clearTextData($_COOKIE['hx']);
	User::autorizeByHash($hash);	
}

if (isset($_SESSION['user_id']) && $_SESSION['user_id']>0) {
	$s->assign("hello","Здравствуйте, ".$_SESSION['user_name'].'!');	
}

$s->assign("_SESSION",$_SESSION);
$s->assign("top_img_num",rand(1,3));
?>