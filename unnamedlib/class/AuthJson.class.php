<?php
class AuthJson extends Auth {
	private $type;
	
	public function __construct($single_file = true) {
		$this->type = $single_file;
	}
	
	private function get_password($login) {
		$db_password = null;
		if (!$this->is_login_correct($login)) {return false;}
		$path = ($this->type)
			? ConfigContainer::JSON_DIR.DIRECTORY_SEPARATOR.'users.json'
			: ConfigContainer::JSON_DIR.DIRECTORY_SEPARATOR.$login.'.json';
		if (!file_exists($path) || !is_file($path)) {return false;}
		
		$json = json_decode($path,true);
		
		if ($this->type) {
			foreach ($json as $user) {
				if ($user['login'] == $login) {
					return $user['password'];
				}
			}
			return false;
		} else {
			return $json['password'];
		}
	}

/*
 * type:
 * 
 * true		Пользователь хранится в json файле
 * 			ConfigContainer::JSON_DIR.DIRECTORY_SEPARATOR.'users.json'
 * 			{[
 * 				{
 * 					'login': base64(<login>),
 * 					'password': <val>
 * 				},{
 * 					'login': base64(<val>),
 * 					'password': <val>
 * 				}
 * 			]}
 * false	Пользователь хранится в json файле
 * 			ConfigContainer::JSON_DIR.DIRECTORY_SEPARATOR.<login>'.json'
 * 			{password:<val>}
 * 			логин ограничен паттерном /[A-Za-z0-9_\-]+/
 */	
}
?>
