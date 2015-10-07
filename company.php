<?
include_once("./include/common.php");

$err = "";
$message = "";
$is_err = false;
$s->assign('session_id', session_id());
$s->assign("title","Профайл");
if (isset($_REQUEST['action'])) {
	$action = clearTextData($_REQUEST['action']);
	if (in_array($action,array('edit','request','companies'))) $action($s);
}			

function request(){
	$msg = clearTextData($_REQUEST['msg'],1000);
	$DOMAIN_ARR = explode('.',$_SERVER["HTTP_HOST"]);			
	$domain = $DOMAIN_ARR[0];	
	$company = Company::getInfoByDomain($domain);
	//$company['email'] = 'g12@bk.ru';	
	$res = mail($company['email'],'Запрос с сайта '.$_SERVER["HTTP_HOST"],$msg,"From: mari12.ru <notify@mari12.ru> \r\n MIME-Version: 1.0\r\nContent-type: text/plain; charset=utf-8");
	echo $report = ($res) ? 'Ваше сообщение отправлено компании.' : 'Ошибка при отправке письма.';
}

function edit($s) {			
	if (empty($_POST)) {
		$id = intval($_REQUEST['id']);		
		$company = Company::getInfo($id);
		if (!isset($_SESSION['user_id']) || $company['id'] != $_SESSION['company_id'] && !$_SESSION["admin"]) {
			header("Location: /index.html");
			exit();
		}				
		//$company['name'] = htmlspecialchars($company['name'], ENT_QUOTES, 'UTF-8');
		$company['contacts'] = strip_tags($company['contacts']); 
		$block_html = Html::pageCompanyEdit($company);
		$s->assign("YANDEX_KEY",YANDEX_KEY);	
		$s->assign("block_html",$block_html);
		$s->display("company_edit.tpl");
	}
	else {
		$id = intval($_REQUEST['id']);
		if (!isset($_SESSION['user_id']) || $id != $_SESSION['company_id'] && !$_SESSION["admin"]) {
			header("Location: /index.html");
			exit();
		}		
		$errors = Company::checkForm($_REQUEST);		
		if (!isset($errors['is_error']) && $id > 0) {
			$_POST['description'] = Realty::prepareDescription($_POST['description']);
			$_POST['address'] = clearTextData($_POST['address'],500);
			$_POST['contacts'] = nl2br(clearTextData($_POST['contacts'],500));				
			Company::updateStatic($id,$_POST);									
			//добавляем новые фото
			if (isset($_POST['photo_company']) && is_array($_POST['photo_company'])) {
				$photo_path = Company::getPhotoPathStatic($id);
				if (!is_dir($photo_path)) {			
					mkdir($photo_path,0777);
					chmod($photo_path,0777);						
				}
				foreach ($_POST['photo_company'] as $fname) {
					$fname = clearTextData($fname);
					if ($fname!='') Company::savePhoto($id,$fname,$_POST);
				}
			}			
			//редактируем существующие фото
			if (isset($_POST['photo_company_exist']) && is_array($_POST['photo_company_exist'])) {
				foreach ($_POST['photo_company_exist'] as $fname) {
					$fname = clearTextData($fname);
					if ($fname!='') Company::updatePhoto($id,$fname,$_POST);					
				}
			}

			//добавляем логотип
			if (isset($_POST['logo']) && is_array($_POST['logo'])) {
				$fname = clearTextData($_POST['logo'][0]);
				if ($fname!='') Company::saveLogo($id,$fname);
			}
			
			header("Location: /company.html?action=edit&id=".$id);
			exit();
		}
		else {
			$id = intval($_REQUEST['id']);		
			$company = Company::getInfo($id);
			if (!isset($_SESSION['user_id']) || $company['id'] != $_SESSION['company_id'] && !$_SESSION["admin"]) {
				header("Location: /index.html");
				exit();
			}				
			$block_html = Html::pageCompanyEdit($company,$errors);
			$s->assign("YANDEX_KEY",YANDEX_KEY);	
			$s->assign("block_html",$block_html);
			$s->display("company_edit.tpl");			
		}
	}
}

    /** Заполняет свойство val для полей фильтра значениями из REQUEST
     *  если значение не задано - может удалять из списка такие поля
     *
     * @param  &$filter - ссылка на список полей фильтра со свойствами
     * @param  $del_empty = true - нужно ли удалять поля фильтра, значения которых не заданы
     * @return void
     */
function fillFilterFromRequest(&$filter, $del_empty = true){

    foreach($filter as $filter_name => &$filter_props){
        if(isset($_REQUEST[$filter_name])){
            if($filter_props['type'] === 'int'){
                $filter_props['val'] = intval($_REQUEST[$filter_name]);
                $filter_props['request_val'] = '&'.$filter_name.'='.$filter_props['val'];
            }
        }
        elseif($del_empty){
            unset($filter[$filter_name]);
        }
    }
}

    /**Вывод списка компаний, используя фильтр (выбранный каталог)
     * значения полей фильтра получает из $_REQUEST
     *
     * @param $s - объект Smarty
     * @param $_REQUEST - данные запроса
     *
     * @return - none
     */
function companies($s){

    // получим список возможных полей фильтра
    $filter = Company::getFilterFields();
    // заполним фильтр значениями из запроса, незаполненные поля уберем
    fillFilterFromRequest($filter);

    // запросим список компаний и количество найденных компаний, независящее от страниц
    $companies = Company::getShortListByFilter($filter);

    // сформируем html код на основе полученных данных
    $html_res = HtmlCompany::getCompanyList($filter, $companies['companies'], $companies['amount'], PER_PAGE, 'companies');

    // заполним и покажем шаблон

	$s->assign("lat",LAT_YOLA);
	$s->assign("lon",LON_YOLA);
    $s->assign("scale",13);
	$s->assign("YANDEX_KEY",YANDEX_KEY);
    $s->assign("block_name",'Компании по недвижимости в Йошкар-Оле');
    $s->assign("title",'Компании по недвижимости в Йошкар-Оле');
	$s->assign("block_html",$html_res['html_block']);
    $s->assign("ids",$html_res['company_ids']);
    $s->assign("names",$html_res['names']);
    $s->assign("lons",$html_res['lons']);
    $s->assign("lats",$html_res['lats']);
    $s->assign("contacts",$html_res['contacts']);
    $s->assign("address",$html_res['address']);
    $s->assign("logo",$html_res['logo']);
    $s->assign("site",$html_res['site']);
    $s->display("company_list.tpl");

}

?>