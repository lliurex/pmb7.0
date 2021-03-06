<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_acquisition_compta_ui.class.php,v 1.1.2.2 2021/01/18 08:28:26 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_acquisition_compta_ui extends list_configuration_acquisition_ui {
	
	protected function _get_query_base() {
		return 'SELECT * FROM exercices';
	}
	
	public function init_filters($filters=array()) {
		
		$this->filters = array(
				'num_entite' => 0,
		);
		parent::init_filters($filters);
	}
	
	protected function init_default_applied_sort() {
		$this->add_applied_sort('date_debut', 'desc');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_column('libelle', 'text', array('italic' => true));
		$this->set_setting_column('date_debut', 'text', array('italic' => true));
		$this->set_setting_column('date_fin', 'text', array('italic' => true));
	}
	
	protected function _get_query_filters() {
		$filter_query = '';
		
		$this->set_filters_from_form();
		
		$filters = array();
		if($this->filters['num_entite']) {
			$filters[] = 'num_entite = "'.$this->filters['num_entite'].'"';
		}
		if(count($filters)) {
			$filter_query .= ' where '.implode(' and ', $filters);
		}
		return $filter_query;
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'libelle' => '103',
				'date_debut' => 'calendrier_date_debut',
				'date_fin' => 'calendrier_date_fin',
				'statut' => 'acquisition_statut',
		);
	}
	
	protected function get_cell_content($object, $property) {
		global $msg;
		
		$content = '';
		switch($property) {
			case 'date_debut':
			case 'date_fin':
				$content .= formatdate($object->{$property});
				break;
			case 'statut':
				switch ($object->statut) {
					case STA_EXE_CLO :
						$content .= $msg['acquisition_statut_clot'];
						break;
					case  STA_EXE_DEF :
						$content .= $msg['acquisition_statut_def'];
						break;
					default:
						$content .= $msg['acquisition_statut_actif'];
						break;
				}
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&action=modif&ent='.$object->num_entite.'&id='.$object->id_exercice;
	}
	
	protected function get_label_button_add() {
		global $msg;
		
		return $msg['acquisition_ajout_exer'];
	}
	
	protected function get_button_add() {
		global $charset;
		
		return "<input class='bouton' type='button' value='".htmlentities($this->get_label_button_add(), ENT_QUOTES, $charset)."' onClick=\"document.location='".static::get_controller_url_base()."&action=add&ent=".$this->filters['num_entite']."';\" />";
	}
}