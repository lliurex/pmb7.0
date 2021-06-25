<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.12.4.1 2021/02/09 07:30:31 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $moyen, $msg;

switch ($moyen) {
	case 'raz':
		include ("./catalog/caddie/pointage/raz.inc.php");
		break;
	case 'selection':
		include ("./catalog/caddie/pointage/selection.inc.php");
		break;
	case 'douchette':
		include ("./catalog/caddie/pointage/douchette.inc.php");
		break;
	case 'panier':
		include ("./catalog/caddie/pointage/panier.inc.php");
		break;
	case 'search_history':
		include ("./catalog/caddie/pointage/search_history.inc.php");
		break;
	default:
		print "<br /><br /><b>".$msg["caddie_select_pointage"]."</b>" ;
		break;
	}
