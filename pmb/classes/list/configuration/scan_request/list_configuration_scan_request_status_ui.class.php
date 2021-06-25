<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_scan_request_status_ui.class.php,v 1.1.2.2 2021/01/20 07:34:18 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/list/configuration/scan_request/list_configuration_scan_request_ui.class.php");

class list_configuration_scan_request_status_ui extends list_configuration_scan_request_ui {
	
	protected function _get_query_base() {
		return 'SELECT * FROM scan_request_status';
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('scan_request_status_label');
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'scan_request_status_label' => 'scan_request_status_label',
				'scan_request_status_opac_show' => 'scan_request_status_visible',
				'scan_request_status_cancelable' => 'scan_request_cancelable',
				'scan_request_status_infos_editable' => 'scan_request_infos_editable',
				'scan_request_status_is_closed' => 'scan_request_is_closed',
		);
	}
	
	protected function get_cell_content($object, $property) {
		$content = '';
		switch($property) {
			case 'scan_request_status_label':
				$content .= "<span class='".$object->scan_request_status_class_html."'  style='margin-right: 3px;'><img src='".get_url_icon('spacer.gif')."' width='10' height='10' /></span>";
				$content .= $object->scan_request_status_label;
				break;
			case 'scan_request_status_opac_show':
			case 'scan_request_status_cancelable':
			case 'scan_request_status_infos_editable':
			case 'scan_request_status_is_closed':
				$content .= $this->get_cell_visible_flag($object, $property);
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&action=edit&id='.$object->id_scan_request_status;
	}
	
	protected function get_label_button_add() {
		global $msg;
		
		return $msg['editorial_content_publication_state_add'];
	}
}