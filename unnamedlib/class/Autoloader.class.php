<?php
class Autoloader extends DebugClass{
	private $dirs = array();
	
	public function __construct($dir) {
		$this->add_directory($dir);
	}
	private function loader($class_name) {
		$this->dbg('Trying to load '.$class_name.' via '.__METHOD__."()");
		foreach ($this->dirs as $dir) {
			if (file_exists($dir.$class_name.'.php')) {
				$this->dbg('file: '.$dir.$class_name.'.php');
				include($dir.$class_name.'.php');
				break;
			}
		}
	}
	public function add_directory($dir) {
		$this->dirs[] = $dir;
		spl_autoload_register(array($this, 'loader'));
	}
}
?>
