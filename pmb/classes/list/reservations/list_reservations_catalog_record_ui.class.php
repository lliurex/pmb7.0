<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_reservations_catalog_record_ui.class.php,v 1.1.2.3 2020/11/05 09:50:52 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_reservations_catalog_record_ui extends list_reservations_catalog_ui {
		
	public static function get_controller_url_base() {
		global $base_path;
		
		return $base_path.'/catalog.php?categ=';
	}
}