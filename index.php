<?php
include_once("./include/common.php");

//$_SERVER["HTTP_HOST"] = 'status.mari12.ru';

if ($_SERVER["HTTP_HOST"]==DOMAIN || $_SERVER["HTTP_HOST"]=='www.'.DOMAIN) {
	header("Location: /flat.html?action=sales");
	exit();	
}
else {
	$DOMAIN_ARR = explode('.',$_SERVER["HTTP_HOST"]);			
	$domain = $DOMAIN_ARR[0];	
	$company = Company::getInfoByDomain($domain);	
	if ($_SERVER['PHP_SELF']=='/index.php') {		
		$photos = Company::getPhotosStatic($company['id']);
		$photo_path = Company::getPhotoWebPathStatic($company['id']);
		$gallery = Html::getPhotosGallery($photos,$photo_path);
		$logo = Company::getLogoURL($company['id']);
		$sort_by = 'updated_on';
		$direction = 'DESC';					
		
		$sql_flats = "t.status='".REALTY_STATUS_ACTIVE."' AND f.status='".REALTY_STATUS_SALE."'";				
		$sql_company = " AND f.user_id IN (SELECT id FROM user WHERE company_id={$company['id']})";		
		//Объявления не старше месяца
		$sql_date = " AND f.updated_on>'".getNextDate(date('Y-m-d'),-61)."'";
		$flats = Html::getFlatList($sql_flats.$sql_company.$sql_date,$sort_by,$direction,100,1,'sales');
		
		$sql_flats = "t.status='".REALTY_STATUS_ACTIVE."' AND f.status='".REALTY_STATUS_RENT."'";
		$flats_rent = Html::getFlatList($sql_flats.$sql_company.$sql_date,$sort_by,$direction,100,1,'rent');
		
		$sql_houses = "f.status='".REALTY_STATUS_SALE."'";
		$houses = Html::getHouseList($sql_houses.$sql_company.$sql_date,$sort_by,$direction,100,1,'sales');
		
		$sql_lands = "f.status='".REALTY_STATUS_SALE."'";
		$lands = Html::getLandList($sql_lands.$sql_company.$sql_date,$sort_by,$direction,100,1,'sales');
		
		$sql_commercial_sale = "f.status='".REALTY_STATUS_SALE."'";
		$commercial = Html::getCommercialList($sql_commercial_sale.$sql_company.$sql_date,$sort_by,$direction,100,1,'sales');
		
		$sql_commercial_rent = "f.status='".REALTY_STATUS_RENT."'";
		$commercial_rent = Html::getCommercialList($sql_commercial_rent.$sql_company.$sql_date,$sort_by,$direction,100,1,'rent');
		
		$s->assign("title",$company['name']);				
		$s->assign("logo",$logo);
		$s->assign("subdomain",$domain);
		$s->assign("HOST",$_SERVER["HTTP_HOST"]);
		$s->assign("MAIN_DOMAIN",DOMAIN);		
		$s->assign("YANDEX_KEY",YANDEX_KEY);
		
		if (count($flats['ids'])>0) $s->assign("flats",$flats['html']);
		if (count($flats_rent['ids'])>0) $s->assign("flats_rent",$flats_rent['html']);
		if (count($houses['ids'])>0) $s->assign("houses",$houses['html']);
		if (count($lands['ids'])>0) $s->assign("lands",$lands['html']);
		$s->assign("company",$company);
        $s->assign("is_agency", $company['type_id'] == Company::COMPANY_TYPE_AGENCY);
		$s->assign("gallery",$gallery);
		$s->display("company_view.tpl");
	}	
} 
?>
