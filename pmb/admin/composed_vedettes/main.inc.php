<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.2.6.1 2021/02/08 11:00:28 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

switch($sub) {
	case 'schemes' :
		include("./admin/composed_vedettes/schemes.inc.php");
		break;
	case 'grammars' :
	default:
		include("./admin/composed_vedettes/grammars.inc.php");
		break;
}