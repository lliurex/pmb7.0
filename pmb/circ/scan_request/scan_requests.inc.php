<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: scan_requests.inc.php,v 1.2.2.1 2020/11/05 09:50:54 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

switch($action) {

	default:
		$list_scan_requests_ui = new list_scan_requests_ui();
		print $list_scan_requests_ui->get_display_list();
		break;
}
