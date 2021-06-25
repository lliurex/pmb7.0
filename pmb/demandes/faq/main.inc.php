<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.4.6.1 2020/07/09 06:34:25 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $id;

$id = intval($id);

require_once($class_path."/demandes/faq_questions_controller.class.php");

faq_questions_controller::proceed($id);
?>