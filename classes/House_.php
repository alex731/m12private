<?php
class House extends Realty {
	public static $_properties = array(
		'id'           => array('label'=>'№ объявления','name'=>'id','tag'=>'text', 'type'=>'int','on_form'=>0),			
        'city_id'      => array('label'=>'city_id','min_val'=>0,'max_val'=>10000,'tag'=>'hidden', 'type'=>'int', 'name'=>'city_id', 'required'=>1),
		'city'         => array('label'=>'Нас. пункт','min_val'=>5,'max_val'=>100,'tag'=>'text', 'type'=>'text', 'name'=>'city', 'required'=>1,'default'=>'г. Йошкар-Ола'),
	    //Здесь вставлены значение $TYPE
        'type_id'      => array('label'=>'Тип дома','min_val'=>0,'max_val'=>5,'tag'=>'select', 'type'=>'int', 'name'=>'type_id','vals'=>array('Деревянный','Кирпичный','Монолитный','Блочный'), 'required'=>1),
	    'price'        => array('label'=>'Цена','name'=>'price','min_val'=>500,'max_val'=>100000000,'tag'=>'text', 'type'=>'int', 'unit'=>'руб.', 'required'=>1),
		'price_m'      => array('label'=>'Цена за кв.м.','name'=>'price_m','min_val'=>0,'max_val'=>1000000,'tag'=>'text', 'type'=>'int', 'unit'=>'руб.', 'on_form'=>0),        	        		
        'storeys'      => array('label'=>'Этажность','min_val'=>1,'max_val'=>25,'tag'=>'text', 'type'=>'int', 'name'=>'storeys', 'required'=>1),
		'total_area'   => array('label'=>'Общая площадь','name'=>'total_area','min_val'=>10,'max_val'=>10000,'tag'=>'text', 'type'=>'int', 'unit'=>'м<sup>2</sup>', 'required'=>1),
		'land_area'    => array('label'=>'Площадь зем. уч.','name'=>'land_area','min_val'=>0,'max_val'=>1000,'tag'=>'text', 'type'=>'int', 'unit'=>'сот.', 'required'=>1),
		'water'        => array('label'=>'Вода','name'=>'water','tag'=>'checkbox','type'=>'bool', 'required'=>1),
		'electricity'  => array('label'=>'Электричество','name'=>'electricity','tag'=>'checkbox','type'=>'bool', 'required'=>1),
		'gas'          => array('label'=>'Газ','name'=>'gas','tag'=>'checkbox','type'=>'bool', 'required'=>1),
		'birthday'     => array('label'=>'Год постройки','min_val'=>1900,'max_val'=>2011,'tag'=>'text', 'type'=>'int', 'name'=>'birthday'),
		'living_area'  => array('label'=>'Жилая площадь','name'=>'living_area','min_val'=>5,'max_val'=>250,'tag'=>'text', 'type'=>'int', 'unit'=>'м<sup>2</sup>'),
		'height'       => array('label'=>'Высота потолка','name'=>'height','min_val'=>1.5,'max_val'=>5,'tag'=>'text', 'type'=>'int','unit'=>'м'),
		'description'  => array('label'=>'Описание','name'=>'description','min_val'=>0,'max_val'=>1000,'tag'=>'textarea', 'type'=>'text'),
        'contacts'     => array('label'=>'Контакты','name'=>'contacts','min_val'=>6,'max_val'=>255,'tag'=>'textarea', 'type'=>'text','required'=>1),
		'is_owner'     => array('label'=>'Собственник','name'=>'is_owner','tag'=>'checkbox','type'=>'bool','required'=>1,'session'=>0),	
		'lat'          => array('label'=>'Широта','min_val'=>0,'max_val'=>90,'tag'=>'hidden', 'type'=>'float', 'name'=>'lat'),
		'lon'          => array('label'=>'Долгота','min_val'=>0,'max_val'=>90,'tag'=>'hidden', 'type'=>'float', 'name'=>'lon')                	        
		);		
	public static $TYPE = array('Деревянный','Кирпичный','Монолитный','Блочный');
	public static $_photo_types = array('Дом','Двор','Подъезд','Другое');		
	
	function __construct($options = null) {
        parent::__construct($options);		
		$this->vals = array();
		$this->_kind = HOUSE;
	}
	
	public static function addStatic(array $values) {
		$options = array();		
		foreach (self::$_properties as $k=>$v) {
			if ($k!='street' && $k!='city' && $k!='price_m') {
				if ($v['type']=='int') {
					$options[$k] = numStrToClearStr($values[$k]);					
				}
				else {
					$options[$k]=(isset($values[$k])) ? $values[$k] : '';
				}
			}
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
		$options['price_m'] = intval($options['price']/$values['total_area']);
		$options['description'] = self::prepareDescription($values['description']);
		$options['contacts'] = self::prepareDescription($values['contacts']);
		$options['status'] = REALTY_STATUS_NEW;			
		$id = parent::addStatic($options);
		return $id;		
    }
       
	public function getFull($id, $addon_where='') {		
		if ($addon_where!='') $addon_where = ' AND '.$addon_where;
		$sql = "SELECT h.*, s.name AS street, c.name AS city, p.name AS photo,
			cm.name AS company_name, cm.tariff_id, cm.domain 
			FROM $this->_class_name AS h			
			LEFT JOIN photo AS p ON p.object_id=h.id AND p.kind_id=".HOUSE." AND p.tag=0      
			LEFT JOIN street AS s ON h.street_id=s.id			 
			LEFT JOIN city AS c ON h.city_id=c.id 
			LEFT JOIN user u ON h.user_id=u.id
			LEFT JOIN company cm ON u.company_id=cm.id 	 
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
			if (!in_array($name,array('street','district','city_id','city','number','lat','lon'))) {
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
								case 'price_m':
									$val = number_format($val,0);
									break;
								case 'is_owner':
									$val = $val ? 'да' : 'нет';
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
		/*
		$sql = "SELECT COUNT(f.id) amount FROM house f $where";		
		$amount = self::getAmountSql($sql);
		return $amount;
		*/
		
		$sql = "SELECT COUNT(am) amount  FROM 
			(SELECT COUNT(f.id) as am    
			FROM house AS f 
			LEFT JOIN city AS c ON f.city_id=c.id 
			LEFT JOIN photo AS p ON p.object_id=f.id AND p.kind_id=".HOUSE."  
			$where) tbl";		
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
		$sql = "SELECT f.*, c.name city, c.dist, p.name photo, 
			u.name user_name,a.name company_name				
			FROM house AS f			 			 
			LEFT JOIN city AS c ON f.city_id=c.id 
			LEFT JOIN photo AS p ON p.object_id=f.id AND p.kind_id=".HOUSE." AND p.tag=0
			LEFT JOIN user AS u ON u.id=f.user_id
			LEFT JOIN company AS a ON u.company_id=a.id			 
			$where";
		$db->query($sql);
		return $db;
	}
	
	public static function delete($id) {
		global $db;		
		$db->query("DELETE FROM house WHERE id=$id");		
		Photo::deletePhotos(HOUSE,$id);				
	}
	
	public static function approve($id,$status) {
		global $db;
		if ($status == REALTY_STATUS_ACTIVE) {			
			$sql = "UPDATE street s, house t SET s.status=".REALTY_STATUS_ACTIVE." WHERE s.id=t.street_id AND t.id=$id";
			$db->query($sql);	
		}
		parent::approve($id,$status);
	}
	
	public function update($values) {
		$id = $this->id;
		$options = array();
		$values['price_m'] = intval($values['price']/$values['total_area']);
		foreach (self::$_properties as $k=>$v) {			
			if ($k=='street') {
				$v = clearTextData($values[$k]);				
				$db_res = Street::getListLink('name="'.$v.'" AND city_id='.intval($values['city_id']).' AND status='.REALTY_STATUS_ACTIVE);
				$row = $db_res->fetchRow();
				if ($row) {
					$options['street_id']=$row['id'];
				}
				else {
					$street_id = Street::create(array('name'=>$v,'status'=>0));
					$options['street_id']=$street_id;
				}
			}
			elseif($k!='city') {
				$options[$k]=$values[$k];
			}
		}
		parent::update($options);		
    }	
}