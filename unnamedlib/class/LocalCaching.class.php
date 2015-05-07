<?php
/*
 * Original code by http://codeigniter.com/forums/viewthread/57117/
 * Al James (C), mod by KostaPC (L)
 */

// if (!defined('BASEPATH')) exit('No direct script access allowed');

class LocalCaching  {
    const DEFAULT_GROUP_VALUE = 'auto';

	var $options = array(
                'dir' => '_cache',				//Default to BASEPATH.'cache
                'cache_postfix' => '.cache',	//Prefix to all cache filenames
                'expiry_postfix' => '.exp',		//Expiry file prefix
                'group_postfix' => '.group', 	//Group directory prefix
                'default_ttl' => ConfigContainer::CACHE_TIME  			//Default time to live = 3600 seconds (One hour).
        );

	/**
	 * 	Constructor
	 *
	 * 	@param	Options to override defaults
	 */
	public function Cache($options = NULL)	{
		if ($options != NULL) $this->options = array_merge($this->options, $options);
	}

	/**
	 * 	Check if a item has a (valid) cache
	 *
	 * 	@param	Cache id
	 * 	@param	Cache group id (optional)
	 * 	@return Boolean indicating if cache available
	 */
	public function is_cached($cache_id, $cache_group = self::DEFAULT_GROUP_VALUE) {
                $this->_check_group_id($cache_id, $cache_group);
		if ($this->_get_expiry($cache_id, $cache_group) > time()) {                    
                    return TRUE;
                }
		$this->remove($cache_id, $cache_group);
		return FALSE;
	}

	/**
	 * 	Save an item to the cache
	 *
	 * 	@param	Cache id
	 * 	@param	Data object
	 * 	@param	Cache group id (optional)
	 * 	@param	Time to live for this item
	 */
	public function save($cache_id, $data, $cache_group = self::DEFAULT_GROUP_VALUE, $ttl = NULL) {
            $this->_check_group_id($cache_id, $cache_group);
            if ($cache_group !== NULL) {
                $group_dir = $this->_group_dir($cache_group);
                //echo 'group dir: '.$group_dir;
                if (!file_exists($group_dir)) mkdir($group_dir);
            } else {
                $group_dir = $this->_group_dir($cache_group);
                if (!file_exists($group_dir)) mkdir($group_dir);
            }
            $file = $this->_file($cache_id, $cache_group);
            $cache_file = $file.$this->options['cache_postfix'];
            $expiry_file = $file.$this->options['expiry_postfix'];

            if ($ttl === NULL) $ttl = $this->options['default_ttl'];
            //
            //	Ok, so setting ttl = 0 is not quite forever, but 1000 years
            //	Is your PHP code going to be running for 1000 years? If so dont use this library (or just regenerate the cache then)!
            //
            if ($ttl == 0) $ttl = 31536000000; //1000 years in seconds

            $expire_time = time() + $ttl;

	    $f1 = fopen($expiry_file, 'w');
            $f2 = fopen($cache_file, 'w');
            if (!$f1 || !$f2) {
                return false;
            }

	    @flock($f1, LOCK_EX);
            @flock($f2, LOCK_EX);

            @fwrite($f1, $expire_time);
            @fwrite($f2, serialize($data));

            @flock($f1, LOCK_UN);
            @flock($f2, LOCK_UN);

            @fclose($f1);
            @fclose($f2);

	}

	/**
	 * 	Get and return an item from the cache
	 *
	 * 	@param	Cache Id
	 * 	@param	Cache group Id
	 * 	@param	Should I check the expiry time?
	 * 	@return The object or NULL if not available
	 */
	public function get($cache_id, $cache_group = self::DEFAULT_GROUP_VALUE, $skip_checking = FALSE) {
                $this->_check_group_id($cache_id, $cache_group);
		if (!$skip_checking && !$this->is_cached($cache_id, $cache_group)) return NULL;
		$cache_file = $this->_file($cache_id, $cache_group).$this->options['cache_postfix'];
		if (!is_file($cache_file)) return NULL;
		return unserialize(file_get_contents($cache_file));

	}

	/**
	 * 	Remove an item from the cache
	 *
	 * 	@param	Cache Id
	 * 	@param 	Cache group Id
	 */
	public function remove($cache_id, $cache_group = self::DEFAULT_GROUP_VALUE) {
            //$rmdir = ($cache_group=='auto')?true:false;
            $this->_check_group_id($cache_id, $cache_group);
            $file = $this->_file($cache_id, $cache_group);
            $cache_file = $file.$this->options['cache_postfix'];
            $expiry_file = $file.$this->options['expiry_postfix'];
            @unlink($cache_file);
            @unlink($expiry_file);
            /*$auto_cache_dir = $this->_group_dir($cache_group);
            if($rmdir && file_exists($auto_cache_dir)) {
                // remove directory if emty
                if (($files = @scandir($auto_cache_dir)) && (count($files) == 2)) {  
                    unlink($auto_cache_dir);
                    rmdir($auto_cache_dir);
                }
            // CODE NOT TESTED
            }*/
	}

	/**
	 * 	Remove an entire group
	 *
	 * 	@param	Cache group Id
	 */
	public function remove_group($cache_group) {
		$group_dir = $this->_group_dir($cache_group);
		if(!$dh = @opendir($group_dir)) return;
		while (($obj = readdir($dh))) {
		    if($obj=='.' || $obj=='..') continue;
                    @unlink($group_dir.'/'.$obj);
		}
		closedir($dh);
		@rmdir($group_dir);
	}

	/**
	 * 	Remove an array of cached items
	 *
	 * 	@param	Array of cache ids
	 * 	@param	Cache group Id
	 */
	public function remove_ids($cache_ids, $cache_group = self::DEFAULT_GROUP_VALUE) {
		if (!is_array($cache_ids)) $cache_ids = array($cache_ids);
		$hashes = array();
		foreach($cache_ids as $cache_id) {
			$hashes[] = md5($cache_id);
		}
		$group_dir = $this->_group_dir($cache_group);
		if(!$dh = @opendir($group_dir)) return;
		$filecount = 0;
		$delcount = 0;
		while (($obj = readdir($dh))) {
		    if($obj=='.' || $obj=='..') continue;
			$parts = explode(".", $obj);
			$hash = $parts[0];
                    if (in_array($hash, $hashes)) {
                        @unlink($group_dir.'/'.$obj);
                        $delcount++;
                    }
                    $filecount ++;
		}
		closedir($dh);
		if ($filecount == $delcount) @rmdir($group_dir);
	}

	//
	//	Private methods
	//

	private function _get_expiry($cache_id, $cache_group = self::DEFAULT_GROUP_VALUE) {
		$file = $this->_file($cache_id, $cache_group).$this->options['expiry_postfix'];
		if (!is_file($file)) return 0;
		return intval(file_get_contents($file));

	}

	private function _file($cache_id, $cache_group = self::DEFAULT_GROUP_VALUE) {
		return $this->_group_dir($cache_group).'/'.md5($cache_id);
	}

	private function _group_dir($cache_group) {
		//$dir = ($cache_group != NULL) ? md5($cache_group).$this->options['group_postfix'] : '';
                $dir = ($cache_group != NULL) ? $cache_group.$this->options['group_postfix'] : '';
        $path = ConfigContainer::PATH_PREFIX;
        if($path===false) {
            $path = getcwd();
        }
		return $path.'/'.$this->options['dir'].'/'.$dir;

	}
        private function _check_group_id($cache_id, &$cache_group) {
            if($cache_group == 'auto') {
                $cache_group = substr(md5($cache_id), 0, 2);
            }
        }

}
?>
