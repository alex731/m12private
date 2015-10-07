<?php
class User extends DataFromDb {
	public function __construct(array $options = null) {
        parent::__construct($options);        
	}

	public static function login($login,$pass) {
		global $db;		
		$sql = "SELECT u.*, c.name company_name, c.tariff_id, c.domain, c.type_id as company_type_id
			FROM user AS u LEFT JOIN company c ON u.company_id=c.id
			WHERE u.login='$login' AND u.pass='$pass' AND u.status='1'";
		
		$db->query($sql);	
		$row = $db->fetchRow();
						
		if ($row) self::_autorize($row);		
		return $row;		
	}
	
	private static function _autorize($row) {
		global $db;
		
		$_SESSION['user_id'] = $row['id'];		
		$_SESSION['user_name'] = $row['name'];
		$_SESSION['company_id'] = $row['company_id'];
		$_SESSION['domain'] = $row['domain'];

        // установим переменную сессии user_as_agency
        // значение 1 - если пользователь не привязан к какой-либо компании (является риелтором),
        //              либо является пользователем, привязанным к агенству недвижимости
        if(!isset($row['company_id']) ||
           (isset($row['company_type_id']) && $row['company_type_id'] == Company::COMPANY_TYPE_AGENCY))
            $_SESSION['user_as_agency'] = 1;

		if ($row['company_name']!='') $_SESSION['user_name'] .= ' ('.$row['company_name'].')'; 
		$_SESSION['user_tariff_id'] = $row['tariff_id'];
		$key = getRandomStr(ADDON_HASH).''.md5($row['id']);
		if (!isset($_COOKIE['hx'])) {
			setcookie('hx',$key);
			$hash_sql = ", hash='$key'";
		}
		else $hash_sql = '';
		$db->query("UPDATE user SET last_login=NOW() $hash_sql WHERE id=".$row['id']);		
	}
	
	public static function autorizeByHash($hash) {
		global $db;		
		$sql = "SELECT u.*, c.name company_name, c.tariff_id, c.domain    
			FROM user AS u LEFT JOIN company c ON u.company_id=c.id
			WHERE u.hash='$hash'";
		$db->query($sql);	
		$row = $db->fetchRow();
		if ($row) self::_autorize($row);
		return $row;
	}

    /** Возващает признак - пользователь работающий в данный момент с сайтом залогинился или является гостем
     * @static
     * @return bool
     */
    public static function isGuest(){
        if(!isset($_SESSION['user_id']) || $_SESSION['user_id'] <= 0)
            return true;
        return false;
    }

    /** Возвращает массив полей (id, name, email, contacts) пользователя указанной компании, логинившегося последним
     * @static
     * @param  $company_id
     * @return array
     */
    public static function getLastLoggedUserByCompany($company_id){
        global $db;
        $sql = "SELECT id, name, email, contacts
                FROM user
                WHERE company_id = $company_id AND
                    last_login = (  SELECT MAX(last_login)
                                    FROM user
                                    WHERE company_id = $company_id)";
        $db->query($sql);
        return $db->fetchRow();        
    }
}