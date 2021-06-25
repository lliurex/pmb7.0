<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: entite.inc.php,v 1.33.4.3 2021/03/23 08:48:52 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $include_path, $msg;

//gestion des coordonnees des etablissements
require_once("$class_path/entites.class.php");
require_once("$include_path/templates/coordonnees.tpl.php");

function show_list_coord() {
	print list_configuration_acquisition_entite_ui::get_instance()->get_display_list();
}

function show_coord_form($id= 0) {
		
	global $msg;
	global $charset;
	global $coord_content_form, $coord_form_biblio, $coord_form_suite;
	global $ptab, $script;
	global $PMBuserid;
	
	$content_form = $coord_content_form;
	$content_form = str_replace('!!id!!', $id, $content_form);
	
	$interface_form = new interface_admin_form('coordform');
	if(!$id){
		$interface_form->set_label($msg['acquisition_ajout_biblio']);
	}else{
		$interface_form->set_label($msg['acquisition_modif_biblio']);
	}
	
	$ptab[1] = $ptab[1].$ptab[10].$ptab[11];
	$ptab[1] = str_replace('!!adresse!!', htmlentities($msg['acquisition_adr_fac'],ENT_QUOTES, $charset), $ptab[1]);
	$ptab[1] = str_replace('!!button_adr_fac!!', $ptab[12], $ptab[1]);

	if(!$id) {
		$content_form = str_replace('!!raison!!', '', $content_form);
		
		$content_form = str_replace('!!contact!!', $ptab[1], $content_form);
		$content_form = str_replace('!!max_coord!!', '2', $content_form);
		
		$content_form = str_replace('!!id1!!', '0', $content_form);
		$content_form = str_replace('!!lib_1!!', '', $content_form);		
		$content_form = str_replace('!!cta_1!!', '', $content_form);		
		$content_form = str_replace('!!ad1_1!!', '', $content_form);
		$content_form = str_replace('!!ad2_1!!', '', $content_form);
		$content_form = str_replace('!!cpo_1!!', '', $content_form);
		$content_form = str_replace('!!vil_1!!', '', $content_form);
		$content_form = str_replace('!!eta_1!!', '', $content_form);
		$content_form = str_replace('!!pay_1!!', '', $content_form);
		$content_form = str_replace('!!te1_1!!', '', $content_form);
		$content_form = str_replace('!!te2_1!!', '', $content_form);
		$content_form = str_replace('!!fax_1!!', '', $content_form);
		$content_form = str_replace('!!ema_1!!', '', $content_form);
		$content_form = str_replace('!!com_1!!', '', $content_form);
		$content_form = str_replace('!!id2!!', '0', $content_form);
		$content_form = str_replace('!!lib_2!!', '', $content_form);		
		$content_form = str_replace('!!cta_2!!', '', $content_form);		
		$content_form = str_replace('!!ad1_2!!', '', $content_form);
		$content_form = str_replace('!!ad2_2!!', '', $content_form);
		$content_form = str_replace('!!cpo_2!!', '', $content_form);
		$content_form = str_replace('!!vil_2!!', '', $content_form);
		$content_form = str_replace('!!eta_2!!', '', $content_form);
		$content_form = str_replace('!!pay_2!!', '', $content_form);
		$content_form = str_replace('!!te1_2!!', '', $content_form);
		$content_form = str_replace('!!te2_2!!', '', $content_form);
		$content_form = str_replace('!!fax_2!!', '', $content_form);
		$content_form = str_replace('!!ema_2!!', '', $content_form);
		$content_form = str_replace('!!com_2!!', '', $content_form);
		
		$content_form = str_replace('!!commentaires!!', '', $content_form);
		$content_form = str_replace('!!siret!!', '', $content_form);
		$content_form = str_replace('!!rcs!!', '', $content_form);
		$content_form = str_replace('!!naf!!', '', $content_form);
		$content_form = str_replace('!!tva!!', '', $content_form);
		$content_form = str_replace('!!site_web!!', '', $content_form);
		$content_form = str_replace('!!logo!!', '', $content_form);

		$content_form = autorisations($PMBuserid, $content_form);
	} else {
		$biblio = new entites($id);
	
		$content_form = str_replace('!!raison!!', htmlentities($biblio->raison_sociale,ENT_QUOTES, $charset), $content_form);

		$content_form = str_replace('!!contact!!', $ptab[1], $content_form);

		$row = pmb_mysql_fetch_object(entites::get_coordonnees($biblio->id_entite,'1'));
		$content_form = str_replace('!!id1!!', $row->id_contact, $content_form);
		$content_form = str_replace('!!lib_1!!', htmlentities($row->libelle,ENT_QUOTES,$charset), $content_form);		
		$content_form = str_replace('!!cta_1!!', htmlentities($row->contact,ENT_QUOTES,$charset), $content_form);		
		$content_form = str_replace('!!ad1_1!!', htmlentities($row->adr1,ENT_QUOTES,$charset), $content_form);
		$content_form = str_replace('!!ad2_1!!', htmlentities($row->adr2,ENT_QUOTES,$charset), $content_form);
		$content_form = str_replace('!!cpo_1!!', htmlentities($row->cp,ENT_QUOTES,$charset), $content_form);
		$content_form = str_replace('!!vil_1!!', htmlentities($row->ville,ENT_QUOTES,$charset), $content_form);
		$content_form = str_replace('!!eta_1!!', htmlentities($row->etat,ENT_QUOTES,$charset), $content_form);
		$content_form = str_replace('!!pay_1!!', htmlentities($row->pays,ENT_QUOTES,$charset), $content_form);
		$content_form = str_replace('!!te1_1!!', htmlentities($row->tel1,ENT_QUOTES,$charset), $content_form);
		$content_form = str_replace('!!te2_1!!', htmlentities($row->tel2,ENT_QUOTES,$charset), $content_form);
		$content_form = str_replace('!!fax_1!!', htmlentities($row->fax,ENT_QUOTES,$charset), $content_form);
		$content_form = str_replace('!!ema_1!!', htmlentities($row->email,ENT_QUOTES,$charset), $content_form);
		$content_form = str_replace('!!com_1!!', htmlentities($row->commentaires,ENT_QUOTES,$charset), $content_form);

		$row = pmb_mysql_fetch_object(entites::get_coordonnees($biblio->id_entite,'2'));
		$content_form = str_replace('!!id2!!', $row->id_contact, $content_form);
		$content_form = str_replace('!!lib_2!!', htmlentities($row->libelle,ENT_QUOTES,$charset), $content_form);		
		$content_form = str_replace('!!cta_2!!', htmlentities($row->contact,ENT_QUOTES,$charset), $content_form);		
		$content_form = str_replace('!!ad1_2!!', htmlentities($row->adr1,ENT_QUOTES,$charset), $content_form);
		$content_form = str_replace('!!ad2_2!!', htmlentities($row->adr2,ENT_QUOTES,$charset), $content_form);
		$content_form = str_replace('!!cpo_2!!', htmlentities($row->cp,ENT_QUOTES,$charset), $content_form);
		$content_form = str_replace('!!vil_2!!', htmlentities($row->ville,ENT_QUOTES,$charset), $content_form);
		$content_form = str_replace('!!eta_2!!', htmlentities($row->etat,ENT_QUOTES,$charset), $content_form);
		$content_form = str_replace('!!pay_2!!', htmlentities($row->pays,ENT_QUOTES,$charset), $content_form);
		$content_form = str_replace('!!te1_2!!', htmlentities($row->tel1,ENT_QUOTES,$charset), $content_form);
		$content_form = str_replace('!!te2_2!!', htmlentities($row->tel2,ENT_QUOTES,$charset), $content_form);
		$content_form = str_replace('!!fax_2!!', htmlentities($row->fax,ENT_QUOTES,$charset), $content_form);
		$content_form = str_replace('!!ema_2!!', htmlentities($row->email,ENT_QUOTES,$charset), $content_form);
		$content_form = str_replace('!!com_2!!', htmlentities($row->commentaires,ENT_QUOTES,$charset), $content_form);
		
		$liste_coord = entites::get_coordonnees($biblio->id_entite,'0');
		$content_form = str_replace('!!max_coord!!', (pmb_mysql_num_rows($liste_coord)+2), $content_form);
		$i=3;
		while ($row = pmb_mysql_fetch_object($liste_coord)) {
			
			$content_form = str_replace('<!--coord_repetables-->', $ptab[2].'<!--coord_repetables-->', $content_form);
			$content_form = str_replace('!!no_X!!', $i, $content_form);
			$i++;
			$content_form = str_replace('!!idX!!', $row->id_contact, $content_form);
			$content_form = str_replace('!!lib_X!!', htmlentities($row->libelle,ENT_QUOTES,$charset), $content_form);		
			$content_form = str_replace('!!cta_X!!', htmlentities($row->contact,ENT_QUOTES,$charset), $content_form);		
			$content_form = str_replace('!!ad1_X!!', htmlentities($row->adr1,ENT_QUOTES,$charset), $content_form);
			$content_form = str_replace('!!ad2_X!!', htmlentities($row->adr2,ENT_QUOTES,$charset), $content_form);
			$content_form = str_replace('!!cpo_X!!', htmlentities($row->cp,ENT_QUOTES,$charset), $content_form);
			$content_form = str_replace('!!vil_X!!', htmlentities($row->ville,ENT_QUOTES,$charset), $content_form);
			$content_form = str_replace('!!eta_X!!', htmlentities($row->etat,ENT_QUOTES,$charset), $content_form);
			$content_form = str_replace('!!pay_X!!', htmlentities($row->pays,ENT_QUOTES,$charset), $content_form);
			$content_form = str_replace('!!te1_X!!', htmlentities($row->tel1,ENT_QUOTES,$charset), $content_form);
			$content_form = str_replace('!!te2_X!!', htmlentities($row->tel2,ENT_QUOTES,$charset), $content_form);
			$content_form = str_replace('!!fax_X!!', htmlentities($row->fax,ENT_QUOTES,$charset), $content_form);
			$content_form = str_replace('!!ema_X!!', htmlentities($row->email,ENT_QUOTES,$charset), $content_form);
			$content_form = str_replace('!!com_X!!', htmlentities($row->commentaires,ENT_QUOTES,$charset), $content_form);				
		 
		}
								
		$content_form = str_replace('!!commentaires!!', htmlentities($biblio->commentaires,ENT_QUOTES, $charset), $content_form);
		$content_form = str_replace('!!siret!!', htmlentities($biblio->siret,ENT_QUOTES, $charset), $content_form);
		$content_form = str_replace('!!rcs!!', htmlentities($biblio->rcs,ENT_QUOTES, $charset), $content_form);
		$content_form = str_replace('!!naf!!', htmlentities($biblio->naf,ENT_QUOTES, $charset), $content_form);
		$content_form = str_replace('!!tva!!', htmlentities($biblio->tva,ENT_QUOTES, $charset), $content_form);
		$content_form = str_replace('!!site_web!!', htmlentities($biblio->site_web,ENT_QUOTES, $charset), $content_form);
		$content_form = str_replace('!!logo!!', htmlentities($biblio->logo,ENT_QUOTES, $charset), $content_form);
		
		$content_form = autorisations($biblio->autorisations, $content_form);
	}
	print $script;
	$interface_form->set_object_id($id)
	->set_confirm_delete_msg($msg['confirm_suppr_de']." ".(!empty($biblio) ? $biblio->raison_sociale : '')." ?")
	->set_content_form($content_form)
	->set_table_name('coordonnees')
	->set_field_focus('raison');
	print $interface_form->get_display();
}


function autorisations($autorisations='', $content_form) {
	global $charset;
	global $ptab;
	
	$id_check_list = '';
	$aut = explode(' ',$autorisations);
	
	//Récupération de la liste des utilisateurs
	$q = "SELECT userid, username FROM users order by username ";
	$r = pmb_mysql_query($q);

	while ($row = pmb_mysql_fetch_object($r)) {
			
		$content_form = str_replace('<!-- autorisations -->', $ptab[4].'<!-- autorisations -->', $content_form);
		
		$content_form = str_replace('!!user_name!!', htmlentities($row->username,ENT_QUOTES, $charset), $content_form);
		$content_form = str_replace('!!user_id!!', $row->userid, $content_form);
		if ($row->userid == 1 || in_array($row->userid, $aut)) {
			$chk = 'checked=\'checked\'';
			if ($row->userid == 1) $chk .= ' readonly onchange=\'this.checked = true;\'';
		} else {
			$chk = '';
		}
		$content_form = str_replace('!!checked!!', $chk, $content_form);

  		if($id_check_list)$id_check_list.='|';
  		$id_check_list.="user_aut[".$row->userid."]";
	}
	$content_form = str_replace('!!auto_id_list!!', $id_check_list, $content_form);
	return $content_form;
}

//Traitement des actions
switch($action) {
	case 'update':
		// vérification validité des données fournies.( pas deux raisons sociales identiques)
		$nbr = entites::exists_rs($raison,0,$id);
		if ($nbr > 0) {
			error_form_message($raison.$msg["acquisition_raison_already_used"]);
			break;
		} 

		$biblio = new entites($id);
		$biblio->type_entite = '1';
		$biblio->raison_sociale = $raison;
		$biblio->commentaires = $comment;
		$biblio->siret = $siret;
		$biblio->naf = $naf;
		$biblio->rcs = $rcs;
		$biblio->tva = $tva;
		$biblio->site_web = $site_web;
		$biblio->logo = $logo;
		
		if (is_array($user_aut)) {
			$biblio->autorisations = ' '.implode(' ',$user_aut).' ';
		} else $biblio->autorisations = ' 1 ';
		$biblio->save();
 
		if ($id) {
			//màj des autorisations dans les rubriques
			$biblio->majAutorisations();			
		}


		$id = $biblio->id_entite;
		
		for($i=1; $i <= $max_coord; $i++) {
			switch ($mod_[$i]) {
				case '1' :

					$coord = new coordonnees($no_[$i]); 
					$coord->num_entite = $id;
					if ($i == 1 || $i == 2) $coord->type_coord = $i; else $coord->type_coord = 0;
					$coord->libelle = $lib_[$i];
					$coord->contact = $cta_[$i];
					$coord->adr1 = $ad1_[$i];
					$coord->adr2 = $ad2_[$i];
					$coord->cp = $cpo_[$i];
					$coord->ville = $vil_[$i];
					$coord->etat = $eta_[$i];
					$coord->pays = $pay_[$i];
					$coord->tel1 = $te1_[$i];
					$coord->tel2 = $te2_[$i];
					$coord->fax = $fax_[$i];
					$coord->email = $ema_[$i];
					$coord->save();
					break;
					
				case '-1' : 
					if($no_[$i]) {
						$coord = new coordonnees($no_[$i]);
						$coord->delete($no_[$i]);
					}
					break;
					
				default :
					break;
				
			}
			
		} 
		show_list_coord();
		break;
	case 'add':
		show_coord_form();
		break;
	case 'modif':
		if (entites::exists($id)) {
			show_coord_form($id);
		} else {
			show_list_coord();
		}
		break;
	case 'del':
		if($id) {
			$total2 = entites::getNbFournisseurs($id);
			$total3 = entites::has_exercices($id);
			$total4 = entites::has_budgets($id);
			$total5 = entites::has_suggestions($id);
			$total7 = entites::has_actes($id,1);
			if (($total2+$total3+$total4+$total5+$total7)==0) {
				entites::delete($id);
				show_list_coord();
			} else {
				$msg_suppr_err = $msg['acquisition_entite_used'] ;
				if ($total2) $msg_suppr_err .= "<br />- ".$msg['acquisition_entite_used_fou'] ;
				if ($total3) $msg_suppr_err .= "<br />- ".$msg['acquisition_entite_used_exe'] ;
				if ($total4) $msg_suppr_err .= "<br />- ".$msg['acquisition_entite_used_bud'] ;
				if ($total5) $msg_suppr_err .= "<br />- ".$msg['acquisition_entite_used_sug'] ;
				if ($total7) $msg_suppr_err .= "<br />- ".$msg['acquisition_entite_used_act'] ;		
				
				error_message($msg[321], $msg_suppr_err, 1, 'admin.php?categ=acquisition&sub=entite');
			}
		} else {
			show_list_coord();
		}
		break;
	default:
		show_list_coord();
		break;
}

?>
