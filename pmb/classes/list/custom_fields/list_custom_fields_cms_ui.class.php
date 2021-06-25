<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_custom_fields_cms_ui.class.php,v 1.1.6.2 2020/11/05 12:32:36 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_custom_fields_cms_ui extends list_custom_fields_ui {
		
	protected static $num_type;
	
	public static function set_num_type($num_type) {
		static::$num_type = $num_type;
	}
	
	protected function _get_query_base() {
		$query = parent::_get_query_base();
		$query .= " where num_type = ".static::$num_type;
		return $query;
	}
	
	public static function get_controller_url_base() {
		global $base_path;
		global $categ, $sub, $elem, $quoi;
		
		return $base_path.'/admin.php?categ='.$categ.'&sub='.$sub.'&elem='.$elem.'&quoi='.$quoi.'&type_id='.static::$num_type;
	}
}