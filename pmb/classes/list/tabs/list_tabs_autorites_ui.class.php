<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_tabs_autorites_ui.class.php,v 1.1.2.3 2020/11/26 17:42:25 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/authperso.class.php");

class list_tabs_autorites_ui extends list_tabs_ui {
	
	protected function _init_tabs() {
		global $pmb_use_uniform_title, $thesaurus_concepts_active;
		
		//Recherche
		$this->add_tab('search', 'search', 'search_authorities');
		$this->add_tab('search', 'search_perso', 'search_perso_menu');
		
		//Autorites
		$this->add_tab('132', 'auteurs', '133');
		if (SESSrights & THESAURUS_AUTH) {
			$this->add_tab('132', 'categories', '134', '', '&parent=0&id=0');
		}
		$this->add_tab('132', 'editeurs', '135');
		$this->add_tab('132', 'collections', '136');
		$this->add_tab('132', 'souscollections', '137');
		$this->add_tab('132', 'series', '333');
		if ($pmb_use_uniform_title) {
			$this->add_tab('132', 'titres_uniformes', 'aut_menu_titre_uniforme');
		}
		$this->add_tab('132', 'indexint', 'indexint_menu');
		if ($thesaurus_concepts_active==true && (SESSrights & CONCEPTS_AUTH)) {
			$this->add_tab('132', 'concepts', 'ontology_skos_menu', 'concept');
		}
		$authpersos = new authpersos();
		if(count($authpersos->info)) {
			foreach($authpersos->info as $elt){
				$this->add_tab('132', 'authperso', $elt['name'], '', '&id_authperso='.$elt['id']);
			}
		}
		
		//Paniers
		$this->add_tab('caddie_menu', 'caddie', 'caddie_menu_gestion');
		$this->add_tab('caddie_menu', 'caddie', 'caddie_menu_collecte', 'collecte');
		$this->add_tab('caddie_menu', 'caddie', 'caddie_menu_pointage', 'pointage');
		$this->add_tab('caddie_menu', 'caddie', 'caddie_menu_action', 'action');
		
		//Semantique
		if (SESSrights & THESAURUS_AUTH) {
			$this->add_tab('semantique', 'semantique', 'word_syn_menu', 'synonyms');
			$this->add_tab('semantique', 'semantique', 'empty_words_libelle', 'empty_words');
		}
	
		//Import
		$this->add_tab('authorities_gest', 'import', 'authorities_import');
	}
	
	protected function is_active_tab($label_code, $categ, $sub='') {
		$active = false;
		switch ($label_code) {
			case 'ontology_skos_menu':
				if($this->is_equal_var_get('categ', 'concepts') && $this->is_equal_var_get('sub', array("concept", "conceptscheme", "collection", "orderedcollection"))) {
					$active = true;
				}
				break;
			default:
				$active = parent::is_active_tab($label_code, $categ, $sub);
				break;
		}
		return $active;
	}
}