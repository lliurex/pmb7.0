<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.6.32.1 2021/02/08 11:00:28 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

switch ($sub) {
	case 'lieux' :
		//Gestion des lieux
		include ("./admin/sauvegarde/lieux.inc.php");
		break;
	case 'tables' :
		//Gestion des groupes de tables
		include ("./admin/sauvegarde/tables.inc.php");
		break;
	case 'gestsauv' :
		//Gestion des sauvegardes
		include ("./admin/sauvegarde/sauvegardes.inc.php");
		break;
	case 'launch' :
		//Page de lancement d'une sauvegarde
		include("./admin/sauvegarde/launch.inc.php");
		break;
	case 'list' :
		//Page de gestion des sauvegardes déjà effectuées
		include("./admin/sauvegarde/sauvegarde_list.inc.php");
		break;

// ------------------ LLIUREX 21/02/2018-------------------------------		
// Ponemos los links en la pagina de administracion/copias de seguridad
	case 'lliurexp':
		//Lliurex modulo exportacion de toda la base de datos
		include("./copia_seg.php");
		break;

	case 'lliureximp':
		//Lliurex modulo exportacion de toda la base de datos
		include("./copia_seg_importa.php");
		break;
// -----------------FIN LLIUREX 21/02/2018-----------------------------
		
	default :
		//Page de gestion des sauvegardes déjà effectuées
		include("$include_path/messages/help/$lang/admin_sauvegarde.txt");
		break;
	}

?>
