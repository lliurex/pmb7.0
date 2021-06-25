<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pro.inc.php,v 1.64.2.1 2020/03/25 07:29:25 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $msg, $id_bannette;

require_once($class_path."/dsi/bannettes_controller.class.php");

print "<h1>".$msg['dsi_ban_pro']."</h1>" ;

bannettes_controller::proceed($id_bannette);
