<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: account.php,v 1.77.2.5 2021/03/26 14:15:09 dgoron Exp $

global $base_path, $base_auth, $base_title, $base_use_dojo, $include_path, $class_path, $menu_bar, $extra2, $account_layout, $use_shortcuts;
global $extra, $extra_info, $footer;

// Définition du minimum nécéssaire 
$base_path = ".";
$base_auth = "PREF_AUTH|ADMINISTRATION_AUTH";
$base_title = "\$msg[933]";
$base_use_dojo = 1;

require_once "$base_path/includes/init.inc.php";
require_once "$class_path/modules/module_account.class.php";
require_once "$include_path/user_error.inc.php";
require_once "$base_path/admin/users/users_func.inc.php";
require_once "$class_path/user.class.php";

include "$include_path/account.inc.php";
include "$include_path/templates/account.tpl.php";

print "<div id='att' style='z-Index:1000'></div>";
print $menu_bar;
print $extra2;
print $account_layout;

if ($use_shortcuts) {
    include "$include_path/shortcuts/circ.sht";
}

require_once $class_path.'/autoloader.class.php';
$autoload = new autoloader();
$autoload->add_register("onto_class");

if(empty($categ)) {
	$categ = 'favorites';
}
$module_account = module_account::get_instance();
$module_account->set_url_base($base_path.'/account.php?categ='.$categ);
$module_account->set_object_id($id);
$module_account->proceed();


print "</div></div>";
print $extra;
print $extra_info;
print $footer;

pmb_mysql_close();