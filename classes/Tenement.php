<?php
class Tenement extends Realty {
	public static $_properties = array(			
        'city_id'      => array('label'=>'city_id','min_val'=>0,'max_val'=>10000,'tag'=>'hidden', 'type'=>'int', 'name'=>'city_id', 'required'=>1),
		'street_id'    => array('label'=>'street_id','min_val'=>0,'max_val'=>100000,'tag'=>'hidden', 'type'=>'int', 'name'=>'street_id', 'required'=>1),
		'city'         => array('label'=>'Нас. пункт','min_val'=>5,'max_val'=>100,'tag'=>'text', 'type'=>'text', 'name'=>'city', 'required'=>1,'default'=>'г. Йошкар-Ола'),
        'street'       => array('label'=>'Улица','min_val'=>5,'max_val'=>255,'tag'=>'text', 'type'=>'text', 'name'=>'street', 'required'=>1),
        'number'       => array('label'=>'Номер','min_val'=>1,'max_val'=>6,'tag'=>'text', 'type'=>'text', 'name'=>'number', 'required'=>1),
        //Здесь вставлены значение $TYPE
        'type_id'      => array('label'=>'Тип дома','min_val'=>0,'max_val'=>5,'tag'=>'select', 'type'=>'int', 'name'=>'type_id','vals'=>array('Кирпичный','Панельный','Монолитный','Блочный','Деревянный'), 'required'=>1),
        'storeys'      => array('label'=>'Этажность','min_val'=>1,'max_val'=>25,'tag'=>'text', 'type'=>'int', 'name'=>'storeys', 'required'=>1),
        'porches'      => array('label'=>'Подъездов','min_val'=>1,'max_val'=>10,'tag'=>'text', 'type'=>'int', 'name'=>'porches'),
        'lifts'        => array('label'=>'Лифтов (в подъезде)','min_val'=>0,'max_val'=>5,'tag'=>'text', 'type'=>'int', 'name'=>'lifts'),
		'birthday'     => array('label'=>'Год постройки','min_val'=>1900,'max_val'=>2011,'tag'=>'text', 'type'=>'int', 'name'=>'birthday'),
		'type_roof'    => array('label'=>'Крыша','min_val'=>0,'max_val'=>9,'tag'=>'select', 'type'=>'int', 'name'=>'type_roof', 'vals'=>array('скатная','наливная','плоская')),
		'hot_water'    => array('label'=>'Гор. вода','name'=>'hot_water','tag'=>'checkbox','type'=>'bool', 'default'=>1, 'required'=>1),
		'type_heating' => array('label'=>'Отопление','min_val'=>0,'max_val'=>9,'tag'=>'select', 'type'=>'int', 'name'=>'type_heating', 'vals'=>array('центральное','поквартирное')),
		'type_energy'  => array('label'=>'Плита','min_val'=>0,'max_val'=>9,'tag'=>'select', 'type'=>'int', 'name'=>'type_energy', 'vals'=>array('газ','электро')),
		'elevators'    => array('label'=>'Грузовых лифтов','min_val'=>0,'max_val'=>5,'tag'=>'text', 'type'=>'int', 'name'=>'elevators', 'default'=>0),
		'height'       => array('label'=>'Высота потолка (м)','name'=>'height','min_val'=>1.5,'max_val'=>5,'tag'=>'text', 'type'=>'int'),	
		'lat'       => array('label'=>'Широта','min_val'=>0,'max_val'=>90,'tag'=>'hidden', 'type'=>'float', 'name'=>'lat'),
		'lon'       => array('label'=>'Долгота','min_val'=>0,'max_val'=>90,'tag'=>'hidden', 'type'=>'float', 'name'=>'lon')                	        
		);		
	public static $TYPE = array('Кирпичный','Панельный','Монолитный','Блочный','Деревянный');
	public static $_photo_types = array('Дом','Двор','Подъезд','Другое');		
	
	function __construct($options = null) {
        parent::__construct($options);		
		$this->vals = array();
		$this->_kind = TENEMENT;
	}
	
	public function add(array $values) {
		$options = array();
		foreach (self::$_properties as $k=>$v) {
			if ($k!='street' && $k!='city') $options[$k]=(isset($values[$k])) ? $values[$k] : '';
			elseif($k=='street') {
				$v = clearTextData($values[$k]);
				$db_res = Street::getListLink('name="'.$v.'" AND city_id="'.$values['city_id'].'"');
				$row = $db_res->fetchRow();
				if ($row) {					
					$options['street_id']=$row['id'];
				}
				else {
					$street_id = Street::createStatic(array('city_id'=>$values['city_id'], 'name'=>$v,'status'=>0));
					$options['street_id']=$street_id;
				}
			}			
		}
		$options['number'] = strtolower($options['number']);
		//$id = $this->create($options);
		$id = parent::addStatic($options);
		$this->_vals['id'] = $id;		
    }
    
	public function update($values,$prefix='') {
		$id = $this->id;		
		$options = array();
		foreach (self::$_properties as $k=>$v) {			
			if ($k=='street') {
				$v = clearTextData($values[$k]);				
				$db_res = Street::getListLink('name="'.$v.'" AND city_id='.intval($values['city_id']).' AND status=1');
				$row = $db_res->fetchRow();
				if ($row) {
					$options['street_id']=$row['id'];
				}
				else {
					$street_id = Street::createStatic(array('name'=>$v,'status'=>0));
					$options['street_id']=$street_id;
				}
			}
			elseif($k!='city') {
				$options[$k]=$values[$prefix.$k];
			}
		}				
		parent::update($options,'');		
    }
    
    public function getFull($id, $addon_where='') {		
		
		/*
		if ($addon_where!='') $addon_where = ' AND '.$addon_where;
		$this->_db->query("SELECT t.*, s.name AS street, d.name AS district, c.name AS city 
			FROM $this->_class_name AS t 			  
			LEFT JOIN street AS s ON t.street_id=s.id 
			LEFT JOIN district AS d ON t.district_id=d.id 
			LEFT JOIN city AS c ON t.city_id=c.id  
			WHERE t.id='$id' $addon_where");
		$this->_vals = $this->_db->fetchRow();
		*/
		$this->_vals = self::getFullStatic($id, $addon_where);
	}
	
	public static function getFullStatic($id, $addon_where='') {
		global $db;
		if ($addon_where!='') $addon_where = ' AND '.$addon_where;
		$db->query("SELECT t.*, s.name AS street, d.name AS district, c.name AS city 
			FROM tenement t  			  
			LEFT JOIN street AS s ON t.street_id=s.id 
			LEFT JOIN district AS d ON t.district_id=d.id 
			LEFT JOIN city AS c ON t.city_id=c.id  
			WHERE t.id='$id' $addon_where");
		return $db->fetchRow();
	}
	
	public function getPropertiesVal(array $options=NULL) {
		if (is_null($options)) {
			$options = $this->getRealVals();
		}
		$res = self::getPropertiesValStatic($options);		
		return $res; 				
	}

	public static function getPropertiesValStatic($options) {
		$res = array();
		foreach ($options as $name=>$val) {
			if (!in_array($name,array('street','district','city_id','number','lat','lon'))) {
				if (isset(self::$_properties[$name])) {
					if (self::$_properties[$name]['tag']!=='hidden' && $val!='') {					
						if (!isset(self::$_properties[$name]['vals'])) {														
							switch ($name) {
								case 'hot_water':
									$val = $val ? 'есть' : 'нет';
									break;
								case 'lifts':
								case 'elevators':
									$val = intval($val);								
									break;							
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
		$sql = "SELECT COUNT(t.id) as amount 
			FROM tenement AS t 			   
			LEFT JOIN street AS s ON t.street_id=s.id 
			LEFT JOIN district AS d ON t.district_id=d.id 
			LEFT JOIN city AS c ON t.city_id=c.id  
			$where";
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
		$sql = "SELECT t.*, s.name AS street, d.name AS district, c.name AS city				
			FROM tenement AS t			   
			LEFT JOIN street AS s ON t.street_id=s.id 
			LEFT JOIN district AS d ON t.district_id=d.id 
			LEFT JOIN city AS c ON t.city_id=c.id			 
			$where";
		$db->query($sql);
		return $db;
	}
	
	public static function delete($id) {
		global $db;		
		$db->query("SELECT id FROM flat WHERE tenement_id=$id");
		$rows = $db->fetchAll();		
		foreach ($rows as $row) {
			Flat::delete($row['id']);
		}
		$db->query("DELETE FROM tenement WHERE id=$id");		
		Photo::deletePhotos(TENEMENT,$id);				
	}
	
	public static function approve($id,$status) {
		global $db;
		if ($status == REALTY_STATUS_ACTIVE) {			
			$sql = "UPDATE street s, tenement t SET s.status=".REALTY_STATUS_ACTIVE." WHERE s.id=t.street_id AND t.id=$id";
			$db->query($sql);	
		}
		parent::approve($id,$status);
	}
/*
	public function getPartPropertiesVal(array $properties) {
		$res = array();		
		foreach ($properties as $realty_type => $prop_names) {								
			foreach ($prop_names as $property) {
				if (!isset(Tenement::$_properties[$property]['vals'])) {
					if ($this->_vals[$property]) $res[Tenement::$_properties[$property]['label']] = $this->_vals[$property];
				}
				else {
					if (isset(Tenement::$_properties[$property]['vals'][$this->_vals[$property]])) {
						$res[Tenement::$_properties[$property]['label']]=Tenement::$_properties[$property]['vals'][$this->_vals[$property]];
					}	
				}									
			}			
		}
		return $res;	
	}
*/	
}