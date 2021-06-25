<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_main.inc.php,v 1.8.6.2 2020/11/05 12:32:36 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/facettes_controller.class.php");

switch ($sub) {
	case 'search_persopac':
		switch($action) {
			case "list":
				lists_controller::proceed_ajax($object_type, 'configuration/'.$categ);
				break;
		}
		break;
	default:
	    $is_external = false;
		if(!isset($type)) $type = 'notices';
		if('notices_externes' == $type) $is_external = true;
		$facettes_controller = new facettes_controller(0, $type, $is_external);
		$facettes_controller->proceed_ajax();
		break;
}	