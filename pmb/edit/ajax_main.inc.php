<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_main.inc.php,v 1.12.6.5 2021/02/01 08:48:47 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $categ, $sub, $action;
global $object_type, $filters;

require_once($class_path.'/encoding_normalize.class.php');
require_once($class_path.'/pnb/pnb.class.php');
switch($categ){
	case "editions_state" :
		include("./edit/editions_state/ajax_main.inc.php");
		break;
	case 'dashboard' :
		include("./dashboard/ajax_main.inc.php");
		break;
	case 'pnb':
	    switch($action) {
	        case 'mailto':
	            $pnb = new pnb();
	            if(isset($commands_ids)){
	                $commands_ids = explode(',',$commands_ids);
	            }
	            print encoding_normalize::json_encode($pnb->get_mailto_data($commands_ids));
	            break;
	    }
	case 'transferts':
	case 'empr':
	case 'expl':
	case 'notices':
	case 'serials':
		switch($action) {
			case "list":
				$directory = $categ;
				switch($categ){
					case 'empr':
						$directory = 'readers';
						break;
					case 'expl':
						$directory = 'loans';
						break;
					case 'serials':
						$directory = 'records';
						break;
					case 'notices':
					    if($sub == 'resa_planning') {
					        $directory = 'resa_planning';
					    } else {
					        $directory = 'reservations';
					    }
						break;
				}
				//Les noms de filtres ont changé - on assure la rétro-compatibilité
				if($object_type == 'reservations_edition_treat_ui') {
					list_reservations_edition_treat_ui::set_globals_from_json_filters(stripslashes($filters));
				}
				lists_controller::proceed_ajax($object_type, $directory);
				break;
		}
		break;
	case 'campaigns' :
		require_once($class_path.'/campaigns/campaigns_controller.class.php');
		campaigns_controller::proceed_ajax($object_type);
		break;
	case 'plugin' :
		$plugins = plugins::get_instance();
		$file = $plugins->proceed_ajax("edit",$plugin,$sub);
		if($file){
			include $file;
		}
		break;
	default:
		switch($action) {
			case "list":
				lists_controller::proceed_ajax($object_type, 'configuration/'.$categ);
				break;
		}
		break;
}