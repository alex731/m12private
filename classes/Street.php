<?php
class Street extends ListData {
	public static function getListByCityLink($where='') {		
		global $db;
		if ($where!='')$where = "WHERE ".$where;
		$sql = "SELECT s.name,s.id FROM street s, city c $where";		
		$db->query($sql);		
		return $db;
	}
}
?>