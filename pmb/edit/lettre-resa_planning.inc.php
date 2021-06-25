<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: lettre-resa_planning.inc.php,v 1.4.12.1 2020/10/07 13:29:55 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// popup d'impression PDF pour lettre de confirmation de résa
/* reçoit : liste d'id_resa séparés par des , */

// la marge gauche des pages
$marge_page_gauche = $pdflettreresa_marge_page_gauche;

// la marge droite des pages
$marge_page_droite = $pdflettreresa_marge_page_droite;

// la largeur des pages
$largeur_page = $pdflettreresa_largeur_page;

// la hauteur des pages
$hauteur_page = $pdflettreresa_hauteur_page;

// le format des pages
$format_page = $pdflettreresa_format_page;

$taille_doc=array($largeur_page,$hauteur_page);

$ourPDF = new $fpdf($format_page, 'mm', $taille_doc);
$ourPDF->Open();

switch($pdfdoc) {
	case "lettre_resa_planning" :
	default :
		// chercher id_empr validé
		$q = "select distinct (resa_idempr) from resa_planning where id_resa in (".addslashes($id_resa).") and resa_validee=1 ";
		$r = pmb_mysql_query($q, $dbh) ;
		while($o=pmb_mysql_fetch_object($r)) {
			lettre_resa_planning_par_lecteur($o->resa_idempr) ;
		}
		$ourPDF->SetMargins($marge_page_gauche,$marge_page_gauche);
		break;
	}

if ($probleme) {
	echo "<script type='text/javascript'> self.close(); </script>" ;
}else {
	$ourPDF->OutPut();
}
