<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: classements.inc.php,v 1.9.6.2 2020/12/11 15:43:14 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $database_window_title, $msg, $id_classement;

require_once($class_path."/dsi/classements_controller.class.php") ;

echo window_title($database_window_title.$msg['dsi_menu_title']);
print "<h1>".$msg['dsi_opt_class']."</h1>" ;
classements_controller::proceed($id_classement);
