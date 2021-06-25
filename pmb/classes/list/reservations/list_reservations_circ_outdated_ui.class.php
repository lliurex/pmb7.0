<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_reservations_circ_outdated_ui.class.php,v 1.1.2.2 2020/12/22 14:10:46 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_reservations_circ_outdated_ui extends list_reservations_circ_ui {
	
	protected function get_title() {
		global $msg, $sub;
		
		return "<h1>".$msg['resa_menu']." > ".$msg["resa_menu_liste_".$sub]."</h1>";
	}
}