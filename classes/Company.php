<?php
class Company extends DataFromDb {
	public static $_properties = array(
		'id'            => array('label'=>'№ компании','name'=>'id','tag'=>'text', 'type'=>'int','on_form'=>0),
		'name'      	=> array('label'=>'Название','name'=>'name','min_val'=>6,'max_val'=>255,'tag'=>'text', 'type'=>'text','required'=>1),
		'birthday'      => array('label'=>'Год основания','min_val'=>1990,'max_val'=>2012,'tag'=>'text', 'type'=>'int', 'name'=>'birthday','on_form'=>0),
		'email'         => array('label'=>'Email','name'=>'email','min_val'=>4,'max_val'=>255,'tag'=>'text', 'type'=>'email','required'=>1),		
		'description'   => array('label'=>'Описание','name'=>'description','min_val'=>0,'max_val'=>5000,'tag'=>'textarea', 'type'=>'text','required'=>1),
		'contacts'      => array('label'=>'Контакты','name'=>'contacts','min_val'=>6,'max_val'=>255,'tag'=>'textarea', 'type'=>'text','required'=>1),
		'address'       => array('label'=>'Адрес','name'=>'address','min_val'=>6,'max_val'=>255,'tag'=>'textarea', 'type'=>'text','required'=>1),
		'lat'       	=> array('label'=>'Широта','min_val'=>0,'max_val'=>90,'tag'=>'hidden', 'type'=>'float', 'name'=>'lat'),
		'lon'       	=> array('label'=>'Долгота','min_val'=>0,'max_val'=>90,'tag'=>'hidden', 'type'=>'float', 'name'=>'lon')
	);

    /**
     * @var array - список возможных полей для фильтра (пока только одно поле - type_id), так же можно использовать page и per_page
     */
    protected static $_filter_fields = array(
        'type_id'       => array('field_name'=>'type_id', 'type'=>'int', 'simple'=>'1')
    );

    const COMPANY_FILTER_PRESET_WITH_LOGGED_USERS   = 'preset_WLU'; // Код фильтра: компании, у которых есть хотябы один пользователь, логинившийся на сайт
    const COMPANY_FILTER_PRESET_PAGINATOR           = 'preset_Paginator'; //Код фильтра: использовать разбивку на страницы
    /**
     *  Константы для использования в качестве значений поля type_id компаний - тип компании
     */
    const COMPANY_TYPE_AGENCY   = 1; // Агенства
    const COMPANY_TYPE_BUILDER  = 2; // Строительные компании
    const COMPANY_TYPE_BANK     = 3; // Банки
    const COMPANY_TYPE_REPAIRER = 4; // Ремонтные организации


	public function __construct(array $options = null) {
        parent::__construct($options);        
	}
	
		
	public static function getInfo($id) {
		global $db;		
		$sql = "SELECT c.* FROM company c WHERE c.id='$id'";
		$db->query($sql);	
		$row = $db->fetchRow();		
		return $row;
	}
	
	public static function getInfoByDomain($domain) {
		global $db;		
		$sql = "SELECT * FROM company WHERE domain='$domain'";
		$db->query($sql);	
		$row = $db->fetchRow();		
		return $row;
	}
	
	public static function getFullInfo($id) {
		global $db;		
		$sql = "SELECT *, c.contacts company_contacts, u.id user_id FROM company c LEFT JOIN user u ON c.id=u.company_id WHERE c.id='$id'";
		$db->query($sql);	
		$row = $db->fetchRow();		
		return $row;
	}

    /** Возвращает список возможных полей фильтра
     * @static
     * @param $presets = null - список или одиночная константа - пресет
     * @return список полей фильтра в качестве ключей массива, в качестве значений массива - массивы свойств полей
     */
    public static function getFilterFields($presets=null){
        $fields = self::$_filter_fields;
        if(isset($presets)){
            if(!is_array($presets)) $presets = array($presets);
            if(in_array(self::COMPANY_FILTER_PRESET_WITH_LOGGED_USERS, $presets))
                $fields[self::COMPANY_FILTER_PRESET_WITH_LOGGED_USERS] = array('type'=>'preset');
            if(in_array(self::COMPANY_FILTER_PRESET_PAGINATOR, $presets)){
                $fields['page'] = array('type'=>'int');
                $fields['per_page'] = array('type'=>'int');
                $fields[self::COMPANY_FILTER_PRESET_PAGINATOR] = array('type'=>'preset');
            }
        }
        return $fields;
    }

    /** Возвращает строку для запроса после WHERE сформированную на основе фильтра и строку LIMIT
     * @static
     * @param  $filter - список непустых полей фильтра со значениями и свойствами
     * @return array - массив:  элемент 'where' - строка для запроса (после WHERE)
     *                          элемент 'limit' - строка для запрос LIMIT
     */
    protected static function generateWhereFromFilter($filter){
        $where = '';

        $current_page = 1;
        $per_page = PER_PAGE;

        foreach($filter as $filter_name => $filter_props){
            if(!empty($filter_props['simple'])){
                if(isset($filter_props['val']) && isset($filter_props['type'])){
                    switch ($filter_props['type']){
                        case 'int':
                            $where .=  " $filter_props[field_name] = $filter_props[val] AND";
                            break;
                    }
                }
            }
            elseif($filter_name == 'page'){
                $current_page = !empty($filter_props['val']) ? $filter_props['val'] : 1;
            }
            elseif($filter_name == 'per_page'){
                $per_page = !empty($filter_props['val']) ? $filter_props['val'] : PER_PAGE;
            }
            elseif($filter_name == self::COMPANY_FILTER_PRESET_WITH_LOGGED_USERS){
                $where .= " id IN (
                        SELECT DISTINCT company_id
                        FROM user
                        WHERE company_id IS NOT NULL AND last_login IS NOT NULL)
                    AND";
            }
        }
        // разбивка на страницы будет использована только в случае указанного фильтра COMPANY_FILTER_PRESET_PAGINATOR
        return array(
                'where' => $where.' 1',
                'limit' => (isset($filter[self::COMPANY_FILTER_PRESET_PAGINATOR]) ? ' LIMIT '.($current_page-1)*$per_page.','.$per_page : '')
        );
    }

    /** Возвращает список компаний (поля id, name, domain, lon, lat, contacts, address) у которых tariff_id>1
     * @static
     * @param  $filter - список непустых полей фильтра со значениями и свойствами
     * @return array - массив двух значений: companies - список компаний,
     *                                       amount - количество найденных компаний всего (независимо от страниц)
     */
    public static function getShortListByFilter($filter, $field_names = 'id, name, domain, lon, lat, contacts, address'){
        if(empty($field_names)) $field_names = 'id';
        global $db;
        $res = array('companies' => array(),
                     'amount' => 0);
        $where_limit = self::generateWhereFromFilter($filter);
        $sql = "SELECT SQL_CALC_FOUND_ROWS $field_names
                FROM company
                WHERE tariff_id>1 AND $where_limit[where]
                ORDER BY name
                $where_limit[limit]";
        if($db->query($sql))
            $res['companies'] = $db->fetchAll();

        $res['amount'] = self::getAmountSql("SELECT FOUND_ROWS() AS amount");

        return $res;
    }

    /** Возвращает список компаний: агенств недвижимости, у которых есть хотябы один привязанный пользователь,
     *  хотя бы раз логинившийся на сайт
     * @static
     * @return array
     */
    public static function getListForAdvertiseByAgency(){
        $filter = self::getFilterFields(self::COMPANY_FILTER_PRESET_WITH_LOGGED_USERS);
        $filter['type_id']['val'] = self::COMPANY_TYPE_AGENCY;

        return self::getShortListByFilter($filter, 'id, name');
    }

    /** Формирует URL к файлу-картинке с логотипом компании
     * @static
     * @param  $company_id - id компании
     * @return string - URL к файлу-логотипу
     */
    public static function getLogoURL($company_id){
        return (is_file(PHOTOS_PATH.LOGO."/".$company_id)) ? PHOTOS_WEBPATH.LOGO.'/'.$company_id : '';
    }

    /** Формирует URL к сайту компании
     * @static
     * @param  $company_domain - домен компании
     * @return string - URL к сайту компании
     */
    public static function getSiteURL($company_domain){
        return (!empty($company_domain)) ? 'http://'.$company_domain.'.'.DOMAIN : '';
    }
}