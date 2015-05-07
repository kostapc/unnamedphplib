<?php
abstract class User extends DebugClass implements IUser {
	protected $id;
	protected $login;
	protected $name;
	protected $pass;
	protected $access_level;
	protected $storage;
	protected $meta;
	
	public function get_id(){
		return $this->id;
	}
	public function get_name(){
		return $this->name;
	}
	public function get_pass(){
		return $this->pass;
	}
	public function change_login($in_arr){
		//$in_arr = array('name','pass','access')
		return $this;
	}
	public function change_pass($in_arr){
		return $this;
	}
	public function get_access_level(){
		return $this->access_level;
	}
	public function load_user_by_id($id) {
		$user = $storage->get_user_by_id($id);
		$this->load_from_array($user);
		return $this;
	}
	public function load_user_by_login($login) {
		$user = $this->storage->get_user_by_login($login);
		$this->load_from_array($user);
		return $this;		
	}
	protected function load_from_array($in_arr) {
		foreach ($in_arr as $key => $val) {
			if (property_exists($this, $key)) {
				$this->$key = $val;
			} else {
				$this->dbg('no property for data: '.$key.' = '.$val);
				$this->meta[$key] = $val;
			}
		}
		return $this;
	}
}
?>
