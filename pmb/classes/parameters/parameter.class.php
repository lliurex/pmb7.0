<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: parameter.class.php,v 1.1.2.2 2021/03/05 15:04:08 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class parameter {

	public function __construct() {
	}

	public static function update($type_param, $sstype_param, $valeur_param) {
		if(empty($type_param) || empty($sstype_param)) {
			return false;
		}
		$varGlobal = $type_param."_".$sstype_param;
		global ${$varGlobal};
		//on enregistre dans la variable globale
		${$varGlobal} = $valeur_param;
		//puis dans la base
		$query = "update parametres set valeur_param='".addslashes($valeur_param)."' where type_param='".$type_param."' and sstype_param='".$sstype_param."'";
		pmb_mysql_query($query);
	}
	
} /* fin de définition de la classe */


