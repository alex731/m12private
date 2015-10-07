<?
include_once("common.php");
$err = "";
$message = "";
$is_err = false;

$_REQUEST['action']($s);

function listNew($s) {
	$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
	$content_html = Html::getAdminLandList('listNew',PER_PAGE,$page);
	$s->assign("title_content","Новые земельные участки");
	$s->assign("content",$content_html);
	$s->display("index.tpl");	
}

function listActive($s) {
	$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
	$content_html = Html::getAdminLandList('listActive',PER_PAGE,$page);
	$s->assign("title_content","Активные земельные участки");
	$s->assign("content",$content_html);
	$s->display("index.tpl");	
}

function activateAll() {
	Land::setStatusMass("status=".REALTY_STATUS_APPLY,REALTY_STATUS_SALE);
	header("Location: ".$_SERVER['HTTP_REFERER']);
	exit();
}

?>