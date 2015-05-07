<?php
/*
 * обработка тимплейтов.
 */
class Template extends DebugClass{
	private $tplDir = '';
	private $tpl = '';
	
	public function __construct($tplDir = null) {
		$this->setTplDir($tplDir);
	}
	
	public function setTplDir ($tplDir = null) {
		$tplDir=(is_null($tplDir))
			? ConfigContainer::TPL_DIR
			: $tplDir;
		$tplDir = ConfigContainer::base_dir().DIRECTORY_SEPARATOR.$tplDir;
		
		if (!file_exists($tplDir) or !is_dir($tplDir))
			die ($tplDir.' is not directory or not exist');
		$this->tplDir = $tplDir;
		return $this;
	}
	
	public function loadTplFile($tplFileName) {
		$this->dbg('Loading template: '.$this->tplDir.DIRECTORY_SEPARATOR.$tplFileName);
		if (!file_exists($this->tplDir.DIRECTORY_SEPARATOR.$tplFileName) or
			!is_file($this->tplDir.DIRECTORY_SEPARATOR.$tplFileName)) {
				die ($this->tplDir.DIRECTORY_SEPARATOR.$tplFileName.' is not file or not exist');
		}
		$this->tpl = file_get_contents($this->tplDir.DIRECTORY_SEPARATOR.$tplFileName);
		return $this;
	}
	
	public function loadTplStr($str) {
		$this->tpl = $str;
		return $this;
	}
	
	public function getTpl() {
		return $this->tpl;
	}
	
	public function buildFormatedFlatArray($inArray) {
		return $this->buildFlatPart($inArray,$this->tpl);
	}

	public function buildFormatedCycledArray($inArray) {
		$parts = $this->getCycledParts($this->tpl);
		$pageData = $parts[1];
		$pageData .= $this->buildCycledPart($inArray,$parts[2]);
		$pageData .= $parts[3];
		return $pageData;
	}

	public function buildFormatedComplexArray($inArray) {
		/* Формат входного массива 
		 * 	'top' => плоская шапка,
		 * 	'body' => массив для Cycled,
		 * 	'bottom' => плоский подвал
		 */
		$parts = $this->getCycledParts($this->tpl);
		$pageData = $this->buildFlatPart($inArray['top'],$parts[1]);
		$pageData .= $this->buildCycledPart($inArray['body'],$parts[2]);
		$pageData .= $this->buildFlatPart($inArray['bottom'],$parts[3]);
		return $pageData;
	}
	
	private function getCycledParts($tpl) {
		$data = $tpl;
		$keywords = array();
		$status = preg_match_all("/([\d\D\n\r]*)#begin#([\d\D\n\r]*)#end#([\d\D\n\r]*)/i", $data, $keywords);
		if (count($keywords)!=4 or ($status === false)) {
			die ('incorrect cycled template');
		}
		return $keywords;
	}
	
	private function buildCycledPart($inArray,$tpl) {
		$pageData='';
		foreach ($inArray as $row) {
			$pageData .= $this->buildFlatPart($row,$tpl);
		}
		return $pageData;
	}
	
	private function buildFlatPart($inArray,$tpl) {
		$pageData=$tpl;
		preg_match_all("/\{(\w+)\}/i", $pageData, $fields);
		$counter = 0;
		foreach ($fields[0] as $key => $item) {
			$pageData = str_replace($item, $inArray[$fields[1][$counter]], $pageData);
			$counter++;
		}
		return $pageData;
	}
}
?>
