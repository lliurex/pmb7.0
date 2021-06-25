<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: abo.inc.php,v 1.32.6.4 2020/10/06 07:06:14 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $msg, $id_bannette, $id_empr, $id;

require_once($class_path."/dsi/bannettes_abo_controller.class.php");

$nom_prenom_abo = '';
if ($id_empr) {
	$result_empr = pmb_mysql_query("select concat(ifnull(concat(empr_nom,' '),''),empr_prenom) as nom_prenom from empr where id_empr=$id_empr") ;
	$nom_prenom_abo = @ pmb_mysql_result($result_empr, '0', 'nom_prenom');
}

if ($nom_prenom_abo) print "<h1>".$msg['dsi_ban_abo']." : $nom_prenom_abo</h1>" ;
	else print "<h1>".$msg['dsi_ban_abo']."</h1>" ;

bannettes_abo_controller::set_id_empr($id_empr);
bannettes_abo_controller::proceed((!empty($id_bannette) ? $id_bannette : $id));

