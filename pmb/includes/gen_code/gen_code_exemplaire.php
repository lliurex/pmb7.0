<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: gen_code_exemplaire.php,v 1.10.6.1 2019/12/17 07:48:48 dbellamy Exp $

function init_gen_code_exemplaire($notice_id,$bull_id) {
	$query="select max(expl_cb)as cb from exemplaires WHERE expl_pnb_flag=0 and expl_cb like 'GEN%'";
	$result = pmb_mysql_query($query);
	$code_exemplaire = pmb_mysql_result($result, 0, 0);
	if(!$code_exemplaire) {
		$query="select max(expl_cb)as cb from exemplaires WHERE expl_pnb_flag=0 and expl_cb REGEXP '^[0-9]*$'";
		$result = pmb_mysql_query($query);
		$code_exemplaire = pmb_mysql_result($result, 0, 0);
		if(!$code_exemplaire) {
// ------------------- LLIUREX 22/02/2018 ------------------------------
	// original $code_exemplaire = "GEN000000" ----------------------------	
			$code_exemplaire = "000000000";
		}else{
			$code_exemplaire = "000000000";
		}
//--------------------FIN LLIUREX-------------------------------------------
	}
	return $code_exemplaire;  	   						
}

function gen_code_exemplaire($notice_id,$bull_id,$code_exemplaire) {
// ----------------- LLIUREX 22/02/2018 -----------------------------	
/*	if(preg_match("/(\D*)([0-9]*)/",$code_exemplaire,$matches)){
		$len = strlen($matches[2]);
		$matches[2]++;
		$code_exemplaire=$matches[1].str_pad($matches[2],$len,"0",STR_PAD_LEFT);
	} else{
		$code_exemplaire++;
	}*/
	$code_exemplaire++;

	if(strlen($code_exemplaire)>0 && strlen($code_exemplaire)<9){
		for($i=strlen($code_exemplaire); $i<9; $i++)
			
			$code_exemplaire="0".$code_exemplaire;
	}
// ---------------- FIN LLIUREX 22/02/2018 ---------------------------		

	return $code_exemplaire;
}
