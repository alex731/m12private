<?
include_once("../include/common.php");
$s->template_dir = $config["admin_template_dir"];
$s->compile_dir = $config["admin_compile_dir"];

if (isset($_SESSION["admin"]) and $_SESSION["admin"] == "a") {
	$admin = "a";
}
else {
	header("Location: login.php");
}
$s->assign("charset_page","UTF-8");
$s->assign("title_page",$config["company"]);
?>