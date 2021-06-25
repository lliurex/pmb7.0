<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_transferts_refus_ui.class.php,v 1.1.6.6 2020/11/10 08:21:01 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_transferts_refus_ui extends list_transferts_ui {
	
	protected function get_title() {
		global $msg;
		return "<h1>".$msg['transferts_circ_menu_titre']." > ".$msg['transferts_circ_menu_refuse']."</h1>";
	}
	
	protected function get_form_title() {
		global $msg;
		return "<h3>".$msg["transferts_circ_retours_lot"]."</h3>";
	}
	
	protected function init_default_columns() {
		global $action;
		$this->add_column('record', '233');
		$this->add_column('cb', '232');
		$this->add_column('empr', 'transferts_circ_empr');
		$this->add_column('source', 'transferts_circ_source');
		$this->add_column('expl_owner', '651');
		$this->add_column('formatted_date_creation', 'transferts_circ_date_creation');
		$this->add_column('formatted_date_refus', 'transferts_circ_date_refus');
		$this->add_column('motif_refus', 'transferts_circ_motif');
		$this->add_column('transfert_ask_user_num', 'transferts_edition_ask_user');
		$this->add_column('transfert_bt_relancer');
		if(($action == '' || $action == 'list')) {
			$this->add_column_sel_button();
		}
	}
	
	protected function get_edition_link() {
		return '';
	}
	
	protected function init_available_filters() {
		$this->available_filters =
		array('main_fields' =>
				array(
						'site_origine' => 'transferts_circ_reception_filtre_source',
				)
		);
		$this->available_filters['custom_fields'] = array();
	}
	
	protected function init_default_selected_filters() {
		$this->add_selected_filter('site_origine');
	}
	
	protected function get_search_filters() {
		global $msg;
		$search_filters = '';
		$search_filters .= "&nbsp;".$msg['transferts_circ_reception_filtre_source'];
		$search_filters .= $this->get_search_filter_site_origine();
		return $search_filters;
	}
	
	protected function get_display_selection_actions() {
		global $msg;
		return "<input type='button' class='bouton' name='".$msg["transferts_circ_btSupprimer"]."' value='".$msg["transferts_circ_btSupprimer"]."' onclick='verifChk(document.".$this->get_form_name().",\"aff_supp\")'>";
	}
	
	protected function get_display_no_results() {
		global $msg;
		global $list_transferts_ui_no_results;
		$display = $list_transferts_ui_no_results;
		$display = str_replace('!!message!!', $msg["transferts_refuse_liste_vide"], $display);
		return $display;
	}
	
	protected function get_valid_form_title() {
	    global $msg, $action;
		
		switch ($action) {
		    case 'aff_supp':
		        return "<h3>".$msg["transferts_circ_refus_valide_liste"]."</h3>";
		    case 'aff_refus':
		    default:
		        return "<h3>".$msg["transferts_circ_validation_refus"]."</h3>";
		}
	}
}