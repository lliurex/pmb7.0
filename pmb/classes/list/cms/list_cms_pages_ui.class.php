<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_cms_pages_ui.class.php,v 1.1.2.2 2021/04/07 13:57:06 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/cms/cms_pages.class.php");

class list_cms_pages_ui extends list_ui {
	
	protected function _get_query_base() {
		return 'SELECT * FROM cms_pages';
	}
	
	protected function get_object_instance($row) {
		return new cms_page($row->id_page);
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('name');
	}
	
	protected function get_js_sort_script_sort() {
		$display = parent::get_js_sort_script_sort();
		$display = str_replace( '!!categ!!', 'pages', $display);
		$display = str_replace( '!!sub!!', '', $display);
		$display = str_replace( '!!action!!', 'list', $display);
		return $display;
	}
	
	protected function init_available_columns() {
		$this->available_columns = array (
				'main_fields' => array (
						'name' => 'infopage_title_infopage'
				)
		);
	}
	
	protected function init_default_columns() {
		$this->add_column('name');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'visible', false);
		$this->set_setting_display('search_form', 'export_icons', false);
		$this->set_setting_display('query', 'human', false);
		$this->set_setting_column('default', 'align', 'left');
	}
	
	protected function get_button_add() {
		global $msg, $charset;
		
		return "<input type='button' class='bouton' name='cms_page_add' value='".htmlentities($msg["cms_new_page_button"], ENT_QUOTES, $charset)."' onclick=\"document.location='".static::get_controller_url_base()."&sub=edit'\" />";
	}
	
	public function get_display_list() {
		$display = parent::get_display_list();
		$display .= $this->get_button_add();
		return $display;
	}
	
	protected function get_display_cell($object, $property) {
		$attributes = array();
		$attributes['onclick'] = "window.location=\"".static::get_controller_url_base()."&sub=edit&id=".$object->get_id()."\"";
		$content = $this->get_cell_content($object, $property);
		$display = $this->get_display_format_cell($content, $property, $attributes);
		return $display;
	}
	
	public static function get_controller_url_base() {
		global $base_path;
		return $base_path.'/cms.php?categ=pages';
	}
}