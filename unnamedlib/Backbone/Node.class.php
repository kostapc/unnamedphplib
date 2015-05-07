<?php
class Node {
	private $id = 0;
	private $par = null;
	private $path = '';
	private $meta = '';
	private $access = 0;
	private $parent_id = 0;
	private $rib = null;
	public $child_list = array();
	
	public function __construct($id,$name,$path,$rib,$parent_id) {
		$this->id = intval($id);
		$this->name = $name;
		$this->path = $path;
		$this->rib	= $rib;
		$this->parent_id = intval($parent_id);
	}
	public function __toString() {
		return $this->name.' /'.implode('/',$this->get_full_path()).' rib: '.$this->rib;
	}
	public function get_full_path(){
		$node = $this;
		$path = array();
		while (!is_null($node)) {
			$path[] = $node->get_path();
			$node = $node->get_parent();
		}
		return array_reverse($path);
	}
	public function get_name(){
		return $this->name;
	}
	public function get_path(){
		return $this->path;
	}	
	public function add_child($node) {
		$this->child_list[] = $node;
		return $this;
	}
	public function get_id() {
		return $this->id;
	}
	public function get_parent() {
		return $this->par;
	}
	public function set_parent_id($in_arr) {
		$this->parent_id = $in_arr;
		return $this;
	}
	public function get_parent_id() {
		return $this->parent_id;
	}	
	public function set_parent($in) {
		$this->par = $in;
		return $this;
	}
	public function is_root() {
		return is_null($this->par);
	}
	public function is_leaf() {
		return empty($this->child_list);
	}
	public function get_rib() {
		return $this->rib;
	}
}
?>
