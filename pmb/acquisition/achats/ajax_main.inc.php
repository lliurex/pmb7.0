<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_main.inc.php,v 1.6.6.1 2020/11/05 12:42:18 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $sub, $action, $id_entity, $id_cde, $ids_line, $object_type;

require_once($class_path."/thresholds.class.php");
require_once($class_path."/lignes_actes.class.php");

switch($sub){
	case 'recept':
		include("./acquisition/achats/receptions/ajax/ajax_receptions.inc.php");
		break;
	case 'thresholds':
		switch ($action) {
			case 'get_data':
				$thresholds = new thresholds($id_entity);
				print $thresholds->get_json_data();
				break;
		}
		break;
	case 'cmde':
		switch ($action) {
			case 'transfer_lines':
				lignes_actes::transfer_lines($id_cde,$ids_line);
				break;
			case 'duplicate_lines':
				lignes_actes::duplicate_lines($id_cde,$ids_line);
				break;
		}
		break;
	default:
		switch($action) {
			case "list":
				lists_controller::proceed_ajax($object_type, 'accounting');
				break;
		}
		break;		
}	
