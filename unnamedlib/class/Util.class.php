<?php
class Util {
	
	/* debug messages */
	public static function pr($in_array) {
		echo '<br><pre>';
		print_r($in_array);
		echo '</pre>';
	}
	public static function pt($in_string,$rows = 5, $cols = 20) {
		echo '<textarea rows='.$rows.' cols='.$cols.'>'.$in_string.'</textarea>';
	}
	public static function dp($str) {
		echo '<h2>' . $str . '</h2>';
	}

	/* strings */
	public static function cut_center_of_string($input, $cutLength = 35) {
		$input = str_replace('www.', '', strtolower($input));
		$len = strlen($input);
		if ($len > $cut_length) {
			$four = intval($cut_length / 10);
			if ($four == 0) $four = 3;
			return substr($input, 0, $four * 6) . '...' . substr($input, $len - ($four * 3), $len);
		}
		return $input;
	}
	public static function cut_first_char($str) {
		return substr($str, 1, strlen($str));
	}
	public static function get_first_char($str) {
		return substr($str, 0, 1);
	}
	public static function cut_some_first_chars($str, $chars) {
		return substr($str, $chars, strlen($str));
	}
	public static function cut_last_chars($str, $chars) {
		return substr($str, 0, strlen($str) - $chars);
	}
	public static function get_file_extension($fileName) {
		return strtolower(substr($fileName, strrpos($fileName, ".") + 1));
	}
	public static function get_file_name($full_file_fath) {
		return substr($full_file_path, strrpos($full_file_path, '/') + 1);
	}	
	public static function get_file_directory($file_name) {
		$arr_file_name = explode("/", $file_name);
		$filename2 = '';
		for ($i = 0; $i < (count($arr_nile_name) - 1); ++$i) {
			$filename2 .= $filename[$i] . '/';
		}
		return $filename2;
	}

	/* HTML sniplets*/
	public static function gen_img_tag($img_path, $img_file, $alt_text='cool image') {
		return '<img src="' . $img_path . '/' . $img_file . '" alt="'.$alt_text.'"/>';
	}
	
	/* filechecks */
	public static function check_file($filename,$param = 'r') {	//r,w,rw
		if (!file_exists($filename) || !is_file($filename)) 
			return false;
		$res = (strpos($param,'r')) ? is_readable($filename) : true;
		$res = (strpos($param,'w')) ? is_writable($filename) : true;
		return $res;
	}
	public static function read_json_file($filename) {
		if (self::check_file($filename) && is_readable($filename)) {
			$ret = json_decode(file_get_contents($filename),true);
			if (!is_null($ret)) {
				return $ret;
			}
		}
		return false;
	}
	public static function path($in) {
		if (is_array($in)) {
			$in = implode(DIRECTORY_SEPARATOR,$in);
		}
		return (self::get_first_char($in) != '/')
			? ConfigContainer::base_dir().DIRECTORY_SEPARATOR.$in
			: $in;
	}
}
?>
