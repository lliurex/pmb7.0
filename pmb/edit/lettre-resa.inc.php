<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: lettre-resa.inc.php,v 1.8.6.1 2020/10/07 13:29:55 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// popup d'impression PDF pour lettre de confirmation de résa
/* reçoit : id_resa */

// la marge gauche des pages
$var = "pdflettreresa_marge_page_gauche";
$marge_page_gauche = ${$var};

// la marge droite des pages
$var = "pdflettreresa_marge_page_droite";
$marge_page_droite = ${$var};

// la largeur des pages
$var = "pdflettreresa_largeur_page";
$largeur_page = ${$var};

// la hauteur des pages
$var = "pdflettreresa_hauteur_page";
$hauteur_page = ${$var};

// le format des pages
$var = "pdflettreresa_format_page";
$format_page = ${$var};

$taille_doc=array($largeur_page,$hauteur_page);

$ourPDF = new $fpdf($format_page, 'mm', $taille_doc);
$ourPDF->Open();

switch($pdfdoc) {
	case "lettre_resa" :
	default :
		if(!isset($id_empr_tmp)) $id_empr_tmp = 0;
		// chercher id_empr validé
		$rqt = "select resa_idempr from resa where id_resa in ($id_resa) ";
		$res = pmb_mysql_query($rqt) ;
		while ($resa_validee=pmb_mysql_fetch_object($res)){
			if($resa_validee->resa_idempr != $id_empr_tmp){
				lettre_resa_par_lecteur($resa_validee->resa_idempr) ;
				$id_empr_tmp=$resa_validee->resa_idempr;	
			}
		}
		$ourPDF->SetMargins($marge_page_gauche,$marge_page_gauche);
		break;
	}

if (isset($probleme) && $probleme) echo "<script> self.close(); </script>" ;
	else $ourPDF->OutPut();
