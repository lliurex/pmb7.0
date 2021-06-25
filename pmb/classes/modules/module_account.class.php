<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: module_account.class.php,v 1.1.2.2 2020/11/23 09:11:17 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($class_path."/modules/module.class.php");
require_once($include_path."/templates/modules/module_account.tpl.php");

class module_account extends module{
	
	public function proceed_favorites() {
		global $base_path;
		
		include "$base_path/account/favorites/favorites.inc.php";
	}
	
	public function proceed_lists() {
		global $action;
		global $object_type;
		
		switch($action){
			case 'edit':
				if(isset($object_type) && $object_type) {
					$list_ui_class_name = 'list_'.$object_type;
					$list_ui_instance = new $list_ui_class_name();
					print $list_ui_instance->get_default_dataset_form($this->object_id);
				}
				break;
			case 'save':
				if(isset($object_type) && $object_type) {
					$list_ui_class_name = 'list_'.$object_type;
					$list_ui_instance = new $list_ui_class_name();
					$list_model = new list_model($this->object_id);
					$list_model->set_num_user(0);
					$list_model->set_objects_type($list_ui_instance->get_objects_type());
					$list_model->set_list_ui($list_ui_instance);
					$list_model->set_properties_from_form();
					$list_model->save();
				}
				break;
			case 'delete':
				
				break;
			default :
				$list_lists_ui = new list_lists_ui();
				print $list_lists_ui->get_display_list();
				break;
		}
	}
	
	public function get_menu_tabs() {
		global $module_account_menu_tabs;
		global $sub;
		
		$menu = $module_account_menu_tabs;
		
		$list_modules_ui = new list_modules_ui();
		$objects = $list_modules_ui->get_objects();
		foreach ($objects as $object) {
			$this->add_sub_tab($object->name, $object->label);
			if($sub == $object->name) {
				$menu = str_replace('!!menu_sous_rub!!', $object->label, $menu);
			}
		}
		$menu = str_replace('!!sub_tabs!!', $this->get_sub_tabs(), $menu);
		return $menu;
	}
	
	public function proceed_tabs() {
		global $sub;
		
		if(empty($sub)) $sub ='admin';
		
		print $this->get_menu_tabs();
		
		$list_tabs_class_name = 'list_tabs_'.$sub.'_ui';
		$list_tabs_class_name::set_module_name($sub);
		$list_tabs_ui = new $list_tabs_class_name();
		print $list_tabs_ui->get_display_list();
	}
	
	public function proceed_modules() {
		$list_modules_ui = new list_modules_ui();
		print $list_modules_ui->get_display_list();
	}
	
	public function proceed_pdf() {
		$list_parameters_pdf_ui = new list_parameters_pdf_ui();
		print $list_parameters_pdf_ui->get_display_list();
	}
	
	public function proceed_mail() {
		$list_parameters_mail_ui = new list_parameters_mail_ui();
		print $list_parameters_mail_ui->get_display_list();
	}
	
	public function proceed_translations() {
		$list_translations_ui = new list_translations_ui();
		print $list_translations_ui->get_display_list();
	}
}