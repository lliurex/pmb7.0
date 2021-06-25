<?php
// +-------------------------------------------------+
//  2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: addon.inc.php,v 1.5.14.98 2021/03/17 10:31:30 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if( !function_exists('traite_rqt') ) {
	function traite_rqt($requete="", $message="") {
	
		global $charset;
		$retour="";
		if($charset == "utf-8"){
			$requete=utf8_encode($requete);
		}
		pmb_mysql_query($requete) ; 
		$erreur_no = pmb_mysql_errno();
		if (!$erreur_no) {
			$retour = "Successful";
		} else {
			switch ($erreur_no) {
				case "1060":
					$retour = "Field already exists, no problem.";
					break;
				case "1061":
					$retour = "Key already exists, no problem.";
					break;
				case "1091":
					$retour = "Object already deleted, no problem.";
					break;
				default:
					$retour = "<font color=\"#FF0000\">Error may be fatal : <i>".pmb_mysql_error()."<i></font>";
					break;
				}
		}		
		return "<tr><td><font size='1'>".($charset == "utf-8" ? utf8_encode($message) : $message)."</font></td><td><font size='1'>".$retour."</font></td></tr>";
	}
}
echo "<table>";

/******************** AJOUTER ICI LES MODIFICATIONS *******************************/

//Assurons-nous que le paramètre ait bien été remis à zéro en montée de version
if($pmb_bdd_subversion) {
    $rqt = "SHOW COLUMNS FROM rss_flux LIKE 'id_tri_rss_flux'" ;
    $res = pmb_mysql_query($rqt);
    if(pmb_mysql_num_rows($res) == 0) {
        $pmb_bdd_subversion = 0;
    }
}

switch ($pmb_bdd_subversion) {
    case 0 :
        // DG - Tri sur les flux RSS
        $rqt = "ALTER TABLE rss_flux ADD id_tri_rss_flux INT NOT NULL DEFAULT 0, ADD INDEX i_id_tri_rss_flux (id_tri_rss_flux)" ;
        echo traite_rqt($rqt,"alter table rss_flux add field id_tri_rss_flux");
    case 1 :
        // DG - Modification du commentaire du parametre gestion de monopole de pret
        $rqt = "update parametres set comment_param = 'Gestion de monopole de prêt\n 0: Non\n x: [message bloquant] Nombre de jours entre 2 prêts d\'un exemplaire d\'une même notice (ou bulletin)\n 1,x: [message non bloquant] Nombre de jours entre 2 prêts d\'un exemplaire d\'une même notice (ou bulletin)' where type_param='pmb' and sstype_param = 'loan_trust_management'";
        echo traite_rqt($rqt,"update parametres pmb_loan_trust_management set comment");
        
        //DG - Paramètre pour la personnalisation en PHP des relances d'acquisitions
        if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='pdfrel_print' "))==0){
            $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'acquisition','pdfrel_print','','Quel script utiliser pour personnaliser l\'impression des relances ?','pdfrel',0)" ;
            echo traite_rqt($rqt,"insert acquisition_pdfrel_print into parametres") ;
        }
        
        // DG - Pré-remplissage du message en fonction de l'objet
        $rqt = "ALTER TABLE contact_form_objects ADD object_message text not null" ;
        echo traite_rqt($rqt,"alter table contact_form_objects add field object_message");
    case 2 :
        // DG - Modification du commentaire du parametre gestion de pret court
        $rqt = "update parametres set comment_param = 'Gestion des prêts courts\n 0: Non\n 1: Oui\n Attention, faire le retour des prêts courts avant de désactiver le module' where type_param='pmb' and sstype_param = 'short_loan_management'";
        echo traite_rqt($rqt,"update parametres pmb_short_loan_management set comment");
    case 3 :
        //DG - Paramètre pour ordonner la liste des exemplaires
        if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='expl_order' "))==0){
            $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'pmb','expl_order','','Ordre d\'affichage des exemplaires, dans l\'ordre donné, séparé par des virgules : location_libelle,section_libelle,expl_cote,tdoc_libelle','',0)" ;
            echo traite_rqt($rqt,"insert pmb_expl_order into parametres") ;
        }
    case 4 :
        //DG - maj Colonnes exemplaires affichées en OPAC - ajout en commentaire des champs personnalisés
        $rqt = "update parametres set comment_param='Colonne des exemplaires, dans l\'ordre donné, séparé par des virgules : expl_cb,expl_cote,tdoc_libelle,location_libelle,section_libelle, #n : id des champs personnalisés' where type_param= 'opac' and sstype_param='expl_data' ";
        echo traite_rqt($rqt,"update opac_expl_data into parametres");
    case 5 :
        //DG - Paramètre pour localiser ou non l'indexation des éléments
        if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='indexation_location' "))==0){
            $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES (0, 'pmb', 'indexation_location', '1', 'Localisation de l\'indexation activée.\n 0: Non\n 1: Oui', '',0) ";
            echo traite_rqt($rqt, "insert pmb_indexation_location into parameters");
        }        
    case 6 :
        // Ajout du pnb_flag dans la Table exemplaire
        $rqt = "ALTER TABLE exemplaires ADD expl_pnb_flag INT(1) UNSIGNED NOT NULL DEFAULT 0";
        echo traite_rqt($rqt,"alter table exemplaires add field expl_pnb_flag");
        // Ajout du pnb_flag dans la Table pret
        $rqt = "ALTER TABLE pret ADD pret_pnb_flag INT(1) UNSIGNED NOT NULL DEFAULT 0";
        echo traite_rqt($rqt,"alter table pret add field pret_pnb_flag");
        // Ajout du pnb_flag dans la Table resa
        $rqt = "ALTER TABLE resa ADD resa_pnb_flag INT(1) UNSIGNED NOT NULL DEFAULT 0";
        echo traite_rqt($rqt,"alter table resa add field resa_pnb_flag");
        // Ajout du pnb_flag dans la Table resa_archive
        $rqt = "ALTER TABLE resa_archive ADD resarc_pnb_flag INT(1) UNSIGNED NOT NULL DEFAULT 0";
        echo traite_rqt($rqt,"alter table resa_archive add field resarc_pnb_flag");
    case 7 :
        // TS & GN - Paramètre de tri par défaut des notices externes
        if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'opac' and sstype_param='default_sort_external' "))==0){
            $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param, section_param)
					VALUES (0, 'opac', 'default_sort_external', 'd_num_6', '0', 'Tri par défaut des recherches externes à l\'OPAC.\nDe la forme, c_num_6 (c pour croissant, d pour décroissant, puis num ou text pour numérique ou texte et enfin l\'identifiant du champ (voir fichier xml sort.xml))', 'd_aff_recherche') ";
            echo traite_rqt($rqt,"INSERT opac_default_sort_external INTO parametres") ;
        }
        
        // TS & GN - Paramètre de définition du sélecteur de tri des notices externes
        if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'opac' and sstype_param='default_sort_external_list' "))==0){
            $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param, section_param)
					VALUES (0, 'opac', 'default_sort_external_list', '1 d_num_6|Trier par pertinence', '0', 'Notices externes :\nAfficher la liste déroulante de sélection d\'un tri ?\n 0 : Non\n 1 : Oui\nFaire suivre d\'un espace pour l\'ajout de plusieurs tris sous la forme : c_num_6|Libelle||d_text_7|Libelle 2||c_num_5|Libelle 3\n\nc pour croissant, d pour décroissant\nnum ou text pour numérique ou texte\nidentifiant du champ (voir fichier xml sort.xml)\nlibellé du tri (optionnel)', 'd_aff_recherche') ";
            echo traite_rqt($rqt,"INSERT opac_default_sort_external_list INTO parametres") ;
        }
        
    case 8 :
        // Ajout champ perso date floue
        $rqt = "create table if not exists notices_custom_dates (
				notices_custom_champ int(10) unsigned NOT NULL default 0,
				notices_custom_origine int(10) unsigned NOT NULL default 0,
				notices_custom_date_type int(11) default NULL,
				notices_custom_date_start date default NULL,
				notices_custom_date_end date default NULL,
				notices_custom_order int(11) unsigned NOT NULL default 0,
				KEY notices_custom_champ (notices_custom_champ),
				KEY notices_custom_origine (notices_custom_origine),
	    		primary key (notices_custom_champ, notices_custom_origine, notices_custom_order)) ";
        echo traite_rqt($rqt,"create table if not exists notices_custom_dates");
        
        $rqt = "create table if not exists author_custom_dates (
				author_custom_champ int(10) unsigned NOT NULL default 0,
				author_custom_origine int(10) unsigned NOT NULL default 0,
				author_custom_date_type int(11) default NULL,
				author_custom_date_start date default NULL,
				author_custom_date_end date default NULL,
				author_custom_order int(11) unsigned NOT NULL default 0,
				KEY author_custom_champ (author_custom_champ),
				KEY author_custom_origine (author_custom_origine),
	    		primary key (author_custom_champ, author_custom_origine, author_custom_order)) ";
        echo traite_rqt($rqt,"create table if not exists author_custom_dates");
        
        $rqt = "create table if not exists authperso_custom_dates (
				authperso_custom_champ int(10) unsigned NOT NULL default 0,
				authperso_custom_origine int(10) unsigned NOT NULL default 0,
				authperso_custom_date_type int(11) default NULL,
				authperso_custom_date_start date default NULL,
				authperso_custom_date_end date default NULL,
				authperso_custom_order int(11) unsigned NOT NULL default 0,
				KEY authperso_custom_champ (authperso_custom_champ),
				KEY authperso_custom_origine (authperso_custom_origine),
	    		primary key (authperso_custom_champ, authperso_custom_origine, authperso_custom_order)) ";
        echo traite_rqt($rqt,"create table if not exists authperso_custom_dates");
        
        $rqt = "create table if not exists categ_custom_dates (
				categ_custom_champ int(10) unsigned NOT NULL default 0,
				categ_custom_origine int(10) unsigned NOT NULL default 0,
				categ_custom_date_type int(11) default NULL,
				categ_custom_date_start date default NULL,
				categ_custom_date_end date default NULL,
				categ_custom_order int(11) unsigned NOT NULL default 0,
				KEY categ_custom_champ (categ_custom_champ),
				KEY categ_custom_origine (categ_custom_origine),
	    		primary key (categ_custom_champ, categ_custom_origine, categ_custom_order)) ";
        echo traite_rqt($rqt,"create table if not exists categ_custom_dates");
        
        $rqt = "create table if not exists cms_editorial_custom_dates (
				cms_editorial_custom_champ int(10) unsigned NOT NULL default 0,
				cms_editorial_custom_origine int(10) unsigned NOT NULL default 0,
				cms_editorial_custom_date_type int(11) default NULL,
				cms_editorial_custom_date_start date default NULL,
				cms_editorial_custom_date_end date default NULL,
				cms_editorial_custom_order int(11) unsigned NOT NULL default 0,
				KEY cms_editorial_custom_champ (cms_editorial_custom_champ),
				KEY cms_editorial_custom_origine (cms_editorial_custom_origine),
	    		primary key (cms_editorial_custom_champ, cms_editorial_custom_origine, cms_editorial_custom_order)) ";
        echo traite_rqt($rqt,"create table if not exists cms_editorial_custom_dates");
        
        $rqt = "create table if not exists collection_custom_dates (
				collection_custom_champ int(10) unsigned NOT NULL default 0,
				collection_custom_origine int(10) unsigned NOT NULL default 0,
				collection_custom_date_type int(11) default NULL,
				collection_custom_date_start date default NULL,
				collection_custom_date_end date default NULL,
				collection_custom_order int(11) unsigned NOT NULL default 0,
				KEY collection_custom_champ (collection_custom_champ),
				KEY collection_custom_origine (collection_custom_origine),
	    		primary key (collection_custom_champ, collection_custom_origine, collection_custom_order)) ";
        echo traite_rqt($rqt,"create table if not exists collection_custom_dates");
        
        $rqt = "create table if not exists collstate_custom_dates (
				collstate_custom_champ int(10) unsigned NOT NULL default 0,
				collstate_custom_origine int(10) unsigned NOT NULL default 0,
				collstate_custom_date_type int(11) default NULL,
				collstate_custom_date_start date default NULL,
				collstate_custom_date_end date default NULL,
				collstate_custom_order int(11) unsigned NOT NULL default 0,
				KEY collstate_custom_champ (collstate_custom_champ),
				KEY collstate_custom_origine (collstate_custom_origine),
	    		primary key (collstate_custom_champ, collstate_custom_origine, collstate_custom_order)) ";
        echo traite_rqt($rqt,"create table if not exists collstate_custom_dates");
        
        $rqt = "create table if not exists demandes_custom_dates (
				demandes_custom_champ int(10) unsigned NOT NULL default 0,
				demandes_custom_origine int(10) unsigned NOT NULL default 0,
				demandes_custom_date_type int(11) default NULL,
				demandes_custom_date_start date default NULL,
				demandes_custom_date_end date default NULL,
				demandes_custom_order int(11) unsigned NOT NULL default 0,
				KEY demandes_custom_champ (demandes_custom_champ),
				KEY demandes_custom_origine (demandes_custom_origine),
	    		primary key (demandes_custom_champ, demandes_custom_origine, demandes_custom_order)) ";
        echo traite_rqt($rqt,"create table if not exists demandes_custom_dates");
        
        $rqt = "create table if not exists empr_custom_dates (
				empr_custom_champ int(10) unsigned NOT NULL default 0,
				empr_custom_origine int(10) unsigned NOT NULL default 0,
				empr_custom_date_type int(11) default NULL,
				empr_custom_date_start date default NULL,
				empr_custom_date_end date default NULL,
				empr_custom_order int(11) unsigned NOT NULL default 0,
				KEY empr_custom_champ (empr_custom_champ),
				KEY empr_custom_origine (empr_custom_origine),
	    		primary key (empr_custom_champ, empr_custom_origine, empr_custom_order)) ";
        echo traite_rqt($rqt,"create table if not exists empr_custom_dates");
        
        $rqt = "create table if not exists explnum_custom_dates (
				explnum_custom_champ int(10) unsigned NOT NULL default 0,
				explnum_custom_origine int(10) unsigned NOT NULL default 0,
				explnum_custom_date_type int(11) default NULL,
				explnum_custom_date_start date default NULL,
				explnum_custom_date_end date default NULL,
				explnum_custom_order int(11) unsigned NOT NULL default 0,
				KEY explnum_custom_champ (explnum_custom_champ),
				KEY explnum_custom_origine (explnum_custom_origine),
	    		primary key (explnum_custom_champ, explnum_custom_origine, explnum_custom_order)) ";
        echo traite_rqt($rqt,"create table if not exists explnum_custom_dates");
        
        $rqt = "create table if not exists expl_custom_dates (
				expl_custom_champ int(10) unsigned NOT NULL default 0,
				expl_custom_origine int(10) unsigned NOT NULL default 0,
				expl_custom_date_type int(11) default NULL,
				expl_custom_date_start date default NULL,
				expl_custom_date_end date default NULL,
				expl_custom_order int(11) unsigned NOT NULL default 0,
				KEY expl_custom_champ (expl_custom_champ),
				KEY expl_custom_origine (expl_custom_origine),
	    		primary key (expl_custom_champ, expl_custom_origine, expl_custom_order)) ";
        echo traite_rqt($rqt,"create table if not exists expl_custom_dates");
        
        $rqt = "create table if not exists indexint_custom_dates (
				indexint_custom_champ int(10) unsigned NOT NULL default 0,
				indexint_custom_origine int(10) unsigned NOT NULL default 0,
				indexint_custom_date_type int(11) default NULL,
				indexint_custom_date_start date default NULL,
				indexint_custom_date_end date default NULL,
				indexint_custom_order int(11) unsigned NOT NULL default 0,
				KEY indexint_custom_champ (indexint_custom_champ),
				KEY indexint_custom_origine (indexint_custom_origine),
	    		primary key (indexint_custom_champ, indexint_custom_origine, indexint_custom_order)) ";
        echo traite_rqt($rqt,"create table if not exists indexint_custom_dates");
        
        $rqt = "create table if not exists pret_custom_dates (
				pret_custom_champ int(10) unsigned NOT NULL default 0,
				pret_custom_origine int(10) unsigned NOT NULL default 0,
				pret_custom_date_type int(11) default NULL,
				pret_custom_date_start date default NULL,
				pret_custom_date_end date default NULL,
				pret_custom_order int(11) unsigned NOT NULL default 0,
				KEY pret_custom_champ (pret_custom_champ),
				KEY pret_custom_origine (pret_custom_origine),
	    		primary key (pret_custom_champ, pret_custom_origine, pret_custom_order)) ";
        echo traite_rqt($rqt,"create table if not exists pret_custom_dates");
        
        $rqt = "create table if not exists publisher_custom_dates (
				publisher_custom_champ int(10) unsigned NOT NULL default 0,
				publisher_custom_origine int(10) unsigned NOT NULL default 0,
				publisher_custom_date_type int(11) default NULL,
				publisher_custom_date_start date default NULL,
				publisher_custom_date_end date default NULL,
				publisher_custom_order int(11) unsigned NOT NULL default 0,
				KEY publisher_custom_champ (publisher_custom_champ),
				KEY publisher_custom_origine (publisher_custom_origine),
	    		primary key (publisher_custom_champ, publisher_custom_origine, publisher_custom_order)) ";
        echo traite_rqt($rqt,"create table if not exists publisher_custom_dates");
        
        $rqt = "create table if not exists serie_custom_dates (
				serie_custom_champ int(10) unsigned NOT NULL default 0,
				serie_custom_origine int(10) unsigned NOT NULL default 0,
				serie_custom_date_type int(11) default NULL,
				serie_custom_date_start date default NULL,
				serie_custom_date_end date default NULL,
				serie_custom_order int(11) unsigned NOT NULL default 0,
				KEY serie_custom_champ (serie_custom_champ),
				KEY serie_custom_origine (serie_custom_origine),
	    		primary key (serie_custom_champ, serie_custom_origine, serie_custom_order)) ";
        echo traite_rqt($rqt,"create table if not exists serie_custom_dates");
        
        $rqt = "create table if not exists skos_custom_dates (
				skos_custom_champ int(10) unsigned NOT NULL default 0,
				skos_custom_origine int(10) unsigned NOT NULL default 0,
				skos_custom_date_type int(11) default NULL,
				skos_custom_date_start date default NULL,
				skos_custom_date_end date default NULL,
				skos_custom_order int(11) unsigned NOT NULL default 0,
				KEY skos_custom_champ (skos_custom_champ),
				KEY skos_custom_origine (skos_custom_origine),
	    		primary key (skos_custom_champ, skos_custom_origine, skos_custom_order)) ";
        echo traite_rqt($rqt,"create table if not exists skos_custom_dates");
        
        $rqt = "create table if not exists subcollection_custom_dates (
				subcollection_custom_champ int(10) unsigned NOT NULL default 0,
				subcollection_custom_origine int(10) unsigned NOT NULL default 0,
				subcollection_custom_date_type int(11) default NULL,
				subcollection_custom_date_start date default NULL,
				subcollection_custom_date_end date default NULL,
				subcollection_custom_order int(11) unsigned NOT NULL default 0,
				KEY subcollection_custom_champ (subcollection_custom_champ),
				KEY subcollection_custom_origine (subcollection_custom_origine),
	    		primary key (subcollection_custom_champ, subcollection_custom_origine, subcollection_custom_order)) ";
        echo traite_rqt($rqt,"create table if not exists subcollection_custom_dates");
        
        $rqt = "create table if not exists tu_custom_dates (
				tu_custom_champ int(10) unsigned NOT NULL default 0,
				tu_custom_origine int(10) unsigned NOT NULL default 0,
				tu_custom_date_type int(11) default NULL,
				tu_custom_date_start date default NULL,
				tu_custom_date_end date default NULL,
				tu_custom_order int(11) unsigned NOT NULL default 0,
				KEY tu_custom_champ (tu_custom_champ),
				KEY tu_custom_origine (tu_custom_origine),
	    		primary key (tu_custom_champ, tu_custom_origine, tu_custom_order)) ";
        echo traite_rqt($rqt,"create table if not exists tu_custom_dates");   
        
    case 9 :
        // NG - PNB : Ajout d'un paramètre caché pour affecter un code statistique à l'exemplaire
        if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='pnb_codestat_id' "))==0){
            $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
                VALUES (0, 'pmb', 'pnb_codestat_id', '0', 'Affectation d\'un code statistique à l\'exemplaire', '', 1)";
            echo traite_rqt($rqt, "insert pmb_pnb_codestat_id into parameters");
        }
        // NG - PNB : Ajout d'un paramètre caché pour affecter un statut à l'exemplaire
        if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='pnb_statut_id' "))==0){
            $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
                VALUES (0, 'pmb', 'pnb_statut_id', '0', 'Affectation d\'un statut à l\'exemplaire', '', 1)";
            echo traite_rqt($rqt, "insert pmb_pnb_statut_id into parameters");
        }
        // NG - PNB : Ajout d'un paramètre caché pour affecter un typedoc à l'exemplaire
        if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='pnb_typedoc_id' "))==0){
            $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
                VALUES (0, 'pmb', 'pnb_typedoc_id', '0', 'Affectation d\'un typedoc à l\'exemplaire', '', 1)";
            echo traite_rqt($rqt, "insert pmb_pnb_typedoc_id into parameters");
        }
    case 10 :
        // DG - Ordre de tri (croissant / décroissant) des résultats de facettes dans les bannettes
        $rqt = "ALTER TABLE bannette_facettes ADD ban_facette_order_sort int(1) NOT NULL DEFAULT 0";
        echo traite_rqt($rqt,"alter table bannette_facettes add ban_facette_order_sort");
        
        // DG - Type de données de tri des résultats de facettes dans les bannettes
        $rqt = "ALTER TABLE bannette_facettes ADD ban_facette_datatype_sort varchar(255) NOT NULL DEFAULT 'alpha'";
        echo traite_rqt($rqt,"alter table bannette_facettes add ban_facette_datatype_sort");
        
        // DG - Ajout d'un commentaire de gestion sur le groupe
        $rqt = "ALTER TABLE groupe ADD comment_gestion TEXT NOT NULL DEFAULT ''";
        echo traite_rqt($rqt,"alter table groupe add comment_gestion");
        
        // DG - Ajout d'un commentaire OPAC sur le groupe
        $rqt = "ALTER TABLE groupe ADD comment_opac TEXT NOT NULL DEFAULT ''";
        echo traite_rqt($rqt,"alter table groupe add comment_opac");
    case 11 :
        //DG - Modification du champ pour un varchar afin d'accueillir les templates Django
        $rqt = "ALTER TABLE rss_flux MODIFY tpl_rss_flux VARCHAR(255) DEFAULT '0'";
        echo traite_rqt($rqt,"ALTER TABLE rss_flux MODIFY tpl_rss_flux VARCHAR(255)");
    case 12 :
        //DG - Ajout du champ pour personnaliser l'affichage du titre des éléments du flux RSS
        $rqt = "ALTER TABLE rss_flux ADD tpl_title_rss_flux VARCHAR(255) DEFAULT '0' AFTER export_court_flux";
        echo traite_rqt($rqt,"ALTER TABLE rss_flux ADD tpl_title_rss_flux VARCHAR(255)");
    case 13 :
        // DB : Parametre d'augmentation par defaut pour les achats
        if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='increase_rate_percent' "))==0){
            $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) VALUES (0, 'acquisition', 'increase_rate_percent', '2.00', 'Pourcentage d\'augmentation par défaut.', '',0) ";
            echo traite_rqt($rqt, "insert acquisition_increase_rate_percent=2.00 into parameters");
        }
    case 14 :
        //BT & QV : Paramètre gérant la regexep du contrôle du mot de passe empr
        if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='websubscribe_password_regexp' ")) == 0) {
            $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			  		VALUES (0, 'opac', 'websubscribe_password_regexp', '', 'Permet de choisir la regexp afin de contrôler le mot de passe emprunteur. Il ne faut pas mettre les délimiteurs.\n\nIl faut modifier le message empr_form_bad_security en conséquence afin de renseigner l\'utilisateur sur la présence du contrôle', 'f_modules', '0')";
            echo traite_rqt($rqt, "insert password_regexp = '' into parametres ");
        }
    case 15 :
        // DG : Ajout dans les préférences utilisateur du statut par défaut en création de document numérique sur une demande de numérisation
        $rqt = "ALTER TABLE users ADD deflt_scan_request_explnum_status INT(1) UNSIGNED NOT NULL DEFAULT 0 " ;
        echo traite_rqt($rqt,"ALTER users ADD deflt_scan_request_explnum_status");
        
        $rqt = "show fields from groupe";
        $res = pmb_mysql_query($rqt);
        $exists = 0;
        if(pmb_mysql_num_rows($res)){
            while($row = pmb_mysql_fetch_object($res)){
                if($row->Field == "lettre_resa" || $row->Field == "mail_resa" || $row->Field == "lettre_resa_show_nomgroup"){
                    $exists++;
                }
            }
        }
        // Il manque au moins un champ sur les 3.
        if($exists < 3){
            //DG - Lettre de réservation au référent
            $rqt = "ALTER TABLE groupe ADD lettre_resa INT( 1 ) UNSIGNED DEFAULT 0 NOT NULL ";
            echo traite_rqt($rqt,"ALTER TABLE groupe ADD lettre_resa default 0");
            
            //DG - Mail de réservation au référent
            $rqt = "ALTER TABLE groupe ADD mail_resa INT( 1 ) UNSIGNED DEFAULT 0 NOT NULL ";
            echo traite_rqt($rqt,"ALTER TABLE groupe ADD mail_resa default 0");
            
            //DG - Impression du nom du groupe sur la lettre de réservation
            $rqt = "ALTER TABLE groupe ADD lettre_resa_show_nomgroup INT( 1 ) UNSIGNED DEFAULT 0 NOT NULL ";
            echo traite_rqt($rqt,"ALTER TABLE groupe ADD lettre_resa_show_nomgroup default 0");
            
            //DG - Mise à jour des informations en suivant le paramétrage existant
            $rqt = "update groupe set lettre_resa=lettre_rappel ";
            echo traite_rqt($rqt,"update groupe set lettre_resa=lettre_rappel");
            $rqt = "update groupe set mail_resa=mail_rappel ";
            echo traite_rqt($rqt,"update groupe set mail_resa=mail_rappel");
            $rqt = "update groupe set lettre_resa_show_nomgroup=lettre_rappel_show_nomgroup ";
            echo traite_rqt($rqt,"update groupe set lettre_resa_show_nomgroup=lettre_rappel_show_nomgroup");
        }
        
        //DG - Evolutions du paramètre pour les notifications sur les réservations OPAC
        $rqt = "update parametres set comment_param='Mode de notification par email des nouvelles réservations aux utilisateurs ? \n0 : Recevoir toutes les notifications \n1 : Notification des utilisateurs du site de gestion du lecteur \n2 : Notification des utilisateurs associés à la localisation par défaut en création d\'exemplaire \n3 : Notification des utilisateurs du site de gestion et de la localisation d\'exemplaire' where type_param= 'pmb' and sstype_param='resa_alert_localized' ";
        echo traite_rqt($rqt,"update pmb_resa_alert_localized into parametres");

    case 16 :
        //DB : Suppression table quotas PNB
        $rqt = "drop table if exists quotas_pnb";
        echo traite_rqt($rqt,"DROP TABLE quotas_pnb");
        
        //DB : Suppression parametres pnb_drm_parameters et pnb_clean_loan_date
        $rqt = "delete from parametres where type_param= 'pmb' and sstype_param='pnb_drm_parameters' ";
        echo traite_rqt($rqt,"DROP PARAMETRES pmb_pnb_drm_parameters");
        $rqt = "delete from parametres where type_param= 'pmb' and sstype_param='pnb_clean_loans_date' ";
        echo traite_rqt($rqt,"DROP PARAMETRES pmb_pnb_clean_loans_date");
        
    case 17 :
        //DB & QV : Ajout de pnb_loan_loanid dans la table pnb_loans
        $rqt = "ALTER TABLE pnb_loans ADD pnb_loan_loanid varchar(255) DEFAULT '' NOT NULL ";
        echo traite_rqt($rqt,"ALTER TABLE pnb_loans ADD pnb_loan_loanid");
        
    case 18 :
        //DB & QV : Ajout d'un paramètre pour l'affichage des exemplaire en prêt numérique
        if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='show_exemplaires_pnb' ")) == 0) {
            $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			  		VALUES (0, 'pmb', 'show_exemplaires_pnb', '0', 'Affichage des exemplaires en prêt numérique \n0 : Ne pas afficher \n1 : Afficher uniquement les exemplaires en prêt \n2 : Afficher tous les exemplaires', '', '0')";
            echo traite_rqt($rqt, "insert show_exemplaires_pnb = '0' into parametres ");
        }
        
        //DB & QV : Ajout d'un paramètre alerte sur un seuil de jetons restants pour le prêt numérique
        if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='pnb_alert_threshold_tokens' ")) == 0) {
            $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			  		VALUES (0, 'pmb', 'pnb_alert_threshold_tokens', '0', 'Seuil d\'alerte sur nombre de jetons restants', '', '1')";
            echo traite_rqt($rqt, "insert pnb_alert_threshold_tokens = '0' into parametres ");
        }
        
        //DB & QV : Ajout du nombre de jetons restant dans la table pnb_orders
        $rqt = "ALTER TABLE pnb_orders ADD pnb_current_nta int(10) DEFAULT 0 NOT NULL ";
        echo traite_rqt($rqt,"ALTER TABLE pnb_orders ADD pnb_current_nta");
    
    case 19 :
        //DB & QV : Ajout d'un statut d'emprunteur pour autoriser les pret numérique
        $rqt = "ALTER TABLE empr_statut ADD allow_pnb tinyint(4) DEFAULT 0 NOT NULL ";
        echo traite_rqt($rqt,"ALTER TABLE empr_statut ADD allow_pnb");
        
    case 20 :
        //QV - Modification du commentaire pour le parametre short_url
        if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='short_url' "))){
            $rqt = "update parametres set comment_param='Afficher le lien permettant de générer un flux RSS et le partage des résultats de la recherche ? \n0 : Non \n1 : Oui' where type_param='opac' and sstype_param='short_url' " ;
            echo traite_rqt($rqt,"update parameters short_url");
        }
        
    case 21 :
        //QV - article_creation_date passer en DATETIME
        $rqt = "ALTER TABLE cms_articles CHANGE article_creation_date article_creation_date DATETIME NULL DEFAULT NULL" ;
        echo traite_rqt($rqt,"ALTER TABLE cms_articles CHANGE article_creation_date");
        
        //QV - section_creation_date passer en DATETIME
        $rqt = "ALTER TABLE cms_sections CHANGE section_creation_date section_creation_date DATETIME NULL DEFAULT NULL" ;
        echo traite_rqt($rqt,"ALTER TABLE cms_sections CHANGE section_creation_date");
        
    case 22 :
        //QV : Ajout d'un paramètre pour afficher le nombre d'article lié à un périodique
        if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='show_nb_analysis' ")) == 0) {
            $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			  		VALUES (0, 'opac', 'show_nb_analysis', '1', 'Affiche le nombre d\'article lié à un périodique \n0 : Non \n1 : Oui', 'e_aff_notice', '0')";
            echo traite_rqt($rqt, "insert show_nb_analysis = '1' into parametres ");
        }
        
    case 23 :
        //DG : Ajout d'un flag pour autoriser l'ajout de notices dans la liste
        $rqt = "show fields from opac_liste_lecture";
        $res = pmb_mysql_query($rqt);
        $exists = false;
        if(pmb_mysql_num_rows($res)){
            while($row = pmb_mysql_fetch_object($res)){
                if($row->Field == "allow_add_records"){
                    $exists = true;
                    break;
                }
            }
        }
        if(!$exists){
            $rqt = "ALTER TABLE opac_liste_lecture ADD allow_add_records int(1) NOT NULL DEFAULT 0 ";
            echo traite_rqt($rqt,"ALTER TABLE opac_liste_lecture ADD allow_add_records");
            
            //Pour les listes n'étant pas en lecture seule, on applique le flag à 1 pour autoriser l'ajout de notices
            $rqt = "UPDATE opac_liste_lecture SET allow_add_records=1 WHERE read_only = 0";
            echo traite_rqt($rqt,"UPDATE opac_liste_lecture SET allow_add_records=1 FOR read_only = 0");
            
        }
        
        //DG : Ajout d'un flag pour autoriser la suppression de notices dans la liste
        $rqt = "ALTER TABLE opac_liste_lecture ADD allow_remove_records int(1) NOT NULL DEFAULT 0 ";
        echo traite_rqt($rqt,"ALTER TABLE opac_liste_lecture ADD allow_remove_records");
        
    case 24 :
        // DG - Modification du commentaire sur l'activation ou non des réservations possibles sur les notices sans exemplaires
        $rqt = "update parametres set comment_param='Réservation sur les notices sans exemplaires \n 0 : Non \n 1 : Oui' where type_param= 'pmb' and sstype_param='resa_records_no_expl' ";
        echo traite_rqt($rqt,"update pmb_resa_records_no_expl into parametres");
        
    case 25 :
        //DG - Libellé OPAC des sections d'exemplaires
        $rqt = "ALTER TABLE docs_section ADD section_libelle_opac VARCHAR(255) DEFAULT '' after section_libelle";
        echo traite_rqt($rqt,"ALTER TABLE docs_section add section_libelle_opac default ''");
        
    case 26 :
        // ER : Ajout d'un paramètre pour afficher le chemin complet en indexation indépendamment de l'affichage standard.
        if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'thesaurus' and sstype_param='categories_show_only_last_indexation' "))==0){
            $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param) VALUES (0, 'thesaurus', 'categories_show_only_last_indexation', '".$thesaurus_categories_show_only_last."', 'En indexation d\'une notice : \n 0 tout afficher \n 1 : afficher uniquement la dernière feuille de l\'arbre de la catégorie','categories')";
            echo traite_rqt($rqt,"insert thesaurus_categories_show_only_last_indexation='".$thesaurus_categories_show_only_last."' into parametres");
        }
        
    case 27 :
        // DG - Modification du commentaire sur l'activation ou non des réservations possibles sur les notices sans exemplaires
        $rqt = "update parametres set comment_param='Se diriger vers quel module après connexion de l\'emprunteur ? \n Vide = Rester sur la même page \n empr.php = Compte emprunteur \n index.php = Retour en accueil' where type_param= 'opac' and sstype_param='show_login_form_next' ";
        echo traite_rqt($rqt,"update opac_show_login_form_next into parametres");
    
    case 28 :
        // TS - Champ pour stocker le tri dans les segments de recherche
        $rqt = "ALTER TABLE search_segments ADD search_segment_sort text not null" ;
        echo traite_rqt($rqt,"alter table search_segments add field search_segment_sort");
        
    case 29 :
        //DB - paramétrages des droits d'accès sur les rubriques
        if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'gestion_acces' and sstype_param='empr_cms_section' "))==0){
            $rqt = "INSERT INTO parametres (type_param,sstype_param,valeur_param,comment_param,section_param,gestion)
					VALUES ('gestion_acces','empr_cms_section',0,'Gestion des droits d\'accès des emprunteurs aux rubriques\n0 : Non.\n1 : Oui.','',0)";
            echo traite_rqt($rqt,"insert gestion_acces_empr_cms_section into parametres");
        }
        if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'gestion_acces' and sstype_param='empr_cms_section_def' "))==0){
            $rqt = "INSERT INTO parametres (type_param,sstype_param,valeur_param,comment_param,section_param,gestion)
					VALUES ('gestion_acces','empr_cms_section_def',0,'Valeur par défaut en modification de contenu éditorial pour les droits d\'accès emprunteurs - rubriques\n0 : Recalculer.\n1 : Choisir.','',0)";
            echo traite_rqt($rqt,"insert gestion_acces_empr_cms_section_def into parametres");
        }
        //DB - paramétrages des droits d'accès sur les articles
        if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'gestion_acces' and sstype_param='empr_cms_article' "))==0){
            $rqt = "INSERT INTO parametres (type_param,sstype_param,valeur_param,comment_param,section_param,gestion)
					VALUES ('gestion_acces','empr_cms_article',0,'Gestion des droits d\'accès des emprunteurs aux articles\n0 : Non.\n1 : Oui.','',0)";
            echo traite_rqt($rqt,"insert gestion_acces_empr_cms_article into parametres");
        }
        if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'gestion_acces' and sstype_param='empr_cms_article_def' "))==0){
            $rqt = "INSERT INTO parametres (type_param,sstype_param,valeur_param,comment_param,section_param,gestion)
					VALUES ('gestion_acces','empr_cms_article_def',0,'Valeur par défaut en modification de contenu éditorial pour les droits d\'accès emprunteurs - articles\n0 : Recalculer.\n1 : Choisir.','',0)";
            echo traite_rqt($rqt,"insert gestion_acces_empr_cms_article_def into parametres");
        }
    case 30 :
        // DG - Répertoire de stockage des pièces jointes
        if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='attachments_folder' "))==0){
            $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'pmb', 'attachments_folder', '',	'Répertoire de stockage des pièces jointes', '', 0) ";
            echo traite_rqt($rqt, "insert pmb_attachments_folder into parameters");
        }
        
        // DG - URL d'accès du répertoire des pièces jointes
        if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='attachments_url' "))==0){
            $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'pmb', 'attachments_url', '',	'URL d\'accès du répertoire des pièces jointes (pmb_attachments_folder)', '', 0) ";
            echo traite_rqt($rqt, "insert pmb_attachments_url into parameters");
        }
    case 31 :
    	// DB - Augmentation taille champ rapport / table taches
    	$rqt = "alter table taches change rapport rapport mediumtext" ;
    	echo traite_rqt($rqt,"alter table taches change rapport to mediumtext");  
	case 32 :
	    // AR - Ajout d'un param caché contenant la dernière entité qu'on a tenté d'indexer
	    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='indexation_last_entity' "))==0){
	        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES (NULL, 'pmb', 'indexation_last_entity', '0', 'Contient la dernière entité qu\'on tente d\'indexer', '', '1')" ;
	        echo traite_rqt($rqt,"insert hidden pmb_indexation_last_entity='' into parametres") ;
	    }
	case 33 :
	    // QV - modification du type du champ cms_editorial_custom_text dans la table cms_editorial_custom_values
	    $rqt = "ALTER TABLE cms_editorial_custom_values CHANGE cms_editorial_custom_text cms_editorial_custom_text MEDIUMTEXT NULL DEFAULT NULL;";
	    echo traite_rqt($rqt,"alter table cms_editorial_custom_values change cms_editorial_custom_text");
	case 34 :
	    // TS - Modification de la clé primaire de la table authorities_fileds_global_index pour tenir compte de la langue
	    if (pmb_mysql_num_rows(pmb_mysql_query("SHOW INDEX FROM authorities_fields_global_index WHERE Key_name='PRIMARY' AND Column_name = 'lang'"))==0){
	        $rqt = "TRUNCATE TABLE authorities_fields_global_index";
	        echo traite_rqt($rqt,"truncate table authorities_fields_global_index");
	        
	        $rqt = "ALTER TABLE `authorities_fields_global_index` DROP PRIMARY KEY,ADD PRIMARY KEY(`id_authority`,`code_champ`,`code_ss_champ`,`ordre`,`lang`)" ;
	        echo traite_rqt($rqt,"ALTER TABLE `authorities_fields_global_index` DROP PRIMARY KEY,ADD PRIMARY KEY(`id_authority`,`code_champ`,`code_ss_champ`,`ordre`,`lang`)") ;
	        
	        // Info de réindexation
	        $rqt = " select 1 " ;
	        echo traite_rqt($rqt,"<b><a href='".$base_path."/admin.php?categ=netbase' style='color : #FF0000' target=_blank>VOUS DEVEZ REINDEXER LES AUTORITES ET LE CONTENU EDITORIAL (APRES ETAPES DE MISE A JOUR) / YOU MUST REINDEX AUTHORITIES AND EDITORIAL CONTENT (STEPS AFTER UPDATE) : Admin > Outils > Nettoyage de base</a></b> ") ;
	    }
	    // TS - Modification de la clé primaire de la table cms_editorial_fields_global_index pour tenir compte de la langue
	    if (pmb_mysql_num_rows(pmb_mysql_query("SHOW INDEX FROM cms_editorial_fields_global_index WHERE Key_name='PRIMARY' AND Column_name = 'lang'"))==0){
	        $rqt = "TRUNCATE TABLE cms_editorial_fields_global_index";
	        echo traite_rqt($rqt,"truncate table cms_editorial_fields_global_index");
	        
	        $rqt = "ALTER TABLE `cms_editorial_fields_global_index` DROP PRIMARY KEY,ADD PRIMARY KEY(`num_obj`, `type`, `code_champ`,`code_ss_champ`,`ordre`,`lang`)" ;
	        echo traite_rqt($rqt,"ALTER TABLE `cms_editorial_fields_global_index` DROP PRIMARY KEY,ADD PRIMARY KEY(`num_obj`, `type`, `code_champ`,`code_ss_champ`,`ordre`,`lang`)") ;
	        
	        // Info de réindexation
	        $rqt = " select 1 " ;
	        echo traite_rqt($rqt,"<b><a href='".$base_path."/admin.php?categ=netbase' style='color : #FF0000' target=_blank>VOUS DEVEZ REINDEXER LES AUTORITES ET LE CONTENU EDITORIAL (APRES ETAPES DE MISE A JOUR) / YOU MUST REINDEX AUTHORITIES AND EDITORIAL CONTENT (STEPS AFTER UPDATE) : Admin > Outils > Nettoyage de base</a></b> ") ;
	    }
	case 35 :
	    // DG - Ajout d'un index sur la section dans la table pret_archive
	    $rqt = "alter table pret_archive drop index i_pa_arc_expl_section";
	    echo traite_rqt($rqt,"alter table pret_archive drop index i_pa_arc_expl_section");
	    $rqt = "alter table pret_archive add index i_pa_arc_expl_section(arc_expl_section)";
	    echo traite_rqt($rqt,"alter table pret_archive add index i_pa_arc_expl_section");
	case 36 :
	    // DG - Rendre la saisie de l'adresse mail expéditrice obligatoire sur le formulaire d'impression de recherche ?
	    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param='opac' and sstype_param='print_email_sender_mandatory' "))==0){
	        $rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
	  			VALUES (0, 'opac', 'print_email_sender_mandatory', '0', 'Rendre la saisie de l\'adresse mail expéditrice obligatoire sur le formulaire d\'impression de recherche ?\n 0 : Non \n 1 : Oui', 'a_general', '0')";
	        echo traite_rqt($rqt,"insert opac_print_email_sender_mandatory=0 into parametres ");
	    }
	case 37 :
	    //DG - Recalcul des dates de parution vide sur les articles de périodiques
	    $res=pmb_mysql_query("SELECT notices.notice_id, bulletins.date_date FROM notices JOIN analysis ON analysis.analysis_notice = notices.notice_id JOIN bulletins ON bulletins.bulletin_id = analysis.analysis_bulletin  WHERE niveau_biblio='a' AND year=''");
	    if($res && pmb_mysql_num_rows($res)){
	        while ($row=pmb_mysql_fetch_object($res)) {
	            $year = '';
	            if($row->date_date != '0000-00-00') {
	                $year = substr($row->date_date,0,4);
	            }
	            pmb_mysql_query("UPDATE notices SET year='".addslashes($year)."', date_parution='".$row->date_date."', update_date=update_date WHERE notice_id=".$row->notice_id);
	        }
	    }
	    $rqt = " select 1 " ;
	    echo traite_rqt($rqt,"UPDATE year and date_parution FOR articles");
	case 38 :
	    //DG - Mise à niveau des dates de parution sur les notices de bulletins
	    $res=pmb_mysql_query("SELECT notices.notice_id, bulletins.date_date FROM bulletins JOIN notices ON notices.notice_id = bulletins.num_notice AND niveau_biblio='b' AND bulletins.date_date != notices.date_parution");
	    if($res && pmb_mysql_num_rows($res)){
	        while ($row=pmb_mysql_fetch_object($res)) {
	            pmb_mysql_query("UPDATE notices SET date_parution='".$row->date_date."', update_date=update_date WHERE notice_id=".$row->notice_id);
	        }
	    }
	    $rqt = " select 1 " ;
	    echo traite_rqt($rqt,"UPDATE date_parution FOR bulletins records");
	case 39:
	    // BT - Ajout d'une colonne contenant un répertoire de template dans la table contribution_area_areas
	    $rqt = "ALTER TABLE contribution_area_areas ADD COLUMN area_repo_template_authorities VARCHAR(255) NOT NULL DEFAULT ''";
	    echo traite_rqt($rqt, "ALTER TABLE contribution_area_areas ADD COLUMN area_repo_template_authorities");
	    $rqt = "ALTER TABLE contribution_area_areas ADD COLUMN area_repo_template_records VARCHAR(255) NOT NULL DEFAULT ''";
	    echo traite_rqt($rqt, "ALTER TABLE contribution_area_areas ADD COLUMN area_repo_template_records");
	case 40 :
	    // DG - (Gestion) Modification du commentaire sur les réservations de documents disponibles
	    $rqt = "update parametres set comment_param='Réservations possibles de documents disponibles ? \n 0 : Non \n 1 : Oui \n 2 : Oui, sauf ceux empruntés' where type_param= 'pmb' and sstype_param='resa_dispo' ";
	    echo traite_rqt($rqt,"update pmb_resa_dispo into parametres");
	    
	    // DG - (OPAC) Modification du commentaire sur les réservations de documents disponibles
	    $rqt = "update parametres set comment_param='Réservations possibles de documents disponibles par l\'OPAC ? \n 0 : Non \n 1 : Oui \n 2 : Oui, sauf ceux empruntés' where type_param= 'opac' and sstype_param='resa_dispo' ";
	    echo traite_rqt($rqt,"update opac_resa_dispo into parametres");
	case 41 :
	    // DG - Ajout de la personnalisation des settings par utilisateur
	    $rqt = "ALTER TABLE lists ADD list_settings mediumtext AFTER list_selected_filters" ;
	    echo traite_rqt($rqt,"ALTER TABLE lists ADD list_settings");
	case 42 :
	    // MO/JL - Ajout d'un parametre forcant l'affichage en accordeon dans les contributions à l'OPAC
	    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='contribution_opac_accordion_result' "))==0){
	        $rqt = "INSERT INTO parametres (type_param,sstype_param,valeur_param,comment_param,section_param,gestion)
                    VALUES ('pmb','contribution_opac_accordion_result','','Parametre forcant l\'affichage en accordeon dans les contributions à l\'OPAC','',1)";
	        echo traite_rqt($rqt,"insert pmb_contribution_opac_accordion_result='' into parametres");
	    }
	case 43 :
	    // QV/GN - Ajout d'une table temporaire pour le copier/coller vers une autre espace du graph en gestion
	    $rqt = "CREATE TABLE IF NOT EXISTS contribution_area_clipboard (
            			`id_clipboard` INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
			            `datas` TEXT,
			            `created_at` DATETIME
			        );";
	    echo traite_rqt($rqt, 'CREATE TABLE contribution_area_clipboard');
	case 44 :
	    // DG : Ajout dans les préférences utilisateur du type d'abonnement par défaut en création de lecteur
	    $rqt = "ALTER TABLE users ADD deflt_type_abts INT(5) UNSIGNED NOT NULL DEFAULT 0 " ;
	    echo traite_rqt($rqt,"ALTER users ADD deflt_type_abts");
	case 45 :
	    //DG - Ajout de la visibilité dans les filtres associés aux champs personalisés
	    $rqt = "ALTER TABLE notices_custom ADD filters INT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER export" ;
	    echo traite_rqt($rqt,"ALTER TABLE notices_custom ADD filters ");
	    
	    $rqt = "ALTER TABLE author_custom ADD filters INT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER export" ;
	    echo traite_rqt($rqt,"ALTER TABLE author_custom ADD filters ");
	    
	    $rqt = "ALTER TABLE authperso_custom ADD filters INT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER export" ;
	    echo traite_rqt($rqt,"ALTER TABLE authperso_custom ADD filters ");
	    
	    $rqt = "ALTER TABLE categ_custom ADD filters INT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER export" ;
	    echo traite_rqt($rqt,"ALTER TABLE categ_custom ADD filters ");
	    
	    $rqt = "ALTER TABLE cms_editorial_custom ADD filters INT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER export" ;
	    echo traite_rqt($rqt,"ALTER TABLE cms_editorial_custom ADD filters ");
	    
	    $rqt = "ALTER TABLE collection_custom ADD filters INT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER export" ;
	    echo traite_rqt($rqt,"ALTER TABLE collection_custom ADD filters ");
	    
	    $rqt = "ALTER TABLE collstate_custom ADD filters INT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER export" ;
	    echo traite_rqt($rqt,"ALTER TABLE collstate_custom ADD filters ");
	    
	    $rqt = "ALTER TABLE demandes_custom ADD filters INT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER export" ;
	    echo traite_rqt($rqt,"ALTER TABLE demandes_custom ADD filters ");
	    
	    $rqt = "ALTER TABLE empr_custom ADD filters INT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER export" ;
	    echo traite_rqt($rqt,"ALTER TABLE empr_custom ADD filters ");
	    
	    $rqt = "ALTER TABLE expl_custom ADD filters INT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER export" ;
	    echo traite_rqt($rqt,"ALTER TABLE expl_custom ADD filters ");
	    
	    $rqt = "ALTER TABLE explnum_custom ADD filters INT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER export" ;
	    echo traite_rqt($rqt,"ALTER TABLE explnum_custom ADD filters ");
	    
	    $rqt = "ALTER TABLE gestfic0_custom ADD filters INT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER export" ;
	    echo traite_rqt($rqt,"ALTER TABLE gestfic0_custom ADD filters ");
	    
	    $rqt = "ALTER TABLE indexint_custom ADD filters INT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER export" ;
	    echo traite_rqt($rqt,"ALTER TABLE indexint_custom ADD filters ");
	    
	    $rqt = "ALTER TABLE publisher_custom ADD filters INT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER export" ;
	    echo traite_rqt($rqt,"ALTER TABLE publisher_custom ADD filters ");
	    
	    $rqt = "ALTER TABLE serie_custom ADD filters INT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER export" ;
	    echo traite_rqt($rqt,"ALTER TABLE serie_custom ADD filters ");
	    
	    $rqt = "ALTER TABLE skos_custom ADD filters INT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER export" ;
	    echo traite_rqt($rqt,"ALTER TABLE skos_custom ADD filters ");
	    
	    $rqt = "ALTER TABLE subcollection_custom ADD filters INT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER export" ;
	    echo traite_rqt($rqt,"ALTER TABLE subcollection_custom ADD custom_classement ");
	    
	    $rqt = "ALTER TABLE tu_custom ADD filters INT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER export" ;
	    echo traite_rqt($rqt,"ALTER TABLE tu_custom ADD custom_classement ");
	case 46 :
	    //DG - Modifications et ajout de commentaires pour les paramètres décrivant l'autoindexation
	    $rqt = "UPDATE parametres SET comment_param = 'Liste des champs de notice à utiliser pour l\'indexation automatique.\n\n";
	    $rqt.= "Syntaxe: nom_champ=poids_indexation|coché par défaut;\n\n";
	    $rqt.= "Les noms des champs sont ceux précisés dans le fichier XML \"pmb/includes/notice/notice.xml\"\n";
	    $rqt.= "Le poids de l\'indexation est une valeur de 0.00 à 1. (Si rien n\'est précisé, le poids est de 1)\n\n";
	    $rqt.= "Le champ est-il coché par défaut en recherche ? 0 : Non, 1 : Oui (Si rien n\'est précisé, sa valeur est 0)\n\n";
	    $rqt.= "Exemple :\n\n";
	    $rqt.= "tit1=1.00|1;n_resume=0.5;' ";
	    $rqt.= "WHERE type_param = 'thesaurus' and sstype_param='auto_index_notice_fields' ";
	    echo traite_rqt($rqt,"UPDATE parametres SET comment_param for thesaurus_auto_index_notice_fields") ;
	case 47 :
	    // DG - Création de la table de stockage des formulaires de contact opac
	    // id_contact_form : Identifiant
	    // contact_form_label : Libellé
	    // contact_form_desc : Description
	    // contact_form_parameters : Tableau des paramètres
	    // contact_form_recipients : Tableau des listes de destinataires
	    $rqt = "create table if not exists contact_forms(
				id_contact_form int unsigned not null auto_increment primary key,
				contact_form_label varchar(255) not null default '',
				contact_form_desc varchar(255) not null default '',
				contact_form_parameters mediumtext not null,
				contact_form_recipients mediumtext not null
			) ";
	    echo traite_rqt($rqt,"create table contact_forms");
	    
	    // DG - Association des objets à un formulaire de contact
	    $rqt = "ALTER TABLE contact_form_objects ADD num_contact_form int NOT NULL DEFAULT 1";
	    echo traite_rqt($rqt,"alter table contact_form_objects add num_contact_form ");
	    
	    // DG - Ajout de l'index sur num_contact_form
	    $rqt = "alter table contact_form_objects add index i_num_contact_form(num_contact_form)";
	    echo traite_rqt($rqt,"alter table contact_form_objects add index i_num_contact_form");
	    
	    // DG - Formulaire de contact par défaut
	    if (pmb_mysql_num_rows(pmb_mysql_query("select * from contact_forms"))==0){
	        global $pmb_contact_form_parameters, $pmb_contact_form_recipients_lists;
	        $contact_form_parameters = unserialize($pmb_contact_form_parameters);
	        if(!is_array($contact_form_parameters)) $contact_form_parameters = array();
	        $contact_form_recipients = unserialize($pmb_contact_form_recipients_lists);
	        if(!is_array($contact_form_recipients)) $contact_form_recipients = array();
	        $rqt = "INSERT INTO contact_forms (contact_form_label, contact_form_parameters, contact_form_recipients) VALUES ('Formulaire de contact','".addslashes(encoding_normalize::json_encode($contact_form_parameters))."','".addslashes(encoding_normalize::json_encode($contact_form_recipients))."')";
	        echo traite_rqt($rqt,"insert default into contact_form_objects");
	    }
	case 48 :
	    // QV/JL- Ajout d'un parametre pour la sauvegarde automatique des contributions à l'OPAC
	    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='contribution_opac_auto_save_draft' "))==0){
	        $rqt = "INSERT INTO parametres (type_param,sstype_param,valeur_param,comment_param,section_param,gestion)
                    VALUES ('pmb','contribution_opac_auto_save_draft','','Parametre permettant la sauvegarde automatique dans les contributions brouillons à l\'OPAC','',1)";
	        echo traite_rqt($rqt,"insert pmb_contribution_opac_auto_save_draft='' into parametres");
	    }
	
	case 49 :
		//DB - Modification de la clé primaire de la table skos_words_global_index
		$query = "SHOW KEYS FROM skos_words_global_index WHERE Key_name = 'PRIMARY'";
		$result = pmb_mysql_query($query);
		$primary_fields = array('id_item','code_champ','code_ss_champ','num_word','position','field_position');
		$flag = false;
		while($row = pmb_mysql_fetch_object($result)) {
			if(!in_array($row->Column_name, $primary_fields)) {
				$flag = true;
			}
		}
		if(!$flag && pmb_mysql_num_rows($result) != 6) {
			$flag = true;
		}
		if($flag) {
			$rqt ="alter table skos_words_global_index drop primary key";
			echo traite_rqt($rqt,"alter table skos_words_global_index drop primary key");
			$rqt ="alter table skos_words_global_index add primary key (id_item,code_champ,code_ss_champ,num_word,position,field_position)";
			echo traite_rqt($rqt,"alter table skos_words_global_index add primary key");
		}
		
	case 50 :
	    //DG - paramètre d'affichage du bloc d'adresse dans le mail de retard (niveau 1)
	    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'mailretard' and sstype_param='1sign_address' "))==0){
	        $rqt = "INSERT INTO parametres VALUES (0,'mailretard','1sign_address','1','Affichage des informations de la bibliothèque ou du centre de ressources dans la signature du mail ?\n 0 : Non\n 1 : Oui','',0)" ;
	        echo traite_rqt($rqt,"insert mailretard_1sign_address into parametres");
	    }
	    
	    //DG - paramètre d'affichage du bloc d'adresse dans le mail de retard (niveau 2)
	    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'mailretard' and sstype_param='2sign_address' "))==0){
	        $rqt = "INSERT INTO parametres VALUES (0,'mailretard','2sign_address','1','Affichage des informations de la bibliothèque ou du centre de ressources dans la signature du mail ?\n 0 : Non\n 1 : Oui','',0)" ;
	        echo traite_rqt($rqt,"insert mailretard_2sign_address into parametres");
	    }
	    
	    //DG - paramètre d'affichage du bloc d'adresse dans le mail de retard (niveau 3)
	    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'mailretard' and sstype_param='3sign_address' "))==0){
	        $rqt = "INSERT INTO parametres VALUES (0,'mailretard','3sign_address','1','Affichage des informations de la bibliothèque ou du centre de ressources dans la signature du mail ?\n 0 : Non\n 1 : Oui','',0)" ;
	        echo traite_rqt($rqt,"insert mailretard_3sign_address into parametres");
	    }
	    
	    //DG - paramètre d'affichage du bloc d'adresse dans le mail de relance d'adhésion
	    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'mailrelanceadhesion' and sstype_param='sign_address' "))==0){
	        $rqt = "INSERT INTO parametres VALUES (0,'mailrelanceadhesion','sign_address','1','Affichage des informations de la bibliothèque ou du centre de ressources dans la signature du mail ?\n 0 : Non\n 1 : Oui','',0)" ;
	        echo traite_rqt($rqt,"insert mailrelanceadhesion_sign_address into parametres");
	    }
	    
	    //DG - paramètre d'affichage du bloc d'adresse dans le mail de confirmation de réservation
	    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pdflettreresa' and sstype_param='sign_address' "))==0){
	        $rqt = "INSERT INTO parametres VALUES (0,'pdflettreresa','sign_address','1','Affichage des informations de la bibliothèque ou du centre de ressources dans la signature du mail ?\n 0 : Non\n 1 : Oui','',0)" ;
	        echo traite_rqt($rqt,"insert pdflettreresa_sign_address into parametres");
	    }
	    
	case 51 :
	    // Message informatif sur le paramétrage des quotas (Ajout du quota sur les nouveautés)
	    global $pmb_quotas_avances;
	    if ($pmb_quotas_avances) {
	        $rqt = " select 1 " ;
	        echo traite_rqt($rqt,"<b><a href='".$base_path."/admin.php?categ=quotas' style='color : #FF0000' target=_blank>VOUS DEVEZ VERIFIER LE PARAMETRAGE DES QUOTAS (APRES ETAPES DE MISE A JOUR) / YOU MUST CHECK THE QUOTAS SETTINGS (STEPS AFTER UPDATE) : Admin > Quotas > Quota sur les nouveautés récemment ajouté</a></b> ") ;
	    }
	    
	case 52 :
	    // DG : Afficher les items supprimés par défaut dans la liste ?
	    $rqt = "ALTER TABLE users ADD deflt_docwatch_watch_filter_deleted INT(1) UNSIGNED NOT NULL DEFAULT 0 " ;
	    echo traite_rqt($rqt,"ALTER users ADD deflt_docwatch_watch_filter_deleted");
	    
	case 53 :
	    //DG - Clarification du paramètre OPAC nb_results_first_page
	    $rqt = "update parametres set comment_param='Nombres de notices à afficher lors d\'une recherche pour le critère Tous les champs sur le niveau 1 de recherche (paramètre autolevel2 à 0).' where type_param= 'opac' and sstype_param='nb_results_first_page' ";
	    echo traite_rqt($rqt,"update opac_nb_results_first_page into parametres");
	
	case 54 :
	    //QV & GN - Ajout de la table responsability_authperso
	    $rqt = "CREATE TABLE if not exists responsability_authperso (
                id_responsability_authperso int(10) UNSIGNED NOT NULL,
                responsability_authperso_author mediumint(8) UNSIGNED NOT NULL DEFAULT 0,
                responsability_authperso_notice mediumint(8) UNSIGNED NOT NULL DEFAULT 0,
                responsability_authperso_fonction varchar(4) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
                responsability_authperso_type mediumint(1) UNSIGNED NOT NULL DEFAULT 0,
                responsability_authperso_ordre smallint(2) UNSIGNED NOT NULL DEFAULT 0
            )";
	    echo traite_rqt($rqt,"CREATE TABLE 'responsability_authperso'");
	    
	    //QV & GN - Ajout de la colonne authperso_responsability
	    $rqt = "ALTER TABLE authperso ADD authperso_responsability TINYINT NOT NULL DEFAULT 0 " ;
	    echo traite_rqt($rqt,"ALTER authperso ADD authperso_responsability");
	    
	case 55 :
	    //QV & GN - renommer la colonne responsability_authperso_notice -> responsability_authperso_num
	    $rqt = "ALTER TABLE responsability_authperso CHANGE responsability_authperso_notice responsability_authperso_num MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0'; " ;
	    echo traite_rqt($rqt,"ALTER responsability_authperso change responsability_authperso_notice responsability_authperso_num");
	    
	    //QV & GN - Ajout de clé dans la table responsability_authperso
	    $rqt = "ALTER TABLE responsability_authperso
                  ADD PRIMARY KEY (id_responsability_authperso,responsability_authperso_author,responsability_authperso_num,responsability_authperso_fonction),
                  ADD KEY responsability_authperso_num (responsability_authperso_num),
                  ADD KEY responsability_authperso_author (responsability_authperso_author);" ;
	    echo traite_rqt($rqt,"ALTER responsability_authperso ADD PRIMARY KEY");
	    
	    //QV & GN - Ajout primary key auto_increment dans la table responsability_authperso
	    $rqt = "ALTER TABLE responsability_authperso
                    MODIFY id_responsability_authperso int(10) UNSIGNED NOT NULL AUTO_INCREMENT;" ;
	    echo traite_rqt($rqt,"ALTER responsability_authperso AUTO_INCREMENT PRIMARY KEY");
	    
	case 56 :
	    // DG - Ajout de filtres optionnels dans la constitution d'étagères
	    $rqt = "ALTER TABLE etagere_caddie ADD etagere_caddie_filters mediumtext" ;
	    echo traite_rqt($rqt,"ALTER TABLE etagere_caddie ADD etagere_caddie_filters");
	    
	case 57 :
	    //JL - renommer la colonne authperso_responsability -> authperso_responsability_authperso
	    $rqt = "ALTER TABLE authperso CHANGE authperso_responsability authperso_responsability_authperso TINYINT(4) NOT NULL DEFAULT '0'" ;
	    echo traite_rqt($rqt,"ALTER TABLE authperso CHANGE authperso_responsability authperso_responsability_authperso");
	    
	case 58 :
	    // DG - Ajout de l'option mail + lettre sur la gestion des retards de niveau 3
	    $rqt = "update parametres set comment_param='Priorité des lettres de retard sur le troisième niveau de relance :\n 0 : Lettre seule \n 1 : Mail, à défaut lettre\n 2 : Mail ET lettre' where type_param= 'mailretard' and sstype_param='priorite_email_3' ";
	    echo traite_rqt($rqt,"update mailretard_priorite_email_3 into parametres");
	    
	case 59 :
	    // DG - Bascule des notices dans une liste de lecture depuis un panier : les enlever ensuite du panier
	    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param='opac' and sstype_param='cart_records_remove' "))==0){
	        $rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
	  			VALUES (0, 'opac', 'cart_records_remove', '0', 'Les notices sont enlevées du panier lors d\'une transformation en suggestion, liste de lecture ou réservation \n0 : Non \n1 : Oui', 'h_cart', '0')";
	        echo traite_rqt($rqt,"insert opac_cart_records_remove=0 into parametres ");
	    }
	    
	case 60 :
	    // GN- Ajout d'un parametre pour la modification d'une contribution validées
	    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='contribution_opac_edit_entity' "))==0){
	        $rqt = "INSERT INTO parametres (type_param,sstype_param,valeur_param,comment_param,section_param,gestion)
                    VALUES ('pmb','contribution_opac_edit_entity','','Parametre permettant la modification des contributions à l\'OPAC','',1)";
	        echo traite_rqt($rqt,"insert pmb_contribution_opac_edit_entity='' into parametres");
	    }
	    
	case 61 :
	    // GN- Ajout d'un parametre pour mettre un espace par défaut
        $rqt = "ALTER TABLE contribution_area_areas ADD area_editing_entity TINYINT NOT NULL DEFAULT '0' AFTER area_repo_template_records";
        echo traite_rqt($rqt,"ALTER TABLE contribution_area_areas ADD area_editing_entity");
        
	case 62 :
	    //DG - Ajout d'un paramètre utilisateur (choix du plan de classement par défaut)
	    $rqt = "ALTER TABLE users ADD deflt_pclassement INT(3) UNSIGNED DEFAULT 1 NOT NULL ";
	    echo traite_rqt($rqt, "ALTER TABLE users ADD deflt_pclassement");
	    
	    //DG - Ajout d'un paramètre utilisateur (campagne de mail cochée par défaut)
	    $rqt = "ALTER TABLE users ADD deflt_associated_campaign INT(1) UNSIGNED DEFAULT 0 NOT NULL ";
	    echo traite_rqt($rqt, "ALTER TABLE users ADD deflt_associated_campaign");
	    
	case 63 :
	    // DG - Opérateur entre les valeurs de facettes
	    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='facettes_operator' "))==0){
	        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES(0,'opac','facettes_operator','and','Opérateur sur le filtrage d\'une ou plusieurs facettes. or : pour le OU, and : pour le ET.','c_recherche',0)" ;
	        echo traite_rqt($rqt,"insert opac_facettes_operator into parametres") ;
	    }
	    
	case 64 :
	    // BT - Ajout d'un paramètre rendant obligatoire un destinataire d'une demande de numérisation
	    if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param = 'pmb' AND sstype_param = 'scan_request_empr_mandatory'")) == 0) {
    	    $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
                    VALUES (0, 'pmb', 'scan_request_empr_mandatory', '0', 'Rendre obligatoire le destinataire lors d\'une demande de numérisation \n 0 : Non \n 1 : Oui', '', 0)";
    	    echo traite_rqt($rqt, "INSERT scan_request_empr_mandatory INTO parametres");
	    }
	    
	case 65 :
	    //DG - Lettres de retard (niveau 1) - script de substitution
	    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pdflettreretard' and sstype_param='1print' "))==0){
	        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'pdflettreretard','1print','','Quel script utiliser pour personnaliser l\'impression des lettres de retard ?','',0)" ;
	        echo traite_rqt($rqt,"insert pdflettreretard_1print into parametres") ;
	    }
	    
	    //DG - Lettres de retard (niveau 2) - script de substitution
	    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pdflettreretard' and sstype_param='2print' "))==0){
	        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'pdflettreretard','2print','','Quel script utiliser pour personnaliser l\'impression des lettres de retard ?','',0)" ;
	        echo traite_rqt($rqt,"insert pdflettreretard_2print into parametres") ;
	    }
	    
	    //DG - Lettres de retard (niveau 3) - script de substitution
	    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pdflettreretard' and sstype_param='3print' "))==0){
	        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'pdflettreretard','3print','','Quel script utiliser pour personnaliser l\'impression des lettres de retard ?','',0)" ;
	        echo traite_rqt($rqt,"insert pdflettreretard_3print into parametres") ;
	    }
	    
	    //DG - Lettres de réservation - script de substitution
	    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pdflettreresa' and sstype_param='print' "))==0){
	        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'pdflettreresa','print','','Quel script utiliser pour personnaliser l\'impression des lettres de réservation ?','',0)" ;
	        echo traite_rqt($rqt,"insert pdflettreresa_print into parametres") ;
	    }
	    
	    //DG - Lettres de prêts en cours - script de substitution
	    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pdflettreloans' and sstype_param='print' "))==0){
	        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'pdflettreloans','print','','Quel script utiliser pour personnaliser l\'impression des lettres de prêts en cours ?','',0)" ;
	        echo traite_rqt($rqt,"insert pdflettreloans_print into parametres") ;
	    }
	    
	    //DG - Lettres de prêts en cours - debut_expl_1er_page
	    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pdflettreloans' and sstype_param='debut_expl_1er_page' "))==0){
	        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
        			VALUES(0,'pdflettreloans','debut_expl_1er_page','35','Début de la liste des exemplaires sur la première page, en mm. Doit être règlé en fonction du texte qui précède la liste des ouvrages, lequel peut être plus ou moins long.','',0)" ;
	        echo traite_rqt($rqt,"insert pdflettreloans_debut_expl_1er_page into parametres") ;
	    }
	    
	    //DG - Lettres de prêts en cours - debut_expl_page
	    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pdflettreloans' and sstype_param='debut_expl_page' "))==0){
	        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
        			VALUES(0,'pdflettreloans','debut_expl_page','10','Début de la liste des exemplaires sur les pages suivantes, en mm depuis le bord supérieur de la page.','',0)" ;
	        echo traite_rqt($rqt,"insert pdflettreloans_debut_expl_page into parametres") ;
	    }
	    
	    //DG - Lettres de prêts en cours - format_page
	    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pdflettreloans' and sstype_param='format_page' "))==0){
	        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
        			VALUES(0,'pdflettreloans','format_page','P','Format de la page : \r\n P : Portrait\r\n L : Landscape = paysage','',0)" ;
	        echo traite_rqt($rqt,"insert pdflettreloans_format_page into parametres") ;
	    }
	    
	    //DG - Lettres de prêts en cours - hauteur_page
	    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pdflettreloans' and sstype_param='hauteur_page' "))==0){
	        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
        			VALUES(0,'pdflettreloans','hauteur_page','297','Hauteur de la page en mm','',0)" ;
	        echo traite_rqt($rqt,"insert pdflettreloans_hauteur_page into parametres") ;
	    }
	    
	    //DG - Lettres de prêts en cours - largeur_page
	    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pdflettreloans' and sstype_param='largeur_page' "))==0){
	        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
        			VALUES(0,'pdflettreloans','largeur_page','210','Largeur de la page en mm','',0)" ;
	        echo traite_rqt($rqt,"insert pdflettreloans_largeur_page into parametres") ;
	    }
	    
	    //DG - Lettres de prêts en cours - limite_after_list
	    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pdflettreloans' and sstype_param='limite_after_list' "))==0){
	        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
        			VALUES(0,'pdflettreloans','limite_after_list','260','Position limite en bas de page. Si un élément imprimé tente de dépasser cette limite, il sera imprimé sur la page suivante.','',0)" ;
	        echo traite_rqt($rqt,"insert pdflettreloans_limite_after_list into parametres") ;
	    }
	    
	    //DG - Lettres de prêts en cours - nb_1ere_page
	    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pdflettreloans' and sstype_param='nb_1ere_page' "))==0){
	        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
        			VALUES(0,'pdflettreloans','nb_1ere_page','19','Nombre d\'ouvrages imprimé sur la première page','',0)" ;
	        echo traite_rqt($rqt,"insert pdflettreloans_nb_1ere_page into parametres") ;
	    }
	    
	    //DG - Lettres de prêts en cours - nb_par_page
	    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pdflettreloans' and sstype_param='nb_par_page' "))==0){
	        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
        			VALUES(0,'pdflettreloans','nb_par_page','21','Nombre d\'ouvrages imprimé sur les pages suivantes','',0)" ;
	        echo traite_rqt($rqt,"insert pdflettreloans_nb_par_page into parametres") ;
	    }
	    
	    //DG - Lettres de prêts en cours - taille_bloc_expl
	    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pdflettreloans' and sstype_param='taille_bloc_expl' "))==0){
	        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
        			VALUES(0,'pdflettreloans','taille_bloc_expl','12','Taille d\'un bloc (2 lignes) d\'ouvrage. Le début de chaque ouvrage sera espacé de cette valeur sur la page','',0)" ;
	        echo traite_rqt($rqt,"insert pdflettreloans_taille_bloc_expl into parametres") ;
	    }
	    
	    //DG - Lettres de prêts en cours - list_order
	    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pdflettreloans' and sstype_param='list_order' "))==0){
	        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
        			VALUES(0,'pdflettreloans','list_order','pret_date','Ordre d\'affichage des ouvrages, dans l\'ordre donné, séparé par des virgules.','',0)" ;
	        echo traite_rqt($rqt,"insert pdflettreloans_list_order into parametres") ;
	    }
	    
	    //DG - Lettres de prêts en cours par groupe - script de substitution
	    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pdflettreloansgroup' and sstype_param='print' "))==0){
	        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'pdflettreloansgroup','print','','Quel script utiliser pour personnaliser l\'impression des lettres de prêts en cours par groupe ?','',0)" ;
	        echo traite_rqt($rqt,"insert pdflettreloansgroup_print into parametres") ;
	    }
	    
	    //DG - Lettres de prêts en cours par groupe - debut_expl_1er_page
	    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pdflettreloansgroup' and sstype_param='debut_expl_1er_page' "))==0){
	        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
        			VALUES(0,'pdflettreloansgroup','debut_expl_1er_page','35','Début de la liste des exemplaires sur la première page, en mm. Doit être règlé en fonction du texte qui précède la liste des ouvrages, lequel peut être plus ou moins long.','',0)" ;
	        echo traite_rqt($rqt,"insert pdflettreloansgroup_debut_expl_1er_page into parametres") ;
	    }
	    
	    //DG - Lettres de prêts en cours par groupe - debut_expl_page
	    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pdflettreloansgroup' and sstype_param='debut_expl_page' "))==0){
	        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
        			VALUES(0,'pdflettreloansgroup','debut_expl_page','10','Début de la liste des exemplaires sur les pages suivantes, en mm depuis le bord supérieur de la page.','',0)" ;
	        echo traite_rqt($rqt,"insert pdflettreloansgroup_debut_expl_page into parametres") ;
	    }
	    
	    //DG - Lettres de prêts en cours par groupe - format_page
	    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pdflettreloansgroup' and sstype_param='format_page' "))==0){
	        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
        			VALUES(0,'pdflettreloansgroup','format_page','P','Format de la page : \r\n P : Portrait\r\n L : Landscape = paysage','',0)" ;
	        echo traite_rqt($rqt,"insert pdflettreloansgroup_format_page into parametres") ;
	    }
	    
	    //DG - Lettres de prêts en cours par groupe - hauteur_page
	    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pdflettreloansgroup' and sstype_param='hauteur_page' "))==0){
	        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
        			VALUES(0,'pdflettreloansgroup','hauteur_page','297','Hauteur de la page en mm','',0)" ;
	        echo traite_rqt($rqt,"insert pdflettreloansgroup_hauteur_page into parametres") ;
	    }
	    
	    //DG - Lettres de prêts en cours par groupe - largeur_page
	    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pdflettreloansgroup' and sstype_param='largeur_page' "))==0){
	        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
        			VALUES(0,'pdflettreloansgroup','largeur_page','210','Largeur de la page en mm','',0)" ;
	        echo traite_rqt($rqt,"insert pdflettreloansgroup_largeur_page into parametres") ;
	    }
	    
	    //DG - Lettres de prêts en cours par groupe - limite_after_list
	    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pdflettreloansgroup' and sstype_param='limite_after_list' "))==0){
	        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
        			VALUES(0,'pdflettreloansgroup','limite_after_list','250','Position limite en bas de page. Si un élément imprimé tente de dépasser cette limite, il sera imprimé sur la page suivante.','',0)" ;
	        echo traite_rqt($rqt,"insert pdflettreloansgroup_limite_after_list into parametres") ;
	    }
	    
	    //DG - Lettres de prêts en cours par groupe - nb_1ere_page
	    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pdflettreloansgroup' and sstype_param='nb_1ere_page' "))==0){
	        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
        			VALUES(0,'pdflettreloansgroup','nb_1ere_page','19','Nombre d\'ouvrages imprimé sur la première page','',0)" ;
	        echo traite_rqt($rqt,"insert pdflettreloansgroup_nb_1ere_page into parametres") ;
	    }
	    
	    //DG - Lettres de prêts en cours par groupe - nb_par_page
	    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pdflettreloansgroup' and sstype_param='nb_par_page' "))==0){
	        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
        			VALUES(0,'pdflettreloansgroup','nb_par_page','21','Nombre d\'ouvrages imprimé sur les pages suivantes','',0)" ;
	        echo traite_rqt($rqt,"insert pdflettreloansgroup_nb_par_page into parametres") ;
	    }
	    
	    //DG - Lettres de prêts en cours par groupe - taille_bloc_expl
	    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pdflettreloansgroup' and sstype_param='taille_bloc_expl' "))==0){
	        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
        			VALUES(0,'pdflettreloansgroup','taille_bloc_expl','12','Taille d\'un bloc (2 lignes) d\'ouvrage. Le début de chaque ouvrage sera espacé de cette valeur sur la page','',0)" ;
	        echo traite_rqt($rqt,"insert pdflettreloansgroup_taille_bloc_expl into parametres") ;
	    }
	    
	    //DG - Lettres de prêts en cours par groupe - list_order
	    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pdflettreloansgroup' and sstype_param='list_order' "))==0){
	        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
        			VALUES(0,'pdflettreloansgroup','list_order','empr_nom, empr_prenom','Ordre d\'affichage des lecteurs, dans l\'ordre donné, séparé par des virgules.','',0)" ;
	        echo traite_rqt($rqt,"insert pdflettreloansgroup_list_order into parametres") ;
	    }
	    
	    //DG - Lettres de prêts en cours par groupe - list_order_from_empr
	    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pdflettreloansgroup' and sstype_param='list_order_from_empr' "))==0){
	        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
        			VALUES(0,'pdflettreloansgroup','list_order_from_empr','pret_date','Ordre d\'affichage des ouvrages, dans l\'ordre donné, séparé par des virgules.','',0)" ;
	        echo traite_rqt($rqt,"insert pdflettreloansgroup_list_order_from_empr into parametres") ;
	    }
	    
	case 66 :
	    // BT - Ajout d'une table de responsabilité pour les contributions
	    $rqt = "CREATE TABLE IF NOT EXISTS responsability_contribution (
                 	id_responsability_contribution INT UNSIGNED NOT NULL AUTO_INCREMENT,
					responsability_contribution_author_num INT UNSIGNED NOT NULL DEFAULT 0,
					responsability_contribution_num INT UNSIGNED NOT NULL DEFAULT 0,
					responsability_contribution_fonction CHAR(4) NOT NULL DEFAULT '',
					responsability_contribution_type INT UNSIGNED NOT NULL DEFAULT 0,
					responsability_contribution_ordre SMALLINT(2) UNSIGNED NOT NULL DEFAULT 0,
					PRIMARY KEY (id_responsability_contribution)
                )";
        echo traite_rqt($rqt, "CREATE TABLE responsability_contribution");
        
	case 67 :
	    // DG - Modification de la taille du champ num_object de la table authorities
	    $rqt = "ALTER TABLE authorities MODIFY num_object int(9) UNSIGNED NOT NULL default 0" ;
	    echo traite_rqt($rqt,"ALTER TABLE authorities MODIFY num_object to int(9)");
	    
	case 68 :
	    // DG - Modification du paramètre notice_controle_doublons
	    $rqt = "update parametres set comment_param = 'Contrôle sur les doublons en saisie de la notice \n 0: Pas de contrôle sur les doublons, \n 1,tit1,tit2, ... : Recherche par méthode _exacte_ de doublons sur des champs, défini dans le fichier notice.xml  \n 2,tit1,tit2, ... : Recherche par _similitude_ \nGénérer les signatures (nettoyage de base) si l\'on change la valeur du paramètre' where type_param='pmb' and sstype_param = 'notice_controle_doublons'";
	    echo traite_rqt($rqt,"update parametres pmb_notice_controle_doublons set comment");
	case 69 :
	    //MO - Ajout d'un parametre de visibilité à l'opac sur les espaces de contribution
	    // Case à cocher pour le rendre accessible ou non par un lecteur à l'opac
	    $rqt = "ALTER TABLE contribution_area_areas ADD area_opac_visibility INT(1) NOT NULL DEFAULT 1 AFTER area_status";
	    echo traite_rqt($rqt,"alter table contribution_area_areas add area_opac_visibility");
	case 70:
	    // BT - Suppression de la table de responsabilité pour les contributions, on enregistre plus proprement via le mécanisme existant!
	    $rqt = "DROP TABLE IF EXISTS responsability_contribution;";
	    echo traite_rqt($rqt, "DROP TABLE responsability_contribution");
	case 71:
	    //GN-QV - Ajout d'un parametre en opac sur les paniers, rendre visible ou non "autres actions"
	    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='cart_more_actions_activate' "))==0){
	        $rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
                VALUES (NULL, 'opac', 'cart_more_actions_activate', '0', 'Activer le bouton \"autres actions\" dans le panier d\'emprunteurs :\n 0 : non activé \n 1 : activé', 'h_cart', '0')";
	        echo traite_rqt($rqt,"insert opac_cart_more_actions_activate='0' into parametres ");
	    }
	case 72:
	    //JL-DB - Ajout d'un parametre de choix du mode d'affichage du dialogue lors du prêt numérique
	    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='pnb_loan_display_mode' "))==0){
	        $rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
                VALUES (NULL, 'opac', 'pnb_loan_display_mode', '0', 'Choix du mode d\'affichage du dialogue lors du prêt numérique:\n 0 : modale \n 1 : inline', '', '0')";
	        echo traite_rqt($rqt,"insert pnb_loan_display_mode='0' into parametres ");
	    }
	case 73 :
		// DB - PNB : Ajout d'un paramètre caché pour affecter une localisation aux exemplaires en prêt numérique
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='pnb_location_id' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
                VALUES (0, 'pmb', 'pnb_location_id', '0', 'Localisation des exemplaires en prêt numérique', '', 1)";
			echo traite_rqt($rqt, "insert pmb_pnb_location_id into parametres");
		}
		// DB - PNB : Ajout d'un paramètre caché pour affecter une section aux exemplaires en prêt numérique
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='pnb_section_id' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
                VALUES (0, 'pmb', 'pnb_section_id', '0', 'Section des exemplaires en prêt numérique', '', 1)";
			echo traite_rqt($rqt, "insert pmb_pnb_section_id into parametres");
		}
		// DB - PNB : Ajout d'un paramètre caché pour affecter un propriétaire aux exemplaires en prêt numérique
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='pnb_owner_id' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
                VALUES (0, 'pmb', 'pnb_owner_id', '0', 'Propriétaire des exemplaires en prêt numérique', '', 1)";
			echo traite_rqt($rqt, "insert pmb_pnb_owner_id into parametres");
		}
		// DB - PNB : Modification commentaire du paramètre pmb_pnb_codestat_id
		$rqt = "update ignore parametres set comment_param='Code statistique des exemplaires en prêt numérique' where type_param='pmb' and sstype_param='pnb_codestat_id'";
		echo traite_rqt($rqt, "update parametres change comment for pmb_pnb_codestat_id");
		// DB - PNB : Modification commentaire du paramètre pmb_pnb_statut_id
		$rqt = "update ignore parametres set comment_param='Statut des exemplaires en prêt numérique' where type_param='pmb' and sstype_param='pnb_statut_id'";
		echo traite_rqt($rqt, "update parametres change comment for pmb_pnb_statut_id");
		// DB - PNB : Modification commentaire du paramètre pmb_pnb_typedoc_id
		$rqt = "update ignore parametres set comment_param='Support des exemplaires en prêt numérique' where type_param='pmb' and sstype_param='pnb_typedoc_id'";
		echo traite_rqt($rqt, "update parametres change comment for pmb_pnb_typedoc_id");
		// DB - PNB : Modification commentaire du paramètre pnb_alert_end_offers
		$rqt = "update ignore parametres set comment_param='Nombre de jours entre le déclenchement de l\'alerte et l\'expiration des commandes' where type_param='pmb' and sstype_param='pnb_alert_end_offers'";
		echo traite_rqt($rqt, "update parametres change comment for pnb_alert_end_offers");
		
		// DB - PNB : Modification commentaire du paramètre pnb_alert_staturation_offers
		$rqt = "update ignore parametres set comment_param='Nombre d\'exemplaires restants avant déclenchement de l\'alerte pour les commandes arrivant à saturation' where type_param='pmb' and sstype_param='pnb_alert_staturation_offers'";
		echo traite_rqt($rqt, "update parametres change comment for pnb_alert_staturation_offers");
	case 74:
	    //DG - maj valeurs possibles pour empr_show_rows
	    $rqt = "update parametres set comment_param='Colonnes affichées en liste de lecteurs, saisir les colonnes séparées par des virgules. Les colonnes disponibles pour l\'affichage de la liste des emprunteurs sont : \n n: nom+prénom \n a: adresse \n b: code-barre \n c: catégories \n g: groupes \n l: localisation \n s: statut \n cp: code postal \n v: ville \n y: année de naissance \n ab: type d\'abonnement \n em: e-mail \n t: téléphone \n #e[n] : [n] = id des champs personnalisés lecteurs \n 1: icône panier' where type_param= 'empr' and sstype_param='show_rows' ";
	    echo traite_rqt($rqt,"update empr_show_rows into parametres");
	case 75 :
	    // DB - JL : Ajout d'une colonne pour y stocker les donnees de la notice provenant du PNB (fichier d'offre)
	    $rqt = "ALTER TABLE pnb_orders ADD pnb_order_data BLOB NOT NULL AFTER pnb_current_nta";
	    echo traite_rqt($rqt,"alter table pnb_orders add pnb_order_data");
	case 76 :
	    //GN - JL - Alerter l'utilisateur par mail des nouvelles contributions proposées ?
	    $rqt = "ALTER TABLE users ADD user_alert_contribmail INT(1) UNSIGNED NOT NULL DEFAULT 0 after user_alert_resamail";
	    echo traite_rqt($rqt,"ALTER TABLE users add user_alert_contribmail default 0");
	case 77 :
	    //MO - TS - Preferences utilisateur pour outrepasser la page de saisie de l'isbn
	    $rqt = "ALTER TABLE users ADD deflt_bypass_isbn_page INT(1) UNSIGNED DEFAULT 0 NOT NULL ";
	    echo traite_rqt($rqt, "ALTER TABLE users ADD deflt_bypass_isbn_page");
	case 78 :
	    //GN - JL - Ajout d'un parametre pour mettre une image de fond sur les espaces de contribution
	    $rqt = "ALTER TABLE contribution_area_areas ADD area_logo VARCHAR(255) NULL AFTER area_editing_entity";
	    echo traite_rqt($rqt, "ALTER TABLE contribution_area_areas ADD area_logo");
}












/******************** JUSQU'ICI **************************************************/
/* PENSER à faire +1 au paramètre $pmb_subversion_database_as_it_shouldbe dans includes/config.inc.php */
/* COMMITER les deux fichiers addon.inc.php ET config.inc.php en même temps */

echo traite_rqt("update parametres set valeur_param='".$pmb_subversion_database_as_it_shouldbe."' where type_param='pmb' and sstype_param='bdd_subversion'","Update to $pmb_subversion_database_as_it_shouldbe database subversion.");
echo "<table>";