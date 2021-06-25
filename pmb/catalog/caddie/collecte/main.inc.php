<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.9.4.1 2021/02/09 07:30:30 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $moyen, $msg;

switch ($moyen) {
	case 'import':
		include ("./catalog/caddie/collecte/import.inc.php");
		break;
	case 'selection':
		include ("./catalog/caddie/collecte/selection.inc.php");
		break;
	case 'douchette':
		include ("./catalog/caddie/collecte/douchette.inc.php");
		break;
	default:
		print "<br /><br /><b>".$msg["caddie_select_collecte"]."</b>" ;
		break;
	}
