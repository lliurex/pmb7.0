<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_rent_requests_ui.class.php,v 1.1.2.5 2021/04/07 14:00:31 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_rent_requests_ui extends list_rent_accounts_ui {
	
	protected function init_default_selected_filters() {
		$this->add_selected_filter('entity');
		$this->add_selected_filter('exercice');
		$this->add_selected_filter('request_type');
		$this->add_selected_filter('num_publisher');
		$this->add_selected_filter('num_supplier');
		$this->add_selected_filter('num_author');
		$this->add_selected_filter('event_date');
		$this->add_selected_filter('request_status');
		$this->add_selected_filter('date');
	}
	
	protected function get_button_add() {
	    global $msg;
	    
	    return "<input class='bouton' type='button' value='".$msg['acquisition_new_request']."' onClick=\"document.location='".static::get_controller_url_base()."&action=edit&id=0';\" />";
	}
	
	protected function get_search_filter_request_type() {
		global $msg;
		$request_types = new marc_select('rent_request_type', $this->objects_type.'_request_type', $this->filters['request_type'], '', 0, $msg['acquisition_account_type_select_all']);
		return $request_types->display;
	}
	
	protected function init_default_columns() {
		$this->add_column_selection();
		$this->add_column('id');
		$this->add_column('num_user');
		$this->add_column('request_type_name');
		$this->add_column('date');
		$this->add_column('title');
		$this->add_column('num_publisher');
		$this->add_column('num_supplier');
		$this->add_column('num_author');
		$this->add_column('event_date');
		$this->add_column('receipt_limit_date');
		$this->add_column('receipt_effective_date');
		$this->add_column('return_date');
		$this->add_column('request_status');
	}
	
	protected function get_selection_actions() {
		global $msg, $base_path;
		
		if(!isset($this->selection_actions)) {
			$this->selection_actions = array();
			$commands_link = array(
					'openPopUp' => $base_path."/pdf.php?pdfdoc=account_command",
					'openPopUpTitle' => 'lettre'
			);
			$this->selection_actions[] = $this->get_selection_action('gen_commands', $msg['acquisition_account_gen_commands'], '', $commands_link);
		}
		return $this->selection_actions;
	}
	
	protected function get_selection_mode() {
		return 'button';
	}
	
	protected function get_name_selected_objects() {
	    return "requests";
	}
}