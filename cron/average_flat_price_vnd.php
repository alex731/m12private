<?php
include_once("/home/mari12/public_html/include/common.php");

//if (!isset($_SESSION["admin"])) exit();
global $db;
$db2 = new DB($config['dbserver'],$config['dbname'],$config['dbuser'],$config['dbpassword']);

//0 - кирпич
//1 - панель

function saveAveragePrices($start_date,$end_date) {
	global $db;
	global $db2;
	//$prev_end_date = getNextDate($end_date,-1);
	
	/*
	//понедельное количество продающихся квартир
	$sql = "SELECT DATE_FORMAT( f.created_on,  '%Y-%m-%d' ) date, f.rooms,  
		COUNT(f.id) count_flats, ROUND( AVG( price ) ) price_avr, ROUND( AVG( price_m ) ) price_m_avr, 
		ROUND( AVG( total_area ) ) area_avr
		FROM flat f, tenement t 
		WHERE 
		f.tenement_id=t.id AND 
		t.city_id=0 AND 
		((f.created_on>='$start_date' AND f.created_on<='$end_date') 
		OR (f.created_on<='$end_date' AND f.updated_on>='$end_date'))		
		AND price >800000
		AND price_m <80000
		AND price_m >15000 
		AND total_area>20 
		";
	/*	AND rooms =3 
		AND is_new =0
	*/
	/*		
	//цена трешки вторичка
	$sql = "SELECT DATE_FORMAT( f.created_on,  '%Y-%m-%d' ) date, f.rooms,  
		ROUND( AVG( price ) ) price_avr, ROUND( AVG( price_m ) ) price_m_avr, 
		ROUND( AVG( total_area ) ) area_avr
		FROM flat f, tenement t 
		WHERE 
		f.tenement_id=t.id AND 
		t.city_id=0 AND 
		((f.created_on>='$start_date' AND f.created_on<='$end_date') 
		OR (f.created_on<='$end_date' AND f.updated_on>='$end_date'))		
		AND rooms =3 
		AND is_new =0
		AND price >1800000
		AND price_m <80000
		AND price_m >15000 
		AND total_area>40 
		";	
	

	//кв. м. 1-комн.	
	$sql = "SELECT DATE_FORMAT( f.created_on,  '%Y-%m-%d' ) date, f.rooms,  
		ROUND( AVG( price ) ) price_avr, ROUND( AVG( price_m ) ) price_m_avr, 
		ROUND( AVG( total_area ) ) area_avr
		FROM flat f, tenement t 
		WHERE 
		f.tenement_id=t.id AND 
		t.city_id=0 AND 
		((f.created_on>='$start_date' AND f.created_on<='$end_date') 
		OR (f.created_on<='$end_date' AND f.updated_on>='$end_date'))		
		AND rooms = 1
		AND price >1000000
		AND price_m <80000
		AND price_m >16000 
		AND total_area>20 
		";		
	
	//кв. м. 2-комн.	
	$sql = "SELECT DATE_FORMAT( f.created_on,  '%Y-%m-%d' ) date, f.rooms,  
		ROUND( AVG( price ) ) price_avr, ROUND( AVG( price_m ) ) price_m_avr, 
		ROUND( AVG( total_area ) ) area_avr
		FROM flat f, tenement t 
		WHERE 
		f.tenement_id=t.id AND 
		t.city_id=0 AND 
		((f.created_on>='$start_date' AND f.created_on<='$end_date') 
		OR (f.created_on<='$end_date' AND f.updated_on>='$end_date'))		
		AND rooms = 2
		AND price >1200000
		AND price_m <80000
		AND price_m >20000 
		AND total_area>40 
		";		

	//кв. м. 3-комн.	
	$sql = "SELECT DATE_FORMAT( f.created_on,  '%Y-%m-%d' ) date, f.rooms,  
		ROUND( AVG( price ) ) price_avr, ROUND( AVG( price_m ) ) price_m_avr, 
		ROUND( AVG( total_area ) ) area_avr
		FROM flat f, tenement t 
		WHERE 
		f.tenement_id=t.id AND 
		t.city_id=0 AND 
		((f.created_on>='$start_date' AND f.created_on<='$end_date') 
		OR (f.created_on<='$end_date' AND f.updated_on>='$end_date'))		
		AND rooms = 3
		AND price >1500000
		AND price_m <80000
		AND price_m >20000 
		AND total_area>45 
		";		
*/	
	/*
	//кв. м. 1-к панель(1) кирпич(0)
	$sql = "SELECT DATE_FORMAT( f.created_on,  '%Y-%m-%d' ) date, f.rooms,  
		ROUND( AVG( price ) ) price_avr, ROUND( AVG( price_m ) ) price_m_avr, 
		ROUND( AVG( total_area ) ) area_avr
		FROM flat f, tenement t 
		WHERE 
		f.tenement_id=t.id AND 
		t.city_id=0 AND 
		((f.created_on>='$start_date' AND f.created_on<='$end_date') 
		OR (f.created_on<='$end_date' AND f.updated_on>='$end_date'))
		AND (t.birthday<=2011 OR t.birthday IS NULL)
		AND t.type_id=1
		AND rooms =1
		AND is_new =0
		AND price >1000000
		AND price_m <80000
		AND price_m >20000 
		AND total_area>20 
		";	
	*/
	//1-шки новостройки

	/*
	$sql = "SELECT DATE_FORMAT( f.created_on,  '%Y-%m-%d' ) date, f.rooms,  
		ROUND( AVG( price ) ) price_avr, ROUND( AVG( price_m ) ) price_m_avr, 
		ROUND( AVG( total_area ) ) area_avr
		FROM flat f, tenement t 
		WHERE 
		f.tenement_id=t.id AND 
		t.city_id=0 AND 
		((f.created_on>='$start_date' AND f.created_on<='$end_date') 
		OR (f.created_on<='$end_date' AND f.updated_on>='$end_date'))
		AND (t.birthday>=2012 OR is_new=1) 
		AND rooms = 1 		
		AND price > 1200000
		AND price < 3000000
		AND price_m <90000
		AND price_m >20000 
		AND total_area>30 
		";
	*/
	
	//кв. м. двушки панель	
	$sql = "SELECT DATE_FORMAT( f.created_on,  '%Y-%m-%d' ) date, f.rooms,  
		ROUND( AVG( price ) ) price_avr, ROUND( AVG( price_m ) ) price_m_avr, 
		ROUND( AVG( total_area ) ) area_avr
		FROM flat f, tenement t 
		WHERE 
		f.tenement_id=t.id AND 
		t.city_id=0 AND 
		((f.created_on>='$start_date' AND f.created_on<='$end_date') 
		OR (f.created_on<='$end_date' AND f.updated_on>='$end_date'))
		AND is_new =0 
		AND rooms = 2
		AND t.type_id=1 
		AND price > 1300000
		AND price < 8000000
		AND price_m <90000
		AND price_m >20000 
		AND total_area>30 
		";	
	/*
	//кв. м. двушки кирпич	
	$sql = "SELECT DATE_FORMAT( f.created_on,  '%Y-%m-%d' ) date, f.rooms,  
		ROUND( AVG( price ) ) price_avr, ROUND( AVG( price_m ) ) price_m_avr, 
		ROUND( AVG( total_area ) ) area_avr
		FROM flat f, tenement t 
		WHERE 
		f.tenement_id=t.id AND 
		t.city_id=0 AND 
		((f.created_on>='$start_date' AND f.created_on<='$end_date') 
		OR (f.created_on<='$end_date' AND f.updated_on>='$end_date'))
		AND is_new =0 
		AND rooms = 2
		AND t.type_id=0 
		AND price > 1300000
		AND price < 8000000
		AND price_m <90000
		AND price_m >20000 
		AND total_area>30 
		";
	
	//кв. м. двушки новостройки	
	$sql = "SELECT DATE_FORMAT( f.created_on,  '%Y-%m-%d' ) date, f.rooms,  
		ROUND( AVG( price ) ) price_avr, ROUND( AVG( price_m ) ) price_m_avr, 
		ROUND( AVG( total_area ) ) area_avr
		FROM flat f, tenement t 
		WHERE 
		f.tenement_id=t.id AND 
		t.city_id=0 AND 
		((f.created_on>='$start_date' AND f.created_on<='$end_date') 
		OR (f.created_on<='$end_date' AND f.updated_on>='$end_date'))
		AND ((t.birthday>= IF (YEAR(f.created_on)=2012,2012,2011)) OR is_new=1)
		AND rooms = 2		
		AND price > 1300000
		AND price < 8000000
		AND price_m <90000
		AND price_m >20000 
		AND total_area>30 
		";
	*/
	
	//трешки кирпич	
	$sql = "SELECT DATE_FORMAT( f.created_on,  '%Y-%m-%d' ) date, f.rooms,  
		ROUND( AVG( price ) ) price_avr, ROUND( AVG( price_m ) ) price_m_avr, 
		ROUND( AVG( total_area ) ) area_avr
		FROM flat f, tenement t 
		WHERE 
		f.tenement_id=t.id AND 
		t.city_id=0 AND 
		((f.created_on>='$start_date' AND f.created_on<='$end_date') 
		OR (f.created_on<='$end_date' AND f.updated_on>='$end_date'))
		AND t.type_id=0
		AND rooms =3 
		AND is_new =0
		AND price >1400000
		AND price_m <90000
		AND price_m >20000 
		AND total_area>40 
		";
	
	//кв. м. 3-шки новостройки	
	$sql = "SELECT DATE_FORMAT( f.created_on,  '%Y-%m-%d' ) date, f.rooms,  
		ROUND( AVG( price ) ) price_avr, ROUND( AVG( price_m ) ) price_m_avr, 
		ROUND( AVG( total_area ) ) area_avr
		FROM flat f, tenement t 
		WHERE 
		f.tenement_id=t.id AND 
		t.city_id=0 AND 
		((f.created_on>='$start_date' AND f.created_on<='$end_date') 
		OR (f.created_on<='$end_date' AND f.updated_on>='$end_date'))
		AND ((t.birthday>= IF (YEAR(f.created_on)=2012,2012,2011)) OR is_new=1)
		AND rooms = 3		
		AND price > 1500000
		AND price < 8000000
		AND price_m <90000
		AND price_m >20000 
		AND total_area>40 
		";	
	
	
	$db->query($sql);
	while ($row = $db->fetchRow()) {
		$sql2 = "INSERT INTO _tmp_prices VALUES 
		('$end_date','{$row['rooms']}','{$row['price_avr']}','{$row['area_avr']}','{$row['price_m_avr']}','{$row['count_flats']}')";
		$db2->query($sql2);
	}
}

$db->query("TRUNCATE TABLE _tmp_prices");
//$end_date = date('Y-m-d');
$end_date = '2011-10-30';//Вс
$start_date = getNextDate($end_date,-6);//Пн

//$prev_end_date = getNextDate($end_date,-1);

$i = 0;
while ($end_date < date("Y-m-d") && $i<1000) {
	saveAveragePrices($start_date,$end_date);
	$start_date = getNextDate($start_date,7);//Пн
	$end_date = getNextDate($start_date,6);//Вс
	$i++;
}		


