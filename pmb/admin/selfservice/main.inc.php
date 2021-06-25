<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.2.24.1 2021/02/08 11:00:28 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

switch($sub) {
	case 'pret':
		include('./admin/selfservice/pret.inc.php');
	break;
	case 'retour':
		include('./admin/selfservice/retour.inc.php');
	break;

	default:
		include("$include_path/messages/help/$lang/admin_selfservice.txt");
	break;
}
?>
