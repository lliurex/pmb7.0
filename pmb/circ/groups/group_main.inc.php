<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: group_main.inc.php,v 1.11.6.1 2021/03/08 13:29:51 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $include_path, $action, $msg, $charset;
global $group_header, $group_footer, $group_query, $groupID, $memberID;
global $empr_groupes_localises, $libelle_resp, $group_add_resp;

require_once("$class_path/group.class.php");
require_once("$include_path/templates/group.tpl.php");

print pmb_bidi($group_header);
$group_search = str_replace("!!group_query!!", htmlentities(stripslashes($group_query),ENT_QUOTES, $charset), $group_search );
if ($empr_groupes_localises) {
	$group_search = str_replace("!!group_combo!!", group::gen_combo_box_grp(), $group_search );
} else {
	$group_search = str_replace("!!group_combo!!", '', $group_search );
}

switch($action) {
	case 'create':
		// création d'un groupe
		$group = new group(0);
		print $group->get_form();
		break;
	case 'modify':
		// modification d'un groupe
		if($groupID) {
			$group = new group($groupID);
			print $group->get_form();
		}
		break;
	case 'update':
		if(!$libelle_resp) $respID = 0;
		$group = new group($groupID);
		$group->set_properties_form_form();
		$group->update();
		$group_add_resp = intval($group_add_resp);
		if ($respID && $group_add_resp) {
			$group->add_member($respID);
		}
		
		if ($group->id && $group->libelle) {
			$groupID = $group->id;
			include('./circ/groups/show_group.inc.php');
		} else {
			error_message($msg[919], $msg[923], 1, './circ.php?categ=groups');
		}
		break;
	case 'addmember':
		// ajout d'un membre
		if($groupID && $memberID) {
			$group = new group($groupID);
			$res = $group->add_member($memberID);
			if($res) {
				include('./circ/groups/show_group.inc.php');
			} else {
				error_message($msg[919], $msg[923], 1, './circ.php?categ=groups');
			}
		}
		break;
	case 'delmember':
		// suppression d'un membre
		if($groupID && $memberID) {
			$group = new group($groupID);
			$res = $group->del_member($memberID);
			if($res) {
				include('./circ/groups/show_group.inc.php');
			} else {
				error_message($msg[919], $msg[923], 1, './circ.php?categ=groups');
			}
		}
		break;
	case 'delgroup':
		// suppression d'un group
		group::delete($groupID);
		print pmb_bidi($group_search);
		break;
	case 'listgroups':
		// affichage résultat recherche
		$list_groups_ui = new list_groups_ui(array('name' => $group_query));
		if(count($list_groups_ui->get_objects()) == 1) {
			$objects = $list_groups_ui->get_objects();
			$groupID = $objects[0]->id;
			include('./circ/groups/show_group.inc.php');
		} else {
			print $list_groups_ui->get_display_list();
		}
		break;
	case 'showgroup':
		// affichage des membres d'un groupe
		if ($groupID) require_once('./circ/groups/show_group.inc.php');
		break;
	case 'prolonggroup':
		// prolonger l'abonnement des membres d'un groupe
		if ($groupID) {
			$group = new group($groupID);
			$group->update_members();
			include('./circ/groups/show_group.inc.php');
		}
		break;
	case 'group_prolonge_pret':
		// prolonger l'abonnement des membres d'un groupe
		if ($groupID) {
			$group = new group($groupID);
			$group->pret_prolonge_members();			
			require_once('./circ/groups/show_group.inc.php');
		}
		break;
	case 'showcompte':
		// Transactions d'un groupe
		if ($groupID) {
			$group = new group($groupID);
			print $group->transactions_proceed();		
		}
		break;
	default:
		// action par défaut : affichage form de recherche
		print pmb_bidi($group_search);
		break;
}
print $group_footer;
