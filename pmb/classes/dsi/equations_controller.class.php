<?php 
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: equations_controller.class.php,v 1.3.4.2 2020/11/05 09:50:52 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/equation.class.php");
require_once($class_path."/list/bannettes/list_equations_ui.class.php");

class equations_controller extends lists_controller {
	
	protected static $model_class_name = 'equation';
	protected static $list_ui_class_name = 'list_equations_ui';
	
	public static function proceed($id=0) {
		global $msg;
		global $suite;
		global $requete;
		global $proprio_equation;
		global $database_window_title;
		
		switch($suite) {
			case 'acces':
				$model_instance = static::get_model_instance($id);
				print $model_instance->show_form();
				break;
			case 'add':
				$model_instance = static::get_model_instance($id);
				print $model_instance->show_form();
				break;
			case 'transform':
				$model_instance = static::get_model_instance($id);
				if (!$id) {
					$model_instance->num_classement = 1;
					$model_instance->nom_equation = "";
					$model_instance->comment_equation = "";
					$model_instance->proprio_equation = 0;
				}
				$model_instance->requete = stripslashes($requete);
				print $model_instance->show_form();
				break;
			case 'delete':
				$model_instance = static::get_model_instance($id);
				$model_instance->delete();
				$list_ui_instance = static::get_list_ui_instance();
				print $list_ui_instance->get_display_list();
				break;
			case 'update':
				if(!isset($proprio_equation)) $proprio_equation = 0;
				$model_instance = static::get_model_instance($id);
				$model_instance->set_properties_from_form();
				$model_instance->save();
				$list_ui_instance = static::get_list_ui_instance(array('name' => $model_instance->nom_equation));
				print $list_ui_instance->get_display_list();
				break;
			case 'duplicate':
				$model_instance = static::get_model_instance($id);
				$model_instance->id_equation = 0;
				print $model_instance->show_form();
				break;
			case 'search':
				$list_ui_instance = static::get_list_ui_instance();
				print $list_ui_instance->get_display_list();
				break;
			default:
				echo window_title($database_window_title.$msg['dsi_menu_title']);
				parent::proceed($id);
				break;
		}
	}
}// end class
