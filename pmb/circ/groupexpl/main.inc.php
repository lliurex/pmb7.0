<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.1.20.1 2021/03/09 09:51:59 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $action, $msg, $id, $form_cb_expl;

require_once("$class_path/groupexpl.class.php");

switch($action) {
	case "form" :
		print "<h1>".$msg["groupexpl_submenu_list_title"]."</h1>";
		$groupexpl=new groupexpl($id);
		print $groupexpl->get_form();
	break;
	case "see_form" :
		$groupexpl=new groupexpl($id);
		print $groupexpl->get_see_form();
	break;
	case "raz_check" :
		$groupexpl=new groupexpl($id);
		$groupexpl->raz_check($form_cb_expl);
		print $groupexpl->get_see_form();
	break;
	case "do_check" :
		$groupexpl=new groupexpl($id);
		$groupexpl->do_check($form_cb_expl);
		print $groupexpl->get_see_form();
	break;
	case 'update':
	case 'save':
		$groupexpl=new groupexpl($id);
		$groupexpl->set_properties_from_form();
		$groupexpl->save();
		$groupexpls=new groupexpls();
		print $groupexpls->get_list();
	break;	
	case 'delete':
		groupexpl::delete($id);
		$groupexpls=new groupexpls();
		print $groupexpls->get_list();
	break;
	case "add_expl" :
		print "<h1>".$msg["groupexpl_submenu_list_title"]."</h1>";
		$groupexpl=new groupexpl($id);
		$groupexpl->add_expl($form_cb_expl);
		print $groupexpl->get_form();
	break;	
	case "search_expl" :		
		if($id=groupexpls::get_group_expl($form_cb_expl)){
			$groupexpl=new groupexpl($id);
			print $groupexpl->get_see_form();	
		}else{
			$groupexpls=new groupexpls();
			$groupexpls->set_error_message($msg["groupexpl_list_error_cb_not_in_group"]);
			print $groupexpls->get_list();		
		}
	break;
	case "del_expl" :
		print "<h1>".$msg["groupexpl_submenu_list_title"]."</h1>";
		$groupexpl=new groupexpl($id);
		$groupexpl->del_expl($form_cb_expl);
		print $groupexpl->get_form();
	break;		
	case "list" :
	default:
		$groupexpls=new groupexpls();
		print $groupexpls->get_list();
	break;
}

