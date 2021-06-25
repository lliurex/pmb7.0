<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.1.26.1 2021/02/09 07:30:30 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// page de switch recherche notice

// inclusions principales

switch($section) {
	case "liste":
		// affichage de la liste des recherches en opac
		include("./admin/opac/search_persopac/liste.inc.php");
	break;	
	default :
		// affichage de la liste des recherches en opac
		include("./admin/opac/search_persopac/liste.inc.php");
	break;
}


