<?
include_once("common.php");
$err = "";
$message = "";
$is_err = false;

$_REQUEST['action']($s);

function newSales($s) {
	$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
	$content_html = Html::getAdminCommercialList('newSales',PER_PAGE,$page);
	$s->assign("title_content","Новая коммерческая недвижимость - продажа");
	$s->assign("content",$content_html);
	$s->display("index.tpl");	
}

function newRent($s) {
	$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
	$content_html = Html::getAdminCommercialList('newRent',PER_PAGE,$page);
	$s->assign("title_content","Новая коммерческая недвижимость - аренда");
	$s->assign("content",$content_html);
	$s->display("index.tpl");	
}


function listActive($s) {
	$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
	$content_html = Html::getAdminCommercialList('listActive',PER_PAGE,$page);
	$s->assign("title_content","Активная коммерческая недвижимость");
	$s->assign("content",$content_html);
	$s->display("index.tpl");	
} 

function activateAll() {
	House::setStatusMass("status=".REALTY_STATUS_APPLY,REALTY_STATUS_SALE);
	header("Location: ".$_SERVER['HTTP_REFERER']);
	exit();
}

?>