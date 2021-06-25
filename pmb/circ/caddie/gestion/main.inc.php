<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.11.6.1 2021/02/09 07:30:30 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once("$class_path/classementGen.class.php") ;

if (empty($quoi)) $quoi = '';

switch ($quoi) {
	case 'razpointage':
		include ("./circ/caddie/gestion/pointage_raz.inc.php");
		break;
	case 'pointage':
		include ("./circ/caddie/gestion/pointage_selection.inc.php");
		break;
	case 'pointagebarcode':
		include ("./circ/caddie/gestion/pointage_barcode.inc.php");
		break;
	case 'selection':
		include ("./circ/caddie/gestion/collecte_selection.inc.php");
		break;
	case 'barcode':
		include ("./circ/caddie/gestion/collecte_barcode.inc.php");
		break;
	case 'procs':
		include ("./circ/caddie/gestion/procs.inc.php");
		break;
	case 'remote_procs':
		include ("./circ/caddie/gestion/remote_procs.inc.php");
		break;
	case "classementGen" :
		$baseLink="./circ.php?categ=caddie&sub=gestion&quoi=classementGen";
		$classementGen = new classementGen("empr_caddie",0);
		$classementGen->proceed($action);
		break;
	case 'pointagepanier':
		empr_caddie_controller::proceed_by_caddie($idemprcaddie);
		break;
	case 'panier':
	default:
		include ("./circ/caddie/gestion/panier.inc.php");
		break;
	}
