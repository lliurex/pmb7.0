<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: update_notice.inc.php,v 1.117 2017/09/05 13:12:50 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if(!isset($forcage)) $forcage = 0;

require_once($class_path."/entities/entities_records_controller.class.php");
require_once($class_path."/parametres_perso.class.php");

$entities_records_controller = new entities_records_controller($id);
if($entities_records_controller->has_rights()) {
	// On a besoin de récupérer le tit1 sur forcage
	if ($forcage == 1) {
		$tab= unserialize(stripslashes($ret_url));
		foreach($tab->GET as $key => $val){
			if (get_magic_quotes_gpc())
				$GLOBALS[$key] = $val;
				else {
					add_sl($val);
					$GLOBALS[$key] = $val;
				}
		}
		foreach($tab->POST as $key => $val){
			if (get_magic_quotes_gpc())
				$GLOBALS[$key] = $val;
				else {
					add_sl($val);
					$GLOBALS[$key] = $val;
				}
		}
	}
	$p_perso=new parametres_perso("notices");
	$perso_=$p_perso->show_editable_fields($entities_records_controller->get_id());
	$error_convo=0;
	for ($i=0; $i<count($perso_["FIELDS"]); $i++) {
		$p=$perso_["FIELDS"][$i];
		$value=$p_perso->read_form_fields_perso($p["NAME"]);
		
		switch($p["NAME"]){
			case "Identificacion":
				switch($value){
					case "ISBN134":
						if(!isISBN13($f_cb)) {
							error_message_history($msg["notice_champs_perso"],$msg["notices_convo_isbn_error"] ,1);
							$error_convo=1;
						}
						break;
					case "ISBN103":
						if(!isISBN10($f_cb)) {
							error_message_history($msg["notice_champs_perso"],$msg["notices_convo_isbn_error"] ,1);
							$error_convo=1;
						}
						break;
					case "ISBN81":
						if(!isISSN($f_cb)) {
							error_message_history($msg["notice_champs_perso"],$msg["notices_convo_isbn_error"] ,1);
							$error_convo=1;
						}
						break;
					case "ISBN247":
						$code_reg = preg_replace('/-|\.| /', '', $f_cb);
						if (strlen($code_reg)!==26){
							error_message_history($msg["notice_champs_perso"],$msg["notices_convo_isbn_error"] ,1);
							$error_convo=1;
						} 
						break;
					case "OTROS":
						break;
				
				}
				break;
			case "Precio":
			
				if ($value!=""){
					if (is_numeric($value)){
						if ($value>999.99){
							error_message_history($msg["notice_champs_perso"],$msg["notices_convo_price_max_error"] ,1);
							$error_convo=1;
						}
					}else{
						error_message_history($msg["notice_champs_perso"],$msg["notices_convo_price_format_error"] ,1);
						$error_convo=1;
					}
				}	
				break;
		}
	}
	if ($error_convo==0){
//----- FIN LLIUREX-------
	
		$nberrors=$p_perso->check_submited_fields();
		$tit1 = clean_string($f_tit1);
		if(trim($tit1)&&(!$nberrors)) {
			$updated = $entities_records_controller->proceed_update();
			if($updated) {
				print $entities_records_controller->get_display_view($entities_records_controller->get_id());
			} else {
				// echec de la requete
				error_message('', $msg[281], 1, "./catalog.php");
			}
			error_message('', $notitle_message, 1, "./catalog.php");
		} else {
			if (!trim($tit1)) {
				// erreur : le champ tit1 est vide
				if($id) {
					$notitle_message = $msg[280];
				} else {
					$notitle_message = $msg[279];
				}
				error_message('', $notitle_message, 1, "./catalog.php");
			} else {
				error_message_history($msg["notice_champs_perso"],$p_perso->error_message,1);
			}
		}
	}
}
