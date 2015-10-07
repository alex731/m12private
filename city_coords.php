<?php
exit();
include_once("./include/common.php");
error_reporting(E_ALL);
$res = $db->query("SELECT * FROM city WHERE lat IS NULL ORDER BY id");
$db2 = new DB($config['dbserver'],$config['dbname'],$config['dbuser'],$config['dbpassword']);		
while ($row = $db->fetchRow()) {
	$name = $row['name'];
	$geo_url = 'http://geocode-maps.yandex.ru/1.x/?geocode='.urlencode($name).'&key='.YANDEX_KEY.'&format=json&ll='.LON_CENTER_REGION.','.LAT_CENTER_REGION.'&spn=4,4&rspn=1';
	$json_str = file_get_contents($geo_url);
	$response = json_decode($json_str);
	//$data.response.GeoObjectCollection.featureMember[0].GeoObject.Point.pos
	echo $name.' - ';
	if ($response->response->GeoObjectCollection->metaDataProperty->GeocoderResponseMetaData->found > 0) {
		echo $response->response->GeoObjectCollection->featureMember[0]->GeoObject->Point->pos;
		$arr = explode(" ",$response->response->GeoObjectCollection->featureMember[0]->GeoObject->Point->pos);
		$lon = $arr[0];
		$lat = $arr[1];
		$db2->query("UPDATE city SET lat='$lat', lon='$lon' WHERE id={$row['id']}");			 		
	}
	else {
		$db2->query("UPDATE city SET lat='0', lon='0' WHERE id={$row['id']}");
		echo "not";
	}
	echo "<br>";
}

?>