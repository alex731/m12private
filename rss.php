<?php
//header("Content-Type: application/xml");
header("Content-Type: text/xml");

//----------объявление  параметров
#параметров для подключения к базе
$server = "localhost";
$db_name = "mari12_an";
$login = "mari12_root";
$password = "W2kHC99=*(Iq";
$url_photo = "http://mari12.ru/photos";
$url_flat_main = "http://mari12.ru/flat.php?action=view&id=";
$url_house_main = "http://mari12.ru/house.php?action=view&id=";
$url_land_main = "http://mari12.ru/land.php?action=view&id=";
#настройка количества экспортируемых объявлений
$limit_flat = 1000;
$limit_house = 100;
$limit_land = 100;
#Настройка запроса 
# идентификатор статуса недвижимости
$status_sale = 2;
$status_rent = 7; 
$day_max = 3; #количество дней для определения актуальности объявления
# тип объекта для таблицы  photo
$kind_id_flat = 2;
$kind_id_house = 3;
$kind_id_land = 5;
$kind_id_tenement = 1;
#параметры для building type
$id_building_brick = 0;
$id_building_panel = 1;
$id_building_block = 3;
$building_brick= "кирпичный";
$building_panel = "панельный";
$building_block = "блочный";

$name_id_flat = "flat";
$name_id_house = "house";

# Категория недвижимости
$category_house = "дом";
$category_flat = "квартира";  
$category_land = "земельный участок";  

$loggia_term = "лоджия";
$owner_term = "собственник";
#название валюты для стоимости
$currency = "руб";
#параметры для rss feed 
$rss_title = "mari12.ru";
$rss_description = "Новые объявления по недвижимости в Йошкар-Оле и Марий Эл";
$rss_link = "http://mari12.ru/";

$price_ration = 1000000;

class SimpleXMLExtended extends SimpleXMLElement {
    public function addCData($cdata_text) {
        $node = dom_import_simplexml($this); 
        $no   = $node->ownerDocument; 
        $node->appendChild($no->createCDATASection($cdata_text)); 
    } 
}

if (!empty($_GET["type"]))
{
	$property_type = $_GET["type"];
	#-----------------------------------------------------------------
	#-----------Подключение к базе
	$resource = mysql_connect ($server, $login, $password)or die(mysql_error());
	$db = mysql_select_db($db_name) or die("Ошибка базы данных");
	#--------------------------------------------------
	
	# переменные для заголовка XML файла
	#$generation_date = date("Y-m-d\TH:i:s+04:00",time());	
	# дата генерации xml файл в формате ISO 8601
	//$generation_date = date("c",time());
	$date1 = date("Y-m-d", time()-3600*24*$day_max); 
	#---------------------------------------------
	#    Генерация XML, запись заголовка
	
	$rss = new SimpleXMLExtended('<?xml version="1.0" encoding="utf-8"?><rss version="2.0"></rss>');
	$xml = $rss -> addChild ('channel');
    $xml -> link = $rss_link ;
	
	//$node = $xml-> addChild ('generation-date', $generation_date); 
	#---------------------------------------------
	#----------------------------------------------
	#       запрос к таблице flat
	if ($property_type == $name_id_flat)
	{        
        $rss_title = "mari12.ru - продажа квартир";
        $rss_description = "Новые объявления по продаже квартир в Йошкар-Оле и Марий Эл";
        
        $xml -> addChild ('title', $rss_title );
        $xml -> addChild ('description', $rss_description );
        
        $sqlrequest =   'SELECT tenement.storeys AS storeys, tenement.type_id AS t_typeId, flat.id AS flat_id, flat.price AS price, flat.rooms AS rooms, flat.storey AS floor,
					flat.description AS description, flat.updated_on AS updated_on, street.name AS street_name, photo.name AS photo_name,
					flat.is_owner  AS is_owner, photoT.name AS photo_name_tenement, flat.loggia AS loggia, flat.tenement_id					
					FROM flat 
					LEFT JOIN tenement ON  flat.tenement_id = tenement.id 
					LEFT JOIN street ON tenement.street_id = street.id 
					LEFT JOIN photo  ON photo.object_id = flat.id AND photo.kind_id= '.$kind_id_flat .
					' LEFT JOIN photo photoT ON photoT.object_id=tenement.id AND photoT.kind_id= '.$kind_id_tenement .
					' WHERE (flat.status = ' . $status_sale .') AND (  flat.updated_on >= "'. $date1 . '" )
					  ORDER BY flat.updated_on DESC LIMIT ' . $limit_flat;   
					  #
	// echo "<br>" . $sqlrequest;	
	#echo  $sqlrequest ." <br>"  ;		
	mysql_query("set character_set_results='utf8'");
	$result= mysql_query($sqlrequest) or die("Ошибка: " . mysql_error());
	
	$previous_id =  -1;
	#цикл по резльтатам запроса	
	while ($row = mysql_fetch_array($result))
	{	            
		$property_id = $row['flat_id'];
		# echo "flat_id: ". $property_id;	
		if ($previous_id != $property_id)
		{	
			$photo_name = $row['photo_name'];
			$photo_name_tenement = $row['photo_name_tenement'];	
			$type_id = $row['t_typeId']; # идентификатор типа дома (кирпичный и т.д.)
			if ($type_id == $id_building_brick )
				$building_type = $building_brick;
			else if  ($type_id == $id_building_panel )
				$building_type = $building_panel;
			else if  ($type_id == $id_building_block )
				$building_type = $building_block;
			else 
				$building_type = "";
			$desc = $row['description'];
			$category = $category_flat; #категория недвижимости для flat 
			$url_flat = $url_flat_main . $property_id ; # URL страницы с объявлением
			$updated_date = date("D, j M Y h:i:s +0400", strtotime($row['updated_on']));
			# echo " 	Creation date " . $creation_date . "from db " . $row['f_createdOn'];		
			# стоимость недвижимости
			$price = number_format($row['price']/$price_ration, 2);
			#описание жилого помещения
			$rooms = $row['rooms'];
			$storeys = $row['storeys']; # количество этажей
			$floor = $row['floor']; #этаж
			$street_name = $row['street_name'];			
			$loggia = $row['loggia'];
			$is_owner = $row['is_owner'];
			#----------------запись в переменую xml

			$item_title = $rooms . "-ком. " . $category . ", " .  $price. " млн. руб." . ", " .$street_name  ;
			$item_title .=  ", ". $floor . "/" . $storeys. " ". $building_type  ; 
			//echo $item_title;
			if ($loggia != "")
				$item_title .=  ", ". $loggia_term;
			if ($is_owner != "")
				$item_title .=  ", ". $owner_term;
			$item_description =  '';
			if ($photo_name_tenement != "")
				$item_description .= '<img src="' . $url_photo.'/'.$kind_id_tenement.'/'.$row['tenement_id'].'/'.$photo_name_tenement . '" style="float: left; max-height:150px; max-width:200px;  padding:5px"/>';
			if ($photo_name != "")
				$item_description .= '<img src="' . $url_photo.'/'.$kind_id_flat.'/'.$property_id.'/'.$photo_name . '" style="float: left; max-height:150px; max-width:200px; padding:5px"/>';
			$item_description .= $desc;
			
			# запись в xml переменную				
			$offer_ = $xml -> addChild ('item');
			$offer_ -> addChild ('title', $item_title ); 			
			$offer_ -> link = $url_flat ; 	
			$offer_ -> addChild ('description')->addCData($item_description);
			$offer_ -> addChild('pubDate', $updated_date );
		}
		$previous_id = $property_id;
	}	
	 mysql_free_result($result); 
	}

	#----------------------------------------------
	#       запрос к таблице house
	if ($property_type == $name_id_house)
	{
        $sqlrequest = 'SELECT house.id AS prop_id, house.price AS price, house.lat AS lat, house.lon AS lon, 
				house.updated_on AS updated_on, city.name AS city_name, house.description AS description,
				city.dist AS distance, house.is_owner  AS is_owner, photo.name AS photo_name
				FROM house 
				LEFT JOIN street ON house.street_id = street.id 
				LEFT JOIN city ON street.city_id = city.id  			
				LEFT JOIN photo  ON photo.object_id = house.id AND photo.kind_id = '.$kind_id_house .				
				' WHERE ((house.status = ' . $status_sale . ' OR  house.status = ' . $status_rent . 
				') AND (  house.updated_on >= "'. $date1 . '" ) )
				 ORDER BY house.updated_on DESC LIMIT ' . $limit_house;

	 mysql_query("set character_set_results='utf8'");
	 #echo  $sqlrequest ." <br>"  ;		
    $result= mysql_query($sqlrequest) or die("Ошибка: " . mysql_error());

	#цикл по резльтатам запроса
	
	$previous_id =  -1;
    while ($row = mysql_fetch_array($result))
    {	
	    $property_id = $row['prop_id'];	
		#echo $property_id ." <br>" ;	
		if ($previous_id != $property_id)
		{
			$photo_name = $row['photo_name'];
			$category = $category_house; #тип недвижимости
			$url_house = $url_house_main . $property_id ; # URL страницы с объявлением
			# echo " urlHouse " . $url_house;
			$updated_date = date("D, j M Y h:i:s +0400",strtotime($row['updated_on']));				
			# стоимость недвижимости
			$price = number_format($row['price']/$price_ration , 2);
			$city_name = $row['city_name'];
			$desc = $row['description'];
			$distance =  $row['distance'];
			$item_title = $category . ", " .  $price. " " . $currency . ", " .$city_name  ;
			if ($distance != "")
				$item_title .=  ", ". $distance . " км от Йошкар-Олы" ; 
			if ($is_owner != "")
				$item_title .=  ", ". $owner_term;
			$item_description =  '<![CDATA[' ;
			if ($photo_name != "")
				$item_description .= '<img src="' . $url_photo.'/'.$kind_id_house.'/'.$property_id.'/'.$photo_name . '" style="float: left"/>';	
			$item_description .= '&lt;p&gt;'. $desc . '&lt;/p&gt;';
            $item_description .= ']]>';			
			
				# запись в xml переменную				
			$offer_ = $xml -> addChild ('item');			
			$offer_ -> addChild ('title', $item_title ); 
			$offer_ -> link = $url_house ; 	
			$offer_ -> addChild ('description', $item_description ); 
            //$guidAttr = $offer_ -> guid =$url_house; 
			//$guidAttr -> addAttribute('isPermaLink', "true");
			$offer_ -> addChild('pubDate', $updated_date );
		}
		$previous_id = $property_id;
	}
	 mysql_free_result($result); 

	#----------------------------------------------
	#       запрос к таблице land
	
	$sqlrequest = 'SELECT land.id AS prop_id, land.price AS price, 	land.updated_on AS updated_on ,  city.name AS city_name, 
                land.lat AS lat, land.lon AS lon, land.description AS description, photo.name AS photo_name,
				city.dist AS distance, land.is_owner  AS is_owner
				FROM land 
				LEFT JOIN city ON land.city_id = city.id  
				LEFT JOIN photo  ON photo.object_id=land.id AND photo.kind_id= '.$kind_id_land .	
				' WHERE ((land.status = ' . $status_sale . ' OR  land.status = ' . $status_rent . 
				') AND (  land.updated_on >= "'. $date1 . '" ) )
				 ORDER BY land.updated_on DESC LIMIT ' . $limit_land;
			 
	 #mysql_query("SET NAMES 'utf-8'");				
	 ##mysql_query("SET CHARACTER SET 'utf-8'");
	 mysql_query("set character_set_results='utf8'");
	 #echo "<br>" . $sqlrequest;		
    $result= mysql_query($sqlrequest) or die("Ошибка: " . mysql_error());	
	#цикл по резльтатам запроса
	$previous_id =  -1;
    while ($row = mysql_fetch_array($result))
    {
		$property_id = $row['prop_id'];
		if ($previous_id != $property_id)
		{
			$photo_name = $row['photo_name'];
			$category = $category_land; #тип недвижимости
			$url_land = $url_land_main . $property_id ; # URL страницы с объявлением
			$updated_date = date("D, j M Y h:i:s +0400",strtotime($row['updated_on']));
			## echo " 	Createion date " . $creation_date . "from db " . $row['created_on'];		
			# стоимость недвижимости
			$price = number_format($row['price']/$price_ration , 2);			
			$city_name = $row['city_name'];
			$desc = $row['description'];
			$distance = $row['distance'];
			$item_title = $category . ", " .  $price. " " . $currency . ", " .$city_name  ;
			if ($distance != "")
				$item_title .=  ", ". $distance . " км от Йошкар-Олы" ; 
			if ($is_owner != "")
				$item_title .=  ", ". $owner_term;
			$item_description =  '<![CDATA[' ;
			if ($photo_name != "")
				$item_description .= '<img src="' . $url_photo.'/'.$kind_id_house.'/'.$property_id.'/'.$photo_name . '" style="float: left"/>';			
			$item_description .= '&lt;p&gt;'. $desc . '&lt;/p&gt;';
            $item_description .= ']]>';				
			# запись в xml переменную				
			$offer_ = $xml -> addChild ('item');	
			$offer_ -> addChild ('title', $item_title ); 
			$offer_ -> link = $url_land ; 	
			$offer_ -> addChild ('description', $item_description ); 
			$offer_ -> addChild('pubDate', $updated_date );
		}		
		$previous_id = $property_id;		
	}		
	 mysql_free_result($result); 
	 
	 	#сохраняем в xml файл	
	//$rss->asXML('rss1.xml');	
	}
	echo $rss->asXML();	
	mysql_close; 	
}
?>