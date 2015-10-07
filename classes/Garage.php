<?php
class Garage extends Realty {
	public static $_properties = array(
		'id'           => array('label'=>'№ объявления','name'=>'id','tag'=>'text', 'type'=>'int','on_form'=>0),			
        'city_id'      => array('label'=>'city_id','min_val'=>0,'max_val'=>10000,'tag'=>'hidden', 'type'=>'int', 'name'=>'city_id', 'required'=>1),
		'garage_id'    => array('label'=>'garage_id','min_val'=>0,'max_val'=>10000,'tag'=>'hidden', 'type'=>'int', 'name'=>'garage_id'),
		'city'         => array('label'=>'Нас. пункт','min_val'=>5,'max_val'=>100,'tag'=>'text', 'type'=>'text', 'name'=>'city', 'required'=>1,'default'=>'г. Йошкар-Ола'),
	    'price'        => array('label'=>'Цена','name'=>'price','min_val'=>500,'max_val'=>100000000,'tag'=>'text', 'type'=>'int', 'unit'=>'руб.', 'required'=>1),		        	        		
		'area'         => array('label'=>'Площадь','name'=>'area','min_val'=>1,'max_val'=>1000,'tag'=>'text', 'type'=>'int', 'unit'=>'м<sup>2</sup>'),		
		'electricity'  => array('label'=>'Электричество','name'=>'electricity','tag'=>'checkbox','type'=>'bool'),
		'description'  => array('label'=>'Описание','name'=>'description','min_val'=>0,'max_val'=>1000,'tag'=>'textarea', 'type'=>'text'),
        'contacts'     => array('label'=>'Контакты','name'=>'contacts','min_val'=>6,'max_val'=>255,'tag'=>'textarea', 'type'=>'text','required'=>1),
		'lat'          => array('label'=>'Широта','min_val'=>0,'max_val'=>90,'tag'=>'hidden', 'type'=>'float', 'name'=>'lat'),
		'lon'          => array('label'=>'Долгота','min_val'=>0,'max_val'=>90,'tag'=>'hidden', 'type'=>'float', 'name'=>'lon')                	        
		);		
	
	function __construct($options = null) {
        parent::__construct($options);		
		$this->vals = array();
		$this->_kind = GARAGE;
	}
	
	public static function addStatic(array $values) {
		$options = array();		
		foreach (self::$_properties as $k=>$v) {			
			if ($v['type']=='int') {
				$options[$k] = numStrToClearStr($values[$k]);					
			}
			else {
				$options[$k]=(isset($values[$k])) ? $values[$k] : '';
			}						
		}		
		$options['description'] = self::prepareDescription($values['description']);	
		$id = parent::addStatic($options);
		return $id;		
    }
       
	public function getFull($id, $addon_where='') {		
		if ($addon_where!='') $addon_where = ' AND '.$addon_where;
		$sql = "SELECT h.*, c.name AS city, p.name AS photo 
			FROM $this->_class_name AS h			
			LEFT JOIN photo AS p ON p.object_id=h.id AND p.kind_id=".GARAGE." AND p.tag=0 						 
			LEFT JOIN city AS c ON h.city_id=c.id			 
			WHERE h.id='$id' $addon_where";	
		$this->_db->query($sql);		
		$this->_vals = $this->_db->fetchRow();		
	}
	
	public function getPropertiesVal() {		
		$options = $this->getRealVals();
		$res = self::getPropertiesValStatic($options);		
		return $res; 				
	}

	public static function getPropertiesValStatic($options) {
		$res = array();
		foreach ($options as $name=>$val) {
			if (!in_array($name,array('district','city_id','city','lat','lon'))) {
				if (isset(self::$_properties[$name])) {
					if (self::$_properties[$name]['tag']!=='hidden' && $val!='') {					
						if (!isset(self::$_properties[$name]['vals'])) {														
							switch ($name) {
								case 'water':
								case 'gas':
								case 'electricity':
									$val = $val ? 'есть' : 'нет';
									break;
								case 'price':
								case 'price_h':
									$val = number_format($val,0);
									break;								
							}
							if (isset(self::$_properties[$name]['unit'])) {
								$val = $val." ".self::$_properties[$name]['unit'];
							}						
							if ($val !== 0) $res[self::$_properties[$name]['label']]=$val;
							else unset($res[self::$_properties[$name]['label']]);							
						}
						else {
							$res[self::$_properties[$name]['label']]=self::$_properties[$name]['vals'][$val];	
						}						
					}
				}
			}
		}
		return $res; 				
	}
	
	public static function getAmountInList($where='') {
		global $db;
		if ($where!='') $where = ' WHERE '.$where;
		$sql = "SELECT COUNT(f.id) amount FROM garage f $where";
		$amount = self::getAmountSql($sql);
		return $amount;
	}
	
	/*
	 * Возвращает ссылку на ресурсы результатов из бд - список домов
	 * Фото кухни
	 */
	public static function getFullListLink($where='') {
		global $db;
		if ($where!='') $where = ' WHERE '.$where;
		$sql = "SELECT f.*, c.name city, c.dist, p.name photo				
			FROM garage AS f 			 			 
			LEFT JOIN city AS c ON f.city_id=c.id 
			LEFT JOIN photo AS p ON p.object_id=f.id AND p.kind_id=".GARAGE." AND p.tag=0			 
			$where";
		$db->query($sql);
		return $db;
	}
	
	public static function delete($id) {
		global $db;		
		$db->query("DELETE FROM garage WHERE id=$id");		
		Photo::deletePhotos(HOUSE,$id);				
	}
	
	public static function approve($id,$status) {
		global $db;
		if ($status == REALTY_STATUS_ACTIVE) {			
			$sql = "UPDATE garage l SET l.status=".REALTY_STATUS_ACTIVE." WHERE l.id=$id";
			$db->query($sql);	
		}
		parent::approve($id,$status);
	}
	
	public function update($values) {
		$id = $this->id;
		$options = array();
		$values['price_h'] = intval($values['price']/$values['area']);
		foreach (self::$_properties as $k=>$v) {			
			if($k!='city') {
				$options[$k]=$values[$k];
			}
		}
		parent::update($options);		
    }	
}