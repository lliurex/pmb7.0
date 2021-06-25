<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.10.4.1 2021/02/09 07:30:28 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $quoi, $baseLink, $categ, $action;

require_once("$class_path/classementGen.class.php") ;

// inclusions principales
switch ($quoi) {
	case 'procs':
		include ("./catalog/caddie/gestion/procs.inc.php");
		break;
	case 'remote_procs':
		include ("./catalog/caddie/gestion/remote_procs.inc.php");
		break;
	case "classementGen" :
		$baseLink="./catalog.php?categ=caddie&sub=gestion&quoi=classementGen";
		$classementGen = new classementGen($categ,0);
		$classementGen->proceed($action);
		break;
	case 'panier':
	default:
		include ("./catalog/caddie/gestion/panier.inc.php");
		break;
	}
