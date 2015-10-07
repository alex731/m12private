<?php
include_once("./include/common.php");

try {
	$_REQUEST['action']($_REQUEST);	
} 
catch (Exception $e) {
	echo 0;
}

function cityList($param) {	
	$val = clearTextData($param['q']);
	//$val = $param['q'];
	//$val = iconv('UTF-8', 'windows-1251', $val);
	$res = City::getListLink('c.name LIKE "%'.$val.'%"');	
	//header('Content-type: text/html; charset=windows-1251');
	while ($row = $res->fetchRow()) {
		echo "{$row['name']} ({$row['region_name']})|{$row['id']}|{$row['lat']}|{$row['lon']}\r\n";
	}	
}

function streetList($param) {	
	$val = clearTextData($param['q']);
	$city_id = intval($param['city_id']);	
	$res = Street::getListLink('city_id='.$city_id.' AND status=1 AND name LIKE "%'.$val.'%"');	
	while ($row = $res->fetchRow()) echo "{$row['name']}|{$row['id']}\r\n";
}

function getTenementInfo($param) {
	if (isset($param['tenement_id'])) {
		$tenement_id = intval($param['tenement_id']);
		$row = Tenement::getFullStatic($tenement_id);		
		if ($row) {
			$vals = Tenement::getPropertiesValStatic($row);
			$vals['id'] = $row['id'];
			$vals['city_id'] = $row['city_id'];
			$vals['street_id'] = $row['street_id'];
			$vals['lat'] = $row['lat'];
			$vals['lon'] = $row['lon'];
			$vals['address'] = $row['street'].', '.$row['number'];			
			echo json_encode($vals);
		}
		else echo '';			
	}
	else {
		$city_id = intval($param['city_id']);
		$number = clearTextData($param['number']);	
		$street = clearTextData($param['street']);
		$sql = "city_id=$city_id AND name='$street'";	
		$res = Street::getListLink($sql);	
		$num_rows = $res->numRows();	
		if ($num_rows<1) {
			/*
			 * Добавлять без проверки на человека не будем чтобы не было дыры
			 */
			//$street_id = $streetObj->add(array('city_id'=>$city_id, 'name'=>$street));
			echo '';	
		}
		else {
			$row = $res->fetchRow();		
			$street_id = $row['id'];				
			$res = Tenement::getBy("city_id=$city_id AND street_id=$street_id AND number LIKE '$number'");
			if ($res) $row = $res->fetchRow();
			if ($row) {
				$vals = Tenement::getPropertiesValStatic($row);
				$vals['id'] = $row['id'];
				$vals['city_id'] = $row['city_id'];
				$vals['street_id'] = $row['street_id'];
				$vals['lat'] = $row['lat'];
				$vals['lon'] = $row['lon'];
				$vals['address'] = $street.', '.$row['number'];			
				echo json_encode($vals);
			}
			else echo ''; 
		}
	}				
}

function updateSession(){
	echo 1;
}

function delPhoto($param){	
	$name = clearTextData($param['name']);		
	$num_deleted = Photo::delete($name);
	echo $name;
}

function getFlatListByTenement($param){
	global $config;
	$id=intval($param['id']);
	if (isset($_GET['limit']) && intval($_GET['limit'])==3) {
		$limit = 3;
	}
	else {
		 $limit = 30;
	}
	$db_res = Flat::getFullListLink("tenement_id=$id AND (f.status=".REALTY_STATUS_SALE." OR f.status=".REALTY_STATUS_RENT." OR f.status=".REALTY_STATUS_SOLD.") GROUP BY f.id ORDER BY f.updated_on DESC LIMIT $limit");
	if (isset($_SESSION['user_id'])&& in_array($_SESSION['user_id'],$config['user_workers'])) {
		$is_editor = true;
		$h = '<th></th>'; 
	}
	else {
		$is_editor = false;
		$h='';
	}
	$html = '
	История продаж в этом доме:<br><br>
	<table class="table table-striped table-bordered table-condensed"><thead>
	<tr>
	<th>Дата</th><th>Комнат</th><th>Цена (руб.)</th><th>Цена за кв.м.(руб.)</th><th>Этаж</th><th>Общ. пл.м<sup>2</sup></th><th>Сделка</th>'.$h.'
	</tr></thead>';
	while ($row = $db_res->fetchRow()) {
		$html .= '<tr>';
		$date = explode(' ',$row['updated_on']);
		$dates = explode('-',$date[0]);
		$date = $dates[2].'.'.$dates[1].'.'.$dates[0];
		//$type = ($row['status']==REALTY_STATUS_SALE) ? 'продажа' : 'аренда';
		switch ($row['status'])
		{
		case REALTY_STATUS_SALE:$type='продажа';break;
		case REALTY_STATUS_RENT:$type='аренда';break;
		case REALTY_STATUS_SOLD:$type='продано';break;
		}
		    
		$t = ($is_editor) ? '<td><a href="/flat.html?action=updateDate&id='.$row['id'].'" title="Обновить дату"><img src="/images/icon_update.png"></a></td>' : '';
		$price = number_format($row['price'],0);
        $price_m = number_format($row['price_m'],0);
		$html .= "<td><a href='/flat.html?action=view&id={$row['id']}' title='смотреть'>$date</a></td><td>{$row['rooms']}</td><td>{$price}</td><td>{$price_m}</td><td>{$row['storey']}</td><td>{$row['total_area']}</td><td>$type</td>$t";
		$html .= '</tr>';
	}
	$html .= '</table>';
	if (isset($date)) echo $html;
	else echo '';
}

function getAnyStreet($param) {
	$id=intval($param['id']);
	$db_res = Street::getListByCityLink("city_id=$id LIMIT 1");
	$row = $db_res->fetchRow();
	echo $row['id'].'|'.$row['name'];
}

function getCommercialListByCity($param){
	global $config;
	$id=intval($param['id']);		
	if (isset($_SESSION['user_id'])&& in_array($_SESSION['user_id'],$config['user_workers'])) {
		$is_editor = true;
		$h = '<th></th>'; 
	}
	else {
		$is_editor = false;
		$h='';
	}
	
	if (!$is_editor) {
		$limit = 5;
	}
	else {
		 $limit = 30;
	}	
	$html = '
	История продаж в этом населенном пункте:<br><br>
	<table class="table table-striped table-bordered table-condensed"><thead>
	<tr>
	<th>Дата</th><th>Цена (руб.)</th><th>Тип</th><th>Общ. пл.м<sup>2</sup></th><th>Описание</th><th>Сделка</th>'.$h.'
	</tr></thead>';
	$db_res = Commercial::getFullListLink("f.city_id=$id AND (f.status=".REALTY_STATUS_SALE." OR f.status=".REALTY_STATUS_RENT.") GROUP BY f.id ORDER BY updated_on DESC LIMIT $limit");
	while ($row = $db_res->fetchRow()) {		
		$html .= '<tr>';
		$date = formatDate($row['updated_on']);						
		$t = ($is_editor) ? '<td><a href="/commercial.html?action=updateDate&id='.$row['id'].'" title="Обновить дату"><img src="/images/icon_update.png"></a></td>' : '';
		$price = number_format($row['price'],0);
		$description = textReduce($row['description']);
		$type = Commercial::$TYPE[$row['type_id']];
		$type_sale = ($row['status']==REALTY_STATUS_SALE) ? 'продажа' : 'аренда';
		$html .= "<td><a href='/commercial.html?action=view&id={$row['id']}' title='смотреть'>$date</a></td><td>{$price}</td><td>{$type}</td><td>{$row['total_area']}</td><td>{$description}</td><td>{$type_sale}</td>$t";		
		$html .= '</tr>';
	}
	$html .= '</table>';
	if (isset($date)) echo $html;
	else echo '';
}


function getHouseListByCity($param){
	global $config;
	$id=intval($param['id']);
		
	if (isset($_SESSION['user_id'])&& in_array($_SESSION['user_id'],$config['user_workers'])) {
		$is_editor = true;
		$h = '<th></th>'; 
	}
	else {
		$is_editor = false;
		$h='';
	}
	
	if (!$is_editor) {
		$limit = 3;
	}
	else {
		 $limit = 30;
	}	
	$html = '
	История продаж в этом населенном пункте:<br><br>
	<table class="table table-striped table-bordered table-condensed"><thead>
	<tr>
	<th>Дата</th><th>Цена (руб.)</th><th>Общ. пл.м<sup>2</sup></th><th>Участок (соток)</th><th>Описание</th>'.$h.'
	</tr></thead>';
	$db_res = House::getFullListLink("f.city_id=$id AND f.status=".REALTY_STATUS_SALE." GROUP BY f.id ORDER BY updated_on DESC LIMIT $limit");
	while ($row = $db_res->fetchRow()) {		
		$html .= '<tr>';
		$date = formatDate($row['updated_on']);						
		$t = ($is_editor) ? '<td><a href="/house.html?action=updateDate&id='.$row['id'].'" title="Обновить дату"><img src="/images/icon_update.png"></a></td>' : '';
		$price = number_format($row['price'],0);
		$description = textReduce($row['description']);
		$html .= "<td><a href='/house.html?action=view&id={$row['id']}' title='смотреть'>$date</a></td><td>{$price}</td><td>{$row['total_area']}</td><td>{$row['land_area']}</td><td>{$description}</td>$t";		
		$html .= '</tr>';
	}
	$html .= '</table>';
	if (isset($date)) echo $html;
	else echo '';
}

function getLandListByCity($param){
	global $config;
	$id=intval($param['id']);		
	if (isset($_SESSION['user_id'])&& in_array($_SESSION['user_id'],$config['user_workers'])) {
		$is_editor = true;
		$h = '<th></th>'; 
	}
	else {
		$is_editor = false;
		$h='';
	}
	
	if (!$is_editor) {
		$limit = 3;
	}
	else {
		 $limit = 30;
	}	
	$html = '
	История продаж в этом населенном пункте:<br><br>
	<table class="table table-striped table-bordered table-condensed"><thead>
	<tr>
	<th>Дата</th><th>Цена (руб.)</th></th><th>Площадь (соток)</th><th>Описание</th>'.$h.'
	</tr></thead>';
	$db_res = Land::getFullListLink("f.city_id=$id AND f.status=".REALTY_STATUS_SALE." GROUP BY f.id ORDER BY updated_on DESC LIMIT $limit");
	while ($row = $db_res->fetchRow()) {		
		$html .= '<tr>';
		$date = formatDate($row['updated_on']);						
		$t = ($is_editor) ? '<td><a href="/land.html?action=updateDate&id='.$row['id'].'" title="Обновить дату"><img src="/images/icon_update.png"></a></td>' : '';
		$price = number_format($row['price'],0);
		$description = textReduce($row['description']);
		$html .= "<td><a href='/land.html?action=view&id={$row['id']}' title='смотреть'>$date</a></td><td>{$price}</td><td>{$row['area']}</td><td>{$description}</td>$t";		
		$html .= '</tr>';
	}
	$html .= '</table>';
	if (isset($date)) echo $html;
	else echo '';
}

?>
