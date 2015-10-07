<?
$admin = "no";
include_once("../include/common.php");
$s->template_dir = $config["admin_template_dir"];
$s->compile_dir = $config["admin_compile_dir"];

if (isset($_POST["Enter"]) and ($_POST["Enter"] == "Enter")) {
	if ( ($_POST["Login"] == $config["admin_login"]) and ($_POST["Password"] == $config["admin_password"]) ){				
		$_SESSION['admin'] = 'a';
		
		Flat::restoreLostObjects(REALTY_STATUS_NEW,REALTY_STATUS_APPLY);
		Flat::restoreLostObjects(REALTY_STATUS_RENT_NEW,REALTY_STATUS_RENT_APPLY);
		House::restoreLostObjects(REALTY_STATUS_NEW,REALTY_STATUS_APPLY);
		Land::restoreLostObjects(REALTY_STATUS_NEW,REALTY_STATUS_APPLY);
		
		header("Location: index.php");
	}
}

$s->display('login.tpl'); 

?>