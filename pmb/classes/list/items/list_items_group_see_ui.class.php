<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_items_group_see_ui.class.php,v 1.1.2.2 2021/03/10 07:39:22 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_items_group_see_ui extends list_items_group_ui {
	
	protected function init_default_columns() {
		global $pmb_sur_location_activate;
		
		$this->add_column('expl_cb', 'groupexpl_form_cb');
		$this->add_column('record_header', 'groupexpl_form_notice');
		if($pmb_sur_location_activate){
			$this->add_column('sur_loc_libelle');
		}
		$this->add_column('location_libelle');
		$this->add_column('section_libelle');
		$this->add_column('expl_cote');
		$this->add_column('statut_libelle');
		$this->add_column('pointed');
		$this->add_column('raz');
	}
}