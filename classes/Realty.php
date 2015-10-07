<?php
abstract class Realty extends DataFromDb {            	

	protected $_photo_path, $_photo_webpath, $_kind;
    
	public function __construct($options = null) {    	
        parent::__construct();        
        $this->_last_id=0;
    	if (is_array($options)) {                        
            $this->create($options);            
        }
    }
    
    
	function __call($method, $args) {		
		switch ($method) {
			case 'update':				
				if (isset($args[1]) || is_array($args[1])) {					
					parent::update($args[0],$args[1]);
				}
				else call_user_method($method,$this,$args); 														
				break;			
			default:				
				call_user_func($method,$args);
				break;
		}		
	}
	
    function getKind() {
    	return $this->_kind;
    }
    
	function __set($name, $value) {
 		parent::__set($name, $value);
    }

    /** Возвращает id агенства, выбранного при подаче объявления, либо null, если не было выбрано
     * @static
     * @return int|null
     */
    public static function getSelectedByAgency(){
        if(isset($_REQUEST['by_agency']) && intval($_REQUEST['by_agency']) >= 0)
            return intval($_REQUEST['by_agency']);
        return null;
    }
    /** Проверяет - указано ли агенство во время подачи объявления, через которое нужно его подавать
     * и если указано возвращает массив полей (id, name, email, contacts) пользователя этого агенства, логинившегося последним
     * @static
     * @return array|null
     */
    protected static function getUserWhenAddByAgency(){

        $company_id = self::getSelectedByAgency();
        if(!isset($company_id)) return null;
        $user = User::getLastLoggedUserByCompany($company_id);
        if($user) return $user;
        return null;
    }
    protected static function sendEmailNotifyAddByAgency($user, $id_advertise, $feedback_contact = '')
    {
        global $config;
        $msg = file_get_contents($config['template_dir'].'email_notify_add_by_agency.tpl');
        $msg = str_replace('%USER', $user['name'], $msg);
        $msg = str_replace('%ID', $id_advertise, $msg);
        $msg = str_replace('%FEEDBACK_CONTACT', $feedback_contact, $msg);
        $msg = str_replace('%SITE', $_SERVER["HTTP_HOST"], $msg);

        mail(
            $user['email'],
            'Запрос с сайта '.$_SERVER["HTTP_HOST"],
            $msg);//,
            //"From: ".EMAIL_FROM." \r\n MIME-Version: 1.0\r\nContent-type: text/plain; charset=utf-8");
    }

    /** Процедура производит корректировку некоторых полей, если объявление добавляется через агенство
     * @static
     * @param  $options - массив значений полей
     * @param  $agent - данные найденного агента
     * @return void
     */
    protected static function correctFieldsWhenAddByAgency(&$options, $agent){
        if(!isset($agent)) return;

        // привяжем объяывление к найденому пользователю
        $options['user_id'] = $agent['id'];
        // сбросим флаг "собственник", если он установлен
        unset($options['is_owner']);
        // изменим контакты - объединим контакты найденного агента и контакты собственника
        $contacts_agent = (isset($agent['contacts']) ? 'Агент: ' . $agent['contacts'] : '');
        $contacts_owner = (isset($options['contacts']) ? 'Собственник: ' . $options['contacts'] : '');
        if(!empty($contacts_agent) && !empty($contacts_owner)) $contacts_agent .= ";\n\r";
        $options['contacts'] = $contacts_agent.$contacts_owner;
    }

	public function add(array $values, $prefix='') {
		$options = array();
		$called_class = get_called_class();		
		foreach ($called_class::$_properties as $k=>$v) {
			if (isset($values[$prefix.$k]) && $values[$prefix.$k]!=='') {
				if (isset($v['type']) && $v['type']=='int') $values[$prefix.$k] = numStrToClearStr($values[$prefix.$k]);			
				$options[$k]=$values[$prefix.$k];
			}
		}
		//$options['status'] = $values['type_deal']==1 ? REALTY_STATUS_NEW : REALTY_STATUS_RENT_NEW;		
		$options['created_on'] = date('Y-m-d H:i:s');
		$options['updated_on'] = $options['created_on'];
        $contacts_owner = isset($options['contacts']) ? $options['contacts'] : ''; // сохраним оригинальное значение контактов для включения в письмо
		if (!User::isGuest()) {
			$options['user_id'] = $_SESSION['user_id'];
		}
        else{
            // если пользователь - Гость нужно проверить - не подается ли объявление через агенство...
            $agent = self::getUserWhenAddByAgency();
            // откорректируем некоторые поля, если обнаружен пользователь, через агенство которого подается объявление
            self::correctFieldsWhenAddByAgency($options, $agent);
        }
		//print_r($options);exit;
		$this->_vals = $options;
		$id = $this->create($options);
		$this->_vals['id'] = $id;

        // если установлен пользователь, через которого подавалось объявление - отправим ему письмо с уведомлением
        if(isset($agent)) self::sendEmailNotifyAddByAgency($agent, $id, $contacts_owner);
    }
    
	public static function addStatic(array $options) {		 

        $contacts_owner = isset($options['contacts']) ? $options['contacts'] : ''; // сохраним оригинальное значение контактов для включения в письмо
        if (!User::isGuest()) {
			$options['user_id'] = $_SESSION['user_id'];
		}
        else{
            // если пользователь - Гость нужно проверить - не подается ли объявление через агенство...
            $agent = self::getUserWhenAddByAgency();
            // откорректируем некоторые поля, если обнаружен пользователь, через агенство которого подается объявление
            self::correctFieldsWhenAddByAgency($options, $agent);
        }
		$options['created_on'] = date('Y-m-d H:i:s');
		$options['updated_on'] = $options['created_on'];		
		$id = self::createStatic($options);
        // если установлен пользователь, через которого подавалось объявление - отправим ему письмо с уведомлением
        if(isset($agent)) self::sendEmailNotifyAddByAgency($agent, $id, $contacts_owner);
        return $id;
    }
           	
	public function getProperties() {
		$called_class = get_called_class();		
		return $called_class::$_properties;
	}
	
	public function getPhotoTypes() {
		$called_class = get_called_class();
		return $called_class::$_photo_types;
	}

	public function getVals() {		
		return $this->_vals;
	}
	
	public function getRealVals() {		
		return $this->_vals;
	}
		
	public function update($values,$prefix='') {		
		$id = $this->id;		
		$options = array();
		$called_class = get_called_class();			
		foreach ($called_class::$_properties as $k=>$v) {
			if (isset($values[$prefix.$k]) && $values[$prefix.$k]!=='') {				
				if ($v['type']=='int') {
					$values[$prefix.$k] = str_replace(',','.',$values[$prefix.$k]);				
					$values[$prefix.$k] = str_replace(' ','',$values[$prefix.$k]);
					$options[$k]=$values[$prefix.$k];
				}					
				else {					
					$options[$k]=htmlspecialchars($values[$prefix.$k], ENT_NOQUOTES, 'UTF-8');
				}					
			}
			elseif (isset($v['type']) && $v['type']=='bool' && !isset($values[$prefix.$k])) {
				$options[$k]=0;
			} 
		}
        // если пользователь гость и объявление подавалось через агенство - уберем is_owner
        if(User::isGuest() && $this->user_id>0 && !isset($_SESSION['admin'])) $options['is_owner'] = 0;
		parent::update($id,$options);
		$this->_vals['id'] = $id;
    }    
    
	public function getPhotoPath() {
		$path = PHOTOS_PATH.$this->_kind.'/'.$this->id.'/';								
		return $path;
	}
	
	public function getPhotoWebPath() {
		$path = PHOTOS_WEBPATH.$this->_kind.'/'.$this->id.'/';
		return $path;
	}
    
    public function addPhoto($fname,$data) {    	
    	$photo_path = $this->getPhotoPath();		
		$photo = new Photo();
		$new_name = mktime().rand(0,1000);
		//rename(PHOTOS_TMP_PATH.$fname,$photo_path.$new_name);		
		rename(PHOTOS_TMP_PATH.$fname.'_prev',$photo_path.$new_name.'_prev');
				
		$img = imagecreatefromjpeg(PHOTOS_TMP_PATH.$fname);
		//$im=Photo::createWatermark($img,WATERMARK,"arial.ttf",255,255,255,100);
		imagejpeg($img,$photo_path.$new_name);
		
		$tag = clearTextData($data['photo_type_'.$fname]);
		$title = clearTextData($data['photo_title_'.$fname]);
		$description = clearTextData($data['photo_desc_'.$fname]);
		$values = array(
			'kind_id'=>$this->_kind,
			'object_id'=>$this->id,
			'tag'=>$tag,
			'name'=>$new_name,
			'title'=>$title,
			'description'=>$description,
			'created_on'=>date("Y-m-d H:i:s"),
			'status'=>0			
		);
		$photo->create($values);		
	}
	
	public function editPhoto($fname,$data) {		
		$photo = new Photo();
		if (isset($data['photo_type_'.$fname])) {
			$tag = clearTextData($data['photo_type_'.$fname]);
			$title = clearTextData($data['photo_title_'.$fname]);
			$description = clearTextData($data['photo_desc_'.$fname]);
			$values = array(			
				'tag'=>$tag,			
				'title'=>$title,
				'description'=>$description,
				'updated_on'=>date("Y-m-d H:i:s"),
				'status'=>0			
			);
			$photo->updateBy("name='$fname'",$values);
		}
	}
	
	public function getPhotos() {		
		$photo = new Photo();		
		$photo->getBy("kind_id='".constant(strtoupper($this->_class_name))."' AND object_id={$this->id}");
		$res = $photo->fetchAll();
		if (!$res) $res = array();
		return $res;
	}
			
	public static function approve($id,$status) {
    	global $db;
    	$called_class = lcfirst(get_called_class());
    	$sql = "UPDATE $called_class SET status=".$status." 
			WHERE id=".$id;
		$db->query($sql);
		$sql = "UPDATE photo SET status=".PHOTO_STATUS_ACTIVE." 
			WHERE object_id=".$id." AND kind_id=".constant(strtoupper(get_called_class()));
		$db->query($sql);
    }
	
	public function incVisitorCount() {		
		$useragent = $_SERVER['HTTP_USER_AGENT'];  
        $notbot = "Mozilla|Opera"; //Chrome|Safari|Firefox|Netscape - все равны Mozilla  
        $bot = "Bot/|robot|Slurp/|yahoo"; //Яндекс иногда как Mozilla представляется  
        if (!(!preg_match("/$notbot/i", $useragent) || preg_match("!$bot!i", $useragent))) {
           	$this->_db->query("SELECT COUNT(ip) as amount FROM {$this->_class_name}_visitors  			  
				WHERE id=$this->id AND ip=INET_ATON('".$_SERVER["REMOTE_ADDR"]."')");
			$row = $this->_db->fetchRow();
			if ($row['amount']==0) {
				$this->_db->query("INSERT INTO {$this->_class_name}_visitors VALUES('$this->id',INET_ATON('".$_SERVER["REMOTE_ADDR"]."'),NOW())");
				$this->_db->query("UPDATE {$this->_class_name} SET counter_views=counter_views+1 WHERE id='$this->id'");
			}
        }              				
	}
	
	public static function incQuickMassVisitorCount($ids) {		
		$useragent = $_SERVER['HTTP_USER_AGENT'];  
        $notbot = "Mozilla|Opera"; //Chrome|Safari|Firefox|Netscape - все равны Mozilla  
        $bot = "Bot/|robot|Slurp/|yahoo"; //Яндекс иногда как Mozilla представляется  
        if (!(!preg_match("/$notbot/i", $useragent) || preg_match("!$bot!i", $useragent))) {
           	global $db;
           	$called_class = lcfirst(get_called_class());
           	$ids_str = implode(',',$ids);
    		$sql = "UPDATE $called_class SET quick_views=quick_views+1   
    			WHERE id IN ($ids_str)";
			$db->query($sql);           	
        }              				
	}
	
	public static function setStatus($id, $status) {
		global $db;
		$called_class = lcfirst(get_called_class());	
		$db->query("UPDATE $called_class SET `status`=$status WHERE id=$id");					
	}
	
	public static function setUserStatus($id, $user_id, $status) {
		global $db;
		$called_class = lcfirst(get_called_class());	
		$db->query("UPDATE $called_class SET `status`=$status WHERE id=$id AND user_id=$user_id");					
	}
	
	public static function setStatusMass($where, $status) {
		global $db;
		$called_class = lcfirst(get_called_class());		
		$db->query("UPDATE $called_class SET `status`=$status WHERE $where");					
	}
	
	public static function updateDate($id, $user_id) {
		global $db,$config;		
		$called_class = lcfirst(get_called_class());
		if (in_array($user_id,$config['user_workers'])) {
			$db->query("UPDATE $called_class SET `updated_on`=NOW() WHERE id=$id");
			$db->query("INSERT INTO user_updates VALUES (0,$user_id,".HOUSE.",$id,NOW())");
		}		
		else {	
			$db->query("UPDATE $called_class SET `updated_on`=NOW() WHERE id=$id AND user_id=$user_id");
		}					
	}
	
	public static function updateByCondition($where, $options) {
		global $db;
		if ($where!='') $where = 'WHERE '.$where;
		$called_class = lcfirst(get_called_class());
		foreach ($options as $k=>$v) {    		    		
			if ($v!=='') {
				$values[$k]="$k='$v'";
			}
		}
		$s = implode(',',$values);	
		$db->query("UPDATE $called_class SET $s $where");
		return $db->numAffectedRows();					
	}
	
	public static function prepareDescription($desc,$max_len=10000) {
		$desc = str_replace('.','. ',$desc);
		$desc = str_replace(',',', ',$desc);
		$desc = str_replace('-',' - ',$desc);
		$desc = str_replace('. ,','.,',$desc);
		$desc = str_replace('  ',' ',$desc);
		$desc = strip_tags(substr(trim($desc),0,$max_len),'<b><br><i><ul><ol><li><hr><table><thead><tbody><th><tr><td><strong><em><p><span>');
		return $desc;
	}
	
	public static function restoreLostObjects($status_old=0,$status_new=1) {
		global $db;					
		$sql = "UPDATE ".lcfirst(get_called_class())." set `status`=$status_new WHERE `status` = $status_old AND created_on<DATE_ADD(NOW(),INTERVAL -1 HOUR) LIMIT 10";
		$db->query($sql);			
	}


}
?>
