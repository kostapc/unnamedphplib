<?php
interface IStorage {
	//public function get_users();
	public function get_user_by_login($login);
	public function get_nodes();
	public function get_user_password($id);
	//public function get_node_meta($id);
	//public function get_user($id);
}
interface IUser {
	public function get_id();
	public function get_name();
	public function get_pass();
	public function change_login($login);
	public function change_pass($pass);
	public function get_access_level();
	public function load_user_by_id($id);
	public function load_user_by_login($login);
}
?>
