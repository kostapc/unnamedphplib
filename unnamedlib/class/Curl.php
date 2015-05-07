<?php
Class Curl extends DebugClass{
	private $handle;
	private $headers = array();
	private $parameters = array();
	private $sleep;
	private $cookieJar;
	private $userAgent;
	private $debugLevel;
	
	public function Curl($debug = 0) {
		$this->debugLevel = $debug;
		$created = ($this->handle = curl_init());
		if (!$created) {
			die("Не удалось создать объект Curl");
		}
		// defaults
		$this->setTimeout(0)->setReturnTransfer(true)->setSleep(0);
	}

	public function setSleep($sec = 0) {
		$this->sleep = $sec;
		return $this;
	}
			
	public function setUrl($url) {
		curl_setopt($this->handle, CURLOPT_URL, $url);
		return $this;
	}
	
	public function setTimeout ($sec = 0){
		curl_setopt($this->handle, CURLOPT_CONNECTTIMEOUT, $sec);
		return $this;
	}
	
	public function setReturnTransfer($state = true) {
		curl_setopt($this->handle, CURLOPT_RETURNTRANSFER, $state);
		return $this;
	}

	public function setNoBody($state = true) {
		curl_setopt($this->handle, CURLOPT_NOBODY, $state);
		return $this;
	}

	public function setHeader($state) {
		curl_setopt($this->handle, CURLOPT_HEADER, $state);
		return $this;
	}

	public function setCustomRequest($state) {
		curl_setopt($this->handle, CURLOPT_CUSTOMREQUEST, $state);
		return $this;
	}	
	
	public function setCookieJar($cookie_jar_file) {
		curl_setopt($this->handle, CURLOPT_COOKIEJAR, $cookie_jar_file);
		return $this;
	}
	
	public function setFollowLocation($follow = true) {
		curl_setopt($this->handle, CURLOPT_FOLLOWLOCATION, $follow);
		return $this;
	}
	
	public function setUserAgent($agent = '"Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:16.0) Gecko/20100101 Firefox/6.0') {
		curl_setopt($this->handle, CURLOPT_USERAGENT, $agent);
		return $this;
	}
	
	public function reset(){
		//todo
		$this->sleep = 0;
		return $this;
	}
	
	public function exec($url = '') {
		if (!empty($url)) $this->setUrl($url);
		sleep($this->sleep);
		return curl_exec($this->handle);
	}
	
	public function grabHead($url = '') {
		if (!empty($url)) {
			$this->setUrl($url);
		}
		$this->reset()->setReturnTransfer(true)->setHeader(true)->setNoBody(true)->setCustomRequest('HEAD');
		$answer = $this->exec();
		$ret = Util::split_head($answer);
		$this->dbg("head of ".$url)->dbg($ret,2);
		return $ret;
	}
	
	public function __destruct () {
		curl_close($this->handle);
	}
}
?>
