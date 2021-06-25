<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_ui.class.php,v 1.1.6.16 2021/03/05 07:38:23 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_ui extends list_ui {
	
	protected static $module;
	
	protected static $categ;
	
	protected static $sub;
	
	protected $separator_parameter;
	
	protected function add_separator_parameter($label_code) {
		global $msg;
		$this->separator_parameter = $msg[$label_code];
	}
	
	protected function get_parameter_id($type_param, $sstype_param) {
		$query = "SELECT id_param FROM parametres WHERE type_param='".addslashes($type_param)."' AND sstype_param='".addslashes($sstype_param)."'";
		return pmb_mysql_result(pmb_mysql_query($query), 0, 'id_param');
	}
	
	protected function get_parameter($type_param, $sstype_param, $label_code='', $values=array()) {
		global $msg;
		
		$parameter = array (
				"id" => $this->get_parameter_id($type_param, $sstype_param),
				"type_param" => $type_param,
				"sstype_param" => $sstype_param,
				"name" => $type_param."_".$sstype_param,
				"label" => (!empty($label_code) ? $msg[$label_code] : ''),
				"valeur_param" => $this->get_parameter_value($type_param."_".$sstype_param),
				"values" => $values,
				"section" => (!empty($this->separator_parameter) ? $this->separator_parameter : '')
		);
		return (object) $parameter;
	}
	
	protected function add_parameter($type_param, $sstype_param, $label_code='', $values=array()) {
		$this->add_object($this->get_parameter($type_param, $sstype_param, $label_code, $values));
	}
	
	protected function get_name_cell_edition($object, $property) {
		if($property == 'valeur_param') {
			return $this->objects_type."_".$object->name;
		} else {
			return parent::get_name_cell_edition($object, $property);
		}
	}
	protected function get_options_cell_edition($object, $property) {
		//on est sur un objet type paramètre
		if($property == 'valeur_param' && !empty($object->values)) {
			return $object->values;
		}
	}
	
	public function get_parameter_value($name) {
		global ${$name};
		return ${$name};
	}
	
	/**
	 * Initialisation de la pagination par défaut
	 */
	protected function init_default_pager() {
	    parent::init_default_pager();
	    $this->pager['nb_per_page'] = 100;
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
	
		$this->available_columns =
		array('main_fields' =>
				$this->get_main_fields_from_sub()
		);
	}
	
	protected function init_default_columns() {
		foreach ($this->available_columns['main_fields'] as $name=>$label) {
			$this->add_column($name);
		}
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'visible', false);
		$this->set_setting_display('search_form', 'export_icons', false);
		$this->set_setting_display('query', 'human', false);
		$this->set_setting_column('default', 'align', 'left');
	}
	
	/**
	 * Construction dynamique de la fonction JS de tri
	 */
	protected function get_js_sort_script_sort() {
		$display = parent::get_js_sort_script_sort();
		$display = str_replace('!!categ!!', static::$categ, $display);
		$display = str_replace('!!sub!!', static::$sub, $display);
		$display = str_replace('!!action!!', 'list', $display);
		return $display;
	}
	
	protected function get_cell_visible_flag($object, $property) {
		$method_name = "get_".$property;
		if (is_object($object) && !empty($object->{$property}) || (method_exists($object, $method_name) && !empty($object->{$method_name}()))) {
			return "<center>X</center>";
		} else {
			return "&nbsp;";
		}
	}
	
	/**
	 * Objet de la liste
	 */
	protected function get_display_content_object_list($object, $indice) {
		if(!isset($this->is_editable_object_list)) {
			$this->is_editable_object_list = true;
		}
		return parent::get_display_content_object_list($object, $indice);
	}
	
	protected function get_button_add() {
		global $charset;
	
		return "<input class='bouton' type='button' value='".htmlentities($this->get_label_button_add(), ENT_QUOTES, $charset)."' onClick=\"document.location='".static::get_controller_url_base()."&action=add';\" />";
	}
	
	public function get_display_list() {
		$display = parent::get_display_list();
		$display .= $this->get_button_add();
		return $display;
	}
	
	protected function save_parameter($object, $property) {
		$value = $this->get_value_from_cell_form($object, $property);
		
		$varGlobal = $object->name;
		global ${$varGlobal};
		//on modifie la valeur de l'objet
		$object->valeur_param = $value;
		//on enregistre dans la variable globale
		${$varGlobal} = $value;
		//puis dans la base
		$query = "UPDATE parametres SET valeur_param='".addslashes($value)."'
					WHERE type_param='".$object->type_param."' AND sstype_param='".$object->sstype_param."'";
		pmb_mysql_query($query);
	}
	
	protected function save_object_property($object, $property) {
		switch ($property) {
			case 'valeur_param':
				$this->save_parameter($object, $property);
				break;
		}
	}
	
	public static function get_controller_url_base() {
		global $base_path;
	
		return $base_path.'/'.static::$module.'.php?categ='.static::$categ.'&sub='.static::$sub;
	}
}