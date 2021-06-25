<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_infopages_ui.class.php,v 1.1.2.6 2020/12/08 13:39:35 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;

require_once($class_path."/classementGen.class.php");

class list_infopages_ui extends list_ui {
	
	public function get_form_title() {
		return '';
	}
	
	protected function get_html_title() {
		return '';
	}
	
	protected function _get_query_base() {
		return "SELECT * FROM infopages";
	}
		
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		$this->filters = array(
		);
		parent::init_filters($filters);
	}
	
	public function init_applied_group($applied_group=array()) {
		$this->applied_group = array(0 => 'infopage_classement');
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns = array(
			'main_fields' => array(
					'id_infopage' => 'infopages_id_infopage',
					'valid_infopage' => 'infopage_valid_infopage',
					'title_infopage' => 'infopage_title_infopage',
					'permalink' => 'infopage_lien_direct',
					'infopage_classement' => '',
					'infopage_classement_selector' => '',
			),
		);
	}
	
	protected function init_default_columns() {
		$this->add_column('id_infopage');
		$this->add_column('valid_infopage');
		$this->add_column('title_infopage');
		$this->add_column('permalink');
		$this->add_column('infopage_classement_selector');
	}
	
	/**
	 * Initialisation des settings par défaut
	 */
	protected function init_default_settings() {
		global $deflt_catalog_expanded_caddies;
		
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'export_icons', false);
		$this->set_setting_column('default', 'align', 'left');
		$this->set_setting_column('id_infopage', 'text', array('bold' => true));
		$this->settings['objects']['default']['display_mode'] = 'expandable_table';
		$this->settings['grouped_objects']['level_1']['display_mode'] = 'expandable_table';
		$this->settings['grouped_objects']['level_1']['expanded_display'] = $deflt_catalog_expanded_caddies;
	}
	
	protected function init_no_sortable_columns() {
		$this->no_sortable_columns = array(
				'id_infopage', 'valid_infopage', 'title_infopage', 'permalink',
				'infopage_classement_selector'
		);
	}
	
	/**
	 * Initialisation de la pagination par défaut
	 */
	protected function init_default_pager() {
		parent::init_default_pager();
		$this->pager['nb_per_page'] = pmb_mysql_result(pmb_mysql_query("SELECT count(*) FROM infopages"), 0, 0); //Illimité;
		$this->set_pager_in_session();
	}
	
	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
		$this->add_applied_sort('valid_infopage', 'desc');
		$this->add_applied_sort('title_infopage');
	}
	
	/**
	 * Tri SQL
	 */
	protected function _get_query_order() {
		
		if($this->applied_sort[0]['by']) {
			$order = '';
			$sort_by = $this->applied_sort[0]['by'];
			switch($sort_by) {
				case 'valid_infopage':
					$order .= $sort_by.", title_infopage";
					break;
				default :
					$order .= $sort_by;
					break;
			}
			if($order) {
				return $this->_get_query_order_sql_build($order);
			} else {
				return "";
			}
		}
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {

		parent::set_filters_from_form();
	}
			
	protected function get_grouped_label($object, $property) {
		$grouped_label = '';
		switch($property) {
			case 'infopage_classement':
				if(!trim($object->infopage_classement)){
					$grouped_label = classementGen::getDefaultLibelle();
				} else {
					$grouped_label = $object->infopage_classement;
				}
				break;
			default:
				$grouped_label = parent::get_grouped_label($object, $property);
				break;
		}
		return $grouped_label;
	}

	/**
	 * Construction dynamique de la fonction JS de tri
	 */
	protected function get_js_sort_script_sort() {
		$display = parent::get_js_sort_script_sort();
		$display = str_replace('!!categ!!', 'infopages', $display);
		$display = str_replace('!!sub!!', '', $display);
		$display = str_replace('!!action!!', 'list', $display);
		return $display;
	}
		
	protected function get_classement_instance($object) {
		return new classementGen('infopages', $object->id_infopage);
	}
	
	protected function get_cell_classement_content($object) {
		global $PMBuserid;
		
		$classementGen = $this->get_classement_instance($object);
		return $classementGen->show_selector(static::get_controller_url_base(),$PMBuserid);
	}
	
	protected function get_cell_content($object, $property) {
		global $charset, $opac_url_base, $PMBuserid;
		
		$content = '';
		switch($property) {
			case 'valid_infopage':
				if($object->valid_infopage) {
					$content .= "X";
				}
				break;
			case 'permalink':
				$content .= "<a href=\"".$opac_url_base."index.php?lvl=infopages&pagesid=".$object->id_infopage."\" target=_blank>".htmlentities($opac_url_base."index.php?lvl=infopages&pagesid=".$object->id_infopage, ENT_QUOTES, $charset)."</a>";
				break;
			case 'infopage_classement_selector':
				$classementGen = $this->get_classement_instance($object);
				$content .= $classementGen->show_selector(static::get_controller_url_base(),$PMBuserid);
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_display_cell($object, $property) {
		$onclick="";
		$class="";
		switch($property) {
			case 'id_infopage':
				$onclick = "document.location=\"".static::get_controller_url_base()."&sub=infopages&action=modif&id=".$object->id_infopage."\"";
				$class = "align_right";
				break;
			case 'valid_infopage':
				$onclick = "document.location=\"".static::get_controller_url_base()."&sub=infopages&action=modif&id=".$object->id_infopage."\"";
				$class = "erreur center";
				break;
			case 'title_infopage':
				$onclick = "document.location=\"".static::get_controller_url_base()."&sub=infopages&action=modif&id=".$object->id_infopage."\"";
				break;
		}
		$attributes = array(
				'onclick' => $onclick,
				'class' => $class,
		);
		$content = $this->get_cell_content($object, $property);
		$display = $this->get_display_format_cell($content, $property, $attributes);
		return $display;
	}
	
	public function get_display_list() {
		//Récupération du script JS de tris
		$display = $this->get_js_sort_script_sort();
		$display .= "<script src='./javascript/classementGen.js' type='text/javascript'></script>";
		
		//Affichage de la liste des objets
		$display .= $this->get_display_objects_list();
		if(count($this->get_selection_actions())) {
			$display .= $this->get_display_selection_actions();
		}
		$display .= $this->get_button_add();
		return $display;
	}
	
	protected function get_button_add() {
		global $msg;
		
		return "<input class='bouton' type='button' value=\" ".$msg['infopages_bt_ajout']." \" onClick=\"document.location='".static::get_controller_url_base()."&sub=infopages&action=add'\" />";
	}
	
	protected function get_selection_actions() {
		global $msg;
	
		if(!isset($this->selection_actions)) {
			$this->selection_actions = array();
// 			$this->selection_actions[] = $this->get_selection_action('delete', $msg['delete'], '', $this->get_link_action('', 'href'));
		}
		return $this->selection_actions;
	}
	
	public static function get_controller_url_base() {
		global $base_path;
		
		return $base_path.'/admin.php?categ=infopages';
	}
}