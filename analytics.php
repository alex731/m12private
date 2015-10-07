<?
include_once("./include/common.php");
$s->assign("title",'Аналитика - динамика изменения цен на недвижимость в Йошкар-Оле и Марий Эл');
$db->query("SELECT * FROM average_flat_price ORDER BY date ASC");
while ($row = $db->fetchRow()) {
	$data[$row['date']][$row['rooms']]=$row['price_m'];
	$data2[$row['date']][$row['rooms']]=$row['price'];
}
foreach ($data as $date=>$arr) {
	if (!isset($arr[1]) || $arr[1]=='')$arr[1] = $a1;
	if (!isset($arr[2]) || $arr[2]=='')$arr[2] = $a2;
	if (!isset($arr[3]) || $arr[3]=='')$arr[3] = $a3;
	$dates = explode('-',$date);
	$date2 = $dates[2].'.'.$dates[1].'.'.$dates[0];
	$m[] = "['$date2',{$arr[1]},{$arr[2]},{$arr[3]}]";
	$a1 = $arr[1];
	$a2 = $arr[2];
	$a3 = $arr[3];		
}
foreach ($data2 as $date=>$arr2) {
	if (!isset($arr2[1]) || $arr2[1]=='')$arr2[1] = $a1;
	if (!isset($arr2[2]) || $arr2[2]=='')$arr2[2] = $a2;
	if (!isset($arr2[3]) || $arr2[3]=='')$arr2[3] = $a3;
	$dates = explode('-',$date);
	$date2 = $dates[2].'.'.$dates[1].'.'.$dates[0];
	$m2[] = "['$date2',{$arr2[1]},{$arr2[2]},{$arr2[3]}]";
	$a1 = $arr2[1];
	$a2 = $arr2[2];
	$a3 = $arr2[3];		
}
$end_date = date('Y-m-d');

if ($end_date > $date) {	
	$sql = "SELECT rooms, ROUND(AVG(price)) price_avr, ROUND(AVG(total_area),1) area_avr, 
		ROUND(AVG(price_m),1) price_m_avr 
		FROM flat WHERE updated_on>'$date' 
			AND updated_on<='$end_date' AND price>1000000 
		GROUP BY rooms";
	$db->query($sql);
	$end_date2 = date('d.m.Y');
	while ($row = $db->fetchRow()) {
		$d[$row['rooms']]=$row['price_m_avr'];
		$d2[$row['rooms']]=$row['price_avr'];	
	}
	
	if (!isset($d[1])) $d[1] = $arr[1];
	if (!isset($d[2])) $d[2] = $arr[2];
	if (!isset($d[3])) $d[3] = $arr[3];
	
	if (!isset($d2[1])) $d2[1] = $arr2[1];
	if (!isset($d2[2])) $d2[2] = $arr2[2];
	if (!isset($d2[3])) $d2[3] = $arr2[3];
		
	$m[] = "['$end_date2',{$d[1]},{$d[2]},{$d[3]}]";
	$m2[] = "['$end_date2',{$d2[1]},{$d2[2]},{$d2[3]}]";	
}

$ms = implode(",", $m);
$ms2 = implode(",", $m2);
$s->assign("ms",$ms);
$s->assign("ms2",$ms2);
$s->display("analytics.tpl");
?>