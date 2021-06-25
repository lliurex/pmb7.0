<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_opac_facettes_ui.class.php,v 1.1.2.2 2021/03/26 10:08:34 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_opac_facettes_ui extends list_configuration_opac_ui {
	
	protected static $facettes_model;
	
	protected $fields;
	
	public static function set_facettes_model($facettes_model) {
		static::$facettes_model = $facettes_model;
	}
	
	protected function get_fields() {
		if(!isset($this->fields)) {
			$this->fields = static::$facettes_model->fields_sort();
		}
		return $this->fields;
	}
	
	protected function _get_query_base() {
		$facettes_model = static::$facettes_model;
		return "SELECT id_facette as id, ".$facettes_model::$table_name.".* FROM ".$facettes_model::$table_name;
	}
	
	public function init_filters($filters=array()) {
		$this->filters = array(
				'type' => ''
		);
		parent::init_filters($filters);
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('facette_order');
	    $this->add_applied_sort('facette_name');
	}
	
	protected function get_main_fields_from_sub() {
		$main_fields = array(
				'facette_order' => 'facette_order',
				'facette_name' => 'intitule_vue_facette'
		);
		if ($this->filters['type'] == 'authperso') {
			$main_fields['facette_authperso'] = 'admin_authperso_form_name';
		}
		$main_fields['facette_critere'] = 'critP_vue_facette';
		$main_fields['facette_ss_critere'] = 'ssCrit_vue_facette';
		$main_fields['facette_nb_result'] = 'nbRslt_vue_facette';
		$main_fields['facette_type_sort'] = 'sort_view_facette';
		$main_fields['facette_visible_gestion'] = 'facettes_admin_visible_gestion';
		$main_fields['facette_visible'] = 'visible_facette';
		return $main_fields;
	}
	
	protected function init_no_sortable_columns() {
		$this->no_sortable_columns = array(
				'facette_order', 'facette_name', 'facette_authperso',
				'facette_critere', 'facette_ss_critere', 'facette_nb_result',
				'facette_type_sort', 'facette_visible_gestion', 'facette_visible'
		);
	}
	
	protected function _get_query_filters() {
		$filter_query = '';
		
		$this->set_filters_from_form();
		
		$filters = array();
		$filters [] = 'facette_type LIKE "'.$this->filters['type'].'%"';
		if(count($filters)) {
			$filter_query .= ' where '.implode(' and ', $filters);
		}
		return $filter_query;
	}
	
	protected function get_cell_content($object, $property) {
		global $msg, $charset;
		
		$content = '';
		switch($property) {
			case 'facette_order':
				$content .= "
					<img src='".get_url_icon('bottom-arrow.png')."' title='".htmlentities($msg['move_bottom_arrow'], ENT_QUOTES, $charset)."' alt='".htmlentities($msg['move_bottom_arrow'], ENT_QUOTES, $charset)."' onClick=\"document.location='".static::get_controller_url_base()."&action=down&id=".$object->id_facette."'\" style='cursor:pointer;'/>
					<img src='".get_url_icon('top-arrow.png')."' title='".htmlentities($msg['move_top_arrow'], ENT_QUOTES, $charset)."' alt='".htmlentities($msg['move_top_arrow'], ENT_QUOTES, $charset)."' onClick=\"document.location='".static::get_controller_url_base()."&action=up&id=".$object->id_facette."'\" style='cursor:pointer;'/>
				";
				break;
			case 'facette_critere':
				$facette_critere = $object->facette_critere;
				if ($facette_critere > static::$facettes_model->get_authperso_start() && $this->filters['type'] != "authperso") {
					$authperso_query = "select authperso_name from authperso where id_authperso =".($facette_critere - $this->get_authperso_start());
					$authperso_result = pmb_mysql_query($authperso_query);
					if (pmb_mysql_num_rows($authperso_result)) {
						$authperso_row = pmb_mysql_fetch_object($authperso_result);
						$content .= $authperso_row->authperso_name;
					}
				} elseif ($this->filters['type'] == "authperso")  {
					$facette_critere = substr($facette_critere, 0, -4) . "0" . substr($facette_critere, 4);
					$authperso =  explode("_",$object->facette_type);
					$authperso_id = 0;
					if (!empty($authperso[1]) && intval($authperso[1])) {
						$authperso_id = $authperso[1];
					}
					$authperso_query = "select authperso_name from authperso where id_authperso =".$authperso_id;
					$authperso_result = pmb_mysql_query($authperso_query);
					if (pmb_mysql_num_rows($authperso_result)) {
						$authperso_row = pmb_mysql_fetch_object($authperso_result);
						$content .= $authperso_row->authperso_name;
					}
				} else {
					$content .= $this->get_fields()[$facette_critere];
				}
				break;
			case 'authperso':
				$content .= (count($this->get_fields()) > 1 ? htmlentities($this->get_fields()[$facette_critere], ENT_QUOTES, $charset) : $msg["admin_opac_facette_ss_critere"]);
				break;
			case 'facette_ss_critere':
				$array_subfields = static::$facettes_model->array_subfields($object->facette_critere);
				$content .= (count($array_subfields) > 1 ? htmlentities($array_subfields[$object->facette_ss_critere], ENT_QUOTES, $charset) : $msg["admin_opac_facette_ss_critere"]);
				break;
			case 'facette_nb_result':
				if ($object->facette_nb_result) {
					$content .= $object->facette_nb_result;
				} else {
					$content .= $msg["admin_opac_facette_illimite"];
				}
				break;
			case 'facette_type_sort':
				if ($object->facette_type_sort) {
					$content .= $msg['intit_gest_tri2'];
				} else {
					$content .= $msg['intit_gest_tri1'];
				}
				$content .= " ";
				if ($object->facette_order_sort) {
					$content .= $msg['intit_gest_tri4'];
				} else {
					$content .= $msg['intit_gest_tri3'];
				}
				break;
			case 'facette_visible_gestion':
			case 'facette_visible':
				$content .= $this->get_cell_visible_flag($object, $property);
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_button_order() {
		global $msg, $charset;
		
		return "<input class='bouton' type='button' value='".htmlentities($msg['facette_order_bt'], ENT_QUOTES, $charset)."' onClick=\"document.location='".static::get_controller_url_base()."&action=order';\" />";
	}
	
	public function get_display_list() {
		$display = parent::get_display_list();
		$display .= $this->get_button_order();
		return $display;
	}
	
	protected function get_label_button_add() {
		global $msg;
		
		return $msg['lib_nelle_facette_form'];
	}
	
	public static function get_controller_url_base() {
		return parent::get_controller_url_base()."&type=".static::$facettes_model->type;
	}
}