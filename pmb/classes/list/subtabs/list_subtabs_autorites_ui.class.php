<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_subtabs_autorites_ui.class.php,v 1.1.2.3 2021/02/09 07:30:29 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_subtabs_autorites_ui extends list_subtabs_ui {
	
	public function get_title() {
		global $msg;
		
		$title = "";
		switch (static::$categ) {
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
		global $sub;
		
		switch (static::$categ) {
			case 'caddie':
				switch ($sub) {
					case 'gestion':
						$this->add_subtab($sub, 'caddie_menu_gestion_panier', '', '&quoi=panier');
						$this->add_subtab($sub, 'caddie_menu_gestion_procs', '', '&quoi=procs');
						$this->add_subtab($sub, 'classementGen_list_libelle', '', '&quoi=classementGen');
						break;
					case 'collecte':
						$this->add_subtab($sub, 'caddie_menu_collecte_selection', '', '&moyen=selection');
						break;
					case 'pointage':
						$this->add_subtab($sub, 'caddie_menu_pointage_selection', '', '&moyen=selection');
						$this->add_subtab($sub, 'caddie_menu_pointage_panier', '', '&moyen=panier');
						$this->add_subtab($sub, 'caddie_menu_pointage_raz', '', '&moyen=raz');
						break;
					case 'action':
						$this->add_subtab($sub, 'caddie_menu_action_suppr_panier', '', '&quelle=supprpanier');
						$this->add_subtab($sub, 'caddie_menu_action_edition', '', '&quelle=edition');
						$this->add_subtab($sub, 'caddie_menu_action_selection', '', '&quelle=selection');
						$this->add_subtab($sub, 'caddie_menu_action_suppr_base', '', '&quelle=supprbase');
						$this->add_subtab($sub, 'caddie_menu_action_reindex', '', '&quelle=reindex');
						break;
				}
				break;
		}
	}
}