<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_lists_ui.class.php,v 1.1.2.5 2021/03/26 14:06:15 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/list/bannettes/list_classements_ui.class.php");
require_once($class_path."/list/bannettes/list_equations_ui.class.php");

class list_lists_ui extends list_ui {
	
	protected static $list_objects_type;
	
	protected function get_list_directory() {
		global $class_path;
		return $class_path."/list/";
	}
	
	protected function fetch_data() {
		$this->objects = array();
		list_ui::set_without_data(true);
		if (file_exists($this->get_list_directory()."catalog_subst.xml")) {
			$filename = $this->get_list_directory()."catalog_subst.xml";
		} else {
			$filename = $this->get_list_directory()."catalog.xml";
		}
		$xml=file_get_contents($filename);
		$directories_lists=_parser_text_no_function_($xml,"LISTS",$filename);
		if(!empty($directories_lists)) {
			foreach ($directories_lists as $directory_lists) {
				foreach ($directory_lists as $lists) {
					$directory_path = $lists['PATH'][0]['value'];
					$directory_label = $lists['LABEL'][0]['value'];
					foreach ($lists['LIST'] as $list) {
						$object = new stdClass();
						$object->directory_path = $directory_path;
						$object->directory_label = get_msg_to_display($directory_label);
						$object->name = $list['NAME'][0]['value'];
						$object->type = $list['NAME'][0]['value']."_ui";
						$object->label = get_msg_to_display($list['LABEL'][0]['value']);
						$object->class_name = "list_".$list['NAME'][0]['value']."_ui";
						if(!empty($list['PROPERTIES'])) {
							$class_name = $object->class_name;
							foreach ($list['PROPERTIES'][0] as $name=>$property) {
								$setter = "set_".strtolower($name);
								$class_name::$setter($property[0]['value']);
							}
						}
						$object->num_dataset = list_model::get_num_dataset_common_list($object->type);
						$object->instance = new $object->class_name();
						$this->add_object($object);
					}
				}
			}
		}
		list_ui::set_without_data(false);
		$this->messages = "";
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
						'label' => 'list_label',
						'default_selected_filters' => 'filters',
						'default_selected_columns' => 'list_ui_options_selected_columns',
						'default_applied_sort' => 'list_applied_sort',
						'default_pager' => 'list_pager',
						'default_applied_group' => 'list_ui_options_group_by',
				)
		);
		$this->available_columns['custom_fields'] = array();
	}
	
	public function init_applied_group($applied_group=array()) {
		$this->applied_group = array(0 => 'directory_label');
	}
	
	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
		$this->add_applied_sort('label', 'asc');
	}
	
	protected function init_no_sortable_columns() {
		$this->no_sortable_columns = array(
				'default_selected_filters', 'default_selected_columns', 'default_applied_sort',
				'default_pager', 'default_applied_group'
		);
	}
	
	protected function init_default_pager() {
		parent::init_default_pager();
		$this->pager['nb_per_page'] = 1000; //Illimité;
		$this->set_pager_in_session();
	}
	
	/**
	 * Fonction de callback
	 * @param $a
	 * @param $b
	 */
	protected function _compare_objects($a, $b) {
		$sort_by = $this->applied_sort[0]['by'];
		switch ($sort_by) {
			
			default:
				return parent::_compare_objects($a, $b);
		}
	}
	
	protected function init_default_columns() {
		
		$this->add_column('label');
		$this->add_column('default_selected_filters');
		$this->add_column('default_selected_columns');
		$this->add_column('default_applied_sort');
		$this->add_column('default_pager');
		$this->add_column('default_applied_group');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'visible', false);
		$this->set_setting_display('search_form', 'export_icons', false);
		$this->set_setting_display('query', 'human', false);
		$this->set_setting_column('default', 'align', 'left');
		$this->settings['objects']['default']['display_mode'] = 'expandable_table';
		$this->settings['grouped_objects']['level_1']['display_mode'] = 'expandable_table';
	}
	
	protected function get_js_sort_script_sort() {
		$display = parent::get_js_sort_script_sort();
		$display = str_replace('!!categ!!', 'lists', $display);
		$display = str_replace('!!sub!!', '', $display);
		$display = str_replace('!!action!!', 'list', $display);
		return $display;
	}
	
	protected function get_cell_content($object, $property) {
		global $msg;
		
		$content = '';
		switch($property) {
		    case 'default_selected_filters' :
		    case 'default_selected_columns' :
		    	$method_name = str_replace('default_', 'get_', $property);
		    	$values = $object->instance->{$method_name}();
		    	$data = array();
		    	if(!empty($values)) {
		    		foreach ($values as $value) {
		    			if($value) {
		    				if(isset($msg[$value])) {
		    					$data[] = $msg[$value];
		    				} else {
		    					$data[] = $value;
		    				}
		    			}
		    		}
		    	}
		    	$content .= implode('<br />', $data);
		    	break;
		    case 'default_applied_sort' :
		    	$sorted_available_columns = $object->instance->get_sorted_available_columns();
		    	$method_name = str_replace('default_', 'get_', $property);
		    	$values = $object->instance->{$method_name}();
		    	if(!empty($values)) {
		    		foreach ($values as $i=>$value) {
		    			if($i > 0) {
		    				$content .= "<br />".$msg['list_ui_sort_by_then']." ";
		    			}
		    			if(!empty($sorted_available_columns[$value['by']])) {
		    				$content .= $sorted_available_columns[$value['by']];
		    			} else {
		    				$content .= $value['by'];
		    			}
		    			$content .= " ".$msg["list_applied_sort_".$value['asc_desc']];
		    		}
		    	}
		    	break;
		    case 'default_pager' :
		    	$method_name = str_replace('default_', 'get_', $property);
		    	$values = $object->instance->{$method_name}();
		    	$content .= $msg['per_page']." ".$values['nb_per_page'];
		    	break;
		    case 'default_applied_group' :
		    	$sorted_available_columns = $object->instance->get_sorted_available_columns();
		    	$method_name = str_replace('default_', 'get_', $property);
		    	$values = $object->instance->{$method_name}();
		    	if(!empty($values)) {
		    		foreach ($values as $i=>$value) {
		    			if($i > 0) {
		    				$content .= "<br />".$msg['list_ui_options_group_by_then']." ";
		    			}
		    			if(!empty($sorted_available_columns[$value])) {
		    				$content .= $sorted_available_columns[$value];
		    			} else {
		    				$content .= $value;
		    			}
		    		}
		    	}
		    	break;
		    	
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_display_cell($object, $property) {
		$attributes = array();
		$attributes['onclick'] = "window.location=\"".static::get_controller_url_base()."&action=edit&object_type=".$object->instance->get_objects_type()."&id=".$object->num_dataset."\"";
		$content = $this->get_cell_content($object, $property);
		$display = $this->get_display_format_cell($content, $property, $attributes);
		return $display;
	}
	
	protected function get_grouped_label($object, $property) {
		$grouped_label = '';
		switch($property) {
			default:
				$grouped_label = parent::get_grouped_label($object, $property);
				break;
		}
		return $grouped_label;
	}
	
	public static function set_list_objects_type($list_objects_type) {
		static::$list_objects_type = $list_objects_type;
	}
}