<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.3.8.1 2021/02/08 11:00:30 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

switch($sub) {
	case 'manager':
		include("./admin/planificateur/manager.inc.php");
		break;
	case 'reporting':
		include("./admin/planificateur/reporting.inc.php");
		break;
	default:
		include("$include_path/messages/help/$lang/admin_planificateur.txt");
		break;
}

