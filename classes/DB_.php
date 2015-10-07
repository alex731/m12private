<?php
class DB {
	public $host,$db_name,$login,$password,$result;
	protected $link;
	
	public function __construct($host,$db_name,$login,$password) {
		$this->host = $host;
		$this->db_name = $db_name;
		$this->login = $login;
		$this->password = $password;
		$this->connect();
		return $this;		
	}
	
	public function connect() {
		$this->link = mysqli_connect($this->host,$this->login,$this->password) or die("Could not connect: " . mysql_error());
		$this->result = mysql_select_db($this->db_name,$this->link);
		return $this->result;
	}
	
	public function query($sql) {
		$this->result = mysql_query($sql,$this->link);
		if ($this->result === false) {
			mysql_close($this->link);
			$this->connect();
			$this->result = mysql_query($sql,$this->link);
			if ($this->result === false) {
				print mysql_error();
				return false;
			}
		}
		else {
			return true;
		}
	}
	
	public function getInsertedID() {
		return mysql_insert_id($this->link);
	}
	
	public function fetchRow() {
		
		return mysql_fetch_array($this->result,MYSQL_ASSOC);
	}
	
	public function numRows() {
				
		return mysql_num_rows($this->result);
	}
}
?>