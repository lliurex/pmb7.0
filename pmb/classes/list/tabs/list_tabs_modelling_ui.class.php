<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_tabs_modelling_ui.class.php,v 1.1.2.2 2020/11/23 09:11:18 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_tabs_modelling_ui extends list_tabs_ui {
	
	protected function _init_tabs() {
		global $pmb_contribution_area_activate;
		
		$this->add_tab('admin_menu_modules', 'ontologies', 'ontologies', 'general');
		$this->add_tab('admin_menu_modules', 'frbr', 'frbr');
		if($pmb_contribution_area_activate){
			$this->add_tab('admin_menu_modules', 'contribution_area', 'admin_menu_contribution_area');
		}
	}
}