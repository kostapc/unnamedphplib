<?php
abstract class DebugClass {
	protected $debug_level = ConfigContainer::DEBUG_LEVEL;
	
	protected function dbg($message = '', $level = 1) {
		if ($this->debug_level >= $level) {
			if (!is_string($message)) {
				echo substr(str_replace("\n","\n\t","\n".print_r($message,true)),1,-1);;
			} else {
				echo '['.date('Y-m-d_H:i:s',time()).'] '.$message."\n";
			}
		}
		return $this;
	}
	
	protected function dbg_sep() {
		return $this->dbg(ConfigContainer::LOG_SEPARATOR);
	}
}
?>
