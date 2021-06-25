<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: fichier.tpl.php,v 1.5.6.3 2021/02/12 22:33:53 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $fichier_layout, $current_module, $fichier_layout_end;
global $fichier_menu;

$module_fichier = module_fichier::get_instance();
$fichier_menu = $module_fichier->get_left_menu();

// $fichier_layout : layout page fichier
$fichier_layout = "
<div id='conteneur' class='$current_module'>
$fichier_menu
<div id='contenu'>
!!menu_contextuel!!
";


// $fichier_layout_end : layout page fichier (fin)
$fichier_layout_end = "
</div>
</div>
";

