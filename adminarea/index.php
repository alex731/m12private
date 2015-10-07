<?
error_reporting(E_ALL);
ini_set("display_errors",1);
include_once("common.php");

if (isset($_REQUEST['action'])) {
	$_REQUEST['action']();
}

function logout() {
	session_destroy();
	header("Location: index.php");
}

$s->assign("title_content","Администрирование");

$_SESSION["time_login"] = date("Y-m-d H:i:s");
$s->display('index.tpl'); 

?>