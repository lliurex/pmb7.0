<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_accounting_budgets_ui.class.php,v 1.1.2.2 2021/03/25 09:06:06 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_accounting_budgets_ui extends list_accounting_ui {
	
	protected function _get_query_base() {
		return 'SELECT id_budget as id, budgets.* FROM budgets';
	}
	
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		$this->available_filters =
		array('main_fields' =>
				array(
						'entity' => 'acquisition_coord_lib',
				)
		);
		$this->available_filters['custom_fields'] = array();
	}
	
	protected function init_default_selected_filters() {
		$this->add_selected_filter('entity');
	}
	
	protected function init_default_applied_sort() {
		$this->add_applied_sort('statut');
		$this->add_applied_sort('libelle');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'visible', false);
		$this->set_setting_display('search_form', 'export_icons', false);
		$this->set_setting_column('default', 'align', 'left');
		$this->set_setting_column('libelle', 'text', array('italic' => true));
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns =
		array('main_fields' =>
				array(
						'libelle' => '103',
						'statut' => 'acquisition_statut',
						'exercice' => 'acquisition_budg_exer',
				)
		);
	}
	
	protected function init_default_columns() {
		$this->add_column('libelle');
		$this->add_column('statut');
		$this->add_column('exercice');
	}
	
	protected function _get_query_filters() {
		$filter_query = '';
		
		$this->set_filters_from_form();
		
		$filters = array();
		if($this->filters['entite']) {
			$filters[] = 'num_entite = "'.$this->filters['entite'].'"';
		}
		if(count($filters)) {
			$filter_query .= ' where '.implode(' and ', $filters);
		}
		return $filter_query;
	}
	
	protected function _get_query_order() {
		$this->applied_sort_type = 'OBJECTS';
		return '';
	}
	
	protected function get_cell_content($object, $property) {
		global $msg;
		
		$content = '';
		switch($property) {
			case 'statut':
				switch ($object->statut) {
					case STA_BUD_VAL :
						$content .= $msg['acquisition_statut_actif'];
						break;
					case  STA_BUD_CLO :
						$content .= $msg['acquisition_statut_clot'];
						break;
					default:
						$content .= $msg['acquisition_budg_pre'];
						break;
				}
				break;
			case 'exercice':
				$exer = new exercices($object->num_exercice);
				$content .= $exer->libelle;
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_display_cell($object, $property) {
		$attributes = array();
		$attributes['onclick'] = "window.location=\"".static::get_controller_url_base()."&action=show&id_bibli=".$object->num_entite."&id_".$this->get_initial_name()."=".$object->id_budget."\"";
		$content = $this->get_cell_content($object, $property);
		$display = $this->get_display_format_cell($content, $property, $attributes);
		return $display;
	}
	
	public function get_type_acte() {
		return 0;
	}
	
	public function get_initial_name() {
		return 'bud';
	}
}