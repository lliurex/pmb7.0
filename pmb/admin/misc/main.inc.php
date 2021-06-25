<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.10.6.2 2021/02/09 18:06:36 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

switch($sub) {
	case 'tables':
		include("./admin/misc/tables.inc.php");
		break;
	case 'mysql':
		include("./admin/misc/mysql.inc.php");
		break;
	case 'files':
		$module_admin = module_admin::get_instance();
		$module_admin->set_url_base($base_path.'/admin.php?categ='.$categ.'&sub='.$sub);
		$module_admin->proceed_misc();
		break;
	default:
		include("$include_path/messages/help/$lang/admin_misc.txt");
		break;
}
