<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: bul_update.inc.php,v 1.57.2.1 2021/03/18 08:36:09 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $msg, $charset;
global $bul_id, $serial_id, $gestion_acces_active, $gestion_acces_user_notice, $pmb_synchro_rdf, $PMBuserid;
global $serial_header, $current_module, $id_form;

require_once($class_path."/authperso_notice.class.php");
require_once($class_path."/vedette/vedette_composee.class.php");
require_once($class_path."/vedette/vedette_link.class.php");
require_once($class_path."/notice_relations.class.php");
require_once($class_path."/notice_relations_collection.class.php");
require_once($class_path."/thumbnail.class.php");
require_once($class_path."/serials.class.php");
require_once($class_path."/indexation_stack.class.php");

if($gestion_acces_active==1) {
	require_once("$class_path/acces.class.php");
	$ac= new acces();
}

require_once($class_path."/index_concept.class.php");

//verification des droits de modification notice
$acces_m=1;
if ($gestion_acces_active==1 && $gestion_acces_user_notice==1) {
	$dom_1= $ac->setDomain(1);
	$acces_m = $dom_1->getRights($PMBuserid,$serial_id,8);
}

if ($acces_m==0) {
	
	if (!$bul_id) {
		error_message('', htmlentities($dom_1->getComment('mod_seri_error'), ENT_QUOTES, $charset), 1, '');
	} else {
		error_message('', htmlentities($dom_1->getComment('mod_bull_error'), ENT_QUOTES, $charset), 1, '');
	}
		
} else {

    // mise a jour de l'entete de page
 //----------------------LLIUREX----------------   
    $p_perso=new parametres_perso("notices");
	$perso_=$p_perso->show_editable_fields($bul_id);
	$error_convo=0;

	
	for ($i=0; $i<count($perso_["FIELDS"]); $i++) {
		$p=$perso_["FIELDS"][$i];
		$value=$p_perso->read_form_fields_perso($p["NAME"]);
		switch($p["NAME"]){
			case "Identificacion":
				switch($value){
					case "ISBN134":
						if(!isISBN13($bul_cb)) {
							error_message_history($msg["notice_champs_perso"],$msg["bul_convo_isbn_error"] ,1);
							$error_convo=1;
						}
						break;
					case "ISBN103":
						if(!isISBN10($bul_cb)) {
							error_message_history($msg["notice_champs_perso"],$msg["bul_convo_isbn_error"] ,1);
							$error_convo=1;
						}
						break;
					case "ISBN81":
						if(!isISSN($bul_cb)) {
							error_message_history($msg["notice_champs_perso"],$msg["bul_convo_isbn_error"] ,1);
							$error_convo=1;
						}
						break;
					case "ISBN247":
						$code_reg = preg_replace('/-|\.| /', '', $bul_cb);
						if (strlen($code_reg)!==26){
							error_message_history($msg["notice_champs_perso"],$msg["bul_convo_isbn_error"] ,1);
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

	//-----------------FIN LLIUREX---------------		
	    echo str_replace('!!page_title!!', $msg[4000].$msg[1003].$msg['catalog_serie_modif_bull'], $serial_header);
	    
	    if($pmb_synchro_rdf){
		require_once($class_path."/synchro_rdf.class.php");
	    }
	    
	    $myBulletinage = new bulletinage($bul_id, $serial_id);
	    $myBulletinage->set_properties_from_form();
	    $saved = $myBulletinage->save();
		if($saved) {
			print "<div class='row'><div class='msg-perio'>".$msg["maj_encours"]."</div></div>";
			$retour = bulletinage::get_permalink($saved);
			print "
				<form class='form-$current_module' name=\"dummy\" method=\"post\" action=\"$retour\" style=\"display:none\">
					<input type=\"hidden\" name=\"id_form\" value=\"$id_form\">
				</form>
				<script type=\"text/javascript\">document.dummy.submit();</script>
				";
		} else {
			error_message($msg['catalog_serie_modif_bull'] , $msg['catalog_serie_modif_bull_imp'], 1, serial::get_permalink($serial_id));
		}
   	 }	

}
