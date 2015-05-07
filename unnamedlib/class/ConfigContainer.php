<?php
class ConfigContainer {
	//для DBShell
	const DBHOST = '127.0.0.1';
	const DBPORT = 3308;
	const UNAME  = 'dblogin';
	const UPASS  = 'dbpass';
	const DBNAME = 'dbndme';
	const PREFIX = 'prefix_';

	// для Auth
	const JSON_DIR = 'users';
	const AUTH_TABLE = 'users';
	
	// для Fl0w
	const FLOW_PREFIX = 'prefix_';
	
	// для Logger
	const LOGDIR = 'logs';
	const DEBUG_LEVEL = 2;
	const LOG_SEPARATOR = "=====================================================================";
	
	// для Backbone
	const NODES_JSON = 'data/nodes.json';
	const USERS_JSON = 'data/users.json';
	
	public static function base_dir() {
		return $_SERVER['DOCUMENT_ROOT'];
	}
	
}
?>
