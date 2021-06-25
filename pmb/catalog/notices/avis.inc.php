<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: avis.inc.php,v 1.36.2.2 2021/02/09 07:30:29 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $quoifaire, $class_path, $include_path, $montrerquoi, $nb_per_page, $page, $debut, $valid_id_avis, $pmb_javascript_office_editor, $sub;
global $msg;

if (!isset($quoifaire)) $quoifaire = '';

// gestion des avis laisses par les lecteurs sur les notices

require_once "$class_path/avis_records.class.php";
require_once "$class_path/avis_articles.class.php";
require_once "$class_path/avis_sections.class.php";
require_once "$include_path/templates/avis.tpl.php";

if (!isset($montrerquoi)) $montrerquoi = '';
if (empty($montrerquoi)) $montrerquoi = 'novalid';
if (empty($nb_per_page)) $nb_per_page = 10;
if (!isset($page)) $page = 0;
if (empty($page)) $page = 1;
$debut = ($page - 1) * $nb_per_page;

if (!empty($pmb_javascript_office_editor)) print $pmb_javascript_office_editor;

$nb_avis = 0;
if (!empty($valid_id_avis)) {
    $nb_avis = count($valid_id_avis);
}

print module_catalog::get_instance()->get_display_subtabs();
switch ($sub) {
	case 'sections':
		if (SESSrights & CMS_AUTH) {
			switch ($quoifaire) {
				case 'valider':
				    for ($i = 0; $i < $nb_avis; $i++) {
						avis_sections::validate($valid_id_avis[$i]);
					}
					break;
				case 'invalider':
				    for ($i = 0; $i < $nb_avis; $i++) {
						avis_sections::unvalidate($valid_id_avis[$i]);
					}
					break;
				case 'supprimer':
				    for ($i = 0; $i < $nb_avis; $i++) {
						avis_sections::delete($valid_id_avis[$i]);
					}
					break;
				default:
					break;
			}
			$avis = new avis_sections();
			print $avis->get_display_list_form();
		}
		break;
	case 'articles':
		if (SESSrights & CMS_AUTH) {
			switch ($quoifaire) {
				case 'valider':
				    for ($i = 0; $i < $nb_avis; $i++) {
						avis_articles::validate($valid_id_avis[$i]);
					}
					break;
				case 'invalider':
				    for ($i = 0; $i < $nb_avis; $i++) {
						avis_articles::unvalidate($valid_id_avis[$i]);
					}
					break;
				case 'supprimer' :
				    for ($i = 0; $i < $nb_avis; $i++) {
						avis_articles::delete($valid_id_avis[$i]);
					}
					break;
				default:
					break;
			}
			$avis = new avis_articles();
			print $avis->get_display_list_form();
		}
		break;
	case 'records':
	default:
		switch ($quoifaire) {
			case 'valider':
			    for ($i = 0; $i < $nb_avis; $i++) {
					if (avis_records::check_records_edit_rights($valid_id_avis[$i])) {
						avis_records::validate($valid_id_avis[$i]);
					}
				}
				break;
			case 'invalider':
			    for ($i = 0; $i < $nb_avis; $i++) {
					if (avis_records::check_records_edit_rights($valid_id_avis[$i])) {
						avis_records::unvalidate($valid_id_avis[$i]);
					}
				}
				break;
			case 'supprimer' :
			    for ($i = 0; $i < $nb_avis; $i++) {
					if (avis_records::check_records_edit_rights($valid_id_avis[$i])) {
						avis_records::delete($valid_id_avis[$i]);
					}
				}
				break;
			default:
				break;
		}
		$avis = new avis_records();
		print $avis->get_display_list_form();
		break;
}
jscript_checkbox() ;
?>
