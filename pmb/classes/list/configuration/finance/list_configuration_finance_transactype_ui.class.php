<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_finance_transactype_ui.class.php,v 1.1.2.2 2021/01/13 11:21:07 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_finance_transactype_ui extends list_configuration_finance_ui {
	
	protected function _get_query_base() {
		return 'SELECT * FROM transactype';
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('transactype_name');
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'transactype_name' => 'transactype_list_libelle',
				'transactype_unit_price' => 'transactype_form_unit_price',
				'transactype_quick_allowed' => 'transactype_form_quick_allowed',
		);
	}
	
	protected function get_cell_content($object, $property) {
		$content = '';
		switch($property) {
			case 'transactype_quick_allowed':
				$content .= $this->get_cell_visible_flag($object, $property);
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&action=edit&id='.$object->transactype_id;
	}
	
	protected function get_label_button_add() {
		global $msg;
		
		return $msg['transactype_list_add'];
	}
}