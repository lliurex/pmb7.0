<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_sticks_sheets_ui.class.php,v 1.1.2.4 2021/03/26 10:08:35 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/sticks_sheet/sticks_sheet.class.php");

class list_sticks_sheets_ui extends list_ui {
	
	protected function _get_query_base() {
		$query = 'SELECT id_sticks_sheet FROM sticks_sheets';
		return $query;
	}
	
	protected function get_object_instance($row) {
		return new sticks_sheet($row->id_sticks_sheet);
	}
	
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		$this->available_filters = array('main_fields' => array());
		$this->available_filters['custom_fields'] = array();
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns = 
		array('main_fields' =>
			array(
					'label' => 'sticks_sheet_label',
					'page_format' => 'sticks_sheet_page_format',
					'page_orientation_label' => 'sticks_sheet_page_orientation',
			)
		);
	}
	
	/**
	 * Initialisation du tri par d�faut appliqu�
	 */
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('label');
	}
	
	/**
	 * Tri SQL
	 */
	protected function _get_query_order() {
		
	    if($this->applied_sort[0]['by']) {
			$order = '';
			$sort_by = $this->applied_sort[0]['by'];
			switch($sort_by) {
				case 'id':
					$order .= 'id_sticks_sheet';
					break;
				case 'label' :
					$order .= 'sticks_sheet_'.$sort_by;
					break;
				default :
					$order .= parent::_get_query_order();
					break;
			}
			if($order) {
				return $this->_get_query_order_sql_build($order);
			} else {
				return "";
			}
		}	
	}
		
	protected function get_button_add() {
		global $msg;
		
		return "<input type='button' class='bouton' value='".$msg['ajouter']."' onClick=\"document.location='".static::get_controller_url_base().'&action=add'."';\" />";
	}
	
	public function get_display_list() {
		$display = parent::get_display_list();
		$display .= $this->get_button_add();
		return $display;
	}
	
	protected function init_default_columns() {
		$this->add_column('label');
		$this->add_column('page_format');
		$this->add_column('page_orientation_label');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'visible', false);
		$this->set_setting_display('search_form', 'export_icons', false);
		$this->set_setting_display('query', 'human', false);
		$this->set_setting_column('label', 'align', 'left');
	}
	
	protected function get_js_sort_script_sort() {
		$display = parent::get_js_sort_script_sort();
		$display = str_replace('!!categ!!', 'sticks_sheet', $display);
		$display = str_replace('!!sub!!', '', $display);
		$display = str_replace('!!action!!', 'list', $display);
		return $display;
	}
	
	
	
	protected function get_display_cell($object, $property) {
		$attributes = array();
		$attributes['onclick'] = "window.location=\"".static::get_controller_url_base()."&action=edit&id=".$object->get_id()."\"";
		$content = $this->get_cell_content($object, $property);
		$display = $this->get_display_format_cell($content, $property, $attributes);
		return $display;
	}
}