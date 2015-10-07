<?
include_once("./include/common.php");
$err = "";
$message = "";
$is_err = false;
$s->assign('session_id', session_id());
$s->assign("title","Продажа квартир в Йошкар-Оле и Марий Эл");
if (isset($_REQUEST['action'])) {
	$action = clearTextData($_REQUEST['action']);
	if (in_array($action,array('add','edit','view','apply','approve',
		'sales','delete','map','userSales','remove','sold','updateDate',
		'rent','userRent','resetFilter','userImportSales','userImportRent'))) 
		$action($s);
}			

function add($s) {	
	if (empty($_POST)) {		
		$tenement = new Tenement();			 						
		$flat = new Flat();
		if (isset($_COOKIE['contacts'])) {			
			$errors[FLAT]['val']['contacts'] = $_COOKIE['contacts'];
		}
		else {
			$errors = NULL;	
		}							
		$block_html = Html::pageFlatAdd($tenement,$flat,$errors);
		$s->assign("block_html",$block_html);
		$s->assign("YANDEX_KEY",YANDEX_KEY);
		$s->assign("LAT_CENTER_REGION",LAT_CENTER_REGION);
		$s->assign("LON_CENTER_REGION",LON_CENTER_REGION);				
		$s->display("flat_add.tpl");
	}
	else {
		$tenement = new Tenement();		
		if ($_REQUEST['city_id']>1) {
			Tenement::$_properties['street']['required']=null;
			Tenement::$_properties['street_id']['required']=null;	
			Tenement::$_properties['number']['required']=null;
		}		
		$errors_tenement = (!isset($_REQUEST['id'])||!$_REQUEST['id']) ? Tenement::checkForm($_REQUEST) : array();		
		$flat = new Flat();
		//Продажа
		$_POST['flat__price'] = intval(numStrToClearStr($_POST['flat__price']));
		if ($_POST['type_deal']==SALE && $_POST['flat__price']<20000) {
			$_POST['flat__price'] *= 1000;
			$_REQUEST['flat__price'] = $_POST['flat__price']; 
		}
		if ($_POST['type_deal']==RENT && $_POST['flat__price']>=100000) {
			$_POST['type_deal']= SALE;
		}
		
		if ($_POST['type_deal']==RENT) {
			Flat::$_properties['price']['min_val']=1000;
		}
		$errors_flat = Flat::checkForm($_REQUEST,'flat__');						
		$errors = array(TENEMENT=>$errors_tenement,FLAT=>$errors_flat);		
		require_once './libs/securimage/securimage.php';
		$securimage = new Securimage();
		if ($securimage->check($_POST['captcha_code']) == false) {
			$errors['captcha']['is_error'] = 1;
		}		
		if (!isset($errors[TENEMENT]['is_error'])&&!isset($errors[FLAT]['is_error'])&&!isset($errors['captcha']['is_error'])) {
			//Дом уже есть в бд
			if (isset($_POST['id']) && $_POST['id']>0) {				
				$tenement_id = intval($_POST['id']);
				$tenement->find($tenement_id);
				if (!$tenement->id) {
					echo "Дом не найден";
					exit();
				}
			}
			//Добавляем дом в бд
			else {				
				$tenement->add($_POST);
			}			
			//Квартира уже есть в бд
			if (isset($_POST['flat_id'])) {
				echo "exist:".$flat_id = intval($_POST['flat_id']);
				$flat->getFull($flat_id);
				$flat_id = $flat->id;				
			}
			//Добавляем кв в бд
			else {
				$data = $_POST;
				$data['flat__tenement_id'] = $tenement->id;
				$data['flat__status'] = $data['type_deal']==SALE ? REALTY_STATUS_NEW : REALTY_STATUS_RENT_NEW;
				if ($tenement_id>0 && isset($_SESSION['user_id'])) {
					if ($data['flat__status']==REALTY_STATUS_NEW) {
						$data['flat__status'] = REALTY_STATUS_SALE;
					}
					else {
						$data['flat__status'] = REALTY_STATUS_RENT;
					}					
				}
				$data['flat__description'] = clearTextData($data['flat__description'],10000);
				$data['flat__contacts'] = clearTextData($data['flat__contacts'],1000); 
				$flat->add($data,'flat__');
			}
			setcookie('contacts',stripslashes($_POST['flat__contacts']));
			$_SESSION['last_flat_id'] = $flat->id;					
			$photo_tenement_path = $tenement->getPhotoPath();
			if (!is_dir($photo_tenement_path)) {
				mkdir($photo_tenement_path,0777,true);				
			}
			if (isset($_POST['photo_tenement']) && is_array($_POST['photo_tenement'])) {
				foreach ($_POST['photo_tenement'] as $fname) {
					$fname = clearTextData($fname);
					$tenement->addPhoto($fname,$_POST);
				}
			}
			$photo_flat_path = $flat->getPhotoPath();
			if (!is_dir($photo_flat_path)) {			
				mkdir($photo_flat_path,0777);						
			}
			if (isset($_POST['photo_flat']) && is_array($_POST['photo_flat'])) {
				foreach ($_POST['photo_flat'] as $fname) {
					$fname = clearTextData($fname);
					$flat->addPhoto($fname,$_POST);
				}
			}
			if ($flat->id > 0) header("Location: /flat.html?action=view&id=".$flat->id);			
			exit();
		}
		else {
			//echo "Error:".print_r($errors);
			$block_html = Html::pageFlatAdd($tenement,$flat,$errors);			
			$s->assign("YANDEX_KEY",YANDEX_KEY);
			if (isset($_SESSION['admin'])) $s->assign("is_admin",$_SESSION['admin']);
			if (isset($_REQUEST['id'])) $s->assign("tenement_id",intval($_REQUEST['id']));			
			$s->assign("city_id",intval($_REQUEST['city_id']));
			$s->assign("street_id",intval($_REQUEST['street_id']));
			$s->assign("lon",clearTextData($_REQUEST['lon']));
			$s->assign("lat",clearTextData($_REQUEST['lat']));
			$s->assign("is_error",1);
			$s->assign("block_html",$block_html);
			$s->display("flat_add.tpl");
		}
	}	
}

function edit($s) {	
	$_SESSION['last_flat_id'] = (isset($_SESSION['last_flat_id'])) ? $_SESSION['last_flat_id'] : -1;	
	if (empty($_POST)) {
		$id = intval($_REQUEST['id']);
		if (isset($_SESSION["admin"])) $_SESSION['last_flat_id'] = $id; 	
		if ($id!=$_SESSION['last_flat_id'] && !$_SESSION['user_id'] && !$_SESSION["admin"]) {
			header("Location: /index.html");
			exit();
		}		
		$flat = new Flat();
		$flat->getFull($id);
		if (isset($_SESSION['user_id']) && $flat->user_id != $_SESSION['user_id'] && !$_SESSION["admin"] 
			&& $id!=$_SESSION['last_flat_id']) {
			header("Location: /index.html");
			exit();
		}		
		$block_html = Html::pageFlatEdit($flat);
		$s->assign("YANDEX_KEY",YANDEX_KEY);	
		$s->assign("block_html",$block_html);
		$s->display("flat_edit.tpl");
	}
	else {
		$flat = new Flat();		
		$id = intval($_REQUEST['id']);
		if ($id!=$_SESSION['last_flat_id'] && !$_SESSION['user_id'] && !$_SESSION["admin"]) {
			header("Location: /index.html");
			exit();
		}
		if (!isset($_SESSION['user_id'])) {
			$flat->getFull($id);
		}
		else {
			$flat->getFull($id,"f.user_id='{$_SESSION['user_id']}'");
			if ($flat->user_id != $_SESSION['user_id'] && !$_SESSION["admin"] && $id!=$_SESSION['last_flat_id']) {
				header("Location: /index.html");
				exit();
			}
			else {
				//$_SESSION['last_flat_id'] = $flat->user_id; 
			}
		}
		$_REQUEST['price'] = intval(numStrToClearStr($_REQUEST['price']));
				
		if (in_array($flat->status,array(REALTY_STATUS_RENT,REALTY_STATUS_RENT_APPLY,REALTY_STATUS_RENT_NEW))) {
			Flat::$_properties['price']['min_val']=1000;
		}
		$errors = $flat->checkForm($_REQUEST);		
		if (!isset($errors['is_error']) && $flat->id > 0) {						
			$flat->update($_POST);
			$photo_flat_path = $flat->getPhotoPath();
			if (!is_dir($photo_flat_path)) {			
				mkdir($photo_flat_path,0777);
				chmod($photo_flat_path,0777);						
			}
			//добавляем новые фото
			if (isset($_POST['photo_flat']) && is_array($_POST['photo_flat'])) {
				foreach ($_POST['photo_flat'] as $fname) {
					$fname = clearTextData($fname);
					if ($fname!='') $flat->addPhoto($fname,$_POST);
				}
			}
			//редактируем существующие фото
			if (isset($_POST['photo_flat_exist']) && is_array($_POST['photo_flat_exist'])) {
				foreach ($_POST['photo_flat_exist'] as $fname) {
					$fname = clearTextData($fname);
					if ($fname!='') $flat->editPhoto($fname,$_POST);
				}
			}			
			header("Location: /flat.html?action=view&id=".$flat->id);
			exit();
		}
		else {
			//echo "error";
			//print_r($errors);
			$id = intval($_REQUEST['id']);			
			$flat->getFull($id);				
			$block_html = Html::pageFlatEdit($flat,$errors);
			$s->assign("YANDEX_KEY",YANDEX_KEY);
			if (isset($_SESSION['admin'])) $s->assign("is_admin",$_SESSION['admin']);			
			$s->assign("block_html",$block_html);
			$s->display("flat_add.tpl");
		}
	}
}

function view($s) {		
	$id = intval($_REQUEST['id']);	
	$flat = new Flat();		
	if (isset($_SESSION['last_flat_id']) && $_SESSION['last_flat_id']>0  
		&& $_SESSION['last_flat_id']!=$id && !isset($_SESSION['admin'])) {
		$flat->getFull($id,"(f.status=".REALTY_STATUS_SALE." 
		OR f.status=".REALTY_STATUS_RENT." 
		OR f.status=".REALTY_STATUS_NEW."
		OR f.status=".REALTY_STATUS_RENT_NEW.") AND t.status=".REALTY_STATUS_ACTIVE);		
	}
	else {
		$flat->getFull($id);		
	}
	if (!$flat->id) {
		echo "Not $flat->id";
		//header("Location: /index.html");
		exit();
	}
	if ($flat->rooms==1) {
		$room_name = "Одно";	
	}
	elseif ($flat->rooms==2) {
		$room_name = "Двух";	
	}
	elseif ($flat->rooms==3) {
		$room_name = "Трех";	
	}
	elseif ($flat->rooms==4) {
		$room_name = "Четырех";	
	}
	else {
		$room_name = "$flat->rooms-";	
	}
	if ($flat->status==REALTY_STATUS_SALE || $flat->status==REALTY_STATUS_NEW) {
		$act = 'продается в';
	}
	elseif ($flat->status==REALTY_STATUS_RENT || $flat->status==REALTY_STATUS_RENT_NEW) {
		$act = 'сдается в';
	}
	else {
		$act = '';
	}
	$res = Html::pageFlatView($flat,$act);
	$s->assign("block_flat_html",$res['block_flat_html']);
	$s->assign("block_tenement_html",$res['block_tenement_html']);
	$_SESSION['last_flat_id'] = (isset($_SESSION['last_flat_id'])) ? $_SESSION['last_flat_id'] : -1;
	if (!isset($_SESSION['admin']) || (isset($_SESSION['user_id']) && $flat->user_id!=$_SESSION['user_id'])) {
		$flat->incVisitorCount();
	}
	$s->assign("YANDEX_KEY",YANDEX_KEY);
	
	$address = $flat->city;	
	if ($flat->street && $flat->street!='') $address .= ', '.$flat->street;
	if ($flat->tnum!='' && $flat->show_address) $address .= ', д.'.$flat->tnum;
	$s->assign("title",$room_name."комнатная  квартира $act $address - Недвижимость Йошкар-Олы");
	$s->display("flat_view.tpl");	
}

function apply($s) {
	$id = intval($_REQUEST['id']);
	if (isset($_SESSION['user_id'])) {
		$flat = new Flat();
		$flat->getFull($id);
		if ($flat->user_id!=$_SESSION['user_id']) {
			header("Location: /index.html");		
			exit();
		}
	}
	else if ($id!=$_SESSION['last_flat_id']) {		
		header("Location: /index.html");
		exit();
	}
	$flat = new Flat();
	$status = ($_GET['status']!=REALTY_STATUS_RENT_APPLY) ? REALTY_STATUS_APPLY : REALTY_STATUS_RENT_APPLY;
	$user_cond = (isset($_SESSION['user_id'])) ? ' AND user_id='.$_SESSION['user_id'] : '';
	$flat->updateBy("id=$id".$user_cond,array('status'=>$status));	
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
	Flat::approve($id,$status);
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
	if (isset($_REQUEST['f_rooms'])) {
		$rooms = intval($_REQUEST['f_rooms']);
		if ($rooms>6) $rooms=0;
		if ($rooms>0) $add_sql .= " AND f.rooms = $rooms";		
		$filter['rooms'] = $rooms;				
	}
	else {
		$rooms  = isset($_GET['rooms']) ? intval($_GET['rooms']) : 0;
		$add_sql .= ($rooms>0) ? " AND f.rooms = $rooms" : '';
		if ($rooms>3) $rooms = 3;
		if ($rooms==3) $add_sql .= " AND f.rooms >= $rooms";
		//setcookie("f_rooms",'',time()+3600);		
	}
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
	
	if (isset($_REQUEST['f_date']) && $_REQUEST['f_date']!='') {
		$f_date = clearTextData($_REQUEST['f_date']);
		$a_tmp = explode('.',$f_date,3);
		$y = intval($a_tmp[2]);
		$m = intval($a_tmp[1]);
		$d = intval($a_tmp[0]); 		
		if ($f_date>0) $add_sql .= " AND f.updated_on>='$y-$m-$d'";		
		$filter['date'] = $f_date; 
	}
	
	
	if (isset($_REQUEST['f_tenement'])) {
		$f_tenement = intval($_REQUEST['f_tenement']);
		if ($f_tenement>5) $f_tenement=0;
		if ($f_tenement>0) $add_sql .= " AND t.type_id =".($f_tenement-1);		
		$filter['tenement'] = $f_tenement; 
	}

	if (isset($_REQUEST['f_kitchen'])) {
		$f_kitchen = intval($_REQUEST['f_kitchen']);
		if ($f_kitchen>4) $f_kitchen=0;
		if ($f_kitchen==1) {
			$add_sql .= " AND f.kitchen_area=0";		
		}
		elseif ($f_kitchen==2) {
			$add_sql .= " AND f.kitchen_area>0 AND f.kitchen_area<8";		
		}
		elseif ($f_kitchen==3) {
			$add_sql .= " AND f.kitchen_area>=8 AND f.kitchen_area<12";		
		}
		elseif ($f_kitchen==4) {
			$add_sql .= " AND f.kitchen_area>12";		
		}
		$filter['kitchen'] = $f_kitchen; 
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
	
	if (isset($_REQUEST['f_balcon'])) {
		$f_balcon = intval($_REQUEST['f_balcon']);		
		if ($f_balcon>0) $add_sql .= " AND (f.loggia>0 OR f.balcony>0)";		
		$filter['balcon'] = $f_balcon; 
	}

	if (isset($_REQUEST['f_no_corner'])) {
		$f_no_corner = intval($_REQUEST['f_no_corner']);		
		if ($f_no_corner>0) $add_sql .= " AND (f.is_corner=0 OR f.is_corner IS NULL)";		
		$filter['no_corner'] = $f_no_corner; 
	}
	if (isset($_REQUEST['f_storey_no_first'])) {
		$f_storey_no_first = intval($_REQUEST['f_storey_no_first']);		
		if ($f_storey_no_first>0) $add_sql .= " AND f.storey>1";		
		$filter['storey_no_first'] = $f_storey_no_first; 
	}
	if (isset($_REQUEST['f_storey_no_last'])) {
		$f_storey_no_last = intval($_REQUEST['f_storey_no_last']);		
		if ($f_storey_no_last>0) $add_sql .= " AND f.storey<t.storeys";		
		$filter['storey_no_last'] = $f_storey_no_last; 
	}
	if (isset($_REQUEST['f_bath'])) {
		$f_bath = intval($_REQUEST['f_bath']);		
		if ($f_bath>0) $add_sql .= " AND (f.type_bathroom=0 OR f.type_bathroom IS NULL)";		
		$filter['bath'] = $f_bath; 
	}
	if (isset($_REQUEST['f_photo'])) {
		$f_photo = intval($_REQUEST['f_photo']);		
		if ($f_photo>0) $add_sql .= " AND p2.name!=''";
		$filter['photo'] = $f_photo; 
	}
	if (isset($_REQUEST['f_regions'])) {
		$f_regions = intval($_REQUEST['f_regions']);		
		if ($f_regions>0) $add_sql .= " AND t.city_id>2";
		$filter['regions'] = $f_regions; 
	}
	elseif($_REQUEST['action']!='userSales'&&$_REQUEST['action']!='userRent') {
		$add_sql .= " AND t.city_id<3";
	}
	
	if (isset($_REQUEST['f_newt'])) {
		$f_newt = intval($_REQUEST['f_newt']);
		if ($f_newt>2) $f_newt=0;
		if ($f_newt==2) $add_sql .= " AND f.is_new=1";
		else $add_sql .= " AND f.is_new!=1";		
		$filter['newt'] = $f_newt; 
	}
	
	if (isset($_REQUEST['f_street_id']) && isset($_REQUEST['f_street']) && $_REQUEST['f_street_id']>0) {
		$f_street_id = intval($_REQUEST['f_street_id']);		
		$add_sql .= " AND t.street_id=$f_street_id";				
		$filter['street_id'] = $f_street_id;
		$filter['street'] = clearTextData($_REQUEST['f_street']); 
	}
	
	if (isset($_REQUEST['f_heating'])) {
		$f_heating = intval($_REQUEST['f_heating']);
		if ($f_heating>2) $f_heating=0;
		if ($f_heating==1) $add_sql .= " AND t.type_heating=1";
		else $add_sql .= " AND t.type_heating=0";		
		$filter['heating'] = $f_heating; 
	}
	if (isset($_REQUEST['f_is_owner'])) {
		$f_is_owner = intval($_REQUEST['f_is_owner']);
		if ($f_is_owner>0) $add_sql .= " AND f.is_owner!=0";
		$filter['is_owner'] = $f_is_owner;
	}
	/*
	foreach ($_REQUEST as $k => $v) {
		if ($k[0]=='f' && $k[1]=='_' && isset($_REQUEST[$k])) {
			setcookie($k,$_REQUEST[$k]);
		}
	}
	*/
	return array('filter'=>$filter,'sql'=>$add_sql);
}

function sales($s,$user_id=NULL,$status=REALTY_STATUS_SALE) {	
	$s->assign("top_img_num",1);
	$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
	$res = _filter($status);
	$tenement_status = ($status!=REALTY_STATUS_IMPORT_SALE && $status!=REALTY_STATUS_IMPORT_RENT) ? "t.status='".REALTY_STATUS_ACTIVE."' AND " : ''; 
	$add_sql = $tenement_status.$res['sql'];
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
	$filter_html = Html::getFlatFilter($filter,$user_id,$status);

	if (in_array($status,array(REALTY_STATUS_SALE,REALTY_STATUS_NEW,REALTY_STATUS_SOLD,REALTY_STATUS_IMPORT_SALE,REALTY_STATUS_IMPORT_RENT))) {
		$action = 'sales';
		if ($user_id && $status!=REALTY_STATUS_IMPORT_SALE) $action = 'userSales';
		elseif ($status==REALTY_STATUS_IMPORT_SALE) $action = 'userImportSales';
	}
	else {
		$action = 'rent';
		if ($user_id && $status!=REALTY_STATUS_IMPORT_RENT) $action = 'userRent';
		elseif ($status==REALTY_STATUS_IMPORT_RENT) $action = 'userImportRent';
	}
	//Объявления не старше месяца
	$add_sql .= " AND f.updated_on>'".getNextDate(date('Y-m-d'),-61)."'";
	$content = Html::getFlatList("$add_sql",$sort_by,$direction,PER_PAGE,$page,$action,$user_id);
	
	if ($status == REALTY_STATUS_SALE || $status == REALTY_STATUS_IMPORT_SALE) {
		$act = 'Продажа';
	}	
	else if ($status == REALTY_STATUS_RENT || $status == REALTY_STATUS_IMPORT_RENT) {
		$act = 'Аренда';
	}
	if ($status == REALTY_STATUS_IMPORT_SALE || $status == REALTY_STATUS_IMPORT_RENT) {
		$add = ' (импортированные)';
	}
	else {
		$add = '';	
	}
	
	if (!$user_id) {
		$block_name = $act.' квартир в Йошкар-Оле - последние объявления';	
	}
	else {
		$block_name = $act.' квартир - мои объявления'.$add;
	}
	$s->assign("block_name",$block_name);
	$s->assign("block_html",$filter_html.$content['html']);
	
	if ((!isset($_SESSION['admin']) || !isset($user_id)) && count($content['ids'])>0) {
		Flat::incQuickMassVisitorCount($content['ids']);	
	}
	
	$s->display("flat_sales.tpl");	
}

function delete() {	
	if (!isset($_SESSION['admin'])) {
		echo "Not admin";		
		//header("Location: /index.html");		
		exit();
	}
	$id = intval($_REQUEST['id']);
	Flat::delete($id);
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
	$s->assign("title","Продажа квартир в Йошкар-Оле и Марий Эл");
	if (!isset($_SESSION['user_id'])) {
		header("Location: index.html");
		exit();
	}
	sales($s,$_SESSION['user_id'],REALTY_STATUS_RENT);	
}

function userImportSales($s) {
	if (!isset($_SESSION['user_id'])) {
		header("Location: index.html");
		exit();
	}
	sales($s,$_SESSION['user_id'],REALTY_STATUS_IMPORT_SALE);	
}

function userImportRent($s) {
	if (!isset($_SESSION['user_id'])) {
		header("Location: index.html");
		exit();
	}
	sales($s,$_SESSION['user_id'],REALTY_STATUS_IMPORT_RENT);	
}


function rent($s) {
	sales($s,null,REALTY_STATUS_RENT);	
}

function updateDate() {
	$id=intval($_GET['id']);
	Flat::updateDate($id,$_SESSION['user_id']);
	header("Location: ".$_SERVER['HTTP_REFERER']);
	exit();
}


function remove() {
	$id=intval($_GET['id']);
	Flat::setUserStatus($id,$_SESSION['user_id'],REALTY_STATUS_DELETED);
	header("Location: ".$_SERVER['HTTP_REFERER']);
	exit();	
}

function sold() {
	$id=intval($_GET['id']);
	if (isset($_SESSION['user_id'])) {
		Flat::setUserStatus($id,$_SESSION['user_id'],REALTY_STATUS_SOLD);		
	}
	else {
		if (!isset($_SESSION['admin'])) {
			echo "Not admin";		
			//header("Location: /index.html");		
			exit();
		}
		Flat::setStatus($id,REALTY_STATUS_SOLD);
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
	
	$scale = (!isset($_GET['f_regions'])) ? 13 : 9;
	
	$add_sql = "f.updated_on>'".getNextDate(date('Y-m-d'),-30)."' AND t.status=".REALTY_STATUS_ACTIVE.' AND '.$res['sql'];
	$db_res = Flat::getFullListLink($add_sql);
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
	$s->assign("scale",$scale);	
	$s->assign("photos",$photos);
	$s->assign("icons",$icons);
		
	$block_html = Html::getBlock('Квартиры на продажу на карте Йошкар-Олы',Html::pageFlatMap());
	$s->assign("block_html",$block_html);
	$s->display("flat_map.tpl");
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