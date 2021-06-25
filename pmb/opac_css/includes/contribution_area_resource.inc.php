<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: contribution_area_resource.inc.php,v 1.3.6.2 2020/10/22 09:08:57 jlaurent Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $opac_contribution_area_activate, $allow_contribution, $class_path, $id, $type, $area_id;

if (!$opac_contribution_area_activate || !$allow_contribution) {
    die();
}

require_once($class_path.'/notice_affichage.class.php');
require_once($class_path.'/authority.class.php');
require_once($class_path.'/contribution_area/contribution_area_store.class.php');
require_once "$class_path/contribution_area/contribution_area.class.php";

$template = "";
if (!is_numeric($id)) {
    $contribution_area_store = new contribution_area_store();
    //on stocke l'id de l'entité en base SQL s'il existe
    $query = "select ?pmb_id where {
					<$id> pmb:identifier ?pmb_id
				}";
    $contribution_area_store->get_datastore()->query($query);
    if ($contribution_area_store->get_datastore()->num_rows()) {
        $id = $contribution_area_store->get_datastore()->get_result()[0]->pmb_id;
    }
}

if (!empty($type) && !empty($id) && is_numeric($id)) {
    $contribution_area = new contribution_area($area_id);
    
    switch ($type) {
        case 'categories':
            $aut_table = AUT_TABLE_CATEG;
            $string_type = 'category';
            break;
        case 'authors':
            $aut_table = AUT_TABLE_AUTHORS;
            $string_type = 'author';
            break;
        case 'publishers':
            $aut_table = AUT_TABLE_PUBLISHERS;
            $string_type = 'publisher';
            break;
        case 'titres_uniformes':
            $aut_table = AUT_TABLE_TITRES_UNIFORMES;
            $string_type = 'titre_uniforme';
            break;
        case 'collections':
            $aut_table = AUT_TABLE_COLLECTIONS;
            $string_type = 'collection';
            break;
        case 'subcollections':
            $aut_table = AUT_TABLE_SUB_COLLECTIONS;
            $string_type = 'subcollection';
            break;
        case 'indexint':
            $aut_table = AUT_TABLE_INDEXINT;
            $string_type = 'indexint';
            break;
        case 'serie':
            $aut_table = AUT_TABLE_SERIES;
            $string_type = 'serie';
            break;
        case 'concepts':
            $aut_table = AUT_TABLE_CONCEPT;
            $string_type = 'concept';
            break;
        case 'notice':
            if (!empty($id)) {
                $template = record_display::get_display_in_contribution($id, $contribution_area->get_repo_template_records());
            }
            break;
        default:
            if(strpos($type,'authperso') !== false){
                $aut_table = AUT_TABLE_AUTHPERSO;
                $string_type = 'authperso';
            }
            break;
    }
    if ($type != 'notice') {
        $authority = new authority(0, $id, $aut_table);
        $template = $authority->get_display_in_contribution($string_type, $contribution_area->get_repo_template_authorities());
    }
}

print $template;