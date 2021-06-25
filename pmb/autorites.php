<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: autorites.php,v 1.15.8.5 2021/03/15 09:11:51 dgoron Exp $

// définition du minimum nécéssaire 
$base_path=".";                            
$base_auth = "AUTORITES_AUTH";  
$base_title = "\$msg[132]";    
$base_use_dojo = 1;
require_once ("$base_path/includes/init.inc.php");

require_once($class_path."/authperso.class.php");

// modules propres à autorites.php ou à ses sous-modules
require_once($class_path."/modules/module_autorites.class.php");
require_once($class_path.'/interface/autorites/interface_autorites_form.class.php');
require("$include_path/templates/autorites.tpl.php");
print "<div id='att' style='z-Index:1000'></div>";

print $menu_bar;
print $extra;
print $extra2;
print $extra_info;
if($use_shortcuts) {
	include("$include_path/shortcuts/circ.sht");
}

if($categ != 'caddie') {
	print $autorites_layout;
}

include("./autorites/autorites.inc.php");

print $autorites_layout_end;

// pied de page
print $footer;

// deconnection MYSql
pmb_mysql_close();
