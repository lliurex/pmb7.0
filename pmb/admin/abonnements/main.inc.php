<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.4.6.1 2021/02/08 11:00:21 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

switch($sub) {
	case 'periodicite':
		include("./admin/abonnements/periodicite.inc.php");
		break;
	case 'status':
		include("./admin/abonnements/status.inc.php");
		break;
	default:
		include("$include_path/messages/help/$lang/admin_abonnements.txt");
		break;
}
