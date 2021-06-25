<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: lettre-retard.inc.php,v 1.42.6.3 2020/10/07 13:29:55 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");
require_once($include_path."/sms.inc.php");

// popup d'impression PDF pour lettre retard de prêt
// reçoit : id_empr et éventuellement cb_doc
function get_texts($relance) {
	global $format_page,$marge_page_gauche, $marge_page_droite, $largeur_page;
	global $biblio_name, $biblio_phone, $biblio_email, $biblio_commentaire;
	
	// la marge gauche des pages
	$var = "pdflettreretard_".$relance."marge_page_gauche";
	global ${$var};
	$marge_page_gauche = ${$var};
	
	// la marge droite des pages
	$var = "pdflettreretard_".$relance."marge_page_droite";
	global ${$var};
	$marge_page_droite = ${$var};
	
	// la largeur des pages
	$var = "pdflettreretard_1largeur_page";
	global ${$var};
	$largeur_page = ${$var};
	
	// la hauteur des pages
	$var = "pdflettreretard_1hauteur_page";
	global ${$var};
	$hauteur_page = ${$var};
	
	// le format des pages
	$var = "pdflettreretard_1format_page";
	global ${$var};
	$format_page = ${$var};
} // fin function get_texts

function get_texts_group($relance) {
	global $format_page,$marge_page_gauche, $marge_page_droite, $largeur_page;
	global $biblio_name, $biblio_phone, $biblio_email, $biblio_commentaire;
	
	// la marge gauche des pages
	$var = "pdflettreretard_".$relance."marge_page_gauche";
	global ${$var};
	$marge_page_gauche = ${$var};
	
	// la marge droite des pages
	$var = "pdflettreretard_".$relance."marge_page_droite";
	global ${$var};
	$marge_page_droite = ${$var};
	
	// la largeur des pages
	$var = "pdflettreretard_1largeur_page";
	global ${$var};
	$largeur_page = ${$var};
	
	// la hauteur des pages
	$var = "pdflettreretard_1hauteur_page";
	global ${$var};
	$hauteur_page = ${$var};
	
	// le format des pages
	$var = "pdflettreretard_1format_page";
	global ${$var};
	$format_page = ${$var};
} // fin function get_texts_group

$largeur_page=$pdflettreretard_1largeur_page;
$hauteur_page=$pdflettreretard_1hauteur_page;

$taille_doc=array($largeur_page,$hauteur_page);

$format_page=$pdflettreretard_1format_page;

$ourPDF = new $fpdf($format_page, 'mm', $taille_doc);
$ourPDF->Open();

switch($pdfdoc) {
    case "lettre_mail_retard_groupe" :
        //TODO 
        break;
	case "lettre_retard_groupe" :
		get_texts_group($relance);
		if (isset($id_groupe) && $id_groupe) lettre_retard_par_groupe($id_groupe, array(), $relance) ;
			else {
				$j=0;
				//Via la nouvelle mécanique de listes
				if(empty($coch_groupe) && !empty($selected_objects)) {
				    $coch_groupe = explode(',', $selected_objects);
				}
				while (!empty($coch_groupe[$j])) {
					$id_groupe=$coch_groupe[$j];
					$rqt = "select distinct groupe_id from pret, empr_groupe where pret_retour < curdate() and empr_id=pret_idempr and groupe_id=$id_groupe" ;
					$req = pmb_mysql_query($rqt, $dbh) or die ($msg['err_sql'].'<br />'.$rqt.'<br />'.pmb_mysql_error()); 
					while ($data = pmb_mysql_fetch_object($req)) {
						lettre_retard_par_groupe($data->groupe_id, array(), $relance) ;
					}
					$j++;
				}
			}
		break;
	case "lettre_retard" :
	default :
		get_texts($relance);	
		if (!$id_empr) {
			$empr=$empr_print;
			$print_all = isset($printall) ? $printall : 0;
			
			$restrict_localisation="";
			if ($empr) {
				$restrict_localisation = " id_empr in (".implode(",",$empr).") and "; 
			} elseif ($pmb_lecteurs_localises) {
				if ($empr_location_id=="") $empr_location_id = $deflt2docs_location ;
				if ($empr_location_id!=0) $restrict_localisation = " empr_location='$empr_location_id' AND ";							
			}
			
			// parametre listant les champs de la table empr pour effectuer le tri d'impression des lettres		
			if($pdflettreretard_impression_tri) $order_by= " ORDER BY $pdflettreretard_impression_tri";
			else $order_by= "";

			$rqt="select id_empr, concat(empr_nom,' ',empr_prenom) as  empr_name, empr_cb, empr_mail, empr_tel1, empr_sms, count(pret_idexpl) as empr_nb, $pdflettreretard_impression_tri from empr, pret, exemplaires where $restrict_localisation pret_retour<curdate() and pret_idempr=id_empr  and pret_idexpl=expl_id group by id_empr $order_by";							
			$req=pmb_mysql_query($rqt) or die('Erreur SQL !<br />'.$rqt.'<br />'.pmb_mysql_error());
			while ($r = pmb_mysql_fetch_object($req)) {
				if (($pmb_gestion_financiere)&&($pmb_gestion_amende)) {
					$amende=new amende($r->id_empr);
					$level=$amende->get_max_level();
					$niveau_min=$level["level_min"];
					$printed=$level["printed"];
					if (($printed==2) || (($mailretard_priorite_email==2) && ($niveau_min<3))) $printed=0;
					pmb_mysql_query("update pret set printed=1 where printed=2 and pret_idempr=".$r->id_empr);
					if (($print_all || !$printed)&&($niveau_min)) {
						$niveau=$niveau_min;
// 						get_texts($niveau);
						lettre_retard_par_lecteur($r->id_empr, $niveau) ;
						$ourPDF->SetMargins($marge_page_gauche,$marge_page_gauche);
					}
				} else {
					if (!$niveau) $niveau=1;
// 					get_texts($niveau);
					lettre_retard_par_lecteur($r->id_empr, $niveau) ;
					$ourPDF->SetMargins($marge_page_gauche,$marge_page_gauche);
				}
				if($r->empr_tel1 && $r->empr_sms && $empr_sms_msg_retard){	
					$res_envoi_sms=send_sms(0, $niveau, $r->empr_tel1, $empr_sms_msg_retard);
				}	
			} // fin while		
		} else {
			if (!$niveau) $niveau=1;
// 			get_texts($niveau);
			lettre_retard_par_lecteur($id_empr, $niveau) ;
			$ourPDF->SetMargins($marge_page_gauche,$marge_page_gauche);
			if($empr_sms_msg_retard) {
				$rqt="select concat(empr_nom,' ',empr_prenom) as  empr_name, empr_mail, empr_tel1, empr_sms from empr where id_empr='".$id_empr."' and empr_tel1!='' and empr_sms=1";							
				$req=pmb_mysql_query($rqt) or die('Erreur SQL !<br />'.$rqt.'<br />'.pmb_mysql_error()); ;
				if ($r = pmb_mysql_fetch_object($req)) {
					if ($r->empr_tel1 && $r->empr_sms) {
						$res_envoi_sms=send_sms(0, $niveau, $r->empr_tel1, $empr_sms_msg_retard);
					}
				}
			}
		}
		break;
	}
$ourPDF->OutPut();
