<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_procs_ui.class.php,v 1.1.2.3 2021/03/26 10:08:34 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_procs_ui extends list_ui {
	
	protected function _get_query_base() {
		$query = 'SELECT idproc as id, procs.*, procs_classements.* FROM procs LEFT JOIN procs_classements ON idproc_classement=num_classement';
		return $query;
	}
	
	protected function add_object($row) {
		global $PMBuserid;
		
		$rqt_autorisation=explode(" ",$row->autorisations);
		if ($PMBuserid==1 || $row->autorisations_all || array_search ($PMBuserid, $rqt_autorisation)!==FALSE) {
			$this->objects[] = $row;
		}
	}
	
	protected function _get_query_order() {
	    if ($this->applied_sort[0]['by']) {
			$order = '';
			$sort_by = $this->applied_sort[0]['by'];
			switch($sort_by) {
				case 'libproc_classement':
					$order .= 'libproc_classement,name';
					break;
				default :
					$order .= parent::_get_query_order();
					break;
			}
			if($order) {
				return $this->_get_query_order_sql_build($order);
			} else {
				return "";
			}
		}
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'export_icons', false);
		$this->set_setting_column('default', 'align', 'left');
		$this->settings['objects']['default']['display_mode'] = 'expandable_table';
		$this->settings['grouped_objects']['level_1']['display_mode'] = 'expandable_table';
	}
	
	protected function init_no_sortable_columns() {
		$this->no_sortable_columns = array(
				'execute', 'name', 'configuration', 'export'
		);
	}
	
	protected function init_default_pager() {
		parent::init_default_pager();
		$this->pager['nb_per_page'] = pmb_mysql_result(pmb_mysql_query("SELECT count(*) FROM procs"), 0, 0); //Illimité;
		$this->set_pager_in_session();
	}
	
	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('libproc_classement');
	    $this->add_applied_sort('name');
	}
	
	public function init_applied_group($applied_group=array()) {
		$this->applied_group = array(0 => 'libproc_classement');
	}
	
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		$this->available_filters =
		array('main_fields' =>
				array(
						'name' => '705',
						'autorisations' => '25',
				)
		);
		$this->available_filters['custom_fields'] = array();
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		$this->filters = array(
				'name' => '',
				'autorisations' => array()
		);
		parent::init_filters($filters);
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		$this->set_filter_from_form('name');
		parent::set_filters_from_form();
	}
	
	protected function get_search_filter_name() {
		global $msg;
		
		return "<input class='saisie-80em' id='".$this->objects_type."_name' type='text' name='".$this->objects_type."_name' value=\"".$this->filters['name']."\" title='$msg[3001]' />";
	}
	
	protected function get_search_filter_autorisations() {
		//TODO
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns =
		array('main_fields' =>
				array(
						'name' => '705',
						'comment' => '707',
						'libproc_classement' => 'proc_clas_lib'
				)
		);
		$this->available_columns['custom_fields'] = array();
	}
	
	protected function init_default_columns() {
		$this->add_column_execute();
		$this->add_column('name');
		$this->add_column('configuration');
		$this->add_column_export();
	}
	
	protected function add_column_execute() {
		global $msg;
		
		$this->columns[] = array(
				'property' => 'execute',
				'label' => '',
				'html' => "<input class='bouton' type='button' value=' $msg[708] ' onClick=\"document.location='".static::get_controller_url_base()."&action=execute&id=!!id!!'\" />",
				'exportable' => false
		);
	}
	
	protected function add_column_export() {
		global $msg;
		
		$this->columns[] = array(
				'property' => 'export',
				'label' => '',
				'html' => "<input class='bouton' type='button' value=\"".$msg['procs_bt_export']."\" onClick=\"document.location='./export.php?quoi=procs&sub=actionsperso&id=!!id!!'\" />",
				'exportable' => false
		);
	}
	
	protected function get_button_add() {
		global $msg;
	
		return "<input class='bouton' type='button' value=' ".$msg['704']." ' onClick=\"document.location='".static::get_controller_url_base()."&action=add'\" />";
	}
	
	protected function get_grouped_label($object, $property) {
		global $msg;
		
		$grouped_label = '';
		switch($property) {
			case 'libproc_classement':
				$grouped_label = (!empty($object->{$property}) ? $object->{$property} : $msg['proc_clas_aucun']);
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
		global $sub;
		
		$display = parent::get_js_sort_script_sort();
		$display = str_replace('!!categ!!', 'proc', $display);
		$display = str_replace('!!sub!!', $sub, $display);
		$display = str_replace('!!action!!', 'list', $display);
		return $display;
	}
	
	protected function get_cell_content($object, $property) {
		global $msg;
		
		$content = '';
		switch($property) {
			case 'name':
				$content .= "<strong>".$object->name."</strong><br />
					<small>".$object->comment."&nbsp;</small>";
				break;
			case 'configuration':
				if (preg_match_all("|!!(.*)!!|U",$object->requete,$query_parameters)) {
					$content .= "<a href='".static::get_controller_url_base()."&action=configure&id_query=".$object->idproc."'>".$msg["procs_options_config_param"]."</a>";
				}
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}

	protected function get_display_cell($object, $property) {
		switch ($property) {
			case 'name':
				$attributes = array(
					'onclick' => "document.location=\"".static::get_controller_url_base()."&action=modif&id=".$object->idproc."\""
				);
				break;
			default:
				break;
		}
		$content = $this->get_cell_content($object, $property);
		$display = $this->get_display_format_cell($content, $property, $attributes);
		return $display;
	}
	
	protected function gen_plus($id, $titre, $contenu, $maximise=0) {
		global $msg;
		if(static::class == 'list_procs_ui') {
			$num_class = procs_classement::get_id_from_libelle($titre);
			$contenu .= "
			<div class='row'>
				<input class='bouton_small' type='button' value=\"".$msg['704']."\" onClick=\"document.location='".static::get_controller_url_base()."&action=add&num_classement=".$num_class."';\" />
				<input class='bouton_small' type='button' value=\"".$msg['procs_bt_import']."\" onClick=\"document.location='".static::get_controller_url_base()."&action=import&num_classement=".$num_class."';\" />
			</div>
			";
		}
		return parent::gen_plus($id, $titre, $contenu, $maximise);
	}
	
	/**
	 * Affiche la recherche + la liste
	 */
	public function get_display_list() {
		$display = parent::get_display_list();
		$display .= "<br />".$this->get_button_add();
		return $display;
	}
}