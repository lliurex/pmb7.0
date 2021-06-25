<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_main.inc.php,v 1.23.6.5 2020/11/05 10:25:09 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $categ, $action, $object_type, $class_path, $filters, $pager, $prefix, $option_visibilite, $sort_by, $sort_asc_desc, $plugin, $sub;
global $opac_search_universes_activate, $search_xml_file, $search_xml_file_full_path, $id_authperso;

//En fonction de $categ, il inclut les fichiers correspondants
switch($categ):
	case 'acces':
		include('./admin/acces/ajax/acces.inc.php');
		break;
	case 'req':
		include('./admin/proc/ajax/req.inc.php');
		break;
	case 'sync':
		include('./admin/connecteurs/in/dosync.php');
		break;
	case 'opac':
		include('./admin/opac/ajax_main.inc.php');
	break;	
	case 'harvest':
		include('./admin/harvest/ajax_main.inc.php');
	break;
	case 'dashboard' :
		include("./dashboard/ajax_main.inc.php");
		break;
	case 'nomenclature' :
		include("./admin/nomenclature/ajax_main.inc.php");
		break;
	case 'webdav' :
		include("./admin/connecteurs/out/webdav/ajax_main.inc.php");
		break;
	case 'connector' :
		include("./admin/connecteurs/ajax_main.inc.php");
		break;
	case 'custom_fields':
		switch ($action) {
			case 'list':
				if (!empty($object_type)) {
					$class_name = "list_$object_type";
					require_once "$class_path/list/custom_fields/$class_name.class.php";
					$filters = (!empty($filters) ? encoding_normalize::json_decode(stripslashes($filters), true) : array());
					$pager = (!empty($pager) ? encoding_normalize::json_decode(stripslashes($pager), true) : array());
					if ($class_name == 'list_custom_fields_custom_ui') {
					    $class_name::set_custom_prefixe($prefix);
					    $class_name::set_num_type($id_authperso);
					}
					$class_name::set_prefix($prefix);
					$class_name::set_option_visibilite(encoding_normalize::json_decode(urldecode(stripslashes($option_visibilite)), true));
					$instance_class_name = new $class_name($filters, $pager, array('by' => $sort_by, 'asc_desc' => (isset($sort_asc_desc) ? $sort_asc_desc : '')));
					print encoding_normalize::utf8_normalize($instance_class_name->get_display_header_list());
					print encoding_normalize::utf8_normalize($instance_class_name->get_display_content_list());
				}
				break;
		}
		break;
	case 'plugin' :
		$plugins = plugins::get_instance();
		$file = $plugins->proceed_ajax("admin",$plugin,$sub);
		if($file){
			include $file;
		}
		break;
	case 'cms':
		include('./admin/cms/ajax_main.inc.php');
		break;
	case 'planificateur':
		include("./admin/planificateur/ajax_main.inc.php");
		break;
	case 'search_universes':
		if ($opac_search_universes_activate) {
			include('./admin/search_universes/ajax_main.inc.php');
		}
		break;
	case 'param':
		include("./admin/param/ajax_main.inc.php");
		break;
	case 'extended_search' :
		require_once($class_path."/search.class.php");
		if(!isset($search_xml_file)) $search_xml_file = '';
		if(!isset($search_xml_file_full_path)) $search_xml_file_full_path = '';
		
		$sc=new search(true, $search_xml_file, $search_xml_file_full_path);
		$sc->proceed_ajax();
		break;
	case 'misc':
		require_once($class_path.'/modules/module_admin.class.php');
		$module_admin = new module_admin();
		$module_admin->proceed_ajax_misc();
		break;
	case 'docnum':
		switch($action) {
			case "list":
				lists_controller::proceed_ajax($object_type, 'configuration/explnum');
				break;
		}
		break;
	case 'contact_forms':
		switch($action) {
			case "list":
				lists_controller::proceed_ajax($object_type, 'contact_forms');
				break;
		}
		break;
	default:
		switch($action) {
			case "list":
				lists_controller::proceed_ajax($object_type, 'configuration/'.$categ);
				break;
		}
		break;		
endswitch;
