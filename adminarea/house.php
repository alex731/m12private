<?
include_once("common.php");
$err = "";
$message = "";
$is_err = false;

$_REQUEST['action']($s);

function listNew($s) {
	$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
	$content_html = Html::getAdminHouseList('listNew',PER_PAGE,$page);
	$s->assign("title_content","Новые частные дома");
	$s->assign("content",$content_html);
	$s->display("index.tpl");	
}

function listActive($s) {
	$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
	$content_html = Html::getAdminHouseList('listActive',PER_PAGE,$page);
	$s->assign("title_content","Активные дома");
	$s->assign("content",$content_html);
	$s->display("index.tpl");	
} 

function activateAll() {
	House::setStatusMass("status=".REALTY_STATUS_APPLY,REALTY_STATUS_SALE);
	header("Location: ".$_SERVER['HTTP_REFERER']);
	exit();
}

?>