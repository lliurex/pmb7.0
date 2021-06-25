<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_reservations_edition_ui.class.php,v 1.1.6.9 2021/03/26 10:29:18 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path."/templates/list/reservations/list_reservations_edition_ui.tpl.php");

class list_reservations_edition_ui extends list_reservations_ui {
	
	protected function get_title() {
		global $msg;
		return "<h1>".$msg[350]."&nbsp;&gt;&nbsp;".$msg['edit_resa_menu']."</h1>";
	}
	
	protected function get_form_title() {
		global $msg;
		
		return $msg['edit_resa_menu'];
	}
	
	protected function init_default_columns() {
		global $pmb_resa_planning;
		
		if(!$this->filters['id_notice'] && !$this->filters['id_bulletin']) {
			$this->add_column('record');
		}
		$this->add_column('expl_cote');
		if(!$this->filters['id_empr']) {
			$this->add_column('empr');
			$this->add_column('empr_location');
		}
		$this->add_column('rank');
		$this->add_column('resa_date');
		$this->add_column('resa_condition');
		if ($pmb_resa_planning) {
			$this->add_column('resa_date_debut');
		}
		$this->add_column('resa_date_fin');
	}
	
	protected function init_default_settings() {
		global $sub;
		
		parent::init_default_settings();
		switch($sub) {
			case "resa_a_traiter" :
				$this->set_setting_display('search_form', 'options', true);
				break;
			default :
				$this->set_setting_display('search_form', 'visible', false);
				break;
		}
	}
	
	protected function _get_query_human() {
		global $sub;
		switch($sub) {
			case "resa_a_traiter" :
				return parent::_get_query_human();
			default :
				return '';
		}
	}
	
	protected function get_display_spreadsheet_title() {
		global $msg;
		$this->spreadsheet->write_string(0,0,$msg[350].": ".$msg['edit_resa_menu']);
	}
	
	protected function get_html_title() {
		global $msg;
		return "<h1>".$msg[350]."&nbsp;&gt;&nbsp;".$msg['edit_resa_menu']."</h1>";
	}
	
	protected function init_default_applied_sort() {
		$this->add_applied_sort('record');
		$this->add_applied_sort('resa_date');
	}
	
	protected function get_js_sort_script_sort() {
		$display = parent::get_js_sort_script_sort();
		$display = str_replace('!!categ!!', 'notices', $display);
		return $display;
	}
}