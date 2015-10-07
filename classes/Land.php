<?php
class Land extends Realty {
	public static $_properties = array(
		'id'           => array('label'=>'№ объявления','name'=>'id','tag'=>'text', 'type'=>'int','on_form'=>0),			
        'city_id'      => array('label'=>'city_id','min_val'=>0,'max_val'=>10000,'tag'=>'hidden', 'type'=>'int', 'name'=>'city_id', 'required'=>1),
		'city'         => array('label'=>'Нас. пункт','min_val'=>5,'max_val'=>100,'tag'=>'text', 'type'=>'text', 'name'=>'city', 'required'=>1,'default'=>'г. Йошкар-Ола'),
	    'price'        => array('label'=>'Цена','name'=>'price','min_val'=>500,'max_val'=>100000000,'tag'=>'text', 'type'=>'int', 'unit'=>'руб.', 'required'=>1),
		'price_h'      => array('label'=>'Цена за сотку.','name'=>'price_h','min_val'=>0,'max_val'=>1000000,'tag'=>'text', 'type'=>'int', 'unit'=>'руб.', 'on_form'=>0),        	        		
		//'type_id'      => array('label'=>'Тип участка','min_val'=>0,'max_val'=>1,'tag'=>'select', 'type'=>'int', 'name'=>'type_id','vals'=>array('Индивид. стр-во','Сад/дача'), 'required'=>1),
		'area'         => array('label'=>'Площадь','name'=>'area','min_val'=>1,'max_val'=>1000,'tag'=>'text', 'type'=>'int', 'unit'=>'сот.', 'required'=>1),
        'dist'         => array('label'=>'Удаленность от г. Йошкар-Ола','name'=>'dist','tag'=>'text','type'=>'int','unit'=>'км.','on_form'=>0),
		'water'        => array('label'=>'Вода','name'=>'water','tag'=>'checkbox','type'=>'bool', 'required'=>1),
		'electricity'  => array('label'=>'Электричество','name'=>'electricity','tag'=>'checkbox','type'=>'bool', 'required'=>1),
		'gas'          => array('label'=>'Газ','name'=>'gas','tag'=>'checkbox','type'=>'bool', 'required'=>1),
		'description'  => array('label'=>'Описание','name'=>'description','min_val'=>0,'max_val'=>1000,'tag'=>'textarea', 'type'=>'text'),
        'contacts'     => array('label'=>'Контакты','name'=>'contacts','min_val'=>6,'max_val'=>255,'tag'=>'textarea', 'type'=>'text','required'=>1),
		'is_owner'     => array('label'=>'Собственник','name'=>'is_owner','tag'=>'checkbox','type'=>'bool','required'=>1,'session'=>0),	
		'lat'          => array('label'=>'Широта','min_val'=>0,'max_val'=>90,'tag'=>'hidden', 'type'=>'float', 'name'=>'lat'),
		'lon'          => array('label'=>'Долгота','min_val'=>0,'max_val'=>90,'tag'=>'hidden', 'type'=>'float', 'name'=>'lon')
		);		
	
	public static $_photo_types = array('Участок','Дорога','Населенный пункт','Другое');		
	
	function __construct($options = null) {
        parent::__construct($options);		
		$this->vals = array();
		$this->_kind = LAND;
	}
	
	public static function addStatic(array $values) {
		$options = array();		
		foreach (self::$_properties as $k=>$v) {
			if ($k!='city' && $k!='price_h') {
				if ($v['type']=='int' && isset($values[$k])) {
					$options[$k] = numStrToClearStr($values[$k]);					
				}
				else {
					$options[$k]=(isset($values[$k])) ? $values[$k] : '';
				}
			}			
		}
		$options['price_h'] = intval($options['price']/$values['area']);
		$options['description'] = self::prepareDescription($values['description']);
		$options['contacts'] = self::prepareDescription($values['contacts']);
		$options['status'] = REALTY_STATUS_NEW;	
		$id = parent::addStatic($options);
		return $id;		
    }
       
	public function getFull($id, $addon_where='') {		
		if ($addon_where!='') $addon_where = ' AND '.$addon_where;
		$sql = "SELECT h.*, c.name AS city, c.dist, p.name AS photo,
			cm.name AS company_name, cm.tariff_id, cm.domain
			FROM $this->_class_name AS h			
			LEFT JOIN photo AS p ON p.object_id=h.id AND p.kind_id=".LAND." AND p.tag=0 						 
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
        /*
         * Nik (Никулин С.) - 20.01.2013
         * изменен порядок прохода по свойствам для возможности
         * сортировки, основанной на порядке расположения элементов в _properties,
         * а не в порядке полей в результате SQL запроса
         *
         * ВНИМАНИЕ!!! в случае дальнейших изменений в логике данной функции
         * следует учесть, что при таком проходе элементы $options,
         * для которых нет соответствия в $_properties будут отброшены
         *
         * для возврата к предыдущему состоянию необходимо убрать следующие 4 строки за комметарием
         * и расскомментировать две строки (foreach и if)
         *
         * (аналогично и в House.php)
         */
        foreach(self::$_properties as $propK=>$propV){
            if(isset($options[$propK])){
                $name = $propK;
                $val = $options[$propK];

		//foreach ($options as $name=>$val) {
			    if (!in_array($name,array('district','city_id','city','lat','lon'))) {
				//if (isset(self::$_properties[$name])) {
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
		//$sql = "SELECT COUNT(f.id) amount FROM land f $where";
		
		$sql = "SELECT COUNT(am) amount  FROM 
			(SELECT COUNT(f.id) as am    
			FROM land AS f 
			LEFT JOIN city AS c ON f.city_id=c.id 
			LEFT JOIN photo AS p ON p.object_id=f.id AND p.kind_id=".LAND."  
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
			u.name user_name, a.name company_name			
			FROM land AS f 			 			 
			LEFT JOIN city AS c ON f.city_id=c.id 
			LEFT JOIN photo AS p ON p.object_id=f.id AND p.kind_id=".LAND." AND p.tag=0 
			LEFT JOIN user AS u ON u.id=f.user_id
			LEFT JOIN company AS a ON u.company_id=a.id			 
			$where";
		$db->query($sql);
		return $db;
	}
	
	public static function delete($id) {
		global $db;		
		$db->query("DELETE FROM land WHERE id=$id");		
		Photo::deletePhotos(HOUSE,$id);				
	}
	
	public static function approve($id,$status) {
		global $db;
		if ($status == REALTY_STATUS_ACTIVE) {			
			$sql = "UPDATE land l SET l.status=".REALTY_STATUS_ACTIVE." WHERE l.id=$id";
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