<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_connecteurs_categ_ui.class.php,v 1.1.2.2 2021/01/15 10:16:40 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/connecteurs_out_sets.class.php");

class list_configuration_connecteurs_categ_ui extends list_configuration_connecteurs_ui {
	
	protected function _get_query_base() {
		return 'SELECT connectors_categ_id, connectors_categ_name FROM connectors_categ';
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('connectors_categ_name');
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'connectors_categ_name' => '103',
				'connecteurs' => 'count_connecteurs_categ',
		);
	}
	
	protected function get_cell_content($object, $property) {
		$content = '';
		switch($property) {
			case 'connecteurs':
				$count_query = 'SELECT count(*) FROM connectors_categ_sources WHERE num_categ='.$object->connectors_categ_id;
				$conn_count = pmb_mysql_result(pmb_mysql_query($count_query), 0, 0);
				$content .= $conn_count;
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&action=modif&id='.$object->connectors_categ_id;
	}
	
	protected function get_label_button_add() {
		global $msg;
		
		return $msg['connecteurs_categ_add'];
	}
}