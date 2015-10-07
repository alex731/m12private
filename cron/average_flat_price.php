<?php
include_once("/home/mari12/public_html/include/common.php");

//if (!isset($_SESSION["admin"])) exit();
global $db;
$db2 = new DB($config['dbserver'],$config['dbname'],$config['dbuser'],$config['dbpassword']);

function saveAveragePrices($start_date,$end_date) {
	global $db;
	global $db2;
	$prev_end_date = getNextDate($end_date,-1);
	$sql = "SELECT rooms, ROUND(AVG(price)) price_avr, ROUND(AVG(total_area),1) area_avr, 
		ROUND(AVG(price_m),1) price_m_avr 
		FROM flat f, tenement t WHERE f.updated_on>='$start_date' 
			AND f.updated_on<'$end_date' AND total_area > 29 AND total_area < 400
			AND f.price>1000000 AND f.price < 6000000 AND f.tenement_id=t.id
			AND f.status IN (2,6)
			AND t.city_id=0 
		GROUP BY rooms";
	
	$db->query($sql);
	while ($row = $db->fetchRow()) {
		$sql2 = "INSERT INTO average_flat_price VALUES 
		('$prev_end_date','{$row['rooms']}','{$row['price_avr']}','{$row['area_avr']}','{$row['price_m_avr']}')";
		$db2->query($sql2);
	}	
}


$start_date = '2014-03-17';//Пн

$db->query("DELETE FROM average_flat_price WHERE date>='$start_date'");

while ($start_date < date('Y-m-d')) {
	$end_date = getNextDate($start_date,7);		
	saveAveragePrices($start_date,$end_date);		
	$start_date = $end_date;
}

$end_date = date('Y-m-d');
//$end_date = '2015-09-14';
$start_date = getNextDate($end_date,-7);
//saveAveragePrices($start_date,$end_date);


