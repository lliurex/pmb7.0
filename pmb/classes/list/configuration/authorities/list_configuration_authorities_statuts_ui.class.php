<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_authorities_statuts_ui.class.php,v 1.1.2.3 2021/01/12 07:30:14 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_authorities_statuts_ui extends list_configuration_authorities_ui {
	
	protected function _get_query_base() {
		return 'SELECT * FROM authorities_statuts';
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('authorities_statut_label');
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'label' => 'noti_statut_libelle',
		);
	}
	
	protected function get_cell_content($object, $property) {
		global $charset;
	
		$content = '';
		switch($property) {
			case 'label':
				$content .= "
					<span class='".$object->authorities_statut_class_html."' style='margin-right:3px;'>
						<img width='10' height='10' src='".get_url_icon('spacer.gif')."'/>
					</span>"
					.htmlentities($object->authorities_statut_label, ENT_QUOTES, $charset);
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&action=edit&id='.$object->id_authorities_statut;
	}
	
	protected function get_label_button_add() {
		global $msg;
		
		return $msg['115'];
	}
}