<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: origins.class.php,v 1.3.8.1 2021/01/20 12:58:00 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/origin.class.php");
require_once($include_path."/templates/origin.tpl.php");


class origins {
	public $type;
	
	public function __construct(){
		//pas grand chose à faire
	}
	
}

class origins_authorities extends origins{
	
	public function __construct(){
		$this->type = "authorities";
	}	
}