<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: circ.php,v 1.25.14.3 2020/11/16 11:35:50 dgoron Exp $

// d�finition du minimum n�c�ssaire 
$base_path=".";                            
$base_auth = "CIRCULATION_AUTH";  
$base_title = "\$msg[5]";
$base_use_dojo = 1;
require_once ("$base_path/includes/init.inc.php");  

if ((SESSrights & RESTRICTCIRC_AUTH) && ($categ!="pret") && ($categ!="pretrestrict") ) {
	$sub="";
	$categ="";
}
// modules propres � circ.php ou � ses sous-modules
require_once($class_path."/modules/module_circ.class.php");
require_once("$include_path/templates/circ.tpl.php");
require_once("$include_path/templates/empr.tpl.php");
require_once("$include_path/templates/expl.tpl.php");

print "<div id='att' style='z-Index:1000'></div>";

	print $menu_bar;
	print $extra;
	print $extra2;
	print $extra_info;
	if($use_shortcuts) {
		include("$include_path/shortcuts/circ.sht");
	}

	print $circ_layout;

	include("./circ/main.inc.php");
	print alert_sound_script();
	

	print $circ_layout_end;
	print $footer;

pmb_mysql_close($dbh);
