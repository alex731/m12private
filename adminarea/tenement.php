<?
include_once("common.php");
$err = "";
$message = "";
$is_err = false;

$_REQUEST['action']($s);

function listNew($s) {
	$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
	$content_html = Html::getAdminTenementList('listNew',PER_PAGE,$page);
	$s->assign("title_content","Новые дома");
	$s->assign("content",$content_html);
	$s->display("index.tpl");	
}

function listActive($s) {
	$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
	$content_html = Html::getAdminTenementList('listActive',PER_PAGE,$page);
	$s->assign("title_content","Активные дома");
	$s->assign("content",$content_html);
	$s->display("index.tpl");	
}

function activateAll() {
	Tenement::setStatusMass("status=".REALTY_STATUS_NEW,REALTY_STATUS_ACTIVE);
	header("Location: ".$_SERVER['HTTP_REFERER']);
	exit();
}

?>