<?
include_once("common.php");
$err = "";
$message = "";
$is_err = false;

$_REQUEST['action']($s);

function newSales($s) {
	$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
	$content_html = Html::getAdminFlatList("f.status='".REALTY_STATUS_APPLY."' GROUP BY f.id",PER_PAGE,$page,'newSales');
	$s->assign("title_content","Новые квартиры - продажа");
	$s->assign("content",$content_html);
	$s->display("index.tpl");
}

function newRent($s) {
	$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
	$content_html = Html::getAdminFlatList("f.status='".REALTY_STATUS_RENT_APPLY."' GROUP BY f.id",PER_PAGE,$page,'newRent');
	$s->assign("title_content","Новые квартиры - аренда");
	$s->assign("content",$content_html);
	$s->display("index.tpl");	
}

function activateAll() {
	Flat::setStatusMass("status=".REALTY_STATUS_APPLY,REALTY_STATUS_SALE);
	header("Location: ".$_SERVER['HTTP_REFERER']);
	exit();
}


/*
switch ($_REQUEST['action']) {
	case "list": {
		break;
	}
	case "add": {		
		$s->assign("title_content","Добавление квартиры");
		
		$tenement = new Tenement();		
		$tenement_form = new FormHtml();
		$tenement_html_form = $tenement_form->getForm($tenement->getProperties()); 
		$s->assign("tenement_html_form",$tenement_html_form);
		
		$flat = new Flat();
		$flat_form = new FormHtml();
		$flat_html_form = $flat_form->getForm($flat->getProperties()); 
		$s->assign("flat_html_form",$flat_html_form);
		
		$s->display("flat_{$_GET['action']}.tpl");		
		break;
	}
	case "add_save": {		
		$tenement = new Tenement();		
		$errors = $tenement->checkForm($_REQUEST);		
		
		$tenement_form = new FormHtml();
		$tenement_html_form = $tenement_form->getForm($tenement->getProperties(),$errors); 
		$s->assign("tenement_html_form",$tenement_html_form);
		$s->assign("title_content","Добавление квартиры");
		$s->assign("errors",$errors);
		$s->display("flat_{$_GET['action']}.tpl");
		break;
	}
} 
*/

 
?>