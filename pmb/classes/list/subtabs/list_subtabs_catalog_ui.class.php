<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_subtabs_catalog_ui.class.php,v 1.1.2.3 2021/02/09 07:30:29 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_subtabs_catalog_ui extends list_subtabs_ui {
	
	public function get_title() {
		global $msg;
		
		$title = "";
		switch (static::$categ) {
			case 'avis':
				$title .= $msg['titre_avis'];
				break;
			case 'caddie':
				$title .= $msg['caddie_menu'];
				break;
		}
		return $title;
	}
	
	public function get_sub_title() {
		global $msg, $sub;
		
		$sub_title = "";
		switch (static::$categ) {
			case 'caddie':
				$sub_title .= $msg["caddie_menu_".$sub];
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
		global $sub, $class_path;
		global $gestion_acces_active, $pmb_scan_request_activate, $pmb_transferts_actif;
		
		switch (static::$categ) {
			case 'avis':
				$this->add_subtab('records', 'avis_menu_records');
				if(defined('SESSrights') && SESSrights & CMS_AUTH) {
					$this->add_subtab('articles', 'avis_menu_articles');
					$this->add_subtab('sections', 'avis_menu_sections');
				}
				break;
			case 'caddie':
				switch ($sub) {
					case 'gestion':
						$this->add_subtab($sub, 'caddie_menu_gestion_panier', '', '&quoi=panier');
						$this->add_subtab($sub, 'caddie_menu_gestion_procs', '', '&quoi=procs');
						$this->add_subtab($sub, 'remote_procedures_catalog_title', '', '&quoi=remote_procs');
						$this->add_subtab($sub, 'classementGen_list_libelle', '', '&quoi=classementGen');
						break;
					case 'collecte':
						$this->add_subtab($sub, 'caddie_menu_collecte_cb', '', '&moyen=douchette');
// 						$this->add_subtab($sub, 'caddie_menu_collecte_import', '', '&moyen=import');
						$this->add_subtab($sub, 'caddie_menu_collecte_selection', '', '&moyen=selection');
						break;
					case 'pointage':
						$this->add_subtab($sub, 'caddie_menu_pointage_cb', '', '&moyen=douchette');
// 						$this->add_subtab($sub, 'caddie_menu_pointage_import', '', '&moyen=import');
// 						$this->add_subtab($sub, 'caddie_menu_pointage_import_unimarc', '', '&moyen=importunimarc');
						$this->add_subtab($sub, 'caddie_menu_pointage_selection', '', '&moyen=selection');
						$this->add_subtab($sub, 'caddie_menu_pointage_panier', '', '&moyen=panier');
						$this->add_subtab($sub, 'caddie_menu_pointage_search_history', '', '&moyen=search_history');
						$this->add_subtab($sub, 'caddie_menu_pointage_raz', '', '&moyen=raz');
						break;
					case 'action':
						$this->add_subtab($sub, 'caddie_menu_action_suppr_panier', '', '&quelle=supprpanier');
						$this->add_subtab($sub, 'caddie_menu_action_transfert', '', '&quelle=transfert');
						$this->add_subtab($sub, 'caddie_menu_action_edition', '', '&quelle=edition');
						$this->add_subtab($sub, 'caddie_menu_action_impr_cote', '', '&quelle=impr_cote');
						$this->add_subtab($sub, 'caddie_menu_action_export', '', '&quelle=export');
						$this->add_subtab($sub, 'caddie_menu_action_exp_docnum', '', '&quelle=expdocnum');
						$this->add_subtab($sub, 'caddie_menu_action_selection', '', '&quelle=selection');
						// On déclenche un événement sur la supression
						require_once($class_path.'/event/events/event_users_group.class.php');
						$evt_handler = events_handler::get_instance();
						$event = new event_users_group("users_group", "get_autorisation_del_base");
						$evt_handler->send($event);
						if(!$event->get_error_message()){
							$this->add_subtab($sub, 'caddie_menu_action_suppr_base', '', '&quelle=supprbase');
						}
						$this->add_subtab($sub, 'caddie_menu_action_reindex', '', '&quelle=reindex');
						if($gestion_acces_active){
							$this->add_subtab($sub, 'caddie_menu_action_access_rights', '', '&quelle=access_rights');
						}
						if((SESSrights & CIRCULATION_AUTH) && $pmb_scan_request_activate){
							$this->add_subtab($sub, 'scan_request_record_button', '', '&quelle=scan_request');
						}
// 						$this->add_subtab($sub, 'caddie_menu_action_change_bloc', '', '&quelle=changebloc');
						if ($pmb_transferts_actif) {
							$this->add_subtab($sub, 'caddie_menu_action_transfert_to_location', '', '&quelle=transfert_to_location');
						}
						break;
				}
				break;
		}
	}
}