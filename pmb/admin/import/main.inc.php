<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.11.18.1 2021/02/08 11:00:27 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

switch($sub) {
	case 'import':
		include("./admin/import/import_expl.inc.php");
		break;
	case 'import_expl':
		include("./admin/import/import_expl.inc.php");
		break;
	case 'pointage_expl':
		include("./admin/import/pointage_expl.inc.php");
		break;
// ------------------ LLIUREX  21/02/2018 ----------------------------

	case 'import_reb':
		include("./admin/import/import_expl.inc.php");
		break;

	case 'import_abies':
		include("./importa_from_abies.php");
		break;

//--------------------FIN LLIUREX 		
	case 'import_skos':
		include("./admin/import/import_skos.inc.php");
		break;
	default:
		include("$include_path/messages/help/$lang/admin_import.txt");
		break;
}

?>
