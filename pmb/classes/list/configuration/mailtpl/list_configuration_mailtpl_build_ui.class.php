<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_mailtpl_build_ui.class.php,v 1.1.2.3 2021/02/02 12:35:32 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/mailtpl.class.php");

class list_configuration_mailtpl_build_ui extends list_configuration_mailtpl_ui {
	
	protected function get_title() {
		global $msg, $charset;
		return "<h1>".htmlentities($msg["admin_mailtpl_title"], ENT_QUOTES, $charset)."</h1>";
	}
	
	protected function _get_query_base() {
		return 'SELECT * FROM mailtpl';
	}
	
	protected function get_object_instance($row) {
		return new mailtpl($row->id_mailtpl);
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('name');
	}
	
	protected function _get_query_filters() {
		global $PMBuserid;
		
		$filter_query = '';
		
		$this->set_filters_from_form();
		
		$filters = array();
		$filters[] = 'mailtpl_users LIKE "% '.$PMBuserid.' %"';
		if(count($filters)) {
			$filter_query .= ' where '.implode(' and ', $filters);
		}
		return $filter_query;
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'name' => 'admin_mailtpl_name'
		);
	}
	
	protected function _compare_objects($a, $b) {
		if($this->applied_sort[0]['by']) {
			$sort_by = $this->applied_sort[0]['by'];
			switch($sort_by) {
				case 'name':
					return strcmp(strtolower(convert_diacrit($a->info[$sort_by])), strtolower(convert_diacrit($b->info[$sort_by])));
					break;
				default :
					return parent::_compare_objects($a, $b);
					break;
			}
		}
	}
	
	protected function get_cell_content($object, $property) {
		$content = '';
		switch($property) {
			case 'name':
				$content .= $object->info[$property];
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&action=modif&id='.$object->id;
	}
	
	protected function get_label_button_add() {
		global $msg;
		
		return $msg['admin_mailtpl_add'];
	}
}