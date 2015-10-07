<?php
class Commercial extends Realty {
	public static $_properties = array(
		'id'           => array('label'=>'№ объявления','name'=>'id','tag'=>'text', 'type'=>'int','on_form'=>0),			
        'city_id'      => array('label'=>'city_id','min_val'=>0,'max_val'=>10000,'tag'=>'hidden', 'type'=>'int', 'name'=>'city_id', 'required'=>1),
		'tenement_id'  => array('label'=>'tenement_id','min_val'=>0,'max_val'=>100000000000,'tag'=>'hidden', 'type'=>'int', 'name'=>'tenement_id', 'required'=>0),
		'street_id'    => array('label'=>'street_id','min_val'=>0,'max_val'=>10000000000000,'tag'=>'hidden', 'type'=>'int', 'name'=>'street_id', 'required'=>0),
		'city'         => array('label'=>'Нас. пункт','min_val'=>5,'max_val'=>100,'tag'=>'text', 'type'=>'text', 'name'=>'city', 'required'=>1,'default'=>'г. Йошкар-Ола'),
		'street'       => array('label'=>'Улица','min_val'=>5,'max_val'=>255,'tag'=>'text', 'type'=>'text', 'name'=>'street'),
        'number'       => array('label'=>'Номер','min_val'=>1,'max_val'=>6,'tag'=>'text', 'type'=>'text', 'name'=>'number'),
		//'address'      => array('label'=>'Адрес','name'=>'address','min_val'=>6,'max_val'=>255,'tag'=>'text', 'type'=>'text','required'=>1),
	    //Здесь вставлены значение $TYPE
        'type_id'      => array('label'=>'Тип','min_val'=>0,'max_val'=>6,'tag'=>'select', 'type'=>'int', 'name'=>'type_id','vals'=>array('Офис','Торговое','Торгово-офисное','Общепит','Склад/Производство','Земля','Бизнес'), 'required'=>1),
	    'price'        => array('label'=>'Цена','name'=>'price','min_val'=>500,'max_val'=>1000000000000,'tag'=>'text', 'type'=>'int', 'unit'=>'руб.', 'required'=>1),
		'price_m'      => array('label'=>'Цена за кв.м.','name'=>'price_m','min_val'=>0,'max_val'=>10000000000000,'tag'=>'text', 'type'=>'int', 'unit'=>'руб.', 'on_form'=>0),        	        		        
		'total_area'   => array('label'=>'Общая площадь','name'=>'total_area','min_val'=>10,'max_val'=>1000000000000,'tag'=>'text', 'type'=>'int', 'unit'=>'м<sup>2</sup>', 'required'=>1),
		'description'  => array('label'=>'Описание','name'=>'description','min_val'=>0,'max_val'=>1000,'tag'=>'textarea', 'type'=>'text'),
        'contacts'     => array('label'=>'Контакты','name'=>'contacts','min_val'=>6,'max_val'=>255,'tag'=>'textarea', 'type'=>'text','required'=>1),
		'is_owner'     => array('label'=>'Собственник','name'=>'is_owner','tag'=>'checkbox','type'=>'bool','required'=>1,'session'=>0),	
		'lat'          => array('label'=>'Широта','min_val'=>0,'max_val'=>90,'tag'=>'hidden', 'type'=>'float', 'name'=>'lat'),
		'lon'          => array('label'=>'Долгота','min_val'=>0,'max_val'=>90,'tag'=>'hidden', 'type'=>'float', 'name'=>'lon')                	        
		);		
	public static $TYPE = array('Офис','Торговое','Торгово-офисное','Общепит','Склад/Производство','Земля','Бизнес');
	public static $_photo_types = array('Внутри','Снаружи','Другое');		
	
	function __construct($options = null) {
        parent::__construct($options);		
		$this->vals = array();
		$this->_kind = COMMERCIAL;
	}
	
	public static function addStatic(array $values) {
		$options = array();		
		foreach (self::$_properties as $k=>$v) {
			if ($k!='city' && $k!='street' && $k!='number' && $k!='price_m') {
				if ($v['type']=='int') {
					$options[$k] = numStrToClearStr($values[$k]);					
				}
				else {
					$options[$k]=(isset($values[$k])) ? $values[$k] : '';
				}				
			}			
		}
		$options['number'] = self::prepareDescription(strtolower($options['number']));
		$options['price_m'] = intval($options['price']/$values['total_area']);
		$options['address'] = self::prepareDescription($values['address']);
		$options['contacts'] = self::prepareDescription($values['contacts']);
		$options['description'] = self::prepareDescription($values['description']);
		$options['status'] = ($values['type_deal']==1) ? REALTY_STATUS_NEW : REALTY_STATUS_RENT_NEW;			
		$id = parent::addStatic($options);
		return $id;		
    }
       
	public function getFull($id, $addon_where='') {		
		if ($addon_where!='') $addon_where = ' AND '.$addon_where;
		$sql = "SELECT h.*, c.name AS city, s.name AS street, t.number AS tnum,
			s2.name AS street_name, t.lat as tlat, t.lon as tlon, p.name AS photo 
			FROM $this->_class_name AS h			
			LEFT JOIN photo AS p ON p.object_id=h.id AND p.kind_id=".COMMERCIAL."  						 
			LEFT JOIN city AS c ON h.city_id=c.id
			LEFT JOIN tenement t ON h.tenement_id=t.id 
			LEFT JOIN street s ON t.street_id=s.id
			LEFT JOIN street s2 ON h.street_id=s2.id	 
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
			FROM commercial AS f 
			LEFT JOIN city AS c ON f.city_id=c.id 
			LEFT JOIN photo AS p ON p.object_id=f.id AND p.kind_id=".COMMERCIAL."  
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
		$sql = "SELECT f.*, c.name city,s.name street,s2.name street_name,
			t.number tnum, p.name photo, p2.name photo_tenement, u.name user_name,a.name company_name				
			FROM commercial AS f	 			 
			LEFT JOIN city AS c ON f.city_id=c.id
			LEFT JOIN tenement AS t ON f.tenement_id=t.id 
			LEFT JOIN street AS s ON s.id=t.street_id 
			LEFT JOIN street AS s2 ON f.street_id=s2.id			
			LEFT JOIN photo AS p ON p.object_id=f.id AND p.kind_id=".COMMERCIAL."
			LEFT JOIN photo AS p2 ON p2.object_id=t.id AND p2.kind_id=".TENEMENT." 
			LEFT JOIN user AS u ON u.id=f.user_id
			LEFT JOIN company AS a ON u.company_id=a.id			 
			$where
			";
		$db->query($sql);
		return $db;
	}
	
	public static function delete($id) {
		global $db;		
		$db->query("DELETE FROM commercial WHERE id=$id");		
		Photo::deletePhotos(COMMERCIAL,$id);				
	}
		
	public function update($values) {
		$id = $this->id;
		$options = array();
		$values['price_m'] = intval($values['price']/$values['total_area']);
		foreach (self::$_properties as $k=>$v) {			
			if($k!='city' && $k!='street' && $k!='number') {
				$options[$k]=$values[$k];
			}
			else if ($k=='number') {
				$options[$k] = clearTextData(strtolower($values[$k]));
			}
		}
		parent::update($options);		
    }	
}