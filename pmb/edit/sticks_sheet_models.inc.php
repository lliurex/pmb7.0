<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sticks_sheet_models.inc.php,v 1.1.10.1 2021/02/01 13:39:05 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $id;

require_once($class_path."/sticks_sheet/sticks_sheets_controller.class.php");

sticks_sheets_controller::proceed($id);