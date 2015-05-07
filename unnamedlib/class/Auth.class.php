<?php
class Auth {
	const SESSION_PARAM = 'checked';
	private $isValidUserFlag = false;

	private function is_login_correct($login) {
		return preg_match('/[\w\-]{2,}/',$login);
	}
	private function get_hash() {
		return strval(sha1($in_password));
	}
    // check user and set valid session
	public function check_user($in_login, $in_password, $db_password_hash) {
		unset($_SESSION[self::SESSION_PARAM]);
		if (is_null($db_password)) {
			return false;
		}
		$in_password = $this->get_hash($in_password);
		if ($in_password == $db_password) {
			$_SESSION[self::SESSION_PARAM] = true;
			$this->isValidUserFlag = true;
			return true;
		} else {
			return false;
		}
	}

	// check is session is valid
	public function validateSession () {
		$this->checkSession();
		return $this->isValidUserFlag;
	}

	private function checkSession() {
		if($_SESSION[self::SESSION_PARAM] === true) {
			$this->isValidUserFlag = true;
			return true;
		} 
		return false;
	}

	public function logout() {
		// dropping session
		$this->isValidUserFlag = false;
		$_SESSION[self::SESSION_PARAM] = false;
	}
   	/*
   	 * Абстрагируемся от хранилища, просто передаём в check_user хэш пароля извне.
   	 * 
	private function get_password($login) {
		$db_password = null;
		$table = ConfigContainer::DB_PREFIX.ConfigContainer::AUTH_TABLE;
	
		if ($this->dbShell == null || !$this->dbShell || !is_login_correct($login)) {
			return false;
		}
		$sql = 'select password from '.$table.' where `login` = '.$login.' limit 1';
		$this->dbShell->execute_query($sql);
		$db_password = $this->dbShell->last_result[0]['password'];
		if($db_password == null || empty($db_password)) {
			return false;
		}
		return $db_password;
	}
	*/
}
?>
