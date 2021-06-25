<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.5.8.1 2021/02/09 07:30:31 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

switch ($quelle) {
	case 'transfert':
		include ("./circ/caddie/action/transfert.inc.php");
		break;
	case 'export':
		include ("./circ/caddie/action/export.inc.php");
		break;
	case 'supprpanier':
		include ("./circ/caddie/action/supprpanier.inc.php");
		break;
	case 'supprbase':
		include ("./circ/caddie/action/supprbase.inc.php");
		break;
	case 'edition':
		include ("./circ/caddie/action/edition.inc.php");
		break;
	case 'selection':
		include ("./circ/caddie/action/selection.inc.php");
		break;
	case 'mailing':
		include ("./circ/caddie/action/mailing.inc.php");
		break;
	default:
		print "<br /><br /><b>".$msg["caddie_select_action"]."</b>" ;
		break;
	}
