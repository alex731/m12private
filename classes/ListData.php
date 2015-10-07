<?php
abstract class ListData extends DataFromDb {	
	
    public function __construct(array $options = null) {
        parent::__construct();        
        $this->last_id=0;
    	if (is_array($options)) {                        
            return $this->create($options);
        }
        return true;
    }
	
	public static function getListLink($where='') {		
		global $db;
		if ($where!='')$where = "WHERE ".$where;
		$sql = "SELECT * FROM ".lcfirst(get_called_class())." $where";		
		$db->query($sql);		
		return $db;
	}		
}