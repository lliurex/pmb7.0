<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.8.32.1 2021/02/08 11:00:26 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

switch($sub) {
	case 'zbib':
		include("./admin/z3950/zbib.inc.php");
		break;
	case 'zattr':
		include("./admin/z3950/zattr.inc.php");
		break;
	default:
		include("$include_path/messages/help/$lang/admin_z3950.txt");
		break;
	}

	