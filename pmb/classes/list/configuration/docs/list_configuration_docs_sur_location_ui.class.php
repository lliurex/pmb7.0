<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_docs_sur_location_ui.class.php,v 1.1.2.2 2021/01/21 12:32:46 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_docs_sur_location_ui extends list_configuration_docs_ui {
	
	protected function get_title() {
		global $msg, $charset;
		return "<h1>".htmlentities($msg["sur_location_list_title"], ENT_QUOTES, $charset)."</h1>";
	}
	
	protected function _get_query_base() {
		return 'SELECT * FROM sur_location';
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('surloc_libelle');
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'surloc_libelle' => '103',
				'surloc_visible_opac' => 'opac_object_visible_short',
				'surloc_comment' => 'sur_location_comment'
		);
	}
	
	protected function get_cell_content($object, $property) {
		$content = '';
		switch($property) {
			case 'surloc_visible_opac':
				$content .= $this->get_cell_visible_flag($object, $property);
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&action=modif&id='.$object->surloc_id;
	}
	
	protected function get_label_button_add() {
		global $msg;
		
		return $msg['sur_location_bt_ajouter'];
	}
}