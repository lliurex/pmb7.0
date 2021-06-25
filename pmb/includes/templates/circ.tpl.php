<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: circ.tpl.php,v 1.56.2.3 2021/02/09 07:30:28 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

// $circ_menu : menu page circulation

global $categ, $circ_menu, $msg;
global $circ_layout, $circ_layout_end;

if ((SESSrights & RESTRICTCIRC_AUTH) && ($categ!="pret") && ($categ!="pretrestrict") ) {
	$circ_menu = '';
} else {
	$module_circ = module_circ::get_instance();
	$circ_menu = $module_circ->get_left_menu();
}

//	----------------------------------
// $circ_layout : layout page circulation

$circ_layout = "
<div id='conteneur' class='circ'>
$circ_menu
	<div id='contenu'>
";

//	----------------------------------
// $circ_layout_end : layout page circulation (fin)

$circ_layout_end = '
	</div>
</div>
';

