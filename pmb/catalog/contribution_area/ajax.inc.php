<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax.inc.php,v 1.1.2.2 2020/11/05 12:49:43 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $sub, $action, $sort_by, $object_type;

switch($sub) {
	default:
		switch($action) {
		    case "list":
		        lists_controller::proceed_ajax($object_type, '');
				break;
			default:
			    print "";
				break;
		}
		break;
}