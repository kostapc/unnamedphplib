<?
class Pagenator {
	var $dirFilesExt = '.php';
	var $dirFilesExtLen = 4; //strlen($dirFilesExt);
	var $page_files = array();
	var $admin_pages = array();
	var $initDir = 'C';
	var $menu_array = array();
	var $page_file = '';
	var $current_title = '';
	
	var $bug = '';
	
	function setAdminPages($adminPagesArray = null) {
		$this->admin_pages = $adminPagesArray;
		if ($adminPagesArray == null) {
			$this->admin_pages = array (
				'default'	=>	'Главная',
			);
		}
	}
	
	function init() {
		$uri = $_SERVER['REQUEST_URI'];
		$get_start = strpos($uri,'?');
		if($get_start>0)
			$uri = substr($uri,0,$get_start);
		$rep=opendir($this->initDir);
		if(!$rep) {
			$this->bug .=  'cannot open this dir! ): ';
		} else {
			$file_tmp = '';
			$file = '';
			$ext = null;
			while ($file = readdir($rep)){
				if($file != '..' && $file !='.' && $file !=''){ 	      
					$ext = substr($file, strlen($file)-$this->dirFilesExtLen,$this->dirFilesExtLen);
					if (is_file($this->initDir.'/'.$file) && $ext==$this->dirFilesExt && substr($file,0,1) != '_') {
						$file_tmp = substr($file, 0, strlen($file)-$this->dirFilesExtLen);
						$this->page_files[$file_tmp] = $file;
					}         
	  			}
			}
		}

		$page_uri = substr($uri, 1, strlen($uri));
		$page_uri = ($page_uri=='')?'default':$page_uri;		
		foreach ($this->page_files as $page_name => $file) {
			if(isset($this->admin_pages[$page_name])) {
				$this->current_title = $this->admin_pages[$page_name];
			} else  {
				$this->current_title = $page_name;
			}	
			if($page_uri == $page_name) {
				array_push($this->menu_array,array('class'=>'selected_menu_item','title'=>$this->current_title,'page'=>$page_name));
				$this->page_file = $_SERVER['DOCUMENT_ROOT'].'/'.$this->initDir.'/'.$file; 
			} else {
				array_push($this->menu_array,array('class'=>'regular_menu_item','title'=>$this->current_title,'page'=>$page_name));
			}
		}
	}
	
	function buildPage() {
		if(file_exists($this->page_file) && is_file($this->page_file)) {
			require ($this->page_file);
		} else {
			echo "\n<BR>".'Page file not found.. ['.$this->page_file.']';
		}
	}
	
	function  getMenuArray() {
		return $this->menu_array;
	}
}
?>