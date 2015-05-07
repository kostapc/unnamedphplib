<?php 
Class Logger extends DebugClass{
	private static $instance = null;
	private $debug;
	private $logfile;
	//private $logdir;
	private $log;
	private $use_fs;
	private $ob_level;

    private function __clone() {}
    private function __wakeup() {}	
	private function __construct($file = null,$append = true) {
		$this->debug = ConfigContainer::DEBUG_LEVEL;
		$this->use_fs = true;
		$this->log = '';
		$this->ob_level = 0;
		$this->start_logging();
		
		if (is_null($file) || ($file == '')) {
			$this->use_fs = false;
		} else {
			$this->logfile = ConfigContainer::base_dir().DIRECTORY_SEPARATOR
				.ConfigContainer::LOGDIR.DIRECTORY_SEPARATOR.$file;
			$this->dbg('Logfile is: '.$this->logfile);
			if (!file_exists($this->logfile)) {
				$this->use_fs = touch($this->logfile);
			}
			if (!is_writable($this->logfile)) {
				$this->use_fs = false;
				$this->dbg('Logfile: '.$this->logfile.' is not writable');
			}
			if (!$append && $this->use_fs) {
				$this->clear_log();
			}
		}
		if (!$this->use_fs) {
			$this->dbg("-------[WARNING, LOG WILL NOT BE SAVED TO DISK]------");
		}
	}
	public static function get_instance($file = null,$append = true){
		if (is_null(self::$instance)) {
			self::$instance = new self($file = null,$append = true);
		}
		return self::$instance;
	}

	
	public function __destruct() {
		$flag = false;
		while ($this->ob_level > 0) {
			$flag = true;
			$this->dbg("emergency flush from level ".$this->ob_level);
			$this->stop_logging();
			//echo $this->log;
		}
		if ($flag) {
			header('Content-type: text/plain');
			echo $this->log;
		}
		//
	}

	public function start_logging(){
		ob_start();
		$this->dbg('Log started');
		$this->ob_level++;
		return $this;
	}
	
	public function stop_logging(){
		$this->dbg('Log stopped');
		$this->flush_log();
		ob_end_clean();
		$this->ob_level--;
		return $this->log;
	}
	
	public function flush_log(){
		$this->dbg('Log flush');
		$buffer = ob_get_contents();
		ob_clean();
		if ($this->use_fs) {
			$this->save_to_disk($buffer);
		}
		$this->log .= $buffer;
		return $this;
	}
	
	public function get_log() {
		return $this->flush_log()->log;
	}
	
	public function sep(){
		$this->dbg_sep();
		return $this;
	}
	
	private function save_to_disk ($str) {
		//return file_put_contents($this->logfile,$str,FILE_APPEND);
	}
	
	private function clear_log () {
		$this->dbg('Log: '.$this->logfile.' cleared');
		return file_put_contents($this->logfile,'');
	}
}
?>
