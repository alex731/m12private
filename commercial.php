<?
include_once("./include/common.php");
$err = "";
$message = "";
$is_err = false;
$s->assign('session_id', session_id());
$s->assign("title","Коммерческая недвижимость в Йошкар-Оле и Марий Эл");
if (isset($_REQUEST['action'])) {
	$action = clearTextData($_REQUEST['action']);
	if (in_array($action,array('add','edit','view','apply','approve',
		'sales','delete','map','userSales','remove','sold','updateDate','rent','userRent',
		'resetFilter'))) 
		$action($s);
}			

function add($s) {
	if (empty($_POST)) {		
		if (isset($_COOKIE['contacts'])) {			
			$errors['val']['contacts'] = $_COOKIE['contacts'];
		}
		else {
			$errors = NULL;	
		}							
		$block_html = Html::pageCommercialAdd($errors);
		$s->assign("block_html",$block_html);
		$s->assign("YANDEX_KEY",YANDEX_KEY);
		$s->assign("LAT_CENTER_REGION",LAT_CENTER_REGION);
		$s->assign("LON_CENTER_REGION",LON_CENTER_REGION);				
		$s->display("commercial_add.tpl");
	}
	else {		
		$errors = Commercial::checkForm($_REQUEST,'');				
		require_once './libs/securimage/securimage.php';
		$securimage = new Securimage();
		if ($securimage->check($_POST['captcha_code']) == false) {
			$errors['captcha']['is_error'] = 1;
		}		
		if (!isset($errors['is_error'])&&!isset($errors['captcha']['is_error'])) {

			//Дом уже есть в бд
			if (isset($_POST['tenement_id']) && $_POST['tenement_id']>0) {				
				$tenement_id = intval($_POST['tenement_id']);
				$tenement = new Tenement();
				$tenement->find($tenement_id);
				if (!$tenement->id) {
					echo "Дом не найден";
					exit();
				}
			}			
			$commercial_id = Commercial::addStatic($_POST);			
			setcookie('contacts',stripslashes($_POST['contacts']));
			$_SESSION['last_commercial_id'] = $commercial_id;			
			if ($commercial_id > 0) header("Location: /commercial.html?action=view&id=".$commercial_id);			
			exit();
		}
		else {
			//echo "Error:".print_r($errors);
			$block_html = Html::pageCommercialAdd($errors);
			$s->assign("YANDEX_KEY",YANDEX_KEY);
			if (isset($_SESSION['admin'])) $s->assign("is_admin",$_SESSION['admin']);
			$s->assign("block_html",$block_html);
			$s->display("commercial_add.tpl");
		}
	}	
}

function edit($s) {	
	$_SESSION['last_commercial_id'] = (isset($_SESSION['last_commercial_id'])) ? $_SESSION['last_commercial_id'] : -1;
	$id = intval($_REQUEST['id']);
	$commercial = new Commercial();
	$commercial->getFull($id);
	if ($commercial->tenement_id > 0)
		$commercial->number = $commercial->tnum;  
	//загрузка дома	
	if (empty($_POST) && $commercial->id>0) {		
		if (isset($_SESSION["admin"])) $_SESSION['last_commercial_id'] = $id; 	
		if ($id!=$_SESSION['last_commercial_id'] && !$_SESSION['user_id'] && !$_SESSION["admin"]) {
			header("Location: /index.html");
			exit();
		}		
		if (isset($_SESSION['user_id']) && $commercial->user_id != $_SESSION['user_id'] && !$_SESSION["admin"] 
			&& $id!=$_SESSION['last_commercial_id']) {
			header("Location: /index.html");
			exit();
		}		
		$block_html = Html::pageCommercialEdit($commercial);
		$s->assign("YANDEX_KEY",YANDEX_KEY);
		
		$s->assign("city_id",$commercial->city_id);
		$s->assign("city",$commercial->city);
		$s->assign("street_id",$commercial->street_id);
		if (!$commercial->tenement_id) $commercial->street = $commercial->street_name;		
		$s->assign("street",$commercial->street);
		if (isset($_SESSION['admin'])) $s->assign("is_admin",$_SESSION['admin']);		
		$s->assign("block_html",$block_html);
		$s->display("commercial_add.tpl");
	}
	//обновление
	elseif (isset($_POST) && $_REQUEST['id']>0) {
		$commercial = new Commercial();		
		$errors = $commercial->checkForm($_REQUEST);
		$id = intval($_REQUEST['id']);
		if ($id!=$_SESSION['last_commercial_id'] && !$_SESSION['user_id'] && !$_SESSION["admin"]) {
			header("Location: /index.html");
			exit();
		}
		//Дом уже есть в бд
		if (isset($_POST['tenement_id']) && $_POST['tenement_id']>0) {				
			$tenement_id = intval($_POST['tenement_id']);
			$tenement = new Tenement();
			$tenement->find($tenement_id);
			if (!$tenement->id) {
				echo "Дом не найден";
				exit();
			}
		}			
	
		if (!isset($_SESSION['user_id'])) {
			$commercial->getFull($id);
		}
		else {
			$commercial->getFull($id,"h.user_id='{$_SESSION['user_id']}'");
			if ($commercial->user_id != $_SESSION['user_id'] && !$_SESSION["admin"] && $id!=$_SESSION['last_commercial_id']) {
				header("Location: /index.html");
				exit();
			}
		}				
		if (!isset($errors['is_error']) && $commercial->id > 0) {						
			$commercial->update($_POST);					
			//добавляем новые фото
			if (isset($_POST['photo_commercial']) && is_array($_POST['photo_commercial'])) {
				$photo_commercial_path = $commercial->getPhotoPath();
				if (!is_dir($photo_commercial_path)) {			
					mkdir($photo_commercial_path,0777);
					chmod($photo_commercial_path,0777);						
				}				
				foreach ($_POST['photo_commercial'] as $fname) {					
					$fname = clearTextData($fname);
					if ($fname!='') $commercial->addPhoto($fname,$_POST);
				}
			}
			//редактируем существующие фото
			if (isset($_POST['photo_'.COMMERCIAL.'_exist']) && is_array($_POST['photo_'.COMMERCIAL.'_exist'])) {
				$photo_commercial_path = $commercial->getPhotoPath();
				if (!is_dir($photo_commercial_path)) {			
					mkdir($photo_commercial_path,0777);
					chmod($photo_commercial_path,0777);						
				}
				foreach ($_POST['photo_'.COMMERCIAL.'_exist'] as $fname) {
					$fname = clearTextData($fname);
					if ($fname!='') $commercial->editPhoto($fname,$_POST);
				}
			}			
			header("Location: /commercial.html?action=view&id=".$commercial->id);
			exit();
		}
		else {
			echo "error";
			$id = intval($_REQUEST['id']);			
			$commercial->getFull($id);				
			$block_html = Html::pageCommercialEdit($commercial,$errors);
			$s->assign("YANDEX_KEY",YANDEX_KEY);
			$s->assign("is_admin",$_SESSION['admin']);			
			$s->assign("block_html",$block_html);
			$s->display("commercial_edit.tpl");
		}
	}
	else {
		echo "error";
	}
}

function view($s) {	
	$id = intval($_REQUEST['id']);
	
	$commercial = new Commercial();		
	if (isset($_SESSION['last_commercial_id']) && $_SESSION['last_commercial_id']>0  
		&& $_SESSION['last_commercial_id']!=$id && !isset($_SESSION['admin'])) {
		$commercial->getFull($id,"(h.status=".REALTY_STATUS_SALE." 
		OR h.status=".REALTY_STATUS_RENT." 
		OR h.status=".REALTY_STATUS_NEW."
		OR h.status=".REALTY_STATUS_RENT_NEW.")");
	}
	else {
		$commercial->getFull($id);
	}
	if (!$commercial->id) {
		echo "Not $commercial->id";
		//header("Location: /index.html");
		exit();
	}
	$address = $commercial->city;
	if ($commercial->tenement_id>0) {
		if ($commercial->street!='') $address .= ', '.$commercial->street;
		if ($commercial->tnum!='') $address .= ', д.'.$commercial->tnum;	
	}
	else {
		if ($commercial->street_name!='') $address .= ', '.$commercial->street_name;
		if ($commercial->number!='') $address .= ', д.'.$commercial->number;		
	}

	if ($commercial->status==REALTY_STATUS_SALE) {
		$act = 'продается в';
		$act2 = 'Продажа';
	}
	elseif ($commercial->status==REALTY_STATUS_RENT) {
		$act = 'сдается в';
		$act2 = 'Аренда';
	}
	else {
		$act = '';
		$act2 = 'Продажа';
	}			
	
	
	$block_html = Html::pageCommercialView($commercial,$address,$act2);
	$s->assign("block_html",$block_html);	
	$_SESSION['last_commercial_id'] = (isset($_SESSION['last_commercial_id'])) ? $_SESSION['last_commercial_id'] : -1;
	$s->assign("id",$commercial->id);
	$s->assign("city_id",$commercial->city_id);

	if (!isset($_SESSION['admin']) || (isset($_SESSION['user_id']) && $commercial->user_id!=$_SESSION['user_id'])) {
		$commercial->incVisitorCount();
	}
	
	$s->assign("YANDEX_KEY",YANDEX_KEY);
	
	$s->assign("title","Коммерческая недвижимость $act $address - Недвижимость Йошкар-Олы");
	$s->display("commercial_view.tpl");	
}

function apply($s) {
	$id = intval($_REQUEST['id']);
	if ($id!=$_SESSION['last_commercial_id']) {		
		//header("Location: /index.html");
		echo "$id!={$_SESSION['last_commercial_id']}";
		exit();
	}
	$commercial = new Commercial();
	$status = ($_GET['status']!=REALTY_STATUS_RENT_APPLY) ? REALTY_STATUS_APPLY : REALTY_STATUS_RENT_APPLY;
	$user_cond = (isset($_SESSION['user_id'])) ? ' AND user_id='.$_SESSION['user_id'] : '';
	$commercial->updateBy("id=$id",array('status'=>$status));	
	$block_html = Html::getBlock('Сообщение',"Ваше объявление отправлено на проверку администратором.");
	$s->assign("block_html",$block_html);
	$s->display("msg.tpl");
}

function approve($s) {	
	if (!isset($_SESSION['admin'])) {		
		//header("Location: /index.html");		
		exit();
	}
	$id = intval($_REQUEST['id']);
	$status = (!isset($_GET['status']) || $_GET['status']==REALTY_STATUS_SALE) ? REALTY_STATUS_SALE : REALTY_STATUS_RENT;
	Commercial::approve($id,$status);
	echo "Объявление активировано";
	/*
	$block_html = Html::getBlock('Сообщение',"Объявление активировано.");
	$s->assign("block_html",$block_html);
	$s->display("msg.tpl");
	*/
}

function _filter($status) {
	$filter = array();
	$add_sql = ' f.status='.$status;
	/*
	foreach ($_COOKIE as $k => $v) {
		if (!isset($_REQUEST[$k]) && $k[0]=='f' && $k[1]=='_' && isset($_COOKIE[$k]) && $_COOKIE[$k]!='' ) {
			//$_REQUEST[$k] = $_COOKIE[$k];
		}
	}
	*/
	$price_sql = "";
	if (isset($_REQUEST['f_price'])) {
		$price = intval($_REQUEST['f_price']);
		if ($price>5) $price=5;
		$filter['price'] = $price;
		if ($status==REALTY_STATUS_SALE) {
			switch ($price) {
				case 1:
					$add_sql .= " AND f.price<=1000000";
					break;
				case 2:
					$add_sql .= " AND f.price>1000000 AND f.price<=1500000";
					break;
				case 3:
					$add_sql .= " AND f.price>1500000 AND f.price<=2000000";
					break;
				case 4:
					$add_sql .= " AND f.price>2000000 AND f.price<=3000000";
					break;
				case 5:
					$add_sql .= " AND f.price>3000000";
					break;
			}
		}
		else {
			switch ($price) {
				case 1:
					$add_sql .= " AND f.price<=3000";
					break;
				case 2:
					$add_sql .= " AND f.price>3000 AND f.price<=6000";
					break;
				case 3:
					$add_sql .= " AND f.price>6000 AND f.price<=10000";
					break;
				case 4:
					$add_sql .= " AND f.price>10000 AND f.price<=15000";
					break;
				case 5:
					$add_sql .= " AND f.price>15000";
					break;
			}
		}
	}
	else {
		if (isset($_REQUEST['price_min']) && $_REQUEST['price_min']>0) {
			$price_min = intval($_REQUEST['price_min']);
			$add_sql .= " AND f.price>=".($price_min*1000);
			$filter['price_min'] = $price_min;
		}
		if (isset($_REQUEST['price_max']) && $_REQUEST['price_max']>0) {
			$price_max = intval($_REQUEST['price_max']);
			$add_sql .= " AND f.price<".($price_max*1000);
			$filter['price_max'] = $price_max;
		}
	}
	
	if (isset($_REQUEST['f_price_sq'])) {
		$f_price_sq = intval($_REQUEST['f_price_sq']);
		if ($f_price_sq>5) $f_price_sq=0;
		if ($f_price_sq==1) {
			$add_sql .= " AND f.price_m<25000";		
		}
		elseif ($f_price_sq==2) {
			$add_sql .= " AND f.price_m>=25000 AND f.price_m<30000";		
		}
		elseif ($f_price_sq==3) {
			$add_sql .= " AND f.price_m>=30000 AND f.price_m<35000";		
		}
		elseif ($f_price_sq==4) {
			$add_sql .= " AND f.price_m>=35000 AND f.price_m<40000";		
		}
		elseif ($f_price_sq==5) {
			$add_sql .= " AND f.price_m>=40000";		
		}
		$filter['price_sq'] = $f_price_sq; 
	}
	
	if (isset($_REQUEST['f_total_area'])) {
		$f_total_area = intval($_REQUEST['f_total_area']);
		if ($f_total_area>7) $f_total_area=0;
		if ($f_total_area==1) {
			$add_sql .= " AND f.total_area<25";		
		}
		elseif ($f_total_area==2) {
			$add_sql .= " AND f.total_area>25 AND f.total_area<50";		
		}
		elseif ($f_total_area==3) {
			$add_sql .= " AND f.total_area>=50 AND f.total_area<60";		
		}
		elseif ($f_total_area==4) {
			$add_sql .= " AND f.total_area>=60 AND f.total_area<70";		
		}
		elseif ($f_total_area==5) {
			$add_sql .= " AND f.total_area>=70 AND f.total_area<80";		
		}
		elseif ($f_total_area==6) {
			$add_sql .= " AND f.total_area>=80 AND f.total_area<100";		
		}
		elseif ($f_total_area==7) {
			$add_sql .= " AND f.total_area>=100";		
		}
		$filter['total_area'] = $f_total_area; 
	}
	
	if (isset($_REQUEST['f_radius']) && ($_REQUEST['f_radius']>0 || $_REQUEST['f_radius']===0)) {
		$f_radius = intval($_REQUEST['f_radius']);		
		$add_sql .= " AND c.dist<=$f_radius";
		$filter['radius'] = $f_radius; 
	}
	
	if (isset($_REQUEST['f_city_id']) && isset($_REQUEST['f_city']) && $_REQUEST['f_city_id']>0) {
		$f_city_id = intval($_REQUEST['f_city_id']);		
		$add_sql .= " AND f.city_id=$f_city_id";				
		$filter['city_id'] = $f_city_id;
		$filter['city'] = clearTextData($_REQUEST['f_city']); 
	}
	
	if (isset($_REQUEST['f_photo'])) {
		$f_photo = intval($_REQUEST['f_photo']);		
		if ($f_photo>0) $add_sql .= " AND p.name !=''";
		$filter['photo'] = $f_photo; 
	}
	if (isset($_REQUEST['f_regions'])) {
		$f_regions = intval($_REQUEST['f_regions']);		
		if ($f_regions>0) $add_sql .= " AND f.city_id>2";
		$filter['regions'] = $f_regions; 
	}
	
	if (isset($_REQUEST['f_Commercial'])) {
		$f_Commercial = intval($_REQUEST['f_Commercial']);
		if ($f_Commercial>4) $f_Commercial=0;
		if ($f_Commercial>0) $add_sql .= " AND f.type_id =".($f_Commercial-1);		
		$filter['Commercial'] = $f_Commercial; 
	}
	
	return array('filter'=>$filter,'sql'=>$add_sql);
}

function sales($s,$user_id=NULL,$status=REALTY_STATUS_SALE) {	
	$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
	$res = _filter($status);
	$add_sql = $res['sql'];
	$filter = $res['filter'];	
	if (isset($_GET['sort'])) {
		$sort_by = clearTextData($_GET['sort']);	
	}
	elseif (isset($_SESSION['sort_by'])) {
		$sort_by = $_SESSION['sort_by'];		 
	}
	else {
		$sort_by = 'updated_on';
	}
	
	if (isset($_GET['sort']) && isset($_SESSION['sort_by']) && $_GET['sort']==$_SESSION['sort_by']) {
		if (isset($_SESSION['direction']) && $_SESSION['direction']=='ASC') $direction = 'DESC';
		else if (isset($_SESSION['direction']) && $_SESSION['direction']=='DESC') $direction = 'ASC';
	}
	else {
		$direction = (isset($_SESSION['direction'])) ? $_SESSION['direction'] : 'DESC'; 
	}
	if ($user_id) {
		$add_sql .= " AND f.user_id=".$_SESSION['user_id'];		
	}
	$_SESSION['direction'] = $direction;
	$_SESSION['sort_by'] = $sort_by;
	$filter_html = Html::getCommercialFilter($filter,$user_id,$status);

	if (in_array($status,array(REALTY_STATUS_SALE,REALTY_STATUS_NEW,REALTY_STATUS_SOLD))) {
		$action = 'sales';
		if ($user_id) $action = 'userSales';
	}
	else {
		$action = 'rent';
		if ($user_id) $action = 'userRent';
	}
	//Объявления не старше месяца
	$add_sql .= " AND f.updated_on>'".getNextDate(date('Y-m-d'),-31)."'";
	$content = Html::getCommercialList("$add_sql",
		$sort_by,$direction,PER_PAGE,$page,
	$action,$user_id);	
	$act = ($status == REALTY_STATUS_SALE) ? 'Продажа' : 'Аренда';
	if (!$user_id) {
		$block_name = $act.' коммерческой недвижимости в Йошкар-Оле и Марий Эл - последние объявления';	
	}
	else {
		$block_name = $act.' коммерческой недвижимости в Йошкар-Оле и Марий Эл - мои объявления';
	}
	
	$s->assign("block_html",Html::getBlock($block_name,$filter_html.$content['html']));
	
	if ((!isset($_SESSION['admin']) || !isset($user_id)) && count($content['ids'])>0) {
		Commercial::incQuickMassVisitorCount($content['ids']);	
	}
	$s->assign("title",$block_name);
	$s->display("commercial_sales.tpl");	
}

function delete() {	
	if (!isset($_SESSION['admin'])) {
		echo "Not admin";		
		//header("Location: /index.html");		
		exit();
	}
	$id = intval($_REQUEST['id']);
	Commercial::delete($id);
	header("Location: ".$_SERVER['HTTP_REFERER']);		
}

function userSales($s) {
	if (!isset($_SESSION['user_id'])) {
		header("Location: index.html");
		exit();
	}
	sales($s,$_SESSION['user_id']);	
}

function userRent($s) {
	if (!isset($_SESSION['user_id'])) {
		header("Location: index.html");
		exit();
	}
	sales($s,$_SESSION['user_id'],REALTY_STATUS_RENT);	
}

function rent($s) {
	sales($s,null,REALTY_STATUS_RENT);	
}

function updateDate() {
	$id=intval($_GET['id']);
	Commercial::updateDate($id,$_SESSION['user_id']);
	header("Location: ".$_SERVER['HTTP_REFERER']);
	exit();
}


function remove() {
	$id=intval($_GET['id']);
	Commercial::setUserStatus($id,$_SESSION['user_id'],REALTY_STATUS_DELETED);
	header("Location: ".$_SERVER['HTTP_REFERER']);
	exit();	
}

function sold() {
	$id=intval($_GET['id']);
	if (isset($_SESSION['user_id'])) {
		Commercial::setUserStatus($id,$_SESSION['user_id'],REALTY_STATUS_SOLD);		
	}
	else {
		if (!isset($_SESSION['admin'])) {
			echo "Not admin";		
			//header("Location: /index.html");		
			exit();
		}
		Commercial::setStatus($id,REALTY_STATUS_SOLD);
	}
	header("Location: ".$_SERVER['HTTP_REFERER']);
	exit();	
}

function map($s) {
	$s->assign("lat",LAT_YOLA);
	$s->assign("lon",LON_YOLA);
	$s->assign("YANDEX_KEY",YANDEX_KEY);
	
	$ids = '';	
	$rooms = '';
	$prices = '';
	$prices_m = '';
	$areas = '';
	$addresses = '';
	$storeys = '';
	$dates = '';
	$types = '';
	$lons = '';
	$lats = '';
	$photos = '';
	$icons = '';
	if (!isset($_REQUEST['act']) || $_REQUEST['act']=='sales') {
		$status=REALTY_STATUS_SALE;
	}
	else {
		$status=REALTY_STATUS_RENT;
	}
	$res = _filter($status);
	$add_sql = "f.updated_on>'".getNextDate(date('Y-m-d'),-30)."' AND t.status=".REALTY_STATUS_ACTIVE.' AND '.$res['sql'];
	$db_res = Commercial::getFullListLink($add_sql);
	while ($row = $db_res->fetchRow()) {
		$ids .= $row['id'].',';
		$rooms .= $row['rooms'].',';
		$prices .= "'".number_format($row['price'],0)."',";
		$prices_m .= "'".number_format($row['price_m'],0)."',";
		$areas .= "'".$row['total_area']."',";
		$addr = "{$row['street']}, {$row['tnum']}";
		$addresses .= "'".$addr."',";
		
		$date = explode(' ',$row['updated_on']);
		$ds = explode('-',$date[0]);
		$date = $ds[2].'.'.$ds[1].'.'.$ds[0];		
		$dates .= "'".$date."',";
		$storeys .= "'".$row['storey']."/".$row['storeys']."',";
		$types .= "'".Tenement::$TYPE[$row['ttype']]."',";		
		$lons .= "'".$row['lon']."',";
		$lats .= "'".$row['lat']."',";
		$photo = ($row['photo_tenement']!='') ? $row['tenement_id']."/".$row['photo_tenement']."_prev" : '';
		$photos .= "'".$photo."',";
		if ($row['price_m']<30000) {
			$color = 'a';				
		}
		else if ($row['price_m']<40000) {
			$color = 'b';				
		}
		else {
			$color = 'c';	
		}
		$ri = ($row['rooms']<4) ? $row['rooms'] : 3;
		$icons .= "'".$ri."k".$color."',";
	}
	
	$s->assign("ids",$ids);
	$s->assign("rooms",$rooms);
	$s->assign("prices",$prices);
	$s->assign("prices_m",$prices_m);
	$s->assign("areas",$areas);
	$s->assign("addresses",$addresses);
	$s->assign("storeys",$storeys);
	$s->assign("dates",$dates);
	$s->assign("types",$types);
	$s->assign("lons",$lons);
	$s->assign("lats",$lats);
	$s->assign("photos",$photos);
	$s->assign("icons",$icons);
		
	$block_html = Html::getBlock('Квартиры на продажу на карте Йошкар-Олы',Html::pageCommercialMap());
	$s->assign("block_html",$block_html);
	$s->display("commercial_map.tpl");
}

function resetFilter() {
	foreach ($_COOKIE as $k => $v) {
		if ($k[0]=='f' && $k[1]=='_') {
			setcookie($k,'',time()+10);
		}
	}	
	header("Location: ".$_SERVER['HTTP_REFERER']);
	exit();
}

?>