<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_modules_ui.class.php,v 1.1.2.4 2021/03/26 14:06:15 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_modules_ui extends list_ui {
	
	protected function _init_modules() {
		global $msg;
		global $dsi_active, $acquisition_active, $pmb_extension_tab, $demandes_active;
		global $fiches_active, $semantic_active, $frbr_active, $modelling_active;
		
		// Tableau de bord
		$this->add_module('dashboard', $msg['dashboard'], $msg['dashboard'], $msg['2001'], 'icon');
		
		//	L'utilisateur fait la CIRCULATION ?
		if (defined('SESSrights') && SESSrights & CIRCULATION_AUTH) {
			$this->add_module('circ', $msg['5'], $msg['742'], $msg['2001']);
		}
		//	L'utilisateur fait le CATALOGAGE ?
		if (defined('SESSrights') && SESSrights & CATALOGAGE_AUTH) {
			$this->add_module('catalog', $msg['6'], $msg['743'], $msg['2002']);
		}
		//	L'utilisateur fait les AUTORITÉS ?
		if (defined('SESSrights') && SESSrights & AUTORITES_AUTH) {
			$this->add_module('autorites', $msg['132'], $msg['744'], $msg['2003']);
		}
		//	L'utilisateur fait l'ÉDITIONS ?
		if (defined('SESSrights') && SESSrights & EDIT_AUTH) {
			$this->add_module('edit', $msg['1100'], $msg['745'], $msg['2004']);
		}
		
		//	L'utilisateur fait la DSI ?
		if ($dsi_active && (defined('SESSrights') && SESSrights & DSI_AUTH)) {
			$this->add_module('dsi', $msg['dsi_menu'], $msg['dsi_menu_title']);
		}
		
		//	L'utilisateur fait l'ACQUISITION ?
		if ($acquisition_active && (defined('SESSrights') && SESSrights & ACQUISITION_AUTH)) {
			$this->add_module('acquisition', $msg['acquisition_menu'], $msg['acquisition_menu_title']);
		}
		
		//	L'utilisateur accède aux extensions ?
		if ($pmb_extension_tab && (defined('SESSrights') && SESSrights & EXTENSIONS_AUTH)) {
			$this->add_module('extensions', $msg['extensions_menu'], $msg['extensions_menu_title']);
		}
		
		//	L'utilisateur fait les DEMANDES ?
		if ($demandes_active && (defined('SESSrights') && SESSrights & DEMANDES_AUTH)) {
			$this->add_module('demandes', $msg['demandes_menu'], $msg['demandes_menu_title']);
		}
		
		//	L'utilisateur fait l'onglet FICHES ?
		if ($fiches_active && (defined('SESSrights') && SESSrights & FICHES_AUTH)) {
			$this->add_module('fichier', $msg['onglet_fichier'], $msg['onglet_fichier']);
		}
		
		//	L'utilisateur fait l'onglet SEMANTIC ?
		if ($semantic_active==true && ((defined('SESSrights') && SESSrights & SEMANTIC_AUTH))) {
			$this->add_module('semantic', $msg['semantic_onglet_title'], $msg['semantic_onglet_title']);
		}
		
		//	L'utilisateur fait l'onglet CMS ?
		if (defined('SESSrights') && SESSrights & CMS_AUTH) {
			$this->add_module('cms', $msg['cms_onglet_title'], $msg['cms_onglet_title']);
		}
		
		//	L'utilisateur fait l'onglet FRBR ?
		if ($frbr_active==true && defined('SESSrights') && SESSrights & FRBR_AUTH) {
			$this->add_module('frbr', $msg['frbr'], $msg['frbr']);
		}
		
		//	L'utilisateur fait l'onglet modélisation ?
		if ($modelling_active==true && defined('SESSrights') && SESSrights & MODELLING_AUTH) {
			$this->add_module('modelling', $msg['modelling'], $msg['modelling']);
		}
		//	L'utilisateur fait l'ADMINISTRATION ?
		if (defined('SESSrights') && SESSrights & ADMINISTRATION_AUTH) {
			$this->add_module('admin', $msg['7'], $msg['746'], $msg['2005']);
		}
	}
	
	protected function fetch_data() {
		$this->objects = array();
		$this->_init_modules();
		$this->messages = "";
	}
	
	public function add_module($name, $label, $title='', $accesskey='', $display_mode='') {
		$module = array(
				'name' => $name,
				'label' => $label,
				'title' => $title,
				'accesskey' => $accesskey,
				'display_mode' => $display_mode,
		);
		$this->add_object((object) $module);
	}
	
	public function get_display_module_link($name) {
		global $base_path;
		global $cms_active;
		
		$link = $base_path."/".static::$module_name.".php";
		switch ($name) {
			case 'autorites':
				$link .= "?categ=search";
				break;
			case 'edit':
				$link .= "?categ=procs";
				break;
			case 'cms':
				$link .= ($cms_active ? "categ=editorial&sub=list" : "categ=frbr_pages&sub=list");
				break;
		}
		return $link;
	}
	
	public function get_display_module($name, $label, $title='', $accesskey='', $display_mode='') {
		global $current, $charset;
		
		$display = "<li id='navbar-".$name."' ";
		if ($current == $name.".php"){
			$display .= " class='current'><a class='current' ";
		} else {
			$display .= "><a ";
		}
		$display .= "title='".htmlentities($title, ENT_QUOTES, $charset)."' href='./".$this->get_display_module_link($name)."' accesskey='".htmlentities($accesskey, ENT_QUOTES, $charset)."'>";
		if($display_mode == 'icon') {
			$display .= "<img title='".htmlentities($title, ENT_QUOTES, $charset)."' alt='".htmlentities($title, ENT_QUOTES, $charset)."' src='".get_url_icon($name.'.png')."'/>";
		} else {
			$display .= htmlentities($label, ENT_QUOTES, $charset);
		}
		$display .= "</a></li>";
		return $display;
	}
	
	public function get_display() {
		$display = '<ul>';
		foreach ($this->objects as $object) {
			$display .= $this->get_display_module($object->name, $object->label, $object->title, $object->accesskey, $object->display_mode);
		}
		$display .= '</ul>';
		return $display;
	}
	
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		$this->available_filters['main_fields'] = array();
		$this->available_filters['custom_fields'] = array();
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns =
		array('main_fields' =>
				array(
						'label' => '103',
				)
		);
		$this->available_columns['custom_fields'] = array();
	}
	
	protected function init_no_sortable_columns() {
		$this->no_sortable_columns = array(
				'label'
		);
	}
	
	protected function init_default_pager() {
		parent::init_default_pager();
		$this->pager['nb_per_page'] = 1000; //Illimité;
		$this->set_pager_in_session();
	}
	
	protected function init_default_columns() {
		
		$this->add_column('label');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'visible', false);
		$this->set_setting_display('search_form', 'export_icons', false);
		$this->set_setting_display('query', 'human', false);
		$this->set_setting_column('default', 'align', 'left');
	}
}