<?php
class Flat extends Realty {
	public static $_properties = array(
		'id'            => array('label'=>'№ объявления','name'=>'id','tag'=>'text', 'type'=>'int','on_form'=>0),
		'tenement_id'   => array('name'=>'tenement_id','tag'=>'hidden', 'type'=>'int'),
        'rooms'         => array('label'=>'Комнат','name'=>'rooms','min_val'=>1,'max_val'=>5,'tag'=>'text', 'type'=>'int','required'=>1),
		'price'         => array('label'=>'Цена','name'=>'price','min_val'=>100000,'max_val'=>100000000,'tag'=>'text', 'type'=>'int', 'unit'=>'руб.', 'required'=>1),
		'price_m'       => array('label'=>'Цена за кв.м.','name'=>'price_m','min_val'=>0,'max_val'=>1000000,'tag'=>'text', 'type'=>'int', 'unit'=>'руб.', 'on_form'=>0),        	        	        
		'is_new'        => array('label'=>'Новостройка','name'=>'is_new','tag'=>'checkbox','type'=>'bool','default'=>0),//по-умолчанию вторичка
		'storey'        => array('label'=>'Этаж','name'=>'storey','min_val'=>1,'max_val'=>30,'tag'=>'text', 'type'=>'int','required'=>1),
        'total_area'    => array('label'=>'Общая площадь','name'=>'total_area','min_val'=>10,'max_val'=>300,'tag'=>'text', 'type'=>'int', 'unit'=>'м<sup>2</sup>', 'required'=>1),
        'kitchen_area'  => array('label'=>'Площадь кухни','name'=>'kitchen_area','min_val'=>0,'max_val'=>40,'tag'=>'text', 'type'=>'int', 'unit'=>'м<sup>2</sup>','required'=>1),
		'loggia'        => array('label'=>'Площадь лоджии','name'=>'loggia','min_val'=>0,'max_val'=>40,'tag'=>'text', 'type'=>'int', 'unit'=>'м<sup>2</sup>', 'default'=>0, 'required'=>1),
		'balcony'       => array('label'=>'Площадь балкона','name'=>'balcony','min_val'=>0,'max_val'=>16,'tag'=>'text', 'type'=>'int', 'unit'=>'м<sup>2</sup>', 'default'=>0, 'required'=>1),
		'is_corner'     => array('label'=>'Угловая','name'=>'is_corner','tag'=>'checkbox','type'=>'bool'),		
        'living_area'   => array('label'=>'Жилая площадь','name'=>'living_area','min_val'=>5,'max_val'=>250,'tag'=>'text', 'type'=>'int', 'unit'=>'м<sup>2</sup>'),
		'hall_area'     => array('label'=>'Площадь зала','name'=>'hall_area','min_val'=>5,'max_val'=>40,'tag'=>'text', 'type'=>'int', 'unit'=>'м<sup>2</sup>'),
		'type_bathroom' => array('label'=>'Санузел совмещен','name'=>'type_bathroom','tag'=>'checkbox','type'=>'bool'),
		'description'   => array('label'=>'Описание','name'=>'description','min_val'=>0,'max_val'=>500,'tag'=>'textarea', 'type'=>'text'),		
        'contacts'      => array('label'=>'Контакты','name'=>'contacts','min_val'=>6,'max_val'=>255,'tag'=>'textarea', 'type'=>'text','required'=>1),
		'is_owner'      => array('label'=>'Собственник','name'=>'is_owner','tag'=>'checkbox','type'=>'bool','required'=>1,'session'=>0),
		'show_address'  => array('label'=>'Показывать адрес','name'=>'show_address', 'tag'=>'checkbox','type'=>'bool', 'default'=>'1', 'on_form'=>0),
		'status'  		=> array('label'=>'Статус','name'=>'status', 'on_form'=>0),
		);
	
	protected static $_photo_types = array('Кухня','Ванная','Туалет','Зал',
			'Лоджия/балкон','Прихожая','Комната','Вид из окна','Другое');
		
	public function __construct($options = null) {		
        parent::__construct($options);        
		$this->_kind = FLAT;		
		$this->_vals = array();
	}	 	
		
	public function getAddress() {		
		$this->_db->query("SELECT f.*, t.city_id, t.street_id, t.district_id, 
			t.number AS tnum, s.name AS street, d.name AS district, c.name AS city 
			FROM $this->_class_name AS f 
			LEFT JOIN tenement AS t ON f.tenement_id=t.id   
			LEFT JOIN street AS s ON t.street_id=s.id 
			LEFT JOIN district AS d ON t.district_id=d.id 
			LEFT JOIN city AS c ON t.city_id=c.id  
			WHERE f.tenement_id='$this->tenement_id'");
		$this->_vals = $this->_db->fetchRow();
	}

	public function getFull($id, $addon_where='') {		
		if ($addon_where!='') $addon_where = ' AND '.$addon_where;
		$sql = "SELECT f.*, t.city_id, t.street_id, t.district_id, 
		t.number AS tnum, t.type_id, t.storeys, t.porches, t.birthday,
		t.type_energy,t.type_heating,t.height, t.lat, t.lon, t.status tenement_status, 
		s.name AS street, d.name AS district, c.name AS city, p.name AS tenement_photo,
		cm.name AS company_name, cm.tariff_id, cm.domain, u.company_id  
			FROM $this->_class_name AS f 
			LEFT JOIN tenement AS t ON f.tenement_id=t.id
			LEFT JOIN photo AS p ON p.object_id=t.id AND p.kind_id=".TENEMENT." 
				AND p.tag=0      
			LEFT JOIN street AS s ON t.street_id=s.id 
			LEFT JOIN district AS d ON t.district_id=d.id 
			LEFT JOIN city AS c ON t.city_id=c.id
			LEFT JOIN user u ON f.user_id=u.id
			LEFT JOIN company cm ON u.company_id=cm.id			 
			WHERE f.id='$id' $addon_where";	
		$this->_db->query($sql);		
		$this->_vals = $this->_db->fetchRow();		
	}
	
	public static function getAmountInList($where='') {
		global $db;
		//if ($where!='') $where = 'AND '.$where;
		if ($where!='') $where = ' WHERE '.$where;
		/*
		$sql = "SELECT COUNT(f.id) as amount FROM flat AS f, tenement AS t			   
			WHERE f.tenement_id=t.id  $where";
		*/
		
		$sql = "SELECT COUNT(am) amount  FROM (SELECT COUNT(f.id) as am    
			FROM flat AS f 
			LEFT JOIN tenement AS t ON f.tenement_id=t.id   
			LEFT JOIN photo AS p2 ON p2.object_id=f.id AND p2.kind_id=".FLAT."  
			$where) tbl
			";		
		$amount = self::getAmountSql($sql);
		return $amount;
	}
	
	/*
	 * Возвращает ссылку на ресурсы результатов из бд - список квартир
	 * Фото кухни
	 */
	
	public static function getAdminListLink($where='') {
		global $db;
		if ($where!='') $where = ' WHERE '.$where;
		$sql = "SELECT f.*,t.city_id, t.street_id, t.id AS tenement_id, t.district_id, 
			t.number AS tnum, t.storeys, s.name AS street, d.name AS district, c.name AS city,
			t.type_id AS ttype, t.lifts, t.status tenement_status, t.lon, t.lat, p.name AS photo_tenement,
			p2.name AS photo_flat,u.name AS user_name,a.name AS company_name 
			FROM flat AS f 
			LEFT JOIN tenement AS t ON f.tenement_id=t.id   
			LEFT JOIN street AS s ON t.street_id=s.id 
			LEFT JOIN district AS d ON t.district_id=d.id 
			LEFT JOIN city AS c ON t.city_id=c.id
			LEFT JOIN photo AS p ON p.object_id=t.id AND p.kind_id=".TENEMENT." AND p.tag=0
			LEFT JOIN photo AS p2 ON p2.object_id=f.id AND p2.kind_id=".FLAT."
			LEFT JOIN user AS u ON u.id=f.user_id
			LEFT JOIN company AS a ON u.company_id=a.id  
			$where			
			";
		$db->query($sql);
		//@file_put_contents("sql.log",$sql,FILE_APPEND);
		return $db;
	}
	
	public static function getFullListLink($where='') {
		global $db;
		if ($where!='') $where = ' WHERE '.$where;
		$sql = "SELECT f.*,t.city_id, t.street_id, t.id AS tenement_id,  
			t.number AS tnum, t.storeys, s.name AS street, c.name AS city,
			t.type_id AS ttype, t.lifts, t.status tenement_status, t.lon, t.lat, p.name AS photo_tenement,
			p2.name AS photo_flat 
			FROM flat AS f 
			LEFT JOIN tenement AS t ON f.tenement_id=t.id   
			LEFT JOIN street AS s ON t.street_id=s.id 			 
			LEFT JOIN city AS c ON t.city_id=c.id
			LEFT JOIN photo AS p ON p.object_id=t.id AND p.kind_id=".TENEMENT." 
			LEFT JOIN photo AS p2 ON p2.object_id=f.id AND p2.kind_id=".FLAT."			  
			$where			
			";
		$db->query($sql);
		return $db;
	}
		
	public function getVals() {
		if ($this->_vals['contacts']==-1) $this->_vals['contacts']='';
		return $this->_vals;
	}

	public static function delete($id) {
		global $db;
		Photo::deletePhotos(FLAT,$id);
		$db->query("DELETE FROM flat WHERE id=$id");						
	}
	
	public function add(array $values, $prefix='') {
		$values[$prefix.'price_m'] = intval(intval(numStrToClearStr($values[$prefix.'price']))/floatval(numStrToClearStr($values[$prefix.'total_area'])));
		$values[$prefix.'description'] = self::prepareDescription($values[$prefix.'description']);
		if (isset($_POST['show_address'])) $values[$prefix.'show_address'] = intval($_POST['show_address']);
		//print_r($values);
		parent::add($values,$prefix);
	}
	
	public function update($values,$prefix='') {
		$values[$prefix.'price_m'] = intval($values[$prefix.'price']/$values[$prefix.'total_area']);
		if (!isset($values[$prefix.'type_bathroom'])) $values[$prefix.'type_bathroom']=0;
        if(User::isGuest())$values[$prefix.'show_address']=1; // Заплатка - принудительно устанавливает признак отображения адреса на объяве гостя
		parent::update($values,$prefix);
	}
	
	public static function addStatic(array $values, $prefix='') {		
		$values[$prefix.'price_m'] = intval($values[$prefix.'price']/$values[$prefix.'total_area']);
		parent::addStatic($values,$prefix);
	}

	public static function changeTenement($old_id,$new_id) {
		global $db;					
		$sql = "UPDATE flat f SET tenement_id=$new_id WHERE tenement_id=$old_id";
		$db->query($sql);			
	}
	
	public function getPropertiesVal(array $values=NULL) {		
		if (is_null($values)) {
			$values = $this->getRealVals();
		}
		$res = array();
		$called_class = get_called_class();
		foreach ($called_class::$_properties as $name=>$prop) {
			$val = $values[$name];
			if (isset($prop['tag']) && $prop['tag']!=='hidden' && $val!='' && $prop['name']!='show_address') {					
				if (!isset($prop['vals'])) {					
					switch ($name) {
						case 'price':
						case 'price_m':
							$val = number_format($val,0).' руб.';
							break;
						case 'contacts':
							$val = $val == -1 ? CONTACTS : $val;
							break;							
						case 'storey':
							$val = $val."/".$values['storeys'];
							break;
						case 'total_area':
						case 'kitchen_area':
						case 'living_area':
						case 'hall_area':
						case 'loggia':
						case 'balcony':
							if ($val>0) $val = $val." м<sup>2</sup>";
							else $val = intval($val);								
							break;									
						case 'is_corner':
						case 'type_bathroom':
						case 'is_owner':
						case 'is_new':
							$val = $val ? 'да' : 'нет';
							break;								
					}		 					
					if ($val !== 0) $res[$prop['label']]=$val;
					else unset($res[$prop['label']]);															
				}
				else {
					$res[$prop['label']]=$prop['vals'][$val];
				}						
			}
		}		
		return $res; 				
	}
	
	public function getTenementInfo(array $properties) {		
		$res = array();		
		foreach ($properties as $property) {								
			if (!isset(Tenement::$_properties[$property]['vals'])) {
				if ($this->_vals[$property]) $res[Tenement::$_properties[$property]['label']] = $this->_vals[$property];					
			}
			else {
				if (isset(Tenement::$_properties[$property]['vals'][$this->_vals[$property]])) {
					$res[Tenement::$_properties[$property]['label']]=Tenement::$_properties[$property]['vals'][$this->_vals[$property]];
				}	
			}			
		}
		return $res;	
	}
}