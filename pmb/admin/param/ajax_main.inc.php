<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_main.inc.php,v 1.2.6.3 2020/11/05 12:32:35 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

switch($action) {
	case 'modif':
		require_once('./admin/param/param_func.inc.php');
		include("./admin/param/param_modif.inc.php");
		break;
	case 'update':
		$requete = "update parametres set "; 
		$requete .= "valeur_param='$form_valeur_param', ";
		$requete .= "comment_param='$comment_param' ";
		$requete .= "where id_param='$form_id_param' ";
		$res = @pmb_mysql_query($requete, $dbh);
		
		$valeur_param = $form_valeur_param;
		// Si $form_valeur_param contient un balise html... on formate la valeur pour l'affichage
		if (preg_match("/<.+>/", $form_valeur_param)) {
		    $valeur_param = "<pre class='params_pre'>"
		        .htmlentities($form_valeur_param, ENT_QUOTES, $charset);
		        "</pre>";
		}
		print encoding_normalize::json_encode(array('param_id'=> $form_id_param, 'param_value' => stripslashes($valeur_param), 'param_comment' => stripslashes($comment_param)));	
		break;
	case 'add':
		require_once('./admin/param/param_func.inc.php');
		param_form();
		break;
	default:
		require_once('./admin/param/param_func.inc.php');
//		show_param();
		$list_parameters_ui = new list_parameters_ui();
		print $list_parameters_ui->get_display_list();
		break;
	}
