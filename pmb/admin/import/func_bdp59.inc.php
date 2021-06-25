<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
//
// Modifs effectu�es pour respecter le param�trage de la MdN (M�diat�que 
// d�patementale du Nord)
// Pb avec Orph�e pour les num�ros d'exemplaires. Utilisation du champ h en lieu
// et place du champ f et ajout de 0021 (code de la MdN) en fin de num�ro 
// d'exemplaire. fredericg@free.fr
// 
// +-------------------------------------------------+
// $Id: func_bdp59.inc.php,v 1.6 2019/08/01 13:16:34 btafforeau Exp $


if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

function recup_noticeunimarc_suite($notice) {
	} // fin recup_noticeunimarc_suite = fin r�cup�ration des variables propres BDP : rien de plus
	
function import_new_notice_suite() {
	global $notice_id;
	global $index_sujets;
	
	global $info_600_a, $info_600_j, $info_600_x, $info_600_y, $info_600_z;
	global $info_601_a, $info_601_j, $info_601_x, $info_601_y, $info_601_z;
	global $info_602_a, $info_602_j, $info_602_x, $info_602_y, $info_602_z;
	global $info_605_a, $info_605_j, $info_605_x, $info_605_y, $info_605_z;
	global $info_606_a, $info_606_j, $info_606_x, $info_606_y, $info_606_z;
	global $info_607_a, $info_607_j, $info_607_x, $info_607_y, $info_607_z;

	if (is_array($index_sujets)) {
	    $mots_cles = implode (" ",$index_sujets);
	} else {
	    $mots_cles = $index_sujets;
	}
	
	$nb_infos_600_a = count($info_600_a);
	for ($a = 0; $a < $nb_infos_600_a; $a++) {
		$mots_cles .= " " . $info_600_a[$a][0];
		
		$nb_infos_600_j = count($info_600_j[$a]);
		for ($j = 0; $j < $nb_infos_600_j; $j++) {
		    $mots_cles .= " " . $info_600_j[$a][$j];
		}
		
		$nb_infos_600_x = count($info_600_x[$a]);
		for ($j = 0; $j < $nb_infos_600_x; $j++) {
		    $mots_cles .= " " . $info_600_x[$a][$j];
		}
		$nb_infos_600_y = count($info_600_y[$a]);
		for ($j = 0; $j < $nb_infos_600_y; $j++) {
		    $mots_cles .= " " . $info_600_y[$a][$j];
		}
		
		$nb_infos_600_z = count($info_600_z[$a]);
		for ($j = 0; $j < $nb_infos_600_z; $j++) {
		    $mots_cles .= " " . $info_600_z[$a][$j];
		}
	}
	
	$nb_infos_601_a = count($info_601_a);
	for ($a = 0; $a < $nb_infos_601_a; $a++) {
	    $mots_cles .= " " . $info_601_a[$a][0];
	    
	    $nb_infos_601_j = count($info_601_j[$a]);
	    for ($j = 0; $j < $nb_infos_601_j; $j++) {
	        $mots_cles .= " " . $info_601_j[$a][$j];
	    }
	    
	    $nb_infos_601_x = count($info_601_x[$a]);
	    for ($j = 0; $j < $nb_infos_601_x; $j++) {
	        $mots_cles .= " " . $info_601_x[$a][$j];
	    }
	    $nb_infos_601_y = count($info_601_y[$a]);
	    for ($j = 0; $j < $nb_infos_601_y; $j++) {
	        $mots_cles .= " " . $info_601_y[$a][$j];
	    }
	    
	    $nb_infos_601_z = count($info_601_z[$a]);
	    for ($j = 0; $j < $nb_infos_601_z; $j++) {
	        $mots_cles .= " " . $info_601_z[$a][$j];
	    }
	}
	
	$nb_infos_602_a = count($info_602_a);
	for ($a = 0; $a < $nb_infos_602_a; $a++) {
	    $mots_cles .= " " . $info_602_a[$a][0];
	    
	    $nb_infos_602_j = count($info_602_j[$a]);
	    for ($j = 0; $j < $nb_infos_602_j; $j++) {
	        $mots_cles .= " " . $info_602_j[$a][$j];
	    }
	    
	    $nb_infos_602_x = count($info_602_x[$a]);
	    for ($j = 0; $j < $nb_infos_602_x; $j++) {
	        $mots_cles .= " " . $info_602_x[$a][$j];
	    }
	    $nb_infos_602_y = count($info_602_y[$a]);
	    for ($j = 0; $j < $nb_infos_602_y; $j++) {
	        $mots_cles .= " " . $info_602_y[$a][$j];
	    }
	    
	    $nb_infos_602_z = count($info_602_z[$a]);
	    for ($j = 0; $j < $nb_infos_602_z; $j++) {
	        $mots_cles .= " " . $info_602_z[$a][$j];
	    }
	}
	
	$nb_infos_605_a = count($info_605_a);
	for ($a = 0; $a < $nb_infos_605_a; $a++) {
	    $mots_cles .= " " . $info_605_a[$a][0];
	    
	    $nb_infos_605_j = count($info_605_j[$a]);
	    for ($j = 0; $j < $nb_infos_605_j; $j++) {
	        $mots_cles .= " " . $info_605_j[$a][$j];
	    }
	    
	    $nb_infos_605_x = count($info_605_x[$a]);
	    for ($j = 0; $j < $nb_infos_605_x; $j++) {
	        $mots_cles .= " " . $info_605_x[$a][$j];
	    }
	    $nb_infos_605_y = count($info_605_y[$a]);
	    for ($j = 0; $j < $nb_infos_605_y; $j++) {
	        $mots_cles .= " " . $info_605_y[$a][$j];
	    }
	    
	    $nb_infos_605_z = count($info_605_z[$a]);
	    for ($j = 0; $j < $nb_infos_605_z; $j++) {
	        $mots_cles .= " " . $info_605_z[$a][$j];
	    }
	}
	
	$nb_infos_606_a = count($info_606_a);
	for ($a = 0; $a < $nb_infos_606_a; $a++) {
	    $mots_cles .= " " . $info_606_a[$a][0];
	    
	    $nb_infos_606_j = count($info_606_j[$a]);
	    for ($j = 0; $j < $nb_infos_606_j; $j++) {
	        $mots_cles .= " " . $info_606_j[$a][$j];
	    }
	    
	    $nb_infos_606_x = count($info_606_x[$a]);
	    for ($j = 0; $j < $nb_infos_606_x; $j++) {
	        $mots_cles .= " " . $info_606_x[$a][$j];
	    }
	    $nb_infos_606_y = count($info_606_y[$a]);
	    for ($j = 0; $j < $nb_infos_606_y; $j++) {
	        $mots_cles .= " " . $info_606_y[$a][$j];
	    }
	    
	    $nb_infos_606_z = count($info_606_z[$a]);
	    for ($j = 0; $j < $nb_infos_606_z; $j++) {
	        $mots_cles .= " " . $info_606_z[$a][$j];
	    }
	}
	
	$nb_infos_607_a = count($info_607_a);
	for ($a = 0; $a < $nb_infos_607_a; $a++) {
	    $mots_cles .= " " . $info_607_a[$a][0];
	    
	    $nb_infos_607_j = count($info_607_j[$a]);
	    for ($j = 0; $j < $nb_infos_607_j; $j++) {
	        $mots_cles .= " " . $info_607_j[$a][$j];
	    }
	    
	    $nb_infos_607_x = count($info_607_x[$a]);
	    for ($j = 0; $j < $nb_infos_607_x; $j++) {
	        $mots_cles .= " " . $info_607_x[$a][$j];
	    }
	    $nb_infos_607_y = count($info_607_y[$a]);
	    for ($j = 0; $j < $nb_infos_607_y; $j++) {
	        $mots_cles .= " " . $info_607_y[$a][$j];
	    }
	    
	    $nb_infos_607_z = count($info_607_z[$a]);
	    for ($j = 0; $j < $nb_infos_607_z; $j++) {
	        $mots_cles .= " " . $info_607_z[$a][$j];
	    }
	}

	$index_matieres = (!empty($mots_cles) ? strip_empty_words($mots_cles) : '');
	$rqt_maj = "update notices set index_l='".addslashes($mots_cles)."', index_matieres=' ".addslashes($index_matieres)." ' where notice_id='$notice_id' ";
	$res_ajout = pmb_mysql_query($rqt_maj);
} // fin import_new_notice_suite
			
// TRAITEMENT DES EXEMPLAIRES ICI
function traite_exemplaires () {
	global $msg, $dbh ;
	
	global $prix, $notice_id, $info_995, $typdoc_995, $tdoc_codage, $book_lender_id, 
		$section_995, $sdoc_codage, $book_statut_id, $locdoc_codage, $codstatdoc_995, $statisdoc_codage,
		$cote_mandatory, $book_location_id ;

        // num�ro de la banque de pr�t (bdp) = 0021 pour la M�diath�que 
	//d�partementale du Nord
	$num_bdp="0021";		
	// lu en 010$d de la notice
	$price = $prix[0];
	
	$nb_infos_995 = count($info_995);
	// la zone 995 est r�p�table
	for ($nb_expl = 0; $nb_expl < count($nb_infos_995); $nb_expl++) {
		/* RAZ expl */
		$expl = array();
		
		/* pr�paration du tableau � passer � la m�thode */
		//$expl['cb'] 	    = $info_995[$nb_expl]['f'];
		// r�cup�ration du num�ro d'exemplaire en h
		//ajout du num�ro de la bdp
		$expl['cb']         = $info_995[$nb_expl]['h'].$num_bdp;
		$expl['notice']     = $notice_id ;
		
		// $expl['typdoc']     = $info_995[$nb_expl]['r']; � chercher dans docs_typdoc
		$data_doc=array();
		//$data_doc['tdoc_libelle'] = $info_995[$nb_expl]['r']." -Type doc import� (".$book_lender_id.")";
		$data_doc['tdoc_libelle'] = $typdoc_995[$info_995[$nb_expl]['r']];
		if (!$data_doc['tdoc_libelle']) $data_doc['tdoc_libelle'] = "\$r non conforme -".$info_995[$nb_expl]['r']."-" ;
		$data_doc['duree_pret'] = 0 ; /* valeur par d�faut */
		$data_doc['tdoc_codage_import'] = $info_995[$nb_expl]['r'] ;
		if ($tdoc_codage) $data_doc['tdoc_owner'] = $book_lender_id ;
			else $data_doc['tdoc_owner'] = 0 ;
		$expl['typdoc'] = docs_type::import($data_doc);
		
		$expl['cote'] = $info_995[$nb_expl]['k'];
                      	
		// $expl['section']    = $info_995[$nb_expl]['q']; � chercher dans docs_section
		$data_doc=array();
		if (!$info_995[$nb_expl]['q']) 
			$info_995[$nb_expl]['q'] = "u";
		$data_doc['section_libelle'] = $section_995[$info_995[$nb_expl]['q']];
		$data_doc['sdoc_codage_import'] = $info_995[$nb_expl]['q'] ;
		if ($sdoc_codage) $data_doc['sdoc_owner'] = $book_lender_id ;
			else $data_doc['sdoc_owner'] = 0 ;
		$expl['section'] = docs_section::import($data_doc);
		
		/* $expl['statut']     � chercher dans docs_statut */
		/* TOUT EST COMMENTE ICI, le statut est maintenant choisi lors de l'import
		if ($info_995[$nb_expl]['o']=="") $info_995[$nb_expl]['o'] = "e";
		$data_doc=array();
		$data_doc['statut_libelle'] = $info_995[$nb_expl]['o']." -Statut import� (".$book_lender_id.")";
		$data_doc['pret_flag'] = 1 ; 
		$data_doc['statusdoc_codage_import'] = $info_995[$nb_expl]['o'] ;
		$data_doc['statusdoc_owner'] = $book_lender_id ;
		$expl['statut'] = docs_statut::import($data_doc);
		FIN TOUT COMMENTE */
		
		$expl['statut'] = $book_statut_id;
		
		$expl['location'] = $book_location_id;
		
		// $expl['codestat']   = $info_995[$nb_expl]['q']; 'q' utilis�, �ventuellement � fixer par combo_box
		$data_doc=array();
		//$data_doc['codestat_libelle'] = $info_995[$nb_expl]['q']." -Pub vis� import� (".$book_lender_id.")";
		$data_doc['codestat_libelle'] = $codstatdoc_995[$info_995[$nb_expl]['q']];
		$data_doc['statisdoc_codage_import'] = $info_995[$nb_expl]['q'] ;
		if ($statisdoc_codage) $data_doc['statisdoc_owner'] = $book_lender_id ;
			else $data_doc['statisdoc_owner'] = 0 ;
		$expl['codestat'] = docs_codestat::import($data_doc);
		
		
		// $expl['creation']   = $info_995[$nb_expl]['']; � pr�ciser
		// $expl['modif']      = $info_995[$nb_expl]['']; � pr�ciser
                      	
		$expl['note']       = $info_995[$nb_expl]['u'];
		$expl['prix']       = $price;
		$expl['expl_owner'] = $book_lender_id ;
		$expl['cote_mandatory'] = $cote_mandatory ;
		
		$expl['date_depot'] = substr($info_995[$nb_expl]['m'],0,4)."-".substr($info_995[$nb_expl]['m'],4,2)."-".substr($info_995[$nb_expl]['m'],6,2) ;      
		$expl['date_retour'] = substr($info_995[$nb_expl]['n'],0,4)."-".substr($info_995[$nb_expl]['n'],4,2)."-".substr($info_995[$nb_expl]['n'],6,2) ;
		
		// quoi_faire
		if ($info_995[$nb_expl]['0']) $expl['quoi_faire'] = $info_995[$nb_expl]['0']  ;
			else $expl['quoi_faire'] = 2 ;
		
		$expl_id = exemplaire::import($expl);
		if ($expl_id == 0) {
			$nb_expl_ignores++;
			}
                      	
		//debug : affichage zone 995 
		/*
		echo "995\$a =".$info_995[$nb_expl]['a']."<br />";
		echo "995\$b =".$info_995[$nb_expl]['b']."<br />";
		echo "995\$c =".$info_995[$nb_expl]['c']."<br />";
		echo "995\$d =".$info_995[$nb_expl]['d']."<br />";
		echo "995\$f =".$info_995[$nb_expl]['f']."<br />";
		echo "995\$h =".$info_995[$nb_expl]['h']."<br />";
		echo "995\$k =".$info_995[$nb_expl]['k']."<br />";
		echo "995\$m =".$info_995[$nb_expl]['m']."<br />";
		echo "995\$n =".$info_995[$nb_expl]['n']."<br />";
		echo "995\$o =".$info_995[$nb_expl]['o']."<br />";
		echo "995\$q =".$info_995[$nb_expl]['q']."<br />";
		echo "995\$r =".$info_995[$nb_expl]['r']."<br />";
		echo "995\$u =".$info_995[$nb_expl]['u']."<br /><br />";
		*/
		} // fin for
	} // fin traite_exemplaires	TRAITEMENT DES EXEMPLAIRES JUSQU'ICI

// fonction sp�cifique d'export de la zone 995
function export_traite_exemplaires ($ex=array()) {
	global $msg, $dbh ;
	
	$subfields["a"] = $ex -> lender_libelle;
	$subfields["c"] = $ex -> lender_libelle;
	//$subfields["f"] = $ex -> expl_cb;
	$subfields["h"] = $ex -> expl_cb;
	$subfields["k"] = $ex -> expl_cote;
	$subfields["u"] = $ex -> expl_note;

	if ($ex->statusdoc_codage_import) $subfields["o"] = $ex -> statusdoc_codage_import;
	if ($ex -> tdoc_codage_import) $subfields["r"] = $ex -> tdoc_codage_import;
		else $subfields["r"] = "uu";
	if ($ex -> sdoc_codage_import) $subfields["q"] = $ex -> sdoc_codage_import;
		else $subfields["q"] = "u";
	
	global $export996 ;
	//$export996['f'] = $ex -> expl_cb ;
	$export996['h'] = $ex -> expl_cb ;
	$export996['k'] = $ex -> expl_cote ;
	$export996['u'] = $ex -> expl_note ;

	$export996['m'] = substr($ex -> expl_date_depot, 0, 4).substr($ex -> expl_date_depot, 5, 2).substr($ex -> expl_date_depot, 8, 2) ;
	$export996['n'] = substr($ex -> expl_date_retour, 0, 4).substr($ex -> expl_date_retour, 5, 2).substr($ex -> expl_date_retour, 8, 2) ;

	$export996['a'] = $ex -> lender_libelle;
	$export996['b'] = $ex -> expl_owner;

	$export996['v'] = $ex -> location_libelle;
	$export996['w'] = $ex -> locdoc_codage_import;

	$export996['x'] = $ex -> section_libelle;
	$export996['y'] = $ex -> sdoc_codage_import;

	$export996['e'] = $ex -> tdoc_libelle;
	$export996['r'] = $ex -> tdoc_codage_import;

	$export996['1'] = $ex -> statut_libelle;
	$export996['2'] = $ex -> statusdoc_codage_import;
	$export996['3'] = $ex -> pret_flag;
	
	global $export_traitement_exemplaires ;
	$export996['0'] = $export_traitement_exemplaires ;
	
	return 	$subfields ;

	}	
