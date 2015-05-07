<?php
class Backbone extends DebugClass {
	private static $instance = null;
	private $storage = null;	//хранилище
	private $roots = array();	//индексы элементов без предков
	private $nodes = array();	//массив нод, дерево развёрнутое в плоскость.
	private $path = array();	//путь в виде массива полученый из строки запроса
	private $trace = array();	//массив нод соответствующих пути
	private $subpath = '';		//остаток пути которому не найдено соответствующих нод.
	private $rib = '';			//текущее "ребро"
	private $autoloader;		//автозагрузчик для рёбер
	private $tools = array();	//массив припаркованых классов
	
	private function __construct(&$storage) {
		$this->dbg_sep()->dbg('BACKBONE INIT')->dbg('Storage_type: '.get_class($storage));
		$this->storage = $storage;
		$this->load_nodes()->build_tree();
		//$this->dbg_sep()->dbg('Router init:')->dbg('$_GET :',2)->dbg($_GET,2);
		$this->build_path($_SERVER['REQUEST_URI'])->dbg($_SERVER['REQUEST_URI']);
		$this->find_trace();
		$this->rib = $this->find_rib();
		$this->dbg('rib: '.$this->rib);
		$this->autoloader = new Autoloader(ConfigContainer::base_dir().DIRECTORY_SEPARATOR.ConfigContainer::RIBS_PATH);
	}
    private function __clone() {}
    private function __wakeup() {}
	public static function get_instance(&$storage = null){
		if (is_null(self::$instance)) {
			$storage = ConfigContainer::STORAGE_TYPE;
			self::$instance = new self($storage::get_instance());
		}
		return self::$instance;
	}

	//tree
	private function load_nodes() {
		$rows = $this->storage->get_nodes();
		$this->dbg($file)->dbg($rows)->dbg('rows');
		foreach ($rows as $val) {
			$node = new Node($val['id'],$val['name'],$val['path'],$val['rib'],$val['parent']);
			$this->nodes[] = $node;
			//$this->dbg('new node: '.$node)->dbg($node,3);
		}
		$this->roots = array();
		return $this;		
	}
	private function build_tree() {
		//перебираем ноды, добавляем линк на предка и линки на потомков
		//дерево избыточно, но ходить по нему куда легче.
		$this->dbg('building tree');
		foreach ($this->nodes as $key => $val) {
			$par = $val->get_parent_id();
			//var_dump($par);
			if (($par == 0) or ($par == null)) {
				$this->nodes[$key]->set_parent(null);
				$this->roots[] = $key;
			} else {
				foreach ($this->nodes as $key2 => $val2) {
					if ($val2->get_id() == $par) {
						$this->nodes[$key]->set_parent($this->nodes[$key2]);
						$this->nodes[$key2]->add_child($this->nodes[$key]);
					}
				}
			}
			$this->dbg('node: '.$this->nodes[$key]);
		}
		return $this;
	}
	public function get_roots() {
		return $this->roots;
	}
	public function walk_tree($node,$callback,$level = 0) {
		call_user_func_array($callback,array($node,$level));
		$level++;
		foreach ($node->child_list as $val) {
			$this->walk_tree($val,$callback,$level);
		}
	}
	public function walk_full_tree() {
		foreach ($this->roots as $val) {
			$this->walk_tree($this->nodes[$val],$callback);
		}
	}
	public function get_node_by_id($id) {
		foreach ($this->nodes as $key => $val) {
			if ($val->get_id() == $id) {
				return $this->nodes[$key];
			}
		}
		return false;
	}

	//router
	private function build_path($qstr) {
		/* *
		 * Строит из query_string путь 
		 * например /my/fancy/path/?z=1&x=2
		 * станет $path = array('my','fancy','path')
		 * */
		preg_match_all('/([^\?]*)(\?.*)?/i',$qstr,$found);
		$this->dbg('found:')->dbg($found);
		$this->path = explode('/',trim($found[1][0],"/"));
		$this->dbg('path:')->dbg($this->path);
		return $this;
	}
	private function find_trace(){
		$current ='';
		
		$current_nodes = array();
		foreach ($this->roots as $root_index) {
			$current_nodes[] = $this->nodes[$root_index];
		}
		
		$par = null;
		$path_index = 0;
		$target_node = null;
		//$this->dbg($current_nodes);
		while (!empty($current_nodes) && isset($this->path[$path_index])) {
			foreach ($current_nodes as $node) {
				$this->dbg('path: '.$node->get_path());
				if ($node->get_path() == $this->path[$path_index]) {
					$par = $node;
					$current_nodes = $node->child_list;
					$path_index++;
					$this->trace[] = $node;
					continue(2);
				}
			}
			break;
		}
		
		$this->subpath = implode('/',array_slice($this->path,$path_index));
		
		$traced = '';
		foreach ($this->trace as $node) {
			$traced .= $node->get_name().'('.$node->get_path().')/';
		}
		
		$this
			->dbg('last node at: '.$path_index)->dbg($this->path)
			->dbg('subpath: '.$this->subpath)
			->dbg('trace: '.$traced);

	}
	
	//ribs
	public function load_rib() {
		//TODO
	}
	private function check_rib() {
		//TODO
	}
	private function find_rib () {
		$z = count($this->trace);
		$this->dbg('count: '.count($this->trace));
		for ($i=$z-1;$i>=0;$i--) {
			$rib = $this->trace[$i]->get_rib();
			$this->dbg('rib: '.$rib);
			if (!is_null($rib)) {
				return $rib;
			}
		}
		if ($z == 0) {
			if ($this->subpath == '') {
				return ConfigContainer::INDEX_RIB;
			} else {
				// тут чпу
				//return $this->storage->get_node_by_alias($this->subpath)
			}
		}
		return ConfigContainer::DEFAULT_RIB;
	}
	public function get_rib() {
		return $this->rib;
	}
	
	//tools
	public function add_tool($name,&$handle) {
		$this->tools[$name] = $handle;
		$this->dbg('new tool '.$name,2)->dbg($handle,2);
		return $this;
	}
	public function get_tool($name) {
		return $this->tools[$name];
	}
}
?>
