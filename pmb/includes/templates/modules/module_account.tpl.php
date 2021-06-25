<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: module_account.tpl.php,v 1.1.2.2 2020/11/23 09:11:18 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $module_account_menu_tabs, $msg;

$module_account_menu_tabs ="
<h1>".$msg["tabs"]." <span>> !!menu_sous_rub!!</span></h1>
<div class=\"hmenu\">
	!!sub_tabs!!
</div>";

?>