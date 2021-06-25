<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: module.class.php,v 1.8.6.7 2021/02/09 18:06:36 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $include_path;
require_once($include_path."/templates/modules/module.tpl.php");

/**
 * class module
 * Un module
 */
class module {
	
	protected $name;
	
	protected $object_id;
	
	protected $url_base = '';
		
	protected $sub_tabs;
	
	protected static $instance;
	
	public function __construct() {
		$this->name = str_replace("module_", "", static::class);
	}
	
	public function proceed(){
		global $categ;
		global $module_layout_end;
		
		if($categ && method_exists($this, "proceed_".$categ)) {
			$method_name = "proceed_".$categ;
			$this->{$method_name}();
		} else {
			$layout_template = $this->get_layout_template();
			$layout_template = str_replace("!!menu_contextuel!!", "", $layout_template);
			print str_replace("!!menu_sous_rub!!","",$layout_template);
		}
		print $module_layout_end;
	}
	
	public function get_display_tabs() {
		$display = "<div id='menu'>";
		$list_tabs_ui_class_name = "list_tabs_".$this->name."_ui";
		$list_tabs_ui_class_name::set_module_name($this->name);
		$list_tabs_ui = new $list_tabs_ui_class_name();
		$display .= $list_tabs_ui->get_display();
		$plugins = plugins::get_instance();
		$display .= $plugins->get_menu($this->name)."
			<div id='div_alert' class='erreur'></div>
		</div>";
		return $display;
	}
	
	public function get_sub_tab($sub, $label, $url_extra='') {
		return "<span".ongletSelect(substr($this->url_base, strpos($this->url_base, '?')+1)."&sub=".$sub.$url_extra).">
			<a title='".$label."' href='".$this->url_base."&sub=".$sub.$url_extra."'>
				".$label."
			</a>
		</span>";
	}
	
	public function add_sub_tab($sub, $label, $url_extra='') {
		if(!isset($this->sub_tabs)) {
			$this->sub_tabs = array();
		}
		$this->sub_tabs[] = $this->get_sub_tab($sub, $label, $url_extra);
	}
	
	public function get_sub_tabs() {
		global $module_sub_tabs;
		
		$template = '';
		if(isset($this->sub_tabs)) {
			$sub_tabs = '';
			foreach ($this->sub_tabs as $sub_tab) {
				$sub_tabs .= $sub_tab;
			}
			$template .= str_replace('!!sub_tabs!!', $sub_tabs, $module_sub_tabs);
		}
		return $template;	
	}
	
	public function get_left_menu() {
		return $this->get_display_tabs();
	}
	
	public function get_layout_template() {
		global $module_layout;
	
		$layout_template = str_replace("!!left_menu!!", $this->get_left_menu(),$module_layout);
		return $layout_template;
	}
	
	public function get_display_subtabs() {
		global $categ, $database_window_title, $msg;
		
		if(empty($categ)) {
			return '';
		}
		$display = "";
		$list_subtabs_ui_class_name = "list_subtabs_".$this->name."_ui";
		$list_subtabs_ui_class_name::set_module_name($this->name);
		$list_subtabs_ui_class_name::set_categ($categ);
		$list_subtabs_ui = new $list_subtabs_ui_class_name();
		$display .= $list_subtabs_ui->get_display();
		if(!empty($list_subtabs_ui->get_selected_subtab())) {
			echo window_title($database_window_title.$list_subtabs_ui->get_selected_subtab()->get_label().$msg["1003"].$msg["1001"]);
		} else {
			echo window_title($database_window_title.$list_subtabs_ui->get_title().$msg["1003"].$msg["1001"]);
		}
		return $display;
	}
	
	public function set_object_id($object_id) {
		$object_id = intval($object_id);
		$this->object_id = $object_id;
	}
	
	public function set_url_base($url_base) {
		$this->url_base = $url_base;
	}
	
	protected function load_class($file){
	    global $base_path;
	    global $class_path;
	    global $include_path;
	    global $javascript_path;
	    global $styles_path;
	    global $msg,$charset;
	    global $current_module;
	    
	    if(file_exists($class_path.$file)){
	        require_once($class_path.$file);
	    }else{
	        return false;
	    }
	    return true;
	}
	
	public static function get_instance() {
		$class_name = static::class;
		if(!isset(static::$instance[$class_name])) {
			static::$instance[$class_name] = new $class_name();
		}
		return static::$instance[$class_name];
	}
} // end of module