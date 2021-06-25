<?php
// +-------------------------------------------------+
// ï¿½ 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: module_modelling.tpl.php,v 1.4.6.2 2020/11/16 09:31:35 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $pmb_contribution_area_activate, $module_modelling_menu_ontologies, $module_modelling_menu_contribution_area; 
global $module_modelling_menu_frbr, $msg, $categ, $sub;

//$module_modelling_menu_ontologies = création d'ontologies
$module_modelling_menu_ontologies ="
<h1>".$msg["admin_ontologies"]." <span>> !!menu_sous_rub!!</span></h1>
<div class=\"hmenu\">
	!!sub_tabs!!
	!!ontologies_menu!!
</div>";

$module_modelling_menu_contribution_area ="
<h1>".$msg["admin_menu_contribution_area"]." <span>> !!menu_sous_rub!!</span></h1>
<div class=\"hmenu\">
	!!sub_tabs!!
</div>";

$module_modelling_menu_frbr ="
<h1>".$msg["frbr"]." <span>> !!menu_sous_rub!!</span></h1>
<div class=\"hmenu\">
	!!sub_tabs!!
</div>";

?>