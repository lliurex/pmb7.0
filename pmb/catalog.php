<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: catalog.php,v 1.23.12.4 2021/03/23 08:48:51 dgoron Exp $

// définition du minimum nécéssaire
$base_path=".";
$base_auth = "CATALOGAGE_AUTH";
$base_title = "\$msg[6]";
$base_use_dojo = 1;
require_once ("$base_path/includes/init.inc.php");

// pour droit UNIQUE d'ajout de notices
if ((SESSrights & RESTRICTCATAL_AUTH) 
	&& ($categ!="create") 
	&& ($categ!="create_form") 
	&& ($categ!="update") 
	&& ($categ!="explnum_update") 
	&& ($categ!="explnum_create") 
	&& ($categ!="isbd") 
	) {
	$sub="";
	$categ="create";
	}

// modules propres à catalog.php ou à ses sous-modules
require_once($class_path."/modules/module_catalog.class.php");
require_once($class_path.'/interface/catalog/interface_catalog_form.class.php');
include("$include_path/templates/expl.tpl.php");
require("$include_path/templates/catalog.tpl.php");

print "<div id='att' style='z-Index:1000'></div>";

print $menu_bar;
print $extra;
print $extra2;
print $extra_info;
if ($use_shortcuts) include("$include_path/shortcuts/circ.sht");
	

if ($categ!="caddie") {
	print $catalog_layout;
	}

include("./catalog/catalog.inc.php");
print alert_sound_script();

print $catalog_layout_end;
print $footer;

// deconnection MYSql
pmb_mysql_close($dbh);
