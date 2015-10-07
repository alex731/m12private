<?php
class City extends ListData {
	public static function getListLink($where='') {		
		global $db;
		if ($where!='')$where = "WHERE ".$where." AND c.region_id=r.id";
		$sql = "SELECT c.*, r.name region_name FROM city c, region r $where";		
		$db->query($sql);		
		return $db;
	}	
}
?>