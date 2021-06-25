<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.18.4.1 2021/02/09 07:30:30 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $quelle, $msg;
switch ($quelle) {
	case 'changebloc':
		break;
	case 'transfert':
		include ("./catalog/caddie/action/transfert.inc.php");
		break;
	case 'export':
		include ("./catalog/caddie/action/export.inc.php");
		break;
	case 'supprpanier':
		include ("./catalog/caddie/action/supprpanier.inc.php");
		break;
	case 'supprbase':
		include ("./catalog/caddie/action/supprbase.inc.php");
		break;
	case 'edition':
		include ("./catalog/caddie/action/edition.inc.php");
		break;
	case 'selection':
		include ("./catalog/caddie/action/selection.inc.php");
		break;
	case 'impr_cote':
		include ("./catalog/caddie/action/impr_cote.inc.php");
		break;
	case 'expdocnum':
		include ("./catalog/caddie/action/expdocnum.inc.php");
		break;
	case 'reindex':
		include ("./catalog/caddie/action/reindex.inc.php");
		break;
	case 'access_rights':
		include ("./catalog/caddie/action/access_rights.inc.php");
		break;
	case 'scan_request':
		include ("./catalog/caddie/action/scan_request.inc.php");
		break;
	case 'transfert_to_location':
		include ("./catalog/caddie/action/transfert_to_location.inc.php");
		break;
	default:
		print "<br /><br /><b>".$msg["caddie_select_action"]."</b>" ;
		break;
	}
