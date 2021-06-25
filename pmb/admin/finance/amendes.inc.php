<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: amendes.inc.php,v 1.6.8.3 2021/03/12 13:24:41 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $include_path, $msg, $charset, $action, $lang;
global $pmb_gestion_amende;
global $quota, $elements;
global $amende_jour, $amende_delai, $amende_delai_recouvrement, $amende_frais_recouvrement;
global $amende_max, $amende_1_2, $amende_2_3;
global $finance_amende_jour, $finance_delai_avant_amende, $finance_delai_recouvrement;
global $finance_frais_recouvrement, $finance_amende_maximum, $finance_delai_1_2, $finance_delai_2_3;
global $finance_amende_content_form;

//Gestion des amendes
require_once("$include_path/templates/finance.tpl.php");
require_once($class_path."/quotas.class.php");
require_once($class_path."/parameters/parameter.class.php");

function show_amende_parameters() {
	global $msg;
	global $charset;
	global $finance_amende_jour,$finance_delai_avant_amende,$finance_delai_recouvrement,$finance_amende_maximum,$finance_delai_1_2,$finance_delai_2_3;
	print "
		<div class='row'>
			<div class='colonne3' style='text-align:right;padding-right:10px'>".$msg["finance_amende_mnt"]."</div><div class='colonne3'>$finance_amende_jour</div><div class='colonne_suite'>&nbsp;</div>
		</div>
		<div class='row'>
			<div class='colonne3' style='text-align:right;padding-right:10px'>".$msg["finance_amende_delai"]."</div><div class='colonne3'>$finance_delai_avant_amende</div><div class='colonne_suite'>&nbsp;</div>
		</div>
		<div class='row'>
			<div class='colonne3' style='text-align:right;padding-right:10px'>".$msg["finance_amende_delai_1_2"]."</div><div class='colonne3'>$finance_delai_1_2</div><div class='colonne_suite'>&nbsp;</div>
		</div>
		<div class='row'>
			<div class='colonne3' style='text-align:right;padding-right:10px'>".$msg["finance_amende_delai_2_3"]."</div><div class='colonne3'>$finance_delai_2_3</div><div class='colonne_suite'>&nbsp;</div>
		</div>
		<div class='row'>
			<div class='colonne3' style='text-align:right;padding-right:10px'>".$msg["finance_amende_delai_recouvrement"]."</div><div class='colonne3'>$finance_delai_recouvrement</div><div class='colonne_suite'>&nbsp;</div>
		</div>
		<div class='row'>
			<div class='colonne3' style='text-align:right;padding-right:10px'>".$msg["finance_amende_max"]."</div><div class='colonne3'>$finance_amende_maximum</div><div class='colonne_suite'>&nbsp;</div>
		</div>
		<div class='row'></div>
		<div class='row'><input type='button' class='bouton' value='".$msg["finance_amende_modifier"]."' onClick=\"document.location='./admin.php?categ=finance&sub=amendes&action=modif';\"></div>
	";
}

if ($pmb_gestion_amende==1) {
	$admin_layout = str_replace('!!menu_sous_rub!!', $msg["finance_amendes"], $admin_layout);  
    print $admin_layout;
	switch ($action) {
		case 'update':
			//Mise � jour !!
			parameter::update('finance', 'amende_jour', stripslashes($amende_jour));
			parameter::update('finance', 'delai_avant_amende', stripslashes($amende_delai));
			parameter::update('finance', 'delai_recouvrement', stripslashes($amende_delai_recouvrement));
			parameter::update('finance', 'amende_maximum', stripslashes($amende_max));
			parameter::update('finance', 'delai_1_2', stripslashes($amende_1_2));
			parameter::update('finance', 'delai_2_3', stripslashes($amende_2_3));
			show_amende_parameters();
			break;
		case 'modif':
			//Formulaire de mise � jour
			$interface_form = new interface_admin_form('finance_amende_form');
			$interface_form->set_label($msg["finance_amende_parameters"]);
			$content_form=$finance_amende_content_form;
			$content_form=str_replace("!!amende_jour!!",htmlentities($finance_amende_jour,ENT_QUOTES,$charset),$content_form);
			$content_form=str_replace("!!amende_delai!!",htmlentities($finance_delai_avant_amende,ENT_QUOTES,$charset),$content_form);
			$content_form=str_replace("!!amende_delai_recouvrement!!",htmlentities($finance_delai_recouvrement,ENT_QUOTES,$charset),$content_form);
			$content_form=str_replace("!!amende_max!!",htmlentities($finance_amende_maximum,ENT_QUOTES,$charset),$content_form);
			$content_form=str_replace("!!amende_1_2!!",htmlentities($finance_delai_1_2,ENT_QUOTES,$charset),$content_form);
			$content_form=str_replace("!!amende_2_3!!",htmlentities($finance_delai_2_3,ENT_QUOTES,$charset),$content_form);
			$interface_form->set_content_form($content_form);
			print $interface_form->get_display_parameters();
			break;
		default:
			//Gestion simple
			show_amende_parameters();
			break;
	}
} else {
	$menu_sous_rub=$msg["finance_amendes"];
	
	//Gestion par quotas
	$descriptor = "$include_path/quotas/own/$lang/finances.xml";
	if ($quota) $qt=new quota($quota,$descriptor); else quota::parse_quotas($descriptor);
	$admin_menu_quotas="";
	$_quotas_types_ = quota::$_quotas_[$descriptor]['_types_'];
	for ($i=0; $i<count($_quotas_types_); $i++) {	
		if ($_quotas_types_[$i]["FILTER_ID"]=="amende") {
			$admin_menu_quotas.="<span><a href='./admin.php?categ=finance&sub=amendes&quota=".$_quotas_types_[$i]["ID"]."'>".$_quotas_types_[$i]["SHORT_COMMENT"]."</a></span>\n";
			if ($quota==$_quotas_types_[$i]["ID"]) {
				$menu_sous_rub.=" > ".$_quotas_types_[$i]["SHORT_COMMENT"];
				if ($elements) $menu_sous_rub.=" > ".$qt->get_title_by_elements_id($elements);
			}
		}
	}
	$admin_layout = str_replace('!!menu_sous_rub!!', $menu_sous_rub, $admin_layout);  
    print $admin_layout;
	print "<div class='row'>".$admin_menu_quotas."</div><div class='row'>&nbsp;</div>";	
	
	switch ($quota) {
		case "":
			break;
		default:
			if (!$elements) {
				$query_compl="&quota=$quota";
				include("./admin/quotas/quotas_list.inc.php");
			} else {
				$query_compl="&quota=$quota";
				include("./admin/quotas/quota_table.inc.php");
			}
			break;
	}
	
}

?>