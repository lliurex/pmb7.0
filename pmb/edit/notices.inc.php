<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: notices.inc.php,v 1.29.4.2 2020/11/10 09:32:21 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $sub, $dest, $msg;
global $f_loc, $f_dispo_loc, $no_notice, $no_bulletin;

$no_notice = intval($no_notice);
$no_bulletin = intval($no_bulletin);

switch($sub) {
	case "resa_a_traiter" :
		//Les noms de filtres ont changé - on assure la rétro-compatibilité
		list_reservations_edition_treat_ui::set_globals_from_selected_filters();
		$list_reservations_edition_treat_ui = new list_reservations_edition_treat_ui(array('id_notice' => 0, 'id_bulletin' => 0, 'id_empr' => 0, 'f_loc' => $f_loc));
		switch($dest) {
			case "TABLEAU":
				$list_reservations_edition_treat_ui->get_display_spreadsheet_list();
				break;
			case "TABLEAUHTML":
				print $list_reservations_edition_treat_ui->get_display_html_list();
				break;
			default:
				print $list_reservations_edition_treat_ui->get_display_list();
				if (SESSrights & EDIT_AUTH) print pmb_bidi("<p class='message'><a href='./circ.php?categ=listeresa&sub=encours'>".$msg['lien_traiter_reservations']."</a></p>");
				break;
		}
		break;
	case "resa_planning" :
	    $list_resa_planning_edition_ui = new list_resa_planning_edition_ui();
	    print $list_resa_planning_edition_ui->get_display_list();
		break;
	case "resa" :
	default:
		$list_reservations_edition_ui = new list_reservations_edition_ui(array('id_notice' => 0, 'id_bulletin' => 0, 'id_empr' => 0));
		print $list_reservations_edition_ui->get_display_list();
		break;
	}
