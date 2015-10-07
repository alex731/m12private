<?php
abstract class DataFromDb {
	protected $_db;
    protected $_class_name;    
    protected $_last_id;
    protected $_vals;	
    public function __construct() {        
        global $db;               
		$this->_db = $db;		
        $this->_class_name = lcfirst(get_class($this));        
    }   	
    
	public function __get($name) {
    	if (isset($this->_vals[$name])) {
    		return $this->_vals[$name];
    	}
    	/*
    	$trace = debug_backtrace();
        trigger_error(
            'Undefined property via __get(): ' . $name .
            ' in ' . $trace[0]['file'] .
            ' on line ' . $trace[0]['line'],
            E_USER_NOTICE);
        */
        return null;
    }
    
	public function __set($name,$value) {
    	$this->_vals[$name] = $value;    	
    }
    
	public static function checkForm(array $values, $prefix='') {
		$errors = array();
		$errors['msg'] = array();
		$errors['val'] = array();
		$called_class = get_called_class();
		foreach ($called_class::$_properties as $k=>$v) {
			$errors['msg'][$k]='';		
			if (isset($called_class::$_properties[$k]['required'])) {				
				if ($called_class::$_properties[$k]['type']=='text'){
					$len = strlen(trim($values[$prefix.$k]));
					if (isset($called_class::$_properties[$k]['min_val']) 
						&& ($len < $called_class::$_properties[$k]['min_val'])) {				
						$errors['is_error'] = 1;
						$errors['msg'][$k].='Слишком короткое значение, минимум '.$called_class::$_properties[$k]['min_val']."<br>";
					}			
					elseif (isset($called_class::$_properties[$k]['max_val'])
						&& ($len>$called_class::$_properties[$k]['max_val'])) {
						$errors['is_error'] = 1;
						$errors['msg'][$k].='Превышена максимальная длина в символах: '.$called_class::$_properties[$k]['max_val']."<br>";
					}			
					$errors['val'][$k] = clearTextData($values[$prefix.$k],$called_class::$_properties[$k]['max_val']);
				}
				elseif ($called_class::$_properties[$k]['type']=='int'){
					$values[$prefix.$k] = str_replace(' ','',$values[$prefix.$k]);
					if (isset($called_class::$_properties[$k]['min_val']) 
						&& ($values[$prefix.$k] < $called_class::$_properties[$k]['min_val'])) {				
						$errors['is_error'] = 1;
						$errors['msg'][$k].='Слишком маленькое значение, минимум '.$called_class::$_properties[$k]['min_val']."<br>";
					}
					elseif (isset($called_class::$_properties[$k]['max_val']) 
						&& ($values[$prefix.$k] > $called_class::$_properties[$k]['max_val'])) {				
						$errors['is_error'] = 1;
						$errors['msg'][$k].='Слишком большое значение, максимум '.$called_class::$_properties[$k]['max_val']."<br>";
					}
					$errors['val'][$k] = clearTextData($values[$prefix.$k]);
				}
				elseif ($called_class::$_properties[$k]['type']=='email'){
					if (!(bool)preg_match('/^[_a-z0-9-\.]+@[_a-z0-9-\.]+(\.[a-z]+)+$/i',$values[$prefix.$k])) {
						$errors['is_error'] = 1;
						$errors['msg'][$k].='Некорректный Email';
					}
				}
			}
			else {
				$errors['val'][$k] = isset($values[$prefix.$k]) ? clearTextData($values[$prefix.$k]) : '';
			}
		}
		return $errors;
	}
    
    
    public function create(array $options) {    	
		$values = array();
    	foreach ($options as $k=>$v) {    		
    		$this->_vals[$k] = $v;
			if ($v!=='') {
				$v = mysql_escape_string($v);
				$values[$k]="$k='$v'";
				//$values[$k]="$k=?";
			}
		}
		$s = implode(',',$values);				
        $sql = "INSERT INTO $this->_class_name SET $s";        
        //$this->_db->prepare($sql);
		$res = $this->_db->query($sql);
		
		$this->_last_id = $this->_db->getInsertedID();
		$this->_vals['id'] = $this->_last_id;
        return $this->_last_id;
    }

	public static function createStatic(array $options) {		
		global $db;
		$values = array();
    	foreach ($options as $k=>$v) {    		    		
			if ($v!=='') {
				$v = mysql_escape_string($v);
				$values[$k]="$k='$v'";
			}
		}
		$s = implode(',',$values);				
        $sql = "INSERT INTO ".lcfirst(get_called_class())." SET $s";
		$res = $db->query($sql);
					 
        return $db->getInsertedID();
    }
    
    public function find($id) {
    	$sql = "SELECT * FROM {$this->_class_name} WHERE id=$id";
		$res = $this->_db->query($sql);		
		$this->_vals = $this->fetchNextRow(); 
    	return $this->_vals;
    }
    
	public static function findBy($where) {
		global $db;
    	$sql = "SELECT * FROM ".lcfirst(get_called_class())." WHERE $where";
		$db->query($sql);		
		return $db->fetchRow(); 
    }
    
    public function fetchNextRow() {
    	return $this->_db->fetchRow();    	
    }
    
	public function fetchAll() {
    	return $this->_db->fetchAll();    	
    }
    
    public static function getBy($where) {
    	global $db;
    	$sql = "SELECT * FROM ".lcfirst(get_called_class())." WHERE $where";
    	$db->query($sql);
		return $db;     	
    } 
    
    public function getAmountBy($where) {
    	$sql = "SELECT COUNT(*) AS amount FROM {$this->_class_name} WHERE $where";
    	$res = $this->_db->query($sql);
    	$row = $this->fetchNextRow();
		return $row['amount']; 
    }

    public static function getAmountSql($sql) {    	
    	global $db;
    	$res = $db->query($sql);
    	$row = $db->fetchRow();    	
		return $row['amount']; 
    }
    
    
	public function query($sql) {    	
		return $res = $this->_db->query($sql);    	
    }
    
	public function numRows() {    	
		return $res = $this->_db->numRows();    	
    }
    
	public static function delete($where) {		
    	global $db;
		$sql = "DELETE FROM ".lcfirst(get_called_class())." WHERE $where";
		$res = $db->query($sql);
		return $db->numAffectedRows();
    }
    
	public function update($id,$options) {		
		$values = array();		
    	foreach ($options as $k=>$v) {    		
			if ($v!=='') {
				$v = mysql_escape_string($v);
				$values[$k]="$k='$v'";		
			}
		}
		$s = implode(',',$values);		
		$now = date('Y-m-d H:i');
    	$sql = "UPDATE {$this->_class_name} SET $s, updated_on='$now' WHERE id=$id";    	    	    	    
		$res = $this->_db->query($sql);
		
		$this->_vals = array_merge($this->_vals,$options);
		return $this->_db->numAffectedRows();
    }

    public static function updateStatic($id,$values) {
    	global $db;		
    	
    	$called_class = get_called_class();			
		foreach ($called_class::$_properties as $k=>$v) {
			if (isset($values[$k]) && $values[$k]!=='') {
				if ($v['type']=='int') {
					$values[$k] = str_replace(',','.',$values[$k]);				
					$values[$k] = str_replace(' ','',$values[$k]);
					$options[$k]=$values[$k];
				}					
				else {					
					$options[$k]=$values[$k];
					//htmlspecialchars
				}					
			}
			elseif ($v['type']=='bool' && !isset($values[$prefix.$k])) {
				$options[$k]=0;
			} 
		}	
    	
		$values = array();		
    	foreach ($options as $k=>$v) {    		
			if ($v!=='') {
				$v = mysql_escape_string($v);
				$values[$k]="$k='$v'";		
			}
		}
		$s = implode(',',$values);		
    	$sql = "UPDATE ".lcfirst(get_called_class())." SET $s WHERE id=$id";    	    	    	    
		$res = $db->query($sql);
		//exit();		
		return $db->numAffectedRows();
    }
    
    
	public function updateBy($where,array $options) {
		$values = array();
    	foreach ($options as $k=>$v) {    		
			if ($v!=='') {
				$v = mysql_escape_string($v);
				$values[$k]="$k='$v'";		
			}
		}
		$s = implode(',',$values);		
    	$sql = "UPDATE {$this->_class_name} SET $s WHERE $where";
		$res = $this->_db->query($sql);		
		if (is_array($this->_vals))
			$this->_vals = array_merge($this->_vals,$options);
		else 
			$this->_vals = $options;
		return $this->_db->numAffectedRows();
    }
    
	public static function getPhotoPathStatic($id) {
		$called_class = constant(strtoupper(get_called_class()));
		$path = PHOTOS_PATH.$called_class.'/'.$id.'/';								
		return $path;
	}
	
	public function getPhotoWebPathStatic($id) {
		$called_class = constant(strtoupper(get_called_class()));
		$path = PHOTOS_WEBPATH.$called_class.'/'.$id.'/';
		return $path;
	}
	
	
	public static function savePhoto($id,$fname,$data) {
		$called_class = get_called_class(); 	
    	$photo_path = $called_class::getPhotoPathStatic($id);		
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
			'kind_id'=>constant(strtoupper($called_class)),
			'object_id'=>$id,			
			'name'=>$new_name,
			'title'=>$title,
			'description'=>$description,
			'created_on'=>date("Y-m-d H:i:s"),
			'status'=>0	
		);
		$photo->create($values);		
	}
	
	public static function updatePhoto($id,$fname,$data) {		
		$photo = new Photo();
		if (isset($data['photo_title_'.$fname])) {			
			$title = clearTextData($data['photo_title_'.$fname]);
			$description = clearTextData($data['photo_desc_'.$fname]);
			$values = array(				
				'title'=>$title,
				'description'=>$description,
				'updated_on'=>date("Y-m-d H:i:s"),
				'status'=>0			
			);		
			$photo->updateBy("name='$fname'",$values);
		}
	}
	
	public static function saveLogo($id,$fname) {
    	$photo_path = PHOTOS_PATH.LOGO."/";					
		$img = imagecreatefromjpeg(PHOTOS_TMP_PATH.$fname);
		imagejpeg($img,$photo_path.$id);				
	}	
	
	
	public static function getPhotosStatic($id) {
		$called_class = get_called_class();		
		$photo = new Photo();		
		$photo->getBy("kind_id='".constant(strtoupper($called_class))."' AND object_id={$id}");
		$res = $photo->fetchAll();
		if (!$res) $res = array();
		return $res;
	}
}
?>