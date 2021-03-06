<?php
// +-------------------------------------------------+
// ? 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serials.class.php,v 1.1.2.1 2020/06/17 09:08:48 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/notice.class.php");

/* ------------------------------------------------------------------------------------
        classe serial : classe de gestion des notices chapeau
--------------------------------------------------------------------------------------- */
class serial extends notice {
	
	// classe de la notice chapeau des p?riodiques
	
	public $serial_id       = 0;         // id de ce p?riodique

	// constructeur
	public function __construct($id=0) {
		$this->id = $id+0; //Propri?t? dans la classe notice
		$this->serial_id = $id+0;
	}
		    
	
} // fin d?finition classe

/* ------------------------------------------------------------------------------------
        classe bulletinage : classe de gestion des bulletinages
--------------------------------------------------------------------------------------- */
class bulletinage extends notice {
	public $bulletin_id      = 0 ;  		// id de ce bulletinage
	public $bulletin_notice  = 0 ;  		// id notice parent = id du p?riodique reli?
	public $serial_id = 0;					// id notice parent = id du p?riodique reli?
	
	// constructeur
	public function __construct($bulletin_id, $serial_id=0, $link_explnum='',$localisation=0,$make_display=true) {
		$this->bulletin_id = $bulletin_id+0;
		
		if($serial_id) {
			$this->bulletin_notice = $serial_id;
			$this->serial_id = $serial_id;
		}
		return $this->bulletin_id;
	}
	
	public static function get_notice_id_from_id($bulletin_id) {
	    $bulletin_id = intval($bulletin_id);
	    $query = "SELECT num_notice, bulletin_notice FROM bulletins WHERE bulletin_id = ".$bulletin_id;
	    $result = pmb_mysql_query($query);
	    $row = pmb_mysql_fetch_object($result);
	    if($row->num_notice) {
	        return $row->num_notice; // Notice de bulletin
	    } else {
	        return $row->bulletin_notice; // Notice de p?riodique
	    }
	}
} // fin d?finition classe

// mark dep

/* ------------------------------------------------------------------------------------
        classe analysis : classe de gestion des d?pouillements
--------------------------------------------------------------------------------------- */
class analysis extends notice {
	
	public $id_bulletinage		= 0;     // id du bulletinage contenant ce d?pouillement
	
	// constructeur
	public function __construct($analysis_id, $bul_id=0) {
		$this->id = $analysis_id+0;
		if ($bul_id) $this->id_bulletinage = $bul_id;
		
		return $this->id;
	}
} // fin d?finition classe