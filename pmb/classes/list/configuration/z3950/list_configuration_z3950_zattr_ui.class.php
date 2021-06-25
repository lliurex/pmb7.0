<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_z3950_zattr_ui.class.php,v 1.1.2.2 2021/01/13 11:21:08 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_z3950_zattr_ui extends list_configuration_z3950_ui {
	
	protected $z3950attributes;
	
	protected function _get_query_base() {
		return 'SELECT * FROM z_attr';
	}
	
	public function init_filters($filters=array()) {
		
		$this->filters = array(
				'attr_bib_id' => 0,
		);
		parent::init_filters($filters);
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('attr_libelle');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_column('attr_libelle', 'text', array('bold' => true));
	}
	
	protected function _get_query_filters() {
		$filter_query = '';
		
		$this->set_filters_from_form();
		
		$filters = array();
		if($this->filters['attr_bib_id']) {
			$filters[] = 'attr_bib_id = "'.$this->filters['attr_bib_id'].'"';
		}
		if(count($filters)) {
			$filter_query .= ' where '.implode(' and ', $filters);
		}
		return $filter_query;
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'attr_libelle' => 'zattr_libelle',
				'attr_attr' => 'zattr_attr',
		);
	}
	
	protected function get_cell_content($object, $property) {
		global $msg;
		
		$content = '';
		switch($property) {
			case 'attr_libelle':
				$content .= $msg["z3950_".$this->get_z3950attributes()[$object->attr_libelle]];
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&action=modif&bib_id='.$object->attr_bib_id.'&attr_libelle='.$object->attr_libelle;
	}
	
	protected function get_label_button_add() {
		global $msg;
		
		return $msg['ajouter'];
	}
	
	protected function get_button_add() {
		global $msg, $charset, $base_path;
		
		return "
			<input class='bouton' type='button' value='".$msg['76']."' onClick=\"document.location='".$base_path."/admin.php?categ=z3950&sub=zbib&action=modif&id=".$this->filters['attr_bib_id']."'\" />
			<input class='bouton' type='button' value='".htmlentities($this->get_label_button_add(), ENT_QUOTES, $charset)."' onClick=\"document.location='".static::get_controller_url_base()."&action=add&bib_id=".$this->filters['attr_bib_id']."';\" />";
	}
	
	public function get_z3950attributes() {
		global $include_path;
		
		if(!isset($this->z3950attributes)) {
			// loading the localized attributes labels
			$la = new XMLlist($include_path."/marc_tables/z3950attributes.xml", 0);
			$la->analyser();
			$this->z3950attributes = $la->table;
		}
		return $this->z3950attributes;
	}
}