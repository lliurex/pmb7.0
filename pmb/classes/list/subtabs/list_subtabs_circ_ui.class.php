<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_subtabs_circ_ui.class.php,v 1.1.2.3 2021/02/09 07:30:29 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_subtabs_circ_ui extends list_subtabs_ui {
	
	public function get_title() {
		global $msg;
		
		$title = "";
		switch (static::$categ) {
			case 'caddie':
				$title .= $msg['empr_caddie_menu'];
				break;
		}
		return $title;
	}
	
	public function get_sub_title() {
		global $msg, $sub, $quoi;
		
		$sub_title = "";
		switch (static::$categ) {
			case 'caddie':
				switch ($sub) {
					case 'gestion':
						switch ($quoi) {
							case 'panier':
							case 'procs':
							case 'remote_procs':
							case 'classementGen':
								$tab_name = 'gestion';
								break;
							case 'barcode':
							case 'selection':
								$tab_name = 'collecte';
								break;
							case 'pointagebarcode':
							case 'pointage':
							case 'pointagepanier':
							case 'razpointage':
								$tab_name = 'pointage';
								break;
						}
						break;
					case 'action':
						$tab_name = 'action';
						break;
				}
				$sub_title .= $msg["empr_caddie_menu_".$tab_name];
				$selected_subtab = $this->get_selected_subtab();
				if(!empty($selected_subtab)) {
					$sub_title .= " > ".$selected_subtab->get_label();
				}
				break;
			default:
				$sub_title .= parent::get_sub_title();
				break;
		}
		return $sub_title;
	}
	
	protected function _init_subtabs() {
		global $sub, $quoi;
		
		switch (static::$categ) {
			case 'caddie':
				switch ($sub) {
					case 'gestion':
						switch ($quoi) {
							case 'panier':
							case 'procs':
							case 'remote_procs':
							case 'classementGen':
								$this->add_subtab($sub, 'empr_caddie_menu_gestion_panier', '', '&quoi=panier');
								$this->add_subtab($sub, 'empr_caddie_menu_gestion_procs', '', '&quoi=procs');
								$this->add_subtab($sub, 'remote_procedures_circ_title', '', '&quoi=remote_procs');
								$this->add_subtab($sub, 'classementGen_list_libelle', '', '&quoi=classementGen');
								break;
							case 'barcode':
							case 'selection':
								$this->add_subtab($sub, 'empr_caddie_menu_collecte_barcode', '', '&quoi=barcode');
								$this->add_subtab($sub, 'empr_caddie_menu_collecte_selection', '', '&quoi=selection');
								break;
							case 'pointagebarcode':
							case 'pointage':
							case 'pointagepanier':
							case 'razpointage':
								$this->add_subtab($sub, 'empr_caddie_menu_pointage_barcode', '', '&quoi=pointagebarcode');
								$this->add_subtab($sub, 'empr_caddie_menu_pointage_selection', '', '&quoi=pointage');
								$this->add_subtab($sub, 'empr_caddie_menu_pointage_panier', '', '&quoi=pointagepanier');
								$this->add_subtab($sub, 'empr_caddie_menu_pointage_raz', '', '&quoi=razpointage');
								break;
						}
						
						break;
					case 'action':
						$this->add_subtab($sub, 'empr_caddie_menu_action_suppr_panier', '', '&quelle=supprpanier');
						$this->add_subtab($sub, 'empr_caddie_menu_action_transfert', '', '&quelle=transfert');
						$this->add_subtab($sub, 'empr_caddie_menu_action_edition', '', '&quelle=edition');
						$this->add_subtab($sub, 'empr_caddie_menu_action_mailing', '', '&quelle=mailing');
						$this->add_subtab($sub, 'empr_caddie_menu_action_selection', '', '&quelle=selection');
						$this->add_subtab($sub, 'empr_caddie_menu_action_suppr_base', '', '&quelle=supprbase');
						break;
				}
				break;
		}
	}
}