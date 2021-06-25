<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_demandes_ui.class.php,v 1.1.2.2 2021/01/13 11:21:07 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_demandes_ui extends list_configuration_ui {
		
	public function __construct($filters=array(), $pager=array(), $applied_sort=array()) {
		static::$module = 'admin';
		static::$categ = 'demandes';
		static::$sub = str_replace(array('list_configuration_demandes_', '_ui'), '', static::class);
		parent::__construct($filters, $pager, $applied_sort);
	}
}