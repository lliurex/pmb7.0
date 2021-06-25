<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: edit.tpl.php,v 1.38.6.2 2020/11/16 13:59:35 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $edit_menu, $edit_layout, $current_module, $edit_layout_end;

// $edit_menu : menu page Editions
$module_edit = module_edit::get_instance();
$edit_menu = $module_edit->get_left_menu();

// $edit_layout : layout page edition
$edit_layout = "
<div id='conteneur' class='$current_module'>
$edit_menu
<div id='contenu'>";

// $edit_layout_end : layout page edition (fin)
$edit_layout_end = "</div></div>";
