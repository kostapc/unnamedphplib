<?php
abstract class Rib extends DebugClass {
	private $backbone;
	
	protected function __construct() {
		$this->get_backbone();
	}
	protected function get_backbone() {
		$this->backbone = Backbone::get_instance();
	}
}
?>
