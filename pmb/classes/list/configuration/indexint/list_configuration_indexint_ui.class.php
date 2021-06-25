<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_indexint_ui.class.php,v 1.1.2.2 2021/03/15 09:11:51 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_indexint_ui extends list_configuration_ui {
		
	public function __construct($filters=array(), $pager=array(), $applied_sort=array()) {
		static::$module = 'autorites';
		static::$categ = 'indexint';
		static::$sub = str_replace(array('list_configuration_indexint_', '_ui'), '', static::class);
		parent::__construct($filters, $pager, $applied_sort);
	}
	
	
}