<?
include_once("./include/common.php");
$err = "";
$message = "";
$is_err = false;
$s->assign('session_id', session_id());

if (isset($_REQUEST['action'])) {
	$action = clearTextData($_REQUEST['action']);
	if (in_array($action,array('add','edit','view','approve','delete'))) 
		$action($s);
}			

function add($s) {
	if (empty($_POST)) {		
		$tenement = new Tenement();					
		$block_html = Html::pageTenementAdd($tenement);
		$s->assign("block_html",$block_html);
		$s->assign("YANDEX_KEY",YANDEX_KEY);
		$s->assign("is_admin",$_SESSION['admin']);
		$s->display("tenement_add.tpl");
	}
	else {
		$tenement = new Tenement();
		$errors_tenement = (!isset($_REQUEST['id'])||!$_REQUEST['id']) ? $tenement->checkForm($_REQUEST) : array();							
		$errors = array(TENEMENT=>$errors_tenement);		
		if (!isset($errors[TENEMENT]['is_error'])) {
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
			if ($tenement->id > 0) header("Location: /tenement.html?action=view&id=".$tenement->id);						
			exit();
		}
		else {
			echo "Error:".print_r($errors);
			$block_html = Html::pageTenementAdd($tenement,$errors);
			$s->assign("block_html",$block_html);
			$s->assign("YANDEX_KEY",YANDEX_KEY);
			$s->assign("is_admin",$_SESSION['admin']);
			$s->display("tenement_add.tpl");
		}
	}
}

function view($s) {	
	$id = intval($_REQUEST['id']);
	$tenement = new Tenement();
	$tenement->getFull($id);
	if (!$tenement->id) {
		echo "Not $tenement->id";
		//header("Location: /index.html");
		exit();
	}
	$block_html = Html::pageTenementView($tenement);
	$s->assign("block_html",$block_html);		
	$s->assign("id",$tenement->id);	
	$s->display("tenement_view.tpl");
}

function edit($s) {
	if (!isset($_SESSION['admin']) && !isset($_SESSION['user_id'])) {
		header("Location: /index.html");
		exit();
	}
	$id = intval($_GET['id']);
	$tenement = new Tenement();
	$tenement->getFull($id);
	if ($tenement->status!=REALTY_STATUS_NEW && !isset($_SESSION['admin'])) {
		header("Location: /index.html");
		exit();
	}
	if (empty($_POST) && $tenement->id>0) {			
		$block_html = Html::pageTenementEdit($tenement);		
		$s->assign("block_html",$block_html);
		$s->assign("YANDEX_KEY",YANDEX_KEY);
		$s->assign("LAT_CENTER_REGION",LAT_CENTER_REGION);
		$s->assign("LON_CENTER_REGION",LON_CENTER_REGION);
		$s->assign("city_id",$tenement->city_id);
		$s->assign("city",$tenement->city);
		$s->assign("street_id",$tenement->street_id);
		$s->assign("street",$tenement->street);
		if (isset($_SESSION['admin']))
			$s->assign("is_admin",$_SESSION['admin']);
		$s->display("tenement_edit.tpl");
	}
	//Смена адреса на существующий дом
	elseif (isset($_POST['id']) && $_POST['id']>0 && (isset($_SESSION['admin']) 
	 || (isset($_SESSION['user_id']) && $tenement->user_id==$_SESSION['user_id']))) {						
		$tenement_id = intval($_POST['id']);		
		$tenement->find($tenement_id);
		if (!$tenement->id) {
			echo "Дом не найден";
			exit();
		}
		//дом есть, переводим все кв в него, старый удаляем
		Flat::changeTenement($id,$tenement_id);
		if ($id != $tenement_id) {
			//Tenement::delete($id);
			//echo "delete $id";
		}
		header("Location: /tenement.html?action=view&id=".$tenement_id);
		exit();		
	}
	elseif($id>0 && !empty($_POST) && (isset($_SESSION['admin']) 
	 || (isset($_SESSION['user_id']) && $tenement->user_id==$_SESSION['user_id']))) {
		if ($_REQUEST['city_id']>1) {
			Tenement::$_properties['street']['required']=null;
			Tenement::$_properties['street_id']['required']=null;	
			Tenement::$_properties['number']['required']=null;
		}				
		$errors = $tenement->checkForm($_REQUEST);			
		if (!isset($errors['is_error']) && $tenement->id > 0) {								
			$tenement->update($_POST);			
			$photo_path = $tenement->getPhotoPath();
			if (!is_dir($photo_path)) {			
				mkdir($photo_path,0777);
				chmod($photo_path,0777);						
			}
			//добавляем новые фото
			if (isset($_POST['photo_tenement']) && is_array($_POST['photo_tenement'])) {
				foreach ($_POST['photo_tenement'] as $fname) {
					$fname = clearTextData($fname);
					if ($fname!='') $tenement->addPhoto($fname,$_POST);
				}
			}
			//редактируем существующие фото
			if (isset($_POST['photo_'.TENEMENT.'_exist']) && is_array($_POST['photo_'.TENEMENT.'_exist'])) {
				foreach ($_POST['photo_'.TENEMENT.'_exist'] as $fname) {
					$fname = clearTextData($fname);
					if ($fname!='') $tenement->editPhoto($fname,$_POST);
				}
			}			
			header("Location: /tenement.html?action=view&id=".$tenement->id);
			exit();
		}
		else {
			print_r($errors);
		}
	}
	else {		
		//$block_html = Html::pageTenementEdit($tenement);
		$s->assign("YANDEX_KEY",YANDEX_KEY);
		if (isset($_SESSION['admin']))
			$s->assign("is_admin",$_SESSION['admin']);
		$s->assign("block_html",'Дом не найден');
		$s->display("tenement_edit.tpl");
	}	
}

function approve($s) {
	if (!isset($_SESSION['admin'])) {		
		header("Location: /index.html");		
		exit();
	}
	$id = intval($_REQUEST['id']);
	Tenement::approve($id,REALTY_STATUS_ACTIVE);	
	$block_html = Html::getBlock('Сообщение',"Информация о доме утверждена.");
	$s->assign("block_html",$block_html);
	$s->display("msg.tpl");
}

function delete() {	
	if (!isset($_SESSION['admin'])) {
		echo "Not admin";		
		//header("Location: /index.html");		
		exit();
	}
	$id = intval($_REQUEST['id']);
	Tenement::delete($id);
	header("Location: ".$_SERVER['HTTP_REFERER']);		
}

?>