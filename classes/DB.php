<?php
class DB {
	public $host,$db_name,$login,$password,$result;
	protected $_mysqli;
	
	public function __construct($host,$db_name,$login,$password) {
		$this->host = $host;
		$this->db_name = $db_name;
		$this->login = $login;
		$this->password = $password;
		$this->connect();
		return $this;		
	}
	
	public function connect() {
		$this->_mysqli = new mysqli($this->host,$this->login,$this->password, $this->db_name);
		if (mysqli_connect_errno()) { 
   			printf("Подключение к серверу MySQL невозможно. Код ошибки: %s\n", mysqli_connect_error()); 
   			exit; 
		}
		$this->_mysqli->query("SET CHARACTER SET 'utf8'");		
		return $this->_mysqli;
	}
	
	public function query($sql) {				
		$this->result = $this->_mysqli->query($sql);		
		if ($this->result === false) {			
			$this->_mysqli->close();
			$this->connect();
			$this->result = $this->_mysqli->query($sql);
			if ($this->result === false) {
				print $this->_mysqli->error;
				return false;
			}
		}
		else {
			return true;
		}
	}
	
	public function getInsertedID() {
		return $this->_mysqli->insert_id;
	}
	
	public function fetchRow() {
		if ($this->result) return $this->result->fetch_assoc();
		else return false;
	}
	
	public function fetchAll() {		
		//return $this->result->fetch_all(MYSQLI_ASSOC);
		$rows = array();
		if ($this->result) {
			while ($row = $this->result->fetch_assoc()) {
				array_push($rows,$row);
			}
		}
		return $rows;
	}
	
	public function numRows() {				
		return $this->result->num_rows;
	}
	
	public function numAffectedRows() {
		return $this->_mysqli->affected_rows;
	}	
}
?>