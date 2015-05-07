<?php
class DBShell extends DebugClass{

	var $link = null;
	var $last_error = 'OK';
	var $last_result = array();

	const SET_CODEPAGE = 'set names UTF8';

	public function __construct() {
		$this->dbg_sep()->dbg('DBSHELL INIT');
		$this->init_database(ConfigContainer::UNAME,ConfigContainer::UPASS,ConfigContainer::DBNAME,ConfigContainer::DBHOST);
		//$this->init_database(ConfigContainer::UNAME,ConfigContainer::UPASS,ConfigContainer::DBNAME,ConfigContainer::DBHOST . ':' . ConfigContainer::DBPORT);
	}
		
	public function __destruct() {
		mysql_close($this->link);
	}
	
	private function init_database($uname, $upass, $dbname, $dbhost) {
		/*
		 * resource mysql_connect (
		 * [ string $server = ini_get("mysql.default_host")
		 * [, string $username = ini_get("mysql.default_user")
		 * [, string $password = ini_get("mysql.default_password")
		 * [, bool $new_link = false [, int $client_flags = 0 ]]]]] )
		 */
		$this->link = mysql_connect($dbhost,$uname,$upass);
		if (!$this->link) {
			die ('cannot connect to mysql database');
		}
		mysql_select_db($dbname, $this->link);
		mysql_query(self::SET_CODEPAGE, $this->link);
	}

	public function execute_query($in_query) {
		$this->dbg('SQL: '.$in_query);
		if ($this->createFetchedArray($in_query) == true) {
			$ret = (sizeof($this->last_result)==0)?true:$this->last_result;
			$this->dbg('RESULT: ')->dbg($ret,2);
			return $ret;
		}
		return false;
	}

	public function getFirstOrNone($query) {
		$res = mysql_query($query, $this->link);
		if (!$res) {
			$this->last_error = 'Cannot first elem... ' . mysql_error();
			return null;
		}
		if (mysql_num_rows($res) == 0) {
			return null;
		} else {
			$ar = mysql_fetch_row($res);
			return $ar[0];
		}
	}

	public function getFlatArray($query) {
		$out = array();
		$res = mysql_query($query,$this->link);
		if (!$res) return null;
		if (mysql_num_rows($res) == 0) {
			return null;
		} else {
			$f = mysql_num_fields($res);
			if ($f == 1) {
				$count = 0;
				while ($row = mysql_fetch_row($res)) {
					$out[$count] = $row[0];
					$count++;
				}
				return $out;
			} else if ($f == 2) {
				while ($row = mysql_fetch_array($res)) {
					$out[$row[0]] = $row[1];
				}
				return $out;
			} else {
				return null;
			}
		}
	}

	public function escape(&$in_string) {
		return mysql_real_escape_string($in_string,$this->link);
    }

	private function safeParams(&$params_string) {
		$prm_out = array();
		foreach ($params_string as $c => $P) {
			if (substr($P, 0, 3) == 'NB_') {
				if (get_magic_quotes_gpc()) {
					$prm_out[] = stripslashes(cut_some_first_chars($P, 3));
				} else {
					$prm_out[] = cut_some_first_chars($P, 3);
				}
			} else {
				$prm_out[] = mysql_real_escape_string($P,$this->link);
			}
		}
		$params_string = implode(',', $prm_out);
		return $params_string;
	}

	public function getLastError() {
		if ($this->last_result == null) {
			$this->last_error = '[' . mysql_errno($this->link) . ']: ' . mysql_error($this->link);
		}
		return $this->last_error;
	}

	private function createFetchedArray($query) {
		$res = null;
		$res = mysql_query($query, $this->link);
		//var_dump($res);
		if (!$res || $res === null) return false;
		if ($res === true) {
			/*
			 * Returns FALSE on failure. For successful SELECT,
			 * SHOW, DESCRIBE or EXPLAIN queries mysqli_query() will return a result object.
			 * For other successful queries mysqli_query() will return TRUE.
			 */
			$this->last_result = null;
			return true;
		}
		if (mysql_num_rows($res) == 0) {
			//if(@mysql_num_rows($res) == 0) {
			$this->last_error = 'no rows returned';
			return true;
		} else {
			$this->last_result = array();
			$count = 0;
			while ($row = mysql_fetch_assoc($res)) {
				$this->last_result[$count++] = $row;
			}
		}
		mysql_free_result($res);
		unset($row);
		unset($res);
		return true;
	}
}

?>
