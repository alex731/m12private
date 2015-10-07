<?php
if ($_SERVER['HTTP_HOST']!='mari12.my') {
	include_once 'config_server.php';
}
else {
	include_once 'config_local.php';
}

define('TENEMENT',1);
define('FLAT',2);
define('HOUSE',3);
define('NEW_TENEMENT',4);
define('LAND',5);
define('GARAGE',6);
define('COMPANY',7);
define('LOGO',8);
define('COMMERCIAL',9);

$config["server"]                         = $_SERVER['HTTP_HOST']."/";
$config["site_root"]                      = "";
$config["site"]                           = "http://".$config["server"].$config["site_root"];
$config["path"]                           = $_SERVER["DOCUMENT_ROOT"]."/";
$config["include_path"]                   = $_SERVER["DOCUMENT_ROOT"]."/include/";
$config["images_upload"]                  = "images_upload/";   

$config["smarty"]                         = $config["path"]."libs/smarty/Smarty.class.php";
$config["template_dir"]                   = $config["path"]."templates/";
$config["compile_dir"]                    = $config["path"]."templates_c/";

$config["admin_template_dir"]             = $config["path"]."adminarea/templates/";
$config["admin_compile_dir"]              = $config["path"]."adminarea/templates_c/";

$config["config_dir"]                     = $config["path"]."include/";
$config["cache_dir"]                      = $config["path"]."cache_dir/";

$config["admin_login"]                    = "admin";
$config["admin_password"]                 = "amari12rnewpass";

$config["company"]                        = "Мой Новый Дом";

$config['user_workers']                   = array(1,4,6,23,26,29,31,32,33,34,36,38,43,44,46,55,57,58,59,60,61,62,63,64,65,66);

define("CONTACTS",'"Мой Новый Дом", респ. Марий Эл, г.Йошкар-Ола, (8362)62-69-71, dom@mari12.ru');

define("SITE_PATH",$_SERVER["DOCUMENT_ROOT"]."/");
define("PHOTOS_TMP_PATH",$config["path"]."tmp_photos/");
define("PHOTOS_PATH",$config["path"]."photos/");
define("PHOTOS_WEBPATH","photos/");
define("IMPORT_PATH",$config["path"]."import/");
/*====================================*/
define("REALTY_STATUS_NEW","0");
define("REALTY_STATUS_APPLY","1");
define("REALTY_STATUS_ACTIVE","1");
define("REALTY_STATUS_SALE","2");

define("REALTY_STATUS_RENT_NEW","3");
define("REALTY_STATUS_RENT_APPLY","4");
define("REALTY_STATUS_RENT","7");

define("REALTY_STATUS_DELETED","5");
define("REALTY_STATUS_SOLD","6");

define("REALTY_STATUS_IMPORT_SALE","10");
define("REALTY_STATUS_IMPORT_RENT","11");
/*====================================*/

define("SALE","1");
define("RENT","2");

define("TARIFF_FREE","1");
define("TARIFF_PAID","2");

define("PHOTO_STATUS_NEW","0");
define("PHOTO_STATUS_ACTIVE","1");

define("PER_PAGE",20);

define("ADDON_HASH",5);

define('WATERMARK',' http://mari12.ru ');

define('LAT_YOLA',56.63178340187885);
define('LON_YOLA',47.88643978536131);

define('LAT_CENTER_REGION',56.63178340187885);
define('LON_CENTER_REGION',47.88643978536131);

define('EMAIL_FROM', "mari12.ru <notify@mari12.ru>");
?>
