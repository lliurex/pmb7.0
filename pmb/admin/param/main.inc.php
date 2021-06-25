<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.13.10.2 2021/02/09 18:06:37 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $id;

require_once('./admin/param/param_func.inc.php');
require_once('./classes/parameters/parameters_controller.class.php');

print "<div class='row'>";
		
parameters_controller::proceed($id);

print "</div>";
