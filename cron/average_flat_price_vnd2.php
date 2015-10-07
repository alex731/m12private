<?php
include_once("/home/mari12/public_html/include/common.php");

//if (!isset($_SESSION["admin"])) exit();
 
$db  = new DB($config['dbserver'],$config['dbname'],$config['dbroot'],$config['dbpassword_root']);
$db2 = new DB($config['dbserver'],$config['dbname'],$config['dbuser'],$config['dbpassword']);
$db3 = new DB($config['dbserver'],$config['dbname'],$config['dbuser'],$config['dbpassword']);
$db4 = new DB($config['dbserver'],$config['dbname'],$config['dbuser'],$config['dbpassword']);
$db5 = new DB($config['dbserver'],$config['dbname'],$config['dbuser'],$config['dbpassword']);
$db6 = new DB($config['dbserver'],$config['dbname'],$config['dbuser'],$config['dbpassword']);
//0 - кирпич
//1 - панель

function saveAveragePrices($start_date,$end_date) {
	
    global $db, $db2, $db3, $db4, $db5, $db6;
	
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
	
    if ($_GET['rooms']==1) {
    
    $rooms = 1;
	$min_price = 1000000;
	$max_price_m = 80000;
	$min_price_m = 20000;
	$min_area = 20;
    }
    else if ($_GET['rooms']==2) {
	$rooms = 2;
	$min_price = 1300000;
	$max_price_m = 80000;
	$min_price_m = 20000;
	$min_area = 22;
	}
    else if ($_GET['rooms']==3) {
	$rooms = 3;
	$min_price = 1400000;
	$max_price_m = 90000;
	$min_price_m = 20000;
	$min_area = 40;
	}
	
	//панель	
	$sql_p = "SELECT DATE_FORMAT( f.created_on,  '%Y-%m-%d' ) date, f.rooms,  
		ROUND( AVG( price ) ) price_avr, ROUND( AVG( price_m ) ) price_m_avr, 
		ROUND( AVG( total_area ) ) area_avr
		FROM flat f, tenement t 
		WHERE 
		f.tenement_id=t.id AND 
		t.city_id=0 AND 
		((f.created_on>='$start_date' AND f.created_on<='$end_date') 
		OR (f.created_on<='$end_date' AND f.updated_on>='$end_date'))
		AND t.type_id=1
		AND rooms =$rooms
		AND is_new =0
		AND price >$min_price
		AND price_m <$max_price_m
		AND price_m >$min_price_m 
		AND total_area>$min_area
		ORDER BY date ASC 
		";
	
	
	// кирпич	
	$sql_k = "SELECT DATE_FORMAT( f.created_on,  '%Y-%m-%d' ) date, f.rooms,  
		ROUND( AVG( price ) ) price_avr, ROUND( AVG( price_m ) ) price_m_avr, 
		ROUND( AVG( total_area ) ) area_avr
		FROM flat f, tenement t 
		WHERE 
		f.tenement_id=t.id AND 
		t.city_id=0 AND 
		((f.created_on>='$start_date' AND f.created_on<='$end_date') 
		OR (f.created_on<='$end_date' AND f.updated_on>='$end_date'))
		AND t.type_id=0
		AND rooms =$rooms 
		AND is_new =0
		AND price >$min_price
		AND price_m <$max_price_m
		AND price_m >$min_price_m 
		AND total_area>$min_area
		
		ORDER BY date ASC 
		";
	
	//новостройки	
	$sql_n = "SELECT DATE_FORMAT( f.created_on,  '%Y-%m-%d' ) date, f.rooms,  
		ROUND( AVG( price ) ) price_avr, ROUND( AVG( price_m ) ) price_m_avr, 
		ROUND( AVG( total_area ) ) area_avr
		FROM flat f, tenement t 
		WHERE 
		f.tenement_id=t.id AND 
		t.city_id=0 AND 
		((f.created_on>='$start_date' AND f.created_on<='$end_date') 
		OR (f.created_on<='$end_date' AND f.updated_on>='$end_date'))
		AND ((t.birthday>= IF (YEAR(f.created_on)=2012,2012,2011)) OR is_new=1)
		AND rooms = $rooms		
		AND price >$min_price
		AND price_m <$max_price_m
		AND price_m >$min_price_m 
		AND total_area>$min_area
		
		ORDER BY date ASC
		";	
	
	//земля	
	$min_price = 10000;
	$max_price_m = 200000;
	$min_price_m = 1000;
	$min_area = 1;		
	$sql_land = "SELECT null rooms, DATE_FORMAT( f.created_on,  '%Y-%m-%d' ) date,  
		ROUND( AVG( price ) ) price_avr, ROUND( AVG( price_h ) ) price_m_avr, 
		ROUND( AVG( area ) ) area_avr
		FROM land f
		WHERE 		
		((f.created_on>='$start_date' AND f.created_on<='$end_date') 
		OR (f.created_on<='$end_date' AND f.updated_on>='$end_date'))
		AND price >$min_price
		AND price_h <$max_price_m
		AND price_h >$min_price_m 
		AND area>$min_area
		
		ORDER BY date ASC
		";
	
	//дома	
	$min_price = 70000;
	$max_price_m = 70000;
	$min_price_m = 8000;
	$min_area = 10;
	$sql_house = "SELECT null rooms, DATE_FORMAT( f.created_on,  '%Y-%m-%d' ) date,  
		ROUND( AVG( price ) ) price_avr, ROUND( AVG( price_m ) ) price_m_avr, 
		ROUND( AVG( total_area ) ) area_avr
		FROM house f
		WHERE 		
		((f.created_on>='$start_date' AND f.created_on<='$end_date') 
		OR (f.created_on<='$end_date' AND f.updated_on>='$end_date'))
		AND price >$min_price
		AND price_m <$max_price_m
		AND price_m >$min_price_m 
		AND total_area>$min_area
		
		ORDER BY date ASC
		";
	
	$db->query($sql_p);
	
	$db2->query($sql_k);
	
	$db3->query($sql_n);
	
	$db4->query($sql_land);
	
	$db5->query($sql_house);
		
	while ($row_p = $db->fetchRow()) {
		$row_k = $db2->fetchRow();
	    $row_n = $db3->fetchRow();
	    $row_land = $db4->fetchRow();
	    $row_house = $db5->fetchRow();
	    
	    $sql2 = "INSERT INTO _tmp_prices2 VALUES 
		('$end_date','{$row_p['rooms']}','{$row_p['price_m_avr']}','{$row_k['price_m_avr']}','{$row_n['price_m_avr']}','{$row_land['price_m_avr']}','{$row_house['price_m_avr']}')";
		$db6->query($sql2);
	}
}

$db->query("TRUNCATE TABLE _tmp_prices2");
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
if (dateDiff('now',$start_date) > 0) {
    saveAveragePrices(getNextDate($start_date,1),date("Y-m-d"));
}



