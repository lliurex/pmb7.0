<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.3.8.1 2021/02/08 11:00:27 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

switch($sub) {
	case "type" :
		include("./admin/cms/editorial/types.inc.php");
		break;
	case 'publication_state':
		include("./admin/cms/editorial/publication_states.inc.php");
		break;
	default:
		include("$include_path/messages/help/$lang/admin_cms_editorial.txt");
		break;
}