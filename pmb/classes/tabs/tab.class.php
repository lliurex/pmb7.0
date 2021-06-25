<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: tab.class.php,v 1.1.2.3 2020/12/24 11:05:36 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

/**
 * class tab
 * Un menu
 */
class tab {
	
	protected $section;
	
	protected $label_code;
	
	protected $categ;
	
	protected $label;
	
	protected $sub;
	
	protected $url_extra;
	
	protected $number;
	
	protected $destination_link;
		
	protected $shortcut;
	
	public function __construct() {
		$this->fetch_data();
	}
	
	protected function fetch_data(){
		
	}
	
	public function get_form() {
		
	}
	
	public function set_properties_from_form() {
		
	}
	
	public function save() {
		
	}
	
	public static function delete() {
		
	}
	
	public function get_section() {
		return $this->section;
	}
	
	public function set_section($section) {
		$this->section = $section;
		return $this;
	}
	
	public function get_label_code() {
		return $this->label_code;
	}
	
	public function set_label_code($label_code) {
		$this->label_code = $label_code;
		return $this;
	}
	
	public function get_categ() {
		return $this->categ;
	}
	
	public function set_categ($categ) {
		$this->categ = $categ;
		return $this;
	}
	
	public function get_label() {
		return $this->label;
	}
	
	public function set_label($label) {
		$this->label = $label;
		return $this;
	}
	
	public function get_sub() {
		return $this->sub;
	}
	
	public function set_sub($sub) {
		$this->sub = $sub;
		return $this;
	}
	
	public function get_url_extra() {
		return $this->url_extra;
	}
	
	public function set_url_extra($url_extra) {
		$this->url_extra = $url_extra;
		return $this;
	}
	
	public function get_number() {
		return $this->number;
	}
	
	public function set_number($number) {
		$this->number = $number;
		return $this;
	}
	
	public function get_destination_link() {
		return $this->destination_link;
	}
	
	public function set_destination_link($destination_link) {
		$this->destination_link = $destination_link;
		return $this;
	}
	
	public function get_shortcut() {
		global $raclavier;
		
		if(!isset($this->shortcut)) {
			if(!empty($raclavier)) {
				foreach ($raclavier as $rac) {
					if($rac[1] == $this->destination_link) {
						$this->shortcut = $rac[0];
					}
				}
			}
		}
		return $this->shortcut;
	}
	
} // end of tab