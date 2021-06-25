<?php
// +-------------------------------------------------+
//  2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: alter_vLlxXenialPlus.inc.php, migración BD a v5.33 (Lliurex 21 Focal desde v.5.28 (Lliurex 16+ Xenial)


if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

settype ($action,"string");

//----------------------LLIUREX 16/12/2018-----------------------------------------
//----Changed calls to database. Used pmb_mysql instead mysql_ ------

pmb_mysql_query("set names latin1 ", $dbh);

//-------------------FIN LLIUREX 19/12/2018------------------------------

switch ($version_pmb_bdd) {
	case "v5.28":
	//	case "v5.28": 
		// 6 actualizaciones desde xenial+ (v5.28) a focal (v5.33)
		$increment=100/6;
		$action=$increment;
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";

			// +-------------------------------------------------+
			// JP - Ajout index sur resa_idempr de la table resa
			$req="SHOW INDEX FROM resa WHERE key_name LIKE 'i_resa_idempr'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table resa add index i_resa_idempr (resa_idempr)";
				echo traite_rqt($rqt,"alter table resa add index i_resa_idempr");
			}
				
			// JP - Ajout index sur num_suggestion de la table suggestions_origine
			$req="SHOW INDEX FROM suggestions_origine WHERE key_name LIKE 'i_num_suggestion'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table suggestions_origine add index i_num_suggestion (num_suggestion)";
				echo traite_rqt($rqt,"alter table suggestions_origine add index i_num_suggestion");
			}
				
			// JP - Ajout index sur resa_trans de la table transferts_demande
			$req="SHOW INDEX FROM transferts_demande WHERE key_name LIKE 'i_resa_trans'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table transferts_demande add index i_resa_trans (resa_trans)";
				echo traite_rqt($rqt,"alter table transferts_demande add index i_resa_trans");
			}
				
			// JP - Ajout index sur realisee de la table transactions
			$req="SHOW INDEX FROM transactions WHERE key_name LIKE 'i_realisee'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table transactions add index i_realisee (realisee)";
				echo traite_rqt($rqt,"alter table transactions add index i_realisee");
			}
				
			// JP - Ajout index sur compte_id de la table transactions
			$req="SHOW INDEX FROM transactions WHERE key_name LIKE 'i_compte_id'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table transactions add index i_compte_id (compte_id)";
				echo traite_rqt($rqt,"alter table transactions add index i_compte_id");
			}
			
			// DG - Ajout index sur id_notice de la table notices_fields_global_index
			$req="SHOW INDEX FROM notices_fields_global_index WHERE key_name LIKE 'i_id_notice'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table notices_fields_global_index add index i_id_notice (id_notice)";
				echo traite_rqt($rqt,"alter table notices_fields_global_index add index i_id_notice");
			}
			
			// DG - Ajout index sur id_authority de la table authorities_words_global_index
			$req="SHOW INDEX FROM authorities_words_global_index WHERE key_name LIKE 'i_id_authority'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table authorities_words_global_index add index i_id_authority (id_authority)";
				echo traite_rqt($rqt,"alter table authorities_words_global_index add index i_id_authority");
			}
				
			// DG - Ajout index sur id_authority de la table authorities_fields_global_index
			$req="SHOW INDEX FROM authorities_fields_global_index WHERE key_name LIKE 'i_id_authority'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table authorities_fields_global_index add index i_id_authority (id_authority)";
				echo traite_rqt($rqt,"alter table authorities_fields_global_index add index i_id_authority");
			}
						
			// VT & AP - Nouvelle table pour les régimes de licence de documents numériques
					// id_explnum_licence : Identifiant
					// explnum_licence_label : Libellé
					// explnum_licence_uri : URI
					$rqt = "CREATE TABLE IF NOT EXISTS explnum_licence (
						id_explnum_licence int(10) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
						explnum_licence_label varchar(255) DEFAULT '',
						explnum_licence_uri varchar(255) DEFAULT ''
					)";
			echo traite_rqt($rqt,"create table explnum_licence");
		
			// VT & AP - Nouvelle table pour les profils de régimes de licence de documents numériques
					// id_explnum_licence_profile : Identifiant
					// explnum_licence_profile_explnum_licence_num : Identifiant du régime de licence
					// explnum_licence_profile_label : Libellé
					// explnum_licence_profile_uri : URI
					// explnum_licence_profile_logo_url : URL du logo
					// explnum_licence_profile_explanation : Texte explicatif
					// explnum_licence_profile_quotation_rights : Droit de citation du profil
					$rqt = "CREATE TABLE IF NOT EXISTS explnum_licence_profiles (
						id_explnum_licence_profile int(10) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
						explnum_licence_profile_explnum_licence_num int(10) unsigned NOT NULL DEFAULT 0,
						explnum_licence_profile_label varchar(255) DEFAULT '',
						explnum_licence_profile_uri varchar(255) DEFAULT '',
						explnum_licence_profile_logo_url varchar(255) DEFAULT '',
						explnum_licence_profile_explanation text,
						explnum_licence_profile_quotation_rights text,
						INDEX i_elp_explnum_licence_num (explnum_licence_profile_explnum_licence_num)
					)";
			echo traite_rqt($rqt,"create table explnum_licence_profiles");
		
			// VT & AP - Nouvelle table pour les droits de régimes de licence de documents numériques
					// id_explnum_licence_right : Identifiant
					// explnum_licence_profile_explnum_licence_num : Identifiant du régime de licence
					// explnum_licence_right_label : Libellé
					// explnum_licence_right_type : Type de droit (Autorisation / Interdiction)
					// explnum_licence_right_logo_url : URL du logo
					// explnum_licence_right_explanation : Texte explicatif
					$rqt = "CREATE TABLE IF NOT EXISTS explnum_licence_rights (
						id_explnum_licence_right int(10) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
						explnum_licence_right_explnum_licence_num int(10) unsigned NOT NULL DEFAULT 0,
						explnum_licence_right_label varchar(255) DEFAULT '',
						explnum_licence_right_type int(2) DEFAULT 0,
						explnum_licence_right_logo_url varchar(255) DEFAULT '',
						explnum_licence_right_explanation text,
						INDEX i_elr_explnum_licence_num (explnum_licence_right_explnum_licence_num)
					)";
			echo traite_rqt($rqt,"create table explnum_licence_rights");
			
			// VT & AP - Nouvelle table pour les liens droits / profils
					// explnum_licence_right_num : Identifiant du droit
					// explnum_licence_profile_num : Identifiant du lien
					$rqt = "CREATE TABLE IF NOT EXISTS explnum_licence_profile_rights (
						explnum_licence_profile_num INT not null DEFAULT 0,
						explnum_licence_right_num INT not null DEFAULT 0,
						PRIMARY KEY (explnum_licence_profile_num, explnum_licence_right_num)
					)";
			echo traite_rqt($rqt,"create table explnum_licence_profile_rights");

			// AP & VT - Nouvelle table associant un document numérique à un régime de licence
			// explnum_licence_explnums_licence_num : Identifiant du régime de licence
			// explnum_licence_explnums_explnum_num : Identifiant du document numérique
			$rqt = "CREATE TABLE IF NOT EXISTS explnum_licence_profile_explnums (
						explnum_licence_profile_explnums_explnum_num int(10) unsigned NOT NULL DEFAULT 0,
						explnum_licence_profile_explnums_profile_num int(10) unsigned NOT NULL DEFAULT 0,
						PRIMARY KEY (explnum_licence_profile_explnums_explnum_num, explnum_licence_profile_explnums_profile_num),
						INDEX i_elpe_explnum_profile_num (explnum_licence_profile_explnums_profile_num)
					)";
			echo traite_rqt($rqt,"create table explnum_licence_profile_explnums");
			
			// DG - Modification de la clé primaire de la table authorities_words_global_index
			$query = "SHOW KEYS FROM authorities_words_global_index WHERE Key_name = 'PRIMARY'";
			$result = pmb_mysql_query($query);
			$primary_fields = array('id_authority','code_champ','code_ss_champ','num_word','position','field_position');
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
				$rqt ="alter table authorities_words_global_index drop primary key";
				echo traite_rqt($rqt,"alter table authorities_words_global_index drop primary key");
				$rqt ="alter table authorities_words_global_index add primary key (id_authority,code_champ,code_ss_champ,num_word,position,field_position)";
				echo traite_rqt($rqt,"alter table authorities_words_global_index add primary key");
			}
			
			// NG - Zone d'affichage par défaut de la carte
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='map_bounding_box' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'pmb', 'map_bounding_box', '-5 50,9 50,9 40,-5 40,-5 50', 'Zone d\'affichage par défaut de la carte. Coordonnées d\'un polygone fermé, en degrés décimaux','map', 0)";
				echo traite_rqt($rqt,"insert pmb_map_bounding_box into parametres");
			}
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='map_bounding_box' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'opac', 'map_bounding_box', '-5 50,9 50,9 40,-5 40,-5 50', 'Zone d\'affichage par défaut de la carte. Coordonnées d\'un polygone fermé, en degrés décimaux','map', 0)";
				echo traite_rqt($rqt,"insert opac_map_bounding_box into parametres");
			}
	
			echo "</table>";
			$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
			$res = pmb_mysql_query($rqt, $dbh) ;
			echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
			$action=$action+$increment;
			//echo form_relance ("v5.29");
		
		//case "v5.29":
			echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
			// +-------------------------------------------------+
			//JP - Impression tickets de prêt via raspberry pi
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='printer_name' "))){
			    $rqt = "update parametres set comment_param=CONCAT(comment_param,'\n\nSi l\'imprimante est connectée à un Raspberry Pi, indiquer l\'ip et le port\nExemple : raspberry@192.168.0.82:3000') where type_param='pmb' and sstype_param='printer_name' " ;
			    echo traite_rqt($rqt,"update parameters pmb_printer_name");
			}
			
			// JP - Rectification index sur index_author / author_type de la table authors
			$rqt = "alter table authors drop index i_index_author_author_type";
			echo traite_rqt($rqt,"alter table authors drop index i_index_author_author_type");
			$rqt = "alter table authors add index i_index_author_author_type (index_author (350), author_type)";
			echo traite_rqt($rqt,"alter table authors add index i_index_author_author_type");
			
			//JP - Ajout d'une colonne commentaire dans la table des recherches prédéfinies
			if (!pmb_mysql_num_rows(pmb_mysql_query("SHOW COLUMNS FROM search_perso LIKE 'search_comment'"))){
			    $rqt = "alter table search_perso add search_comment text not null";
			    echo traite_rqt($rqt,"alter table search_perso add search_comment");
			}
			//JP - Export des informations de documents numériques dans les notices en unimarc pmb xml
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='export_allow_expl' "))){
			    $rqt = "update parametres set comment_param='Exporter les exemplaires et les documents numériques avec les notices :\n 0 : Aucun\n 1 : Uniquement les exemplaires\n 2 : Uniquement les documents numériques\n 3 : Les exemplaires et les documents numériques' where type_param='opac' and sstype_param='export_allow_expl' " ;
			    echo traite_rqt($rqt,"update parameters opac_export_allow_expl");
			}
			
			//JP - Paramètre gérant l'entête de la fiche lecteur
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'empr' and sstype_param='header_format' "))){
			    $rqt = "update parametres set comment_param='Champs qui seront affichés dans l\'entête de la fiche emprunteur. Séparer les valeurs par des virgules. \nPour les champs personnalisés, saisir les identifiants. Les autres valeurs possibles sont les propriétés de la classe PHP \"pmb/opac_css/classes/emprunteur.class.php\".' where type_param='empr' and sstype_param='header_format' " ;
			    echo traite_rqt($rqt,"update parameters empr_header_format");
			}
			
			//JP & MB - Ajout d'index sur la table cms_cache_cadres
			$req="SHOW INDEX FROM cms_cache_cadres WHERE key_name LIKE 'i_cache_cadre_create_date'";
			$res=pmb_mysql_query($req);
			if($res && (pmb_mysql_num_rows($res) == 0)){
			    $rqt = "alter table cms_cache_cadres add index i_cache_cadre_create_date(cache_cadre_create_date)";
			    echo traite_rqt($rqt,"alter table cache_cadre_create_date add index i_cache_cadre_create_date");
			}
			
			// AP & VT - Gestion des traductions
			$req="SHOW COLUMNS from translation like 'trans_small_text'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
			    // AP & VT - Modification du nom de la colonne trans_text sur la table translation
			    $rqt = "ALTER TABLE translation change trans_text trans_small_text varchar(255)";
			    echo traite_rqt($rqt,"ALTER TABLE translation rename trans_text to trans_small_text varchar(255)") ;
			    
			    // AP & VT - Modification de la table translation ajout d'une colonne trans_text
			    $rqt = "ALTER TABLE translation add trans_text text";
			    echo traite_rqt($rqt,"ALTER TABLE translation add trans_text text") ;
			}
			
			// VT & AP - Ajout d'un droit sur le statut de lecteur pour les contributions
			$rqt = "alter table empr_statut add allow_contribution int unsigned not null default 0";
			echo traite_rqt($rqt,"alter table empr_statut add allow_contribution");
			
			
			// AP & VT - Modification du nom du parametre empr_contribution en empr_contribution_area
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'gestion_acces' and sstype_param='empr_contribution' "))){
			    $rqt = "update parametres set sstype_param='empr_contribution_area' where type_param='gestion_acces' and sstype_param='empr_contribution' " ;
			    echo traite_rqt($rqt,"update parameters set sstype_param='empr_contribution_area' where sstype_param='empr_contribution'");
			}
			
			// AP & VT - Modification du nom du parametre empr_contribution_def en empr_contribution_area_def
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'gestion_acces' and sstype_param='empr_contribution_def' "))){
			    $rqt = "update parametres set sstype_param='empr_contribution_area_def' where type_param='gestion_acces' and sstype_param='empr_contribution_def' " ;
			    echo traite_rqt($rqt,"update parameters set sstype_param='empr_contribution_area_def' where sstype_param='empr_contribution_def'");
			}
			
			// AP & VT - Ajout du parametre empr_contribution_scenario dans les droits d'acces
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'gestion_acces' and sstype_param='empr_contribution_scenario' "))==0){
			    $rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'gestion_acces', 'empr_contribution_scenario', '0', 'Gestion des droits d\'accès des emprunteurs aux scénarios de contribution\n0 : Non.\n1 : Oui.', '', 0)";
			    echo traite_rqt($rqt,"insert empr_contribution_scenario into parametres");
			}
			
			// AP & VT - Ajout du parametre empr_contribution_scenario_def dans les droits d'acces
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'gestion_acces' and sstype_param='empr_contribution_scenario_def' "))==0){
			    $rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'gestion_acces', 'empr_contribution_scenario_def', '0', 'Valeur par défaut en modification de scenario de contribution pour les droits d\'accès emprunteurs - scénarios\n0 : Recalculer.\n1 : Choisir.', '', 0)";
			    echo traite_rqt($rqt,"insert empr_contribution_scenario into parametres");
			}
			
			// AP & VT - Ajout du parametre contribution_moderator_empr dans les droits d'acces
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'gestion_acces' and sstype_param='contribution_moderator_empr' "))==0){
			    $rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'gestion_acces', 'contribution_moderator_empr', '0', 'Gestion des droits d\'accès des modérateurs sur les contributeurs\n0 : Non.\n1 : Oui.', '', 0)";
			    echo traite_rqt($rqt,"insert contribution_moderator_empr into parametres");
			}
			
			// AP & VT - Ajout du parametre contribution_moderator_empr_def dans les droits d'acces
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'gestion_acces' and sstype_param='contribution_moderator_empr_def' "))==0){
			    $rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'gestion_acces', 'contribution_moderator_empr_def', '0', 'Valeur par défaut en modification d\'emprunteur pour les droits d\'accès modérateur - emprunteur\n0 : Recalculer.\n1 : Choisir.', '', 0)";
			    echo traite_rqt($rqt,"insert contribution_moderator_empr_def into parametres");
			}
			
			// AP & VT - Suppression du statut sur les formulaires de contribution
			if (pmb_mysql_num_rows(pmb_mysql_query("SHOW COLUMNS FROM contribution_area_forms LIKE 'form_status'"))){
			    $rqt = "ALTER TABLE contribution_area_forms drop column form_status";
			    echo traite_rqt($rqt,"ALTER TABLE contribution_area_forms drop column form_status");
			}
			
			// AP & VT - Ajout d'un statut sur les espaces de contributions
			if (pmb_mysql_num_rows(pmb_mysql_query("SHOW COLUMNS FROM contribution_area_areas LIKE 'area_status'"))==0){
			    $rqt = "ALTER TABLE contribution_area_areas add column area_status int(10) unsigned not null default 1";
			    echo traite_rqt($rqt,"ALTER TABLE contribution_area_areas add column area_status");
			}
			
			// TS & VT - table des univers de recherche
			$rqt = "CREATE TABLE IF NOT EXISTS exploded_search_universes (
						exploded_search_universes_id int(10) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
						exploded_search_universes_label varchar(255) DEFAULT ''
					)";
			echo traite_rqt($rqt,"create table exploded_search_universes");
			
			
			// TS & VT - Table associant un univers à des vues
			// exploded_search_universes_views_num_universe : Identifiant de l'univers
			// exploded_search_universes_views_num_view : Identifiant de la view
			$rqt = "CREATE TABLE IF NOT EXISTS exploded_search_universes_views (
						exploded_search_universes_views_num_universe int(10) unsigned NOT NULL DEFAULT 0,
						exploded_search_universes_views_num_view int(10) unsigned NOT NULL DEFAULT 0,
						PRIMARY KEY (exploded_search_universes_views_num_universe, exploded_search_universes_views_num_view),
						INDEX i_esuv_num_view (exploded_search_universes_views_num_view)
					)";
			echo traite_rqt($rqt,"create table exploded_search_universes_views");
			
			// TS & VT - Table associant un univers à des segments
			// exploded_search_universes_segments_num_universe : Identifiant de l'univers
			// exploded_search_universes_segments_num_segment : Identifiant du segment
			$rqt = "CREATE TABLE IF NOT EXISTS exploded_search_universes_segments (
						exploded_search_universes_segments_num_universe int(10) unsigned NOT NULL DEFAULT 0,
						exploded_search_universes_segments_num_segment int(10) unsigned NOT NULL DEFAULT 0,
						PRIMARY KEY (exploded_search_universes_segments_num_universe, exploded_search_universes_segments_num_segment),
						INDEX i_esus_num_segment (exploded_search_universes_segments_num_segment)
					)";
			echo traite_rqt($rqt,"create table exploded_search_universes_segments");
			
			// TS & VT - Segments de recherche
			$rqt = "CREATE TABLE IF NOT EXISTS exploded_search_segments (
						exploded_search_segments_id int(10) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
						exploded_search_segments_label varchar(255) DEFAULT '',
						exploded_search_segments_icon varchar(255) default '',
						exploded_search_segments_type varchar(255) not null default '',
						exploded_search_segments_description TEXT NOT null,
						exploded_search_segments_parameters TEXT NOT NULL
					)";
			echo traite_rqt($rqt,"create table exploded_search_segments");
			
			// TS & VT - Table associant un segment à des recherches prédéfinies
			// exploded_search_segments_views_num_universe : Identifiant de l'univers
			// exploded_search_universes_views_num_view : Identifiant de la view
			$rqt = "CREATE TABLE IF NOT EXISTS exploded_search_segments_predefined (
						exploded_search_segments_predefined_num_segment int(10) unsigned NOT NULL DEFAULT 0,
						exploded_search_segments_predefined_num_predefined int(10) unsigned NOT NULL DEFAULT 0,
						PRIMARY KEY (exploded_search_segments_predefined_num_segment, exploded_search_segments_predefined_num_predefined),
						INDEX i_essp_num_predefined (exploded_search_segments_predefined_num_predefined)
					)";
			echo traite_rqt($rqt,"create table exploded_search_segments_predefined");
			
			
			// TS & VT - Ajout d'une colonne dans la table search_persopac permettant de typer la recherche prédéfinie
			if (!pmb_mysql_num_rows(pmb_mysql_query("SHOW COLUMNS FROM search_persopac LIKE 'search_type'"))){
			    $rqt = "ALTER TABLE search_persopac add column search_type varchar(255) not null default 'record'";
			    echo traite_rqt($rqt,"ALTER TABLE search_persopac add column search_type varchar(255) not null default 'record'");
			}

			// NG - Si concept actif, attribution des droits de modification des concepts CONCEPTS_AUTH,
			// à tous les utilisateurs ayant acces à THESAURUS_AUTH,
			// seulement si aucun utilisateur n'a ce droit sur les concepts
			if($thesaurus_concepts_active) {
				if (!pmb_mysql_num_rows(pmb_mysql_query("select 1 from users where rights>=4194304"))) {
					$rqt = "update users set rights=rights+4194304 where rights<4194304 and rights&2048";
					echo traite_rqt($rqt, "update users add rights CONCEPTS_AUTH");
				}
			}
			
			// NG - Ajout template pour les impressions de panier en OPAC
			$rqt="CREATE TABLE IF NOT EXISTS print_cart_tpl (
	            id_print_cart_tpl int unsigned not null auto_increment primary key,
	            print_cart_tpl_name varchar(255) not null default '',
	            print_cart_tpl_header text not null,
	            print_cart_tpl_footer text not null
       	        )";
			echo traite_rqt($rqt,"create table print_cart_tpl");
			
			// Ajout du paramètre indiquant le template à utiliser pour les impressions de panier en OPAC
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='print_cart_header_footer' "))==0){
			    $rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'opac', 'print_cart_header_footer', '', 'Identifiant du template à utiliser pour insérer un en-tête et un pied de page en impression de panier. Les templates sont créés en Administration > Template de Mail > Template impression de panier.','h_cart', 0)";
			    echo traite_rqt($rqt,"insert opac_print_cart_header_footer into parametres");
			}

			// VT - Paramètre de définition du style dojo en gestion
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='dojo_gestion_style' "))==0){
			    $rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
			     VALUES ( 'pmb', 'dojo_gestion_style', 'claro', 'Styles disponibles: tundra, claro, flat, nihilo, soria','', 0)";
			    echo traite_rqt($rqt,"insert pmb_dojo_gestion_style into parametres");
			}
			
			// DG - Mode d'affichage par défaut de création d'entités dans les pop-up (sélecteurs)
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='popup_form_display_mode' "))==0){
			    $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES (0, 'pmb', 'popup_form_display_mode', '1', 'Mode d\'affichage par défaut du formulaire de création dans une popup de sélection.\n 1 : Simple \n 2 : Avancé','',0)";
			    echo traite_rqt($rqt,"insert pmb_popup_form_display_mode into parametres");
			}
			
			// TS - VT - Afficher template même sans donnée
			$rqt = "ALTER TABLE frbr_cadres ADD cadre_display_empty_template tinyint(1) UNSIGNED NOT NULL default 1" ;
			echo traite_rqt($rqt,"ALTER TABLE frbr_cadres ADD cadre_display_empty_template");
			
			// AP - VT Création d'une table pour la gestion d'une pile d'indexation
			// indexation_stack_entity_id : Identifiant de l'entité à indexer
			// indexation_stack_entity_type : Type de l'entité à indexer (cf init.inc.php)
			// indexation_stack_datatype : Datatype de l'indexation
			// indexation_stack_timestamp : Timestamp de la demande d'indexation
			$rqt = "CREATE TABLE IF NOT EXISTS indexation_stack (
        			indexation_stack_entity_id int(8) unsigned not null default 0,
        			indexation_stack_entity_type int(3) unsigned not null default 0,
        			indexation_stack_datatype varchar(255) not null default '',
        			indexation_stack_timestamp bigint not null default 0,
        			indexation_stack_parent_id int(8) unsigned not null default 0,
        			indexation_stack_parent_type int(3) unsigned not null default 0,
        			primary key (indexation_stack_entity_id, indexation_stack_entity_type, indexation_stack_datatype)
        		)";
			echo traite_rqt($rqt,"create table indexation_stack");
			
			// AP - VT - Ajout d'un paramètre caché permettant de définir si une indexation est en cours
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='indexation_in_progress' "))==0){
			    $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES (NULL, 'pmb', 'indexation_in_progress', '0', 'Paramètre caché permettant de définir si une indexation est en cours', '', '1')" ;
			    echo traite_rqt($rqt,"insert hidden pmb_indexation_in_progress=0 into parametres") ;
			}
			
			// AP - VT - Ajout d'un paramètre caché permettant de définir si une indexation est nécessaire
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='indexation_needed' "))==0){
			    $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES (NULL, 'pmb', 'indexation_needed', '0', 'Paramètre caché permettant de définir si une indexation est nécessaire', '', '1')" ;
			    echo traite_rqt($rqt,"insert hidden pmb_indexation_needed=0 into parametres") ;
			}			
			
			// JP - Index incorrect sur faq_questions_words_global_index
			$rqt ="alter table faq_questions_words_global_index drop primary key";
			echo traite_rqt($rqt,"alter table faq_questions_words_global_index drop primary key");
			$rqt ="alter table faq_questions_words_global_index add primary key (id_faq_question,code_champ,code_ss_champ,num_word,position,field_position)";
			echo traite_rqt($rqt,"alter table faq_questions_words_global_index add primary key");
			if ($faq_active) {
			    // Info de réindexation
			    $rqt = " select 1 " ;
			    echo traite_rqt($rqt,"<b><a href='".$base_path."/admin.php?categ=netbase' target=_blank>VOUS DEVEZ REINDEXER LA FAQ / YOU MUST REINDEX THE FAQ : Admin > Outils > Nettoyage de base > Réindexer la faq</a></b> ") ;
			}
			
			// JP - choix des liens à conserver en remplacement de notice et en import
			$rqt = "ALTER TABLE users ADD deflt_notice_replace_links int(1) UNSIGNED DEFAULT 0";
			echo traite_rqt($rqt,"ALTER TABLE users ADD deflt_notice_replace_links");
			
			// TS - Paramètre pour l'activation de l'autopostage dans les concepts
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'thesaurus' and sstype_param='concepts_autopostage' "))==0) {
			    $rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param,comment_param, section_param, gestion)
				VALUES ( 'thesaurus','concepts_autopostage', '0', 'Activer l\'autopostage dans les concepts. \n 0 : Non, \n 1 : Oui', 'concepts', 0)" ;
			    echo traite_rqt($rqt,"insert into parameters thesaurus_concepts_autopostage=0") ;
			}
			
			// TS - Paramètre pour le nombre de niveaux de recherche de l'autopostage dans les concepts génériques
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'thesaurus' and sstype_param='concepts_autopostage_generic_levels_nb' "))==0) {
			    $rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES ( 'thesaurus', 'concepts_autopostage_generic_levels_nb', '3', 'Nombre de niveaux de recherche dans les concepts génériques. \n * : Tous, \n n : nombre de niveaux', 'concepts', 0)" ;
			    echo traite_rqt($rqt,"insert into parameters thesaurus_concepts_autopostage_generic_levels_nb=3") ;
			}
			
			// TS - Paramètre pour le nombre de niveaux de recherche de l'autopostage dans les concepts spécifiques
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'thesaurus' and sstype_param='concepts_autopostage_specific_levels_nb' "))==0) {
			    $rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES ( 'thesaurus', 'concepts_autopostage_specific_levels_nb', '3', 'Nombre de niveaux de recherche dans les concepts spécifiques. \n * : Tous, \n n : nombre de niveaux', 'concepts', 0)" ;
			    echo traite_rqt($rqt,"insert into parameters thesaurus_concepts_autopostage_specific_levels_nb=3") ;
			}
			
			// TS - Paramètre pour l'activation de l'autopostage dans les concepts à l'OPAC
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='concepts_autopostage' "))==0) {
			    $rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param,comment_param, section_param, gestion)
				VALUES ( 'opac','concepts_autopostage', '0', 'Activer l\'autopostage dans les concepts. Le nombre de niveaux vers génériques et/ou spécifiques est défini par les paramètres de gestion . \n 0 : Non, \n 1 : Oui', 'c_recherche', 0)" ;
			    echo traite_rqt($rqt,"insert into parameters opac_concepts_autopostage") ;
			}
			
			// JP & DG - Nettoyage des autorités
			$rqt = "DELETE FROM authorities WHERE num_object = 0";
			echo traite_rqt($rqt,"DELETE FROM authorities WITH num_object = 0");
			
			// JP & DG - Nettoyage des doublons autorités
			require_once($class_path."/indexation_authority.class.php");
			require_once($class_path."/authority.class.php");
			$rqt = "SELECT COUNT(*) AS nbr_doublon, num_object, type_object FROM authorities GROUP BY num_object, type_object HAVING COUNT(*) > 1";
			$result = pmb_mysql_query($rqt);
			if($result && pmb_mysql_num_rows($result)) {
			    while($row = pmb_mysql_fetch_object($result)) {
			        $query_authority = "select id_authority from authorities where num_object = ".$row->num_object." and type_object = ".$row->type_object." order by id_authority";
			        $result_authority = pmb_mysql_query($query_authority);
			        $id_authority = 0;
			        while($row_authority = pmb_mysql_fetch_object($result_authority)) {
			            if(!$id_authority) {
			                $id_authority = $row_authority->id_authority;
			            } else {
			                $query_caddie_content = "select caddie_id from authorities_caddie_content where object_id = ".$row_authority->id_authority;
			                $result_caddie_content = pmb_mysql_query($query_caddie_content);
			                while($row_caddie_content = pmb_mysql_fetch_object($result_caddie_content)) {
			                    if(pmb_mysql_result(pmb_mysql_query("select count(*) from authorities_caddie_content where object_id = ".$id_authority." and caddie_id = ".$row_caddie_content->caddie_id), 0, 0)) {
			                        $requete = "delete from authorities_caddie_content where object_id = ".$row_authority->id_authority." and caddie_id = ".$row_caddie_content->caddie_id;
			                        pmb_mysql_query($requete);
			                    } else {
			                        $requete = "update authorities_caddie_content set object_id = ".$id_authority." where object_id = ".$row_authority->id_authority." and caddie_id = ".$row_caddie_content->caddie_id;
			                        pmb_mysql_query($requete);
			                    }
			                }
			                
			                // nettoyage indexation
			                indexation_authority::delete_all_index($row_authority->id_authority, "authorities", "id_authority", $row->type_object);
			                
			                pmb_mysql_query("delete from authorities where id_authority=".$row_authority->id_authority);
			            }
			        }
			    }
			}
			
			// JP & DG - Passage de l'index en unique
			$rqt = "ALTER TABLE authorities DROP INDEX i_a_num_object_type_object,
			ADD UNIQUE KEY i_a_num_object_type_object(num_object,type_object)";
			echo traite_rqt($rqt,$rqt);
			
			// AP - Statistiques de fréquentation en mode horaire
			$errors = '';
			if (pmb_mysql_num_rows(pmb_mysql_query('SHOW COLUMNS FROM visits_statistics LIKE "visits_statistics_value"'))) {
			    $errors.= pmb_mysql_error();
			    // Renommage de la table existante pour réinjection des données
			    $rqt = 'RENAME TABLE visits_statistics TO visits_statistics_old';
			    echo traite_rqt($rqt, $rqt);
			    $errors.= pmb_mysql_error();
			    
			    // Création de la nouvelle table
			    // visits_statistics_id : ID
			    // visits_statistics_date : Date
			    // visits_statistics_location : Localisation
			    // visits_statistics_type : Type de service
			    $rqt = 'CREATE TABLE if not exists visits_statistics (
						visits_statistics_id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
						visits_statistics_date DATETIME NOT NULL DEFAULT "0000-00-00 00:00:00",
						visits_statistics_location SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0,
						visits_statistics_type VARCHAR(255) NOT NULL DEFAULT "",
						INDEX i_vs_visits_statistics_date (visits_statistics_date),
						INDEX i_vs_visits_statistics_location_visits_statistics_type (visits_statistics_location, visits_statistics_type)
				        )';
			    echo traite_rqt($rqt, 'CREATE TABLE visits_statistics');
			    $errors.= pmb_mysql_error();
			    
			    // On va chercher les anciennes données pour ensuite les insérer dans la nouvelle table
			    $rqt = 'SELECT visits_statistics_date, visits_statistics_location, visits_statistics_type, visits_statistics_value FROM visits_statistics_old ORDER BY visits_statistics_date';
			    $result = pmb_mysql_query($rqt);
			    $errors.= pmb_mysql_error();
			    if (pmb_mysql_num_rows($result)) {
			        $insert = array();
			        while ($row = pmb_mysql_fetch_assoc($result)) {
			            for ($i = 0; $i < $row['visits_statistics_value']; $i++) {
			                $insert[] = '(0, "'.$row['visits_statistics_date'].' 00:00:00", "'.$row['visits_statistics_location'].'", "'.$row['visits_statistics_type'].'")';
			            }
			        }
			        if (count($insert)) {
			            $rqt = 'INSERT INTO visits_statistics(visits_statistics_id, visits_statistics_date, visits_statistics_location, visits_statistics_type) VALUES '.implode(',', $insert);
			            echo traite_rqt($rqt, 'INSERT INTO visits_statistics') ;
			            $errors.= pmb_mysql_error();
			        }
			    }
			    
			    // Si tout va bien, on supprime l'ancienne table
			    if (!$errors) {
			        $rqt = 'DROP TABLE visits_statistics_old';
			        echo traite_rqt($rqt, $rqt.($empr_visits_statistics_active ? "<br/><b>Stat de fréquentation modifiées, vous devez mettre à jour vos états personnalisables.</b>": ""));
				} else if ($empr_visits_statistics_active) {
			        $rqt="select 1";
			        traite_rqt($rqt, "<b>Problème avec le traitement des modifications des tables de statistiques de fréquentation, la nouvelle table est créée mais les archives n'y ont pas été insérées, elles sont dans la table visits_statistics_old.</b>");
			    }
			}
			
			// NG - Ajout dans la table explnum: explnum_create_date, explnum_update_date, explnum_file_size
			$rqt = "ALTER TABLE explnum ADD explnum_create_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ";
			echo traite_rqt($rqt,"ALTER TABLE explnum ADD explnum_create_date");
			$rqt = "ALTER TABLE explnum ADD explnum_update_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00'";
			echo traite_rqt($rqt,"ALTER TABLE explnum ADD explnum_update_date");
			$rqt = "ALTER TABLE explnum ADD explnum_file_size int not null default 0";
			echo traite_rqt($rqt,"ALTER TABLE explnum ADD explnum_file_size");
				
			//DG / VT - Ajout du droit sur le module FRBR
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'frbr' and sstype_param='active' "))==0){
			    $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'frbr', 'active', '0', 'Module \'FRBR\' activé.\n 0 : Non.\n 1 : Oui.', '',0) ";
			    echo traite_rqt($rqt, "insert frbr_active=0 into parameters");
			}
			
			//DG / VT - Ajout du droit sur le module modélisation
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'modelling' and sstype_param='active' "))==0){
			    $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'modelling', 'active', '0', 'Module \'Modélisation\' activé.\n 0 : Non.\n 1 : Oui.', '',0) ";
			    echo traite_rqt($rqt, "insert modelling_active=0 into parameters");
			}
			
			//DG - Options pour le debogage - Afficher les erreurs PHP en gestion
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='display_errors' "))==0){
			    $rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (NULL, 'pmb', 'display_errors', '0', 'Afficher les erreurs PHP ? \n 0 : Non \n 1 : Oui' , 'debug', '0')";
			    echo traite_rqt($rqt,"insert pmb_display_errors='0' into parametres ");
			}
			
			//DG - Options pour le debogage - Afficher les erreurs PHP en OPAC
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='display_errors' "))==0){
			    $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'display_errors', '0', 'Afficher les erreurs PHP ? \n 0 : Non \n 1 : Oui', 'debug', 0)" ;
			    echo traite_rqt($rqt,"insert opac_display_errors=0 into parametres");
			}
			
			// DG / VT - Ajout du droit sur le module FRBR
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'frbr' and sstype_param='active' "))==0){
			    $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'frbr', 'active', '0', 'Module \'FRBR\' activé.\n 0 : Non.\n 1 : Oui.', '',0) ";
			    echo traite_rqt($rqt, "insert frbr_active=0 into parameters");
			}
			
			// DG / VT - Ajout du droit sur le module modélisation
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'modelling' and sstype_param='active' "))==0){
			    $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'modelling', 'active', '0', 'Module \'Modélisation\' activé.\n 0 : Non.\n 1 : Oui.', '',0) ";
			    echo traite_rqt($rqt, "insert modelling_active=0 into parameters");
			}
			
			// DG - Options pour le debogage - Afficher les erreurs PHP en gestion
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='display_errors' "))==0){
			    $rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (NULL, 'pmb', 'display_errors', '0', 'Afficher les erreurs PHP ? \n 0 : Non \n 1 : Oui' , 'debug', '0')";
			    echo traite_rqt($rqt,"insert pmb_display_errors='0' into parametres ");
			}
			
			// DG - Options pour le debogage - Afficher les erreurs PHP en OPAC
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='display_errors' "))==0){
			    $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'display_errors', '0', 'Afficher les erreurs PHP ? \n 0 : Non \n 1 : Oui', 'debug', 0)" ;
			    echo traite_rqt($rqt,"insert opac_display_errors=0 into parametres");
			}
			
			// PLM - Description pour recherche dans les concepts à l'OPAC
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='modules_search_concept' "))){
			    $rqt = "UPDATE parametres
					SET comment_param='Recherche dans les concepts : \n 0 : interdite, \n 1 : autorisée, \n 2 : autorisée et validée par défaut, \n -1 : également interdite en recherche multi-critères'
					WHERE type_param='opac' AND sstype_param='modules_search_concept'";
			    echo traite_rqt($rqt, "update parametres opac_modules_search_concept set comment_param = ... ");
			}
			
			// NG - Création de la table des status des abonnements de périodiques
			//   abts_status_id : identifiant du statut
			//   abts_status_gestion_libelle : libellé du statut en gestion
			//   abts_status_opac_libelle : libellé du statut en OPAC
			//   abts_status_class_html : classe HTML du statut
			//   abts_status_bulletinage_active : abonnement actif ou non dans le bulletinage
			$rqt="create table if not exists abts_status(
				abts_status_id int unsigned not null auto_increment primary key,
				abts_status_gestion_libelle varchar(255) not null default '',
				abts_status_opac_libelle varchar(255) not null default '',
				abts_status_class_html varchar(255) not null default '',
				abts_status_bulletinage_active TINYINT(1) UNSIGNED NOT NULL DEFAULT 1
			     )";
			echo traite_rqt($rqt, "create table abts_status");
			
			// NG - Statut par défaut
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from abts_status where abts_status_id ='1' "))==0) {
			    $rqt = 'INSERT INTO abts_status (abts_status_id, abts_status_gestion_libelle, abts_status_opac_libelle, abts_status_class_html)
						VALUES (1, "Statut par défaut", "Statut par défaut", "statutnot1")';
			    echo traite_rqt($rqt,"insert default abts_status");
			}
			
			// NG - Ajout du statut dans les abonnements
			$rqt = "ALTER TABLE abts_abts ADD abt_status int(1) UNSIGNED NOT NULL DEFAULT 1 ";
			echo traite_rqt($rqt,"ALTER TABLE abts_abts ADD abt_status ");
			
			//AP & DG - Mise à jour des liens perdus entre les notices de bulletin et les périodiques
			require_once($class_path."/notice_relations.class.php");
			$query = "SELECT bulletins.num_notice, bulletins.bulletin_notice from bulletins left join notices_relations ON notices_relations.num_notice = bulletins.num_notice AND notices_relations.linked_notice = bulletins.bulletin_notice where bulletins.num_notice<>0 AND id_notices_relations IS NULL";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_fetch_object($result)) {
				while($row = pmb_mysql_fetch_object($result)) {
					notice_relations::insert($row->num_notice, $row->bulletin_notice, 'b', 1, 'up', false);
				}
				echo traite_rqt("SELECT 1","ALTER TABLE notices_relations UPDATE relations ");
			}

			// JP - Paramètre pour définir l'état par défaut de la case à cocher "Abonnement actif" dans le navigateur de périodiques
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='perio_a2z_default_active_subscription_filter' "))==0) {
			    $rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES ( 'opac', 'perio_a2z_default_active_subscription_filter', '0', 'Filtre sur les abonnements actifs coché par défaut dans le navigateur de périodiques ?\n 0 : Non\n 1 : Oui', 'c_recherche', 0)" ;
			    echo traite_rqt($rqt,"insert into parameters opac_perio_a2z_default_active_subscription_filter") ;
			}
			
			//JP - Liste des imprimantes ticket de prêt
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='printer_list' "))==0){
			    $rqt = "INSERT INTO parametres (type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			VALUES ('pmb', 'printer_list', '', 'Liste des imprimantes de ticket de prêt gérées par raspberry, séparées par un point-virgule. Indiquer un identifiant, un libellé et une IP de raspberry (facultative) alternative à celle du paramètre général printer_name.\nExemple : 1_Imprimante prêt;2_Autre imprimante(192.168.0.83:3000).','', 0)";
			    echo traite_rqt($rqt,"insert pmb_printer_list into parametres");
			}
			$rqt = "ALTER TABLE users ADD deflt_printer int(3) UNSIGNED DEFAULT 0";
			echo traite_rqt($rqt,"ALTER TABLE users ADD deflt_printer");
			
			// DG - Gestion des classements pour le catalogage FRBR
			$rqt="create table if not exists frbr_cataloging_categories(
				id_cataloging_category int unsigned not null auto_increment primary key,
				cataloging_category_title varchar(255) not null default '',
				cataloging_category_num_parent int unsigned not null default 0
				)";
			echo traite_rqt($rqt, "create table frbr_cataloging_categories");
				
			// DG - Gestion des jeux de données
			$rqt="create table if not exists frbr_cataloging_datanodes(
				id_cataloging_datanode int unsigned not null auto_increment primary key,
				cataloging_datanode_title varchar(255) not null default '',
				cataloging_datanode_comment TEXT not null,
				cataloging_datanode_owner int unsigned not null default 0,
				cataloging_datanode_allowed_users varchar(255) not null default '',
				cataloging_datanode_num_category int unsigned not null default 0,
				index i_cataloging_datanode_title(cataloging_datanode_title)
				)";
			echo traite_rqt($rqt, "create table frbr_cataloging_datanodes");
				
			// DG - Gestion de la liste des éléments sélectionnées pour le catalogage FRBR
			$rqt="create table if not exists frbr_cataloging_items (
    			num_cataloging_item int UNSIGNED NOT NULL,
    			type_cataloging_item varchar(255) not null default '',
    			cataloging_item_num_user int UNSIGNED NOT NULL default 0,
    			cataloging_item_added_date datetime,
    			cataloging_item_num_datanode int unsigned not null default 0,
    			index i_cataloging_item_num_datanode(cataloging_item_num_datanode),
    			primary key (num_cataloging_item, type_cataloging_item, cataloging_item_num_datanode))";
			echo traite_rqt($rqt, "create table if not exists frbr_cataloging_items");
				
			// DG - Gestion des campagnes de mails
			$rqt="create table if not exists campaigns (
    			id_campaign int UNSIGNED NOT NULL auto_increment primary key,
    			campaign_type varchar(255) not null default '',
    			campaign_label varchar(255) not null default '',
    			campaign_date datetime,
    			campaign_num_user int UNSIGNED NOT NULL default 0)
    			";
			echo traite_rqt($rqt, "create table if not exists campaigns");
				
			// DG - Gestion des descripteurs de campagnes de mails
			$rqt="create table if not exists campaigns_descriptors (
				num_campaign int unsigned not null default 0,
				num_noeud int unsigned not null default 0,
				campaign_descriptor_order int not null default 0,
				primary key (num_campaign, num_noeud)
				)";
			echo traite_rqt($rqt, "create table campaign_descriptors");
				
			// DG - Gestion des tags de campagnes de mails
			$rqt="create table if not exists campaigns_tags (
				num_campaign int unsigned not null default 0,
				num_tag int unsigned not null default 0,
				campaign_tag_order int not null default 0,
				primary key (num_campaign, num_tag)
				)";
			echo traite_rqt($rqt, "create table campaigns_tags");
				
			// DG - Gestion des destinataires liés aux campagnes de mails
			$rqt = "create table if not exists campaigns_recipients(
				id_campaign_recipient int unsigned not null auto_increment primary key,
				campaign_recipient_hash varchar(255) not null default '',
				campaign_recipient_num_campaign int not null default 0,
				campaign_recipient_num_empr int not null default 0,
				campaign_recipient_empr_cp varchar(5) not null default '',
				campaign_recipient_empr_ville varchar(255) not null default '',
				campaign_recipient_empr_prof varchar(255) not null default '',
				campaign_recipient_empr_year int not null default 0,
				campaign_recipient_empr_categ smallint(5) unsigned default 0,
				campaign_recipient_empr_codestat smallint(5) unsigned default 0,
				campaign_recipient_empr_sexe tinyint(3) unsigned default 0,
				campaign_recipient_empr_statut bigint(20) unsigned default 0,
				campaign_recipient_empr_location int(6) unsigned default 0,
				index i_campaign_recipient_num_campaign(campaign_recipient_num_campaign)
			)";
			echo traite_rqt($rqt,"create table campaigns_recipients");
				
			// AP & DG - Gestion des logs liés aux campagnes de mails
			$rqt = "create table if not exists campaigns_logs(
				campaign_log_num_campaign int not null default 0,
				campaign_log_num_recipient int not null default 0,
				campaign_log_hash varchar(255) not null default '',
				campaign_log_url varchar(255) not null default '',
				campaign_log_date datetime not null default '0000-00-00 00:00:00',
				index i_campaign_log_num_campaign(campaign_log_num_campaign),
				index i_campaign_log_num_recipient(campaign_log_num_recipient)
			)";
			echo traite_rqt($rqt,"create table campaigns_logs");
				
			// AP & DG - Consolidation des logs liés aux campagnes de mails
			$rqt = "create table if not exists campaigns_stats(
				campaign_stat_num_campaign int not null default 0 primary key,
				campaign_stat_data text not null,
				campaign_stat_date datetime not null default '0000-00-00 00:00:00'
			)";
			echo traite_rqt($rqt,"create table campaigns_stats");
			
			// DG - Ajout dans les bannettes la possibilité d'établir un suivi
			$rqt = "ALTER TABLE bannettes ADD associated_campaign INT( 1 ) UNSIGNED NOT NULL default 0 ";
			echo traite_rqt($rqt,"alter table bannettes add associated_campaign");
			
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
			
			// DG - Table de définition/personnalisation des listes par utilisateur
			$rqt = "CREATE TABLE IF NOT EXISTS lists (
					id_list int unsigned not null auto_increment primary key,
        			list_num_user int(8) unsigned not null default 0,
					list_objects_type varchar(255) not null default '',
					list_label varchar(255) not null default '',
        			list_selected_columns text,
					list_filters text,
					list_applied_group text,
					list_applied_sort text,
					list_pager text,
					list_autorisations mediumtext,
					list_default_selected int(1) unsigned not null default 0,
					list_order int not null default 0
        		)";
			echo traite_rqt($rqt,"create table lists");
			
			// DG - File d'attente de mails
			$rqt = "CREATE TABLE IF NOT EXISTS mails_waiting (
					id_mail int unsigned not null auto_increment primary key,
					mail_waiting_to_name varchar(255) not null default '',
					mail_waiting_to_mail varchar(255) not null default '',
					mail_waiting_object varchar(255) not null default '',
					mail_waiting_content mediumtext not null,
					mail_waiting_from_name varchar(255) not null default '',
					mail_waiting_from_mail varchar(255) not null default '',
					mail_waiting_headers text not null,
					mail_waiting_copy_cc varchar(255) not null default '',
        			mail_waiting_copy_bcc varchar(255) not null default '',
					mail_waiting_do_nl2br int(1) unsigned not null default 0,
					mail_waiting_attachments text not null,
					mail_waiting_reply_name varchar(255) not null default '',
					mail_waiting_reply_mail varchar(255) not null default '',
					mail_waiting_date datetime not null default '0000-00-00 00:00:00'
        		)";
			echo traite_rqt($rqt,"create table mails_waiting");
			
			// AP - Ajout d'un index sur la signature des documents numériques
			$rqt = "alter table explnum add index i_e_explnum_signature(explnum_signature)";
			echo traite_rqt($rqt,"alter table explnum add index i_e_explnum_signature");
			
			//DG - Ajout du classement associé aux champs personalisés
			$rqt = "ALTER TABLE notices_custom ADD custom_classement varchar(255) not null default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE notices_custom ADD custom_classement ");
			
			$rqt = "ALTER TABLE author_custom ADD custom_classement varchar(255) not null default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE author_custom ADD custom_classement ");
			
			$rqt = "ALTER TABLE authperso_custom ADD custom_classement varchar(255) not null default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE authperso_custom ADD custom_classement ");
			
			$rqt = "ALTER TABLE categ_custom ADD custom_classement varchar(255) not null default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE categ_custom ADD custom_classement ");
			
			$rqt = "ALTER TABLE cms_editorial_custom ADD custom_classement varchar(255) not null default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE cms_editorial_custom ADD custom_classement ");
			
			$rqt = "ALTER TABLE collection_custom ADD custom_classement varchar(255) not null default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE collection_custom ADD custom_classement ");
			
			$rqt = "ALTER TABLE collstate_custom ADD custom_classement varchar(255) not null default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE collstate_custom ADD custom_classement ");
			
			$rqt = "ALTER TABLE demandes_custom ADD custom_classement varchar(255) not null default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE demandes_custom ADD custom_classement ");
			
			$rqt = "ALTER TABLE empr_custom ADD custom_classement varchar(255) not null default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE empr_custom ADD custom_classement ");
			
			$rqt = "ALTER TABLE expl_custom ADD custom_classement varchar(255) not null default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE expl_custom ADD custom_classement ");
			
			$rqt = "ALTER TABLE explnum_custom ADD custom_classement varchar(255) not null default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE explnum_custom ADD custom_classement ");
			
			$rqt = "ALTER TABLE gestfic0_custom ADD custom_classement varchar(255) not null default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE gestfic0_custom ADD custom_classement ");
			
			$rqt = "ALTER TABLE indexint_custom ADD custom_classement varchar(255) not null default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE indexint_custom ADD custom_classement ");
			
			$rqt = "ALTER TABLE pret_custom ADD custom_classement varchar(255) not null default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE pret_custom ADD custom_classement ");
			
			$rqt = "ALTER TABLE publisher_custom ADD custom_classement varchar(255) not null default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE publisher_custom ADD custom_classement ");
			
			$rqt = "ALTER TABLE serie_custom ADD custom_classement varchar(255) not null default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE serie_custom ADD custom_classement ");
			
			$rqt = "ALTER TABLE skos_custom ADD custom_classement varchar(255) not null default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE skos_custom ADD custom_classement ");
			
			$rqt = "ALTER TABLE subcollection_custom ADD custom_classement varchar(255) not null default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE subcollection_custom ADD custom_classement ");
			
			$rqt = "ALTER TABLE tu_custom ADD custom_classement varchar(255) not null default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE tu_custom ADD custom_classement ");
			
			// DG - Ajout index sur resp_groupe de la table groupe
			$req="SHOW INDEX FROM groupe WHERE key_name LIKE 'i_resp_groupe'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table groupe add index i_resp_groupe (resp_groupe)";
				echo traite_rqt($rqt,"alter table groupe add index i_resp_groupe");
			}
			
			// DG - Ajout index sur num_empr de la table opac_liste_lecture
			$req="SHOW INDEX FROM opac_liste_lecture WHERE key_name LIKE 'i_num_empr'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table opac_liste_lecture add index i_num_empr (num_empr)";
				echo traite_rqt($rqt,"alter table opac_liste_lecture add index i_num_empr");
			}
			
			// +------------------------------------------
			echo "</table>";
			$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
			$res = pmb_mysql_query($rqt, $dbh) ;
			echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
			$action=$action+$increment;
			//echo form_relance ("v5.30");

		//case "v5.30":
		    echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
		    // +-------------------------------------------------+
		    
		    // TS & VT & NG - Suppression des anciennes tables des recherches ségmentées
		    $query = "drop table if exists exploded_search_universes";
		    echo traite_rqt($query, "drop table if exists exploded_search_universes");
		    $query = "drop table if exists exploded_search_universes_views";
		    echo traite_rqt($query, "drop table if exists exploded_search_universes_views");
		    $query = "drop table if exists exploded_search_universes_segments";
		    echo traite_rqt($query, "drop table if exists exploded_search_universes_segments");
		    $query = "drop table if exists exploded_search_segments";
		    echo traite_rqt($query, "drop table if exists exploded_search_segments");
		    $query = "drop table if exists exploded_search_segments_predefined";
		    echo traite_rqt($query, "drop table if exists exploded_search_segments_predefined");
		    
		    // TS & VT & NG - Modification du paramètre OPAC des recherches ségmentées
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='exploded_search_activate' "))==1){
		        $query = "update parametres set sstype_param = 'search_universes_activate' where sstype_param='exploded_search_activate'";
		        echo traite_rqt($query, "update parametres set sstype_param = 'search_universes_activate' where sstype_param='exploded_search_activate'");
		    } elseif (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='search_universes_activate' ")) == 0) {
		        $rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
            	VALUES (NULL, 'opac', 'search_universes_activate', '0', 'Univers de recherche activés : \r\n0: Non\r\n1: Oui', 'c_recherche', '0')";
		        echo traite_rqt($rqt,"insert opac_search_universes_activate=0 into parametres ");
		    }
		    
		    
		    // TS & VT & NG - table des univers de recherche
		    $rqt = "CREATE TABLE IF NOT EXISTS search_universes (
        			id_search_universe int unsigned not null auto_increment primary key,
        			search_universe_label varchar(255) not null default '',
			        search_universe_description varchar(255) not null default '',
			        search_universe_template_directory varchar(255) not null default '',
			        search_universe_opac_views varchar(255) not null default ''
        		)";
		    echo traite_rqt($rqt,"create table search_universes");
		    
		    // TS & VT & NG - table des segments de recherche
		    $rqt = "CREATE TABLE IF NOT EXISTS search_segments (
        			id_search_segment int unsigned not null auto_increment primary key,
        			search_segment_label varchar(255) not null default '',
    			    search_segment_description varchar(255) not null default '',
    			    search_segment_template_directory varchar(255) not null default '',
    			    search_segment_num_universe int not null default 0,
    			    search_segment_type int not null default 0,
    			    search_segment_order int not null default 0,
    			    search_segment_set text,
    			    search_segment_logo varchar(255) not null default ''
        		)";
		    echo traite_rqt($rqt,"create table search_segments");
		    
		    // TS & VT & NG - table de liaison entre les segments et les prédéfinies
		    $rqt = "CREATE TABLE IF NOT EXISTS search_segments_search_perso (
        			num_search_segment int unsigned not null default 0,
			        num_search_perso int unsigned not null default 0,
			        search_segment_search_perso_opac int unsigned not null default 0,
			        search_segment_search_perso_order int not null default 0,
			        primary key (num_search_segment, num_search_perso)
        		)";
		    echo traite_rqt($rqt,"create table search_segments_search_perso");
		    
		    // TS & VT & NG - table de liaison entre les segments et les facettes
		    $rqt = "CREATE TABLE IF NOT EXISTS search_segments_facets (
        			num_search_segment int not null default 0,
			        num_facet int not null default 0 ,
			        search_segment_facet_order int not null default 0,
        			primary key (num_search_segment, num_facet)
        		)";
		    echo traite_rqt($rqt,"create table search_segments_facets");
		    
		    // DG - Type sur les facettes
		    $rqt = "alter table facettes add facette_type varchar(255) not null default 'notices' after id_facette ";
		    echo traite_rqt($rqt,"alter table facettes add facette_type");
		    
		    // NG - Changement des champs date en INT pour la gestion des dates avant JC
		    $rqt = " ALTER TABLE author_custom_dates CHANGE author_custom_date_start author_custom_date_start INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table author_custom_dates change author_custom_date_start");
		    $rqt = " ALTER TABLE author_custom_dates CHANGE author_custom_date_end author_custom_date_end INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table author_custom_dates change author_custom_date_end");
		    
		    $rqt = " ALTER TABLE authperso_custom_dates CHANGE authperso_custom_date_start authperso_custom_date_start INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table authperso_custom_dates change authperso_custom_date_start");
		    $rqt = " ALTER TABLE authperso_custom_dates CHANGE authperso_custom_date_end authperso_custom_date_end INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table authperso_custom_dates change authperso_custom_date_end");
		    
		    $rqt = " ALTER TABLE categ_custom_dates CHANGE categ_custom_date_start categ_custom_date_start INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table categ_custom_dates change categ_custom_date_start");
		    $rqt = " ALTER TABLE categ_custom_dates CHANGE categ_custom_date_end categ_custom_date_end INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table categ_custom_dates change categ_custom_date_end");
		    
		    $rqt = " ALTER TABLE cms_editorial_custom_dates CHANGE cms_editorial_custom_date_start cms_editorial_custom_date_start INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table cms_editorial_custom_dates change cms_editorial_custom_date_start");
		    $rqt = " ALTER TABLE cms_editorial_custom_dates CHANGE cms_editorial_custom_date_end cms_editorial_custom_date_end INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table cms_editorial_custom_dates change cms_editorial_custom_date_end");
		    
		    $rqt = " ALTER TABLE collection_custom_dates CHANGE collection_custom_date_start collection_custom_date_start INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table collection_custom_dates change collection_custom_date_start");
		    $rqt = " ALTER TABLE collection_custom_dates CHANGE collection_custom_date_end collection_custom_date_end INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table collection_custom_dates change collection_custom_date_end");
		    
		    $rqt = " ALTER TABLE collstate_custom_dates CHANGE collstate_custom_date_start collstate_custom_date_start INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table collstate_custom_dates change collstate_custom_date_start");
		    $rqt = " ALTER TABLE collstate_custom_dates CHANGE collstate_custom_date_end collstate_custom_date_end INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table collstate_custom_dates change collstate_custom_date_end");
		    
		    $rqt = " ALTER TABLE demandes_custom_dates CHANGE demandes_custom_date_start demandes_custom_date_start INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table demandes_custom_dates change demandes_custom_date_start");
		    $rqt = " ALTER TABLE demandes_custom_dates CHANGE demandes_custom_date_end demandes_custom_date_end INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table demandes_custom_dates change demandes_custom_date_end");
		    
		    $rqt = " ALTER TABLE empr_custom_dates CHANGE empr_custom_date_start empr_custom_date_start INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table empr_custom_dates change empr_custom_date_start");
		    $rqt = " ALTER TABLE empr_custom_dates CHANGE empr_custom_date_end empr_custom_date_end INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table empr_custom_dates change empr_custom_date_end");
		    
		    $rqt = " ALTER TABLE explnum_custom_dates CHANGE explnum_custom_date_start explnum_custom_date_start INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table explnum_custom_dates change explnum_custom_date_start");
		    $rqt = " ALTER TABLE explnum_custom_dates CHANGE explnum_custom_date_end explnum_custom_date_end INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table explnum_custom_dates change explnum_custom_date_end");
		    
		    $rqt = " ALTER TABLE expl_custom_dates CHANGE expl_custom_date_start expl_custom_date_start INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table expl_custom_dates change expl_custom_date_start");
		    $rqt = " ALTER TABLE expl_custom_dates CHANGE expl_custom_date_end expl_custom_date_end INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table expl_custom_dates change expl_custom_date_end");
		    
		    $rqt = " ALTER TABLE indexint_custom_dates CHANGE indexint_custom_date_start indexint_custom_date_start INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table indexint_custom_dates change indexint_custom_date_start");
		    $rqt = " ALTER TABLE indexint_custom_dates CHANGE indexint_custom_date_end indexint_custom_date_end INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table indexint_custom_dates change indexint_custom_date_end");
		    
		    $rqt = " ALTER TABLE notices_custom_dates CHANGE notices_custom_date_start notices_custom_date_start INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table notices_custom_dates change notices_custom_date_start");
		    $rqt = " ALTER TABLE notices_custom_dates CHANGE notices_custom_date_end notices_custom_date_end INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table notices_custom_dates change notices_custom_date_end");
		    
		    $rqt = " ALTER TABLE pret_custom_dates CHANGE pret_custom_date_start pret_custom_date_start INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table pret_custom_dates change pret_custom_date_start");
		    $rqt = " ALTER TABLE pret_custom_dates CHANGE pret_custom_date_end pret_custom_date_end INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table pret_custom_dates change pret_custom_date_end");
		    
		    $rqt = " ALTER TABLE publisher_custom_dates CHANGE publisher_custom_date_start publisher_custom_date_start INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table publisher_custom_dates change publisher_custom_date_start");
		    $rqt = " ALTER TABLE publisher_custom_dates CHANGE publisher_custom_date_end publisher_custom_date_end INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table publisher_custom_dates change publisher_custom_date_end");
		    
		    $rqt = " ALTER TABLE serie_custom_dates CHANGE serie_custom_date_start serie_custom_date_start INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table serie_custom_dates change serie_custom_date_start");
		    $rqt = " ALTER TABLE serie_custom_dates CHANGE serie_custom_date_end serie_custom_date_end INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table serie_custom_dates change serie_custom_date_end");
		    
		    $rqt = " ALTER TABLE skos_custom_dates CHANGE skos_custom_date_start skos_custom_date_start INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table skos_custom_dates change skos_custom_date_start");
		    $rqt = " ALTER TABLE skos_custom_dates CHANGE skos_custom_date_end skos_custom_date_end INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table skos_custom_dates change skos_custom_date_end");
		    
		    $rqt = " ALTER TABLE subcollection_custom_dates CHANGE subcollection_custom_date_start subcollection_custom_date_start INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table subcollection_custom_dates change subcollection_custom_date_start");
		    $rqt = " ALTER TABLE subcollection_custom_dates CHANGE subcollection_custom_date_end subcollection_custom_date_end INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table subcollection_custom_dates change subcollection_custom_date_end");
		    
		    $rqt = " ALTER TABLE tu_custom_dates CHANGE tu_custom_date_start tu_custom_date_start INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table tu_custom_dates change tu_custom_date_start");
		    $rqt = " ALTER TABLE tu_custom_dates CHANGE tu_custom_date_end tu_custom_date_end INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table tu_custom_dates change tu_custom_date_end");
		    
		    $rqt = " ALTER TABLE authperso_custom_dates CHANGE authperso_custom_date_start authperso_custom_date_start INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table authperso_custom_dates change authperso_custom_date_start");
		    $rqt = " ALTER TABLE authperso_custom_dates CHANGE authperso_custom_date_end authperso_custom_date_end INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"alter table authperso_custom_dates change authperso_custom_date_end");
		    
		    // DG - Type sur les facettes externes
		    $rqt = "alter table facettes_external add facette_type varchar(255) not null default 'notices' after id_facette ";
		    echo traite_rqt($rqt,"alter table facettes_external add facette_type");
		    
		    // DG - Ajout d'index sur les tables de circulation de périodiques
		    $indexes = array(
		        'serialcirc' => array('num_serialcirc_abt'),
		        'serialcirc_ask' => array('num_serialcirc_ask_perio', 'num_serialcirc_ask_serialcirc', 'num_serialcirc_ask_empr', 'serialcirc_ask_type', 'serialcirc_ask_statut'),
		        'serialcirc_circ' => array('num_serialcirc_circ_diff', 'num_serialcirc_circ_expl', 'num_serialcirc_circ_empr', 'num_serialcirc_circ_serialcirc'),
		        'serialcirc_copy' => array('num_serialcirc_copy_empr', 'num_serialcirc_copy_bulletin'),
		        'serialcirc_diff' => array('num_serialcirc_diff_serialcirc', 'serialcirc_diff_empr_type', 'serialcirc_diff_type_diff', 'num_serialcirc_diff_empr'),
		        'serialcirc_expl' => array('num_serialcirc_expl_id', 'num_serialcirc_expl_serialcirc', 'num_serialcirc_expl_serialcirc_diff', 'num_serialcirc_expl_current_empr'),
		        'serialcirc_group' => array('num_serialcirc_group_diff', 'num_serialcirc_group_empr')
		    );
		    foreach ($indexes as $table_name => $fields) {
		        foreach($fields as $field_name) {
		            $req="SHOW INDEX FROM ".$table_name." WHERE key_name LIKE 'i_".$field_name."'";
		            $res=pmb_mysql_query($req);
		            if($res && (pmb_mysql_num_rows($res) == 0)){
		                $rqt = "ALTER TABLE ".$table_name." ADD INDEX i_".$field_name."(".$field_name.")";
		                echo traite_rqt($rqt,"ALTER TABLE ".$table_name." ADD INDEX i_".$field_name);
		            }
		        }
		    }
		    
		    //DG - MAJ du template de bannettes par défaut (identifiant 1) - N'allons pas altérer ceux déjà personnalisés
		    $rqt = "UPDATE bannette_tpl SET bannettetpl_tpl='{{info.header}}\r\n<br /><br />\r\n<div class=\"summary\">\r\n    <ul>\r\n        {% for sommaire in sommaires %}\r\n            {% if sommaire.level==1 %}\r\n                <li>\r\n                    <a href=\"#[{{loop.counter}}]\">{{sommaire.title}}</a>\r\n                </li>\r\n            {% endif %}\r\n        {% endfor %}
			    		\r\n    </ul>\r\n</div>\r\n{% for sommaire in sommaires %}\r\n    {% if sommaire.level==1 %}\r\n        <h2 class=\"dsi_rang_1\"><a name=\"[{{loop.counter}}]\"></a>{{sommaire.title}}</h2>\r\n    {% endif %}\r\n    {% if sommaire.level==2 %}\r\n        <h3 class=\"dsi_rang_2\">{{sommaire.title}}</h3>\r\n    {% endif %}\r\n    {% if sommaire.level==3 %}\r\n        <h4 class=\"dsi_rang_3\">{{sommaire.title}}</h4>
			    		\r\n    {% endif %}\r\n    {% for record in sommaire.records %}\r\n        {{record.render}}\r\n    {% endfor %}
			    		\r\n    <br />\r\n{% endfor %}\r\n{{info.footer}}'
					WHERE bannettetpl_id=1 AND bannettetpl_tpl='{{info.header}}\r\n<br /><br />\r\n<div class=\"summary\">\r\n{% for sommaire in sommaires %}\r\n<a href=\"#[{{sommaire.level}}]\">\r\n{{sommaire.level}} - {{sommaire.title}}\r\n</a>\r\n<br />\r\n{% endfor %}\r\n</div>\r\n<hr>\r\n{% for sommaire in sommaires %}\r\n<a name=\"[{{sommaire.level}}]\" />\r\n<h1>{{sommaire.level}} - {{sommaire.title}}</h1>\r\n{% for record in sommaire.records %}\r\n{{record.render}}\r\n<hr>\r\n{% endfor %}\r\n<br />\r\n{% endfor %}\r\n{{info.footer}}'";
		    echo traite_rqt($rqt,"ALTER minimum into bannette_tpl");
		  
		    // VT, TS, NG - Lignes de commande PNB
		    $rqt = "CREATE TABLE IF NOT EXISTS pnb_orders (
		    		id_pnb_order int unsigned not null auto_increment primary key,
        			pnb_order_id_order varchar(255) not null default '',
        			pnb_order_line_id varchar(255) not null default '',
			        pnb_order_num_notice int unsigned not null default 0,
			        pnb_order_loan_max_duration int unsigned not null default 0,
			        pnb_order_nb_loans int unsigned not null default 0,
			        pnb_order_nb_simultaneous_loans int unsigned not null default 0,
			        pnb_order_nb_consult_in_situ int unsigned not null default 0,
			        pnb_order_nb_consult_ex_situ int unsigned not null default 0,
			        pnb_order_offer_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			        pnb_order_offer_date_end datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			        pnb_order_offer_duration int unsigned not null default 0
        		)";
		    echo traite_rqt($rqt,"create table pnb_orders");
		    
		    // VT, TS, NG - Liaisons entre les lignes de commande PNB et les exemplaires
		    $rqt = "CREATE TABLE IF NOT EXISTS pnb_orders_expl (
        			pnb_order_num int unsigned not null default 0,
			        pnb_order_expl_num int unsigned not null default 0,
        			primary key (pnb_order_num, pnb_order_expl_num)
        		)";
		    echo traite_rqt($rqt,"create table pnb_orders_expl");
		    
		    // NG - Paramètres de connection au PNB
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='pnb_param_login' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'pmb', 'pnb_param_login', '', 'Paramétrage du Login de PNB.', '', 1)" ;
		        echo traite_rqt($rqt,"insert pmb_pnb_param_login into parametres");
		    }
		    
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='pnb_param_password' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'pmb', 'pnb_param_password', '', 'Paramétrage du mot de passe de PNB.', '', 1)" ;
		        echo traite_rqt($rqt,"insert pmb_pnb_param_password into parametres");
		    }
		    
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='pnb_param_ftp_login' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'pmb', 'pnb_param_ftp_login', '', 'Paramétrage du login du FTP de PNB.', '', 1)" ;
		        echo traite_rqt($rqt,"insert pmb_pnb_param_ftp_login into parametres");
		    }
		    
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='pnb_param_ftp_password' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'pmb', 'pnb_param_ftp_password', '', 'Paramétrage du mot de passe du FTP de PNB.', '', 1)" ;
		        echo traite_rqt($rqt,"insert pmb_pnb_param_ftp_password into parametres");
		    }
		    
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='pnb_param_ftp_server' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'pmb', 'pnb_param_ftp_server', '', 'Paramétrage de l\'url du FTP de PNB.', '', 1)" ;
		        echo traite_rqt($rqt,"insert pmb_pnb_param_ftp_server into parametres");
		    }
		    
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='pnb_param_ws_user_name' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'pmb', 'pnb_param_ws_user_name', '', 'Paramétrage du nom de l\'utilisateur externe.', '', 1)" ;
		        echo traite_rqt($rqt,"insert pmb_pnb_param_ws_user_name into parametres");
		    }
		    
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='pnb_param_ws_user_password' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'pmb', 'pnb_param_ws_user_password', '', 'Paramétrage du mot de passe de l\'utilisateur externe.', '', 1)" ;
		        echo traite_rqt($rqt,"insert pmb_pnb_param_ws_user_password into parametres");
		    }
		    
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='pnb_param_dilicom_url' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'pmb', 'pnb_param_dilicom_url', '', 'Paramétrage de l\'url du webservice Dilicom.', '', 1)" ;
		        echo traite_rqt($rqt,"insert pmb_pnb_param_dilicom_url into parametres");
		    }
		    
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='pnb_param_webservice_url' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'pnb_param_webservice_url', '', 'Paramétrage de l\'url du webservice de gestion pour les prêts numériques.', '', 1)" ;
		        echo traite_rqt($rqt,"insert opac_pnb_param_webservice_url into parametres");
		    }
		    
		    // AP & VT - Table de liaison des emprunteurs à un ou plusieurs périphérique(s) de lecture
		    $rqt = "CREATE TABLE IF NOT EXISTS empr_devices (
        			empr_num int unsigned not null default 0,
			        device_id int unsigned not null default 0,
        			primary key (empr_num, device_id)
        		)";
		    echo traite_rqt($rqt,"create table empr_devices");				
			
// +------------------------------------------
			echo "</table>";
			$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
			$res = pmb_mysql_query($rqt, $dbh) ;
			echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
			$action=$action+$increment;
			//echo form_relance ("v5.30");

		//case "v5.31":
		    echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
		    // +-------------------------------------------------+
		    
		    //Attention opération susceptible d'être longue, peut être prévoir une sous version de l'alter v5
		    // AP & VT - Ajout d'un flag dans la table notice permettant de dire si la notice est numérique
		    $rqt = "alter table notices add is_numeric TINYINT(1) not null default 0 ";
		    echo traite_rqt($rqt,"alter table notices add is_numeric");
		    
		    //Attention opération susceptible d'être longue, peut être prévoir une sous version de l'alter v5
		    // CC & VT - Ajout d'une colonne contenant le mot de passe du PNB dans la table emprunteur
		    $rqt = "alter table empr add empr_pnb_password varchar(255) not null default '' ";
		    echo traite_rqt($rqt,"alter table empr add pnb_password");
		    
		    //Attention opération susceptible d'être longue, peut être prévoir une sous version de l'alter v5
		    // CC & VT - Ajout d'une colonne contenant l'indice du mot de passe du PNB dans la table emprunteur
		    $rqt = "alter table empr add empr_pnb_password_hint varchar(100) not null default '' ";
		    echo traite_rqt($rqt,"alter table empr add pnb_password_hint");
		    
		    // NG - Quotas du PNB
		    $rqt = "create table if not exists quotas_pnb (
				quota_type int(10) unsigned not null default 0,
				constraint_type varchar(255) not null default '',
				elements int(10) unsigned not null default 0,
				value text not null,
				primary key(quota_type,constraint_type,elements)
			)";
		    echo traite_rqt($rqt,"create table quotas_pnb");
		    
		    
		    // CC - VT Ajout d'un parametre caché contenant le compteur de prêt numérique
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='pnb_loan_counter' "))==0){
		        $rqt = "INSERT INTO parametres (type_param,sstype_param,valeur_param,comment_param,section_param,gestion)
					VALUES ('pmb','pnb_loan_counter','0','Paramètre caché contenant le compteur de prêt numérique','',1)";
		        echo traite_rqt($rqt,"insert pmb_pnb_loan_counter='0' into parametres");
		    }
		    
		    // CC - VT Table contenant les prêts numeriques
		    $rqt = "CREATE TABLE IF NOT EXISTS pnb_loans (
		    		id_pnb_loan int unsigned not null auto_increment primary key,
        			pnb_loan_order_line_id varchar(255) not null default '',
			        pnb_loan_link varchar(255) not null default '',
			        pnb_loan_request_id varchar(255) not null default '',
			        pnb_loan_num_expl int unsigned not null default 0,
		    		pnb_loan_num_loaner int unsigned not null default 0
        		)";
		    echo traite_rqt($rqt,"create table pnb_loans");
		    
		    // CC - TS - Parametre pour filtrer les bannettes privees avec une equation de recherche
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'dsi' and sstype_param='private_bannette_search_equation' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
                VALUES (0, 'dsi', 'private_bannette_search_equation', '0', 'Id de l\'équation de recherche utilisée par défaut en complément de l\'équation privée en diffusion de bannettes privées.', '', 0)";
		        echo traite_rqt($rqt, "insert private_bannette_search_equation into parameters");
		    }
		    
		    // AP | VT - Ajout d'un paramètre pour l'activation de l'édition des documents numériques en popup
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='enable_explnum_edition_popup' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
                VALUES (0, 'pmb', 'enable_explnum_edition_popup', '0', 'Activation de l\'édition des documents numériques en popup ?\n0 : Non\n1 : Oui', '', 0)";
		        echo traite_rqt($rqt, "insert enable_explnum_edition_popup into parameters");
		    }
		    
		    // NG - Ajout d'un paramètre caché pour mémoriser les informations des DRM du pnb
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='pnb_drm_parameters' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
                VALUES (0, 'pmb', 'pnb_drm_parameters', '0', 'Mémorise les informations des DRM du PNB', '', 1)";
		        echo traite_rqt($rqt, "insert pmb_pnb_drm_parameters into parameters");
		    }
		    
		    // VT - Ajout de l'information du DRM utilisé dans la table des prêts numériques
		    $rqt = "alter table pnb_loans add pnb_loan_drm varchar(100) not null default '' ";
		    echo traite_rqt($rqt,"alter table pnb_loans add pnb_loan_drm");
		    
		    // NG - Ajout d'un paramètre caché pour déclencher l'alerte des commandes arrivant à expiration
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='pnb_alert_end_offers' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
                VALUES (0, 'pmb', 'pnb_alert_end_offers', '0', 'Nombre de jours entre le déclanchement de l\'alerte et l\'expiration des commandes', '', 1)";
		        echo traite_rqt($rqt, "insert pmb_pnb_alert_end_offers into parameters");
		    }
		    
		    // NG - Ajout d'un paramètre caché pour déclencher l'alerte des commandes arrivant à saturation de prêts
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='pnb_alert_staturation_offers' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
                VALUES (0, 'pmb', 'pnb_alert_staturation_offers', '0', 'Nombre d\'exemplaires restants avant le déclanchement de l\'alerte pour les commandes arrivant à saturation de prêt', '', 1)";
		        echo traite_rqt($rqt, "insert pmb_pnb_alert_staturation_offers into parameters");
		    }
		    
		    // NG - Ajout flag de pret pnb dans la table pret_archive
		    $rqt = "ALTER TABLE pret_archive ADD arc_pnb_flag INT(1) NOT NULL DEFAULT 0 ";
		    echo traite_rqt($rqt,"alter table pret_archive add arc_pnb_flag");
		    
		    // NG - Ajout d'un paramètre caché pour suprimer les prêts pnb arrivés à expiration, une seule fois par jour
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='pnb_clean_loans_date' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
                VALUES (0, 'pmb', 'pnb_clean_loans_date', date(now()), 'Date du nettoyage des prêts PNB expirés', '', 1)";
		        echo traite_rqt($rqt, "insert pmb_pnb_clean_loans_date into parameters");
		    }
		    
		    // DG - Durée maximale de la session sans rafraîchissement (en secondes).
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='session_reactivate' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'pmb', 'session_reactivate', '', 'Durée maximale de la session sans rafraîchissement (en secondes). Si vide ou 0, valeur fixée à 120 minutes', '', 0)" ;
		        echo traite_rqt($rqt,"insert pmb_session_reactivate into parametres");
		    }
		    
		    // DG - Durée maximale de la session (en secondes).
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='session_maxtime' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'pmb', 'session_maxtime', '', 'Durée maximale de la session (en secondes). Si vide ou 0, valeur fixée à 24 heures', '', 0)" ;
		        echo traite_rqt($rqt,"insert pmb_session_maxtime into parametres");
		    }
		    
		    // DG - Tri sur les documents numériques en Gestion
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='explnum_order' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param)
				VALUES (0, 'pmb', 'explnum_order', 'explnum_mimetype, explnum_nom, explnum_id','Ordre d\'affichage des documents numériques, dans l\'ordre donné, séparé par des virgules : explnum_mimetype, explnum_nom, explnum_id','')";
		        echo traite_rqt($rqt,"insert pmb_explnum_order=explnum_mimetype, explnum_nom, explnum_id into parametres");
		    }
		    
		    // DG - Création automatique d'une réservation lors de la réception d'une ligne de commande liée à une suggestion d'emprunteur
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='sugg_to_cde_resa_auto' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'acquisition','sugg_to_cde_resa_auto','1','Création automatique d\'une réservation lors de la réception d\'une ligne de commande liée à une suggestion d\'emprunteur.\nLa réservation sur les notices sans exemplaires (Paramètres généraux > resa_records_no_expl) doit être activée pour cela.\n Non : 0 \n Oui : 1','',0)" ;
		        echo traite_rqt($rqt,"insert acquisition_sugg_to_cde_resa_auto into parametres") ;
		    }
		    
		    // DG - Ajout d'un index sur l'identifiant de template d'une bannette
		    $rqt = "alter table bannettes add index i_bannette_tpl_num(bannette_tpl_num)";
		    echo traite_rqt($rqt,"alter table bannettes add index i_bannette_tpl_num");
		    
		    // DG - Statut de notice par défaut en création d'article
		    $rqt = "ALTER TABLE users ADD deflt_notice_statut_analysis INT(6) UNSIGNED DEFAULT 0 AFTER deflt_notice_statut";
		    echo traite_rqt($rqt,"ALTER TABLE users ADD deflt_notice_statut_analysis");
		    
		    // DG - Modification de la taille du champ name de la table etagere
		    $rqt = "ALTER TABLE etagere MODIFY name varchar(255) NOT NULL default ''" ;
		    echo traite_rqt($rqt,"ALTER TABLE etagere MODIFY name to varchar(255)");
		    
		    // DG - Modification du champ section_publication_state de la table cms_sections
		    $rqt = "ALTER TABLE cms_sections MODIFY section_publication_state INT UNSIGNED NOT NULL default 0" ;
		    echo traite_rqt($rqt,"ALTER TABLE cms_sections MODIFY section_publication_state to INTEGER");
		    
		    // DG - Modification du champ article_publication_state de la table cms_articles
		    $rqt = "ALTER TABLE cms_articles MODIFY article_publication_state INT UNSIGNED NOT NULL default 0" ;
		    echo traite_rqt($rqt,"ALTER TABLE cms_articles MODIFY article_publication_state to INTEGER");
		    
		    // DG - Utilisation d'un type de produit pour les commandes
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='type_produit' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'acquisition','type_produit','0','Utilisation d\'un type de produit pour les commandes.\n 0:optionnel\n 1:obligatoire','',0)" ;
		        echo traite_rqt($rqt,"insert acquisition_type_produit into parametres") ;
		    }
		    
		    // DG - Préférence utilisateur : Type de produit par défaut
		    $rqt = "ALTER TABLE users ADD deflt3type_produit INT(8) UNSIGNED DEFAULT 0 AFTER deflt3rubrique";
		    echo traite_rqt($rqt,"ALTER TABLE users ADD deflt3type_produit");
		    
		    // TS - Parametre pour la date des notices à utiliser pour calculer les nouveautés des bannettes
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='private_bannette_date_used_to_calc' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
                VALUES (0, 'opac', 'private_bannette_date_used_to_calc', '0', 'Date des notices à utiliser en diffusion de bannettes privées ? 0 : date de création, 1 : date de modification, 2 : Sélectionnable par l\'usager en OPAC', 'l_dsi', 0)";
		        echo traite_rqt($rqt, "insert opac_private_bannette_date_used_to_calc into parameters");
		    }
		    
		    // TS - Modification du nom du parametre dsi_private_bannette_date_used_to_calc en opac_private_bannette_date_used_to_calc
		    if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param = 'dsi' AND sstype_param = 'private_bannette_date_used_to_calc'"))){
		        $rqt = "UPDATE parametres SET type_param = 'opac', section_param= 'l_dsi', comment_param='Date des notices à utiliser en diffusion de bannettes privées ? 0 : date de création, 1 : date de modification, 2 : Sélectionnable par l\'usager en OPAC' WHERE type_param = 'dsi' AND sstype_param = 'private_bannette_date_used_to_calc'" ;
		        echo traite_rqt($rqt,"UPDATE parametres SET type_param = 'opac' WHERE type_param = 'dsi' AND sstype_param = 'private_bannette_date_used_to_calc'");
		    }
		    
		    // AR - Création d'un paramètre pour la méthode de calcul de la pertinence avec Sphinx
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'sphinx' and sstype_param='pert_calc_method' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'sphinx','pert_calc_method','(sum(lcs*user_weight)+top(exact_order*user_weight/min_hit_pos)+top(3*exact_hit*user_weight))*1000+bm25','Méthode de calcul de la pertinence pour Sphinx en mode expression (par défaut dans PMB). \n La liste des facteurs disponibles : http://sphinxsearch.com/docs/current.html#expression-ranker \n Exemples des autre modes : \n SPH_RANK_PROXIMITY_BM25 = sum(lcs*user_weight)*1000+bm25 \n SPH_RANK_BM25 = bm25 \n SPH_RANK_WORDCOUNT = sum(hit_count*user_weight) \n SPH_RANK_PROXIMITY = sum(lcs*user_weight) \n SPH_RANK_MATCHANY = sum((word_count+(lcs-1)*max_lcs)*user_weight) \n SPH_RANK_FIELDMASK = field_mask \n SPH_RANK_SPH04 = sum((4*lcs+2*(min_hit_pos==1)+exact_hit)*user_weight)*1000+bm25','',0)" ;
		        echo traite_rqt($rqt,"insert sphinx_pert_calc_method into parametres") ;
		    }
		    
		    // TS - Ajout de l'identifiant du segment à afficher par défaut
		    $rqt = "ALTER TABLE search_universes ADD search_universe_default_segment INT NOT NULL DEFAULT 0";
		    echo traite_rqt($rqt,"ALTER TABLE search_universes ADD search_universe_default_segment");
		    
		    // PLM - Modification description paramêtre avis_show_writer
		    $rqt = "update parametres set comment_param = 'Afficher le rédacteur de l\'avis \r\n 0 : non \r\n 1 : Prénom NOM \r\n 2 : login OPAC uniquement\r\n 3 : Prénom uniquement'
		      where type_param = 'opac' and sstype_param = 'avis_show_writer'";
		    echo traite_rqt($rqt, "update parametres set comment_param where type_param = 'opac' and sstype_param = 'avis_show_writer'");	   
		     
		    // DG - Modification du paramètre opac_websubscribe_num_carte_auto
		    $rqt = "update parametres set valeur_param = '1' where valeur_param = '' and type_param='opac' and sstype_param = 'websubscribe_num_carte_auto'";
		    echo traite_rqt($rqt,"update parametres opac_websubscribe_num_carte_auto set value");
		    $rqt = "update parametres set comment_param = 'Numéro de carte de lecteur automatique ?\n 1: www + Identifiant du lecteur \n 2,a,b,c: a=longueur du préfixe, b=nombre de chiffres de la partie numérique, c=préfixe fixé (facultatif)\n 3,fonction: fonction de génération spécifique dans fichier nommé de la même façon, à placer dans pmb/opac_css/circ/empr' where type_param='opac' and sstype_param = 'websubscribe_num_carte_auto'";
		    echo traite_rqt($rqt,"update parametres opac_websubscribe_num_carte_auto set comment");
		    
		    // AP - Support par défaut en création d'exemplaire de périodique
		    $rqt = "ALTER TABLE users ADD deflt_serials_docs_type INT( 6 ) UNSIGNED DEFAULT 1 NOT NULL AFTER deflt_docs_type" ;
		    echo traite_rqt($rqt,"ALTER TABLE users ADD deflt_serials_docs_type");
		    
		    // VT - Table des entités verrouillées
		    $rqt = "CREATE TABLE IF NOT EXISTS locked_entities (
		    		id_entity int unsigned not null,
        			type int unsigned not null default 0,
			        date datetime not null default '0000-00-00 00:00:00',
			        parent_id int unsigned not null default 0,
			        parent_type int unsigned not null default 0,
		    		user_id int unsigned not null default 0,
                    empr_id int unsigned not null default 0,
                    primary key(id_entity, type)
        		)";
		    echo traite_rqt($rqt,"create table locked_entities");
		    
		    // CC / VT - Définition du temps de verrouillage d'une entité
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='entity_locked_time' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'pmb', 'entity_locked_time', '0', 'Temps de verrouillage des entités en minutes. \n 0: aucun verrouillage, \n ## : durée de blocage de l\'entité après enregistrement ou abandon de la modification. Conseillé 5. Permet d\'éviter les entités restées verrouillées.', '', 0)" ;
		        echo traite_rqt($rqt,"insert pmb_entity_locked_time into parametres");
		    }
		    
		    // VT - Ajout d'un parametre système contenant le temps de rafraichissement de la date de dernier accès à une entité (en minute)
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='entity_locked_refresh_time' "))==0){
		        $rqt = "INSERT INTO parametres (type_param,sstype_param,valeur_param,comment_param,section_param,gestion)
					VALUES ('pmb','entity_locked_refresh_time','5','Paramètre système contenant le temps de rafraichissement de la date de dernier accès à une entité (en minute)', '', 0)";
		        echo traite_rqt($rqt,"insert pmb_entity_locked_refresh_time='1' into parametres");
		    }
		    
		    // DG - Afficher les exemplaires du bulletin sous l'article
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='show_exemplaires_analysis' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param) VALUES (0, 'pmb', 'show_exemplaires_analysis', '0', 'Afficher les exemplaires du bulletin sous l\'article affiché ? \n 0: Non \n 1: Oui','')";
		        echo traite_rqt($rqt,"insert pmb_show_exemplaires_analysis=0 into parametres");
		    }
		    
		    // DG - Ajout d'un champ  pour les statuts de documents numériques pour outrepasser les droits à l'affichage de la vignette, si coché la vignette reste accessible même si les droits du doc la verrouille.
		    $rqt = "ALTER TABLE explnum_statut ADD explnum_thumbnail_visible_opac_override tinyint(1) UNSIGNED NOT NULL DEFAULT 0 ";
		    echo traite_rqt($rqt,"alter table explnum add explnum_thumbnail_visible_opac_override ");
		    
		    // DG - Paramètre d'activation/désactivation de connexion auto en DSI
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'dsi' and sstype_param='connexion_auto' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param) VALUES (0, 'dsi', 'connexion_auto', '1', 'Connexion automatique de l\'usager à l\'OPAC à partir d\'un mail de la DSI activée ? \n 0: Non \n 1: Oui','')";
		        echo traite_rqt($rqt,"insert dsi_connexion_auto=1 into parametres");
		    }

		    // NG - chat, préférence utilisateur
		    $rqt = "ALTER TABLE users ADD param_chat_activate INT(1) NOT NULL default 0 AFTER param_rfid_activate" ;
		    echo traite_rqt($rqt,"ALTER TABLE users ADD param_chat_activate");
		    
		    // NG - chat, mémorisation des discussions
		    $rqt = "create table if not exists chat_messages (
	    		id_chat_message int unsigned not null auto_increment primary key,
				chat_message_from_user_type int unsigned not null default 0,
				chat_message_from_user_num int unsigned not null default 0,
				chat_message_to_user_type int unsigned not null default 0,
				chat_message_to_user_num int unsigned not null default 0,
				chat_message_text text not null,
				chat_message_file blob,
				chat_message_read int unsigned not null default 0,
				chat_message_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
				INDEX i_from_user_num (chat_message_from_user_num, chat_message_from_user_type)
			)";
		    echo traite_rqt($rqt,"create table chat_messages");
		    
		    // NG - chat, mémorisation des groupes de discussion
		    $rqt = "create table if not exists chat_groups (
	    		id_chat_group int unsigned not null auto_increment primary key,
				chat_group_name varchar(255) not null default '',
				chat_group_author_user_type int unsigned not null default 0,
				chat_group_author_user_num int unsigned not null default 0
			)";
		    echo traite_rqt($rqt,"create table chat_groups");
		    
		    // NG - chat, mémorisation des inscrits aux groupes
		    $rqt = "create table if not exists chat_users_groups (
	    		chat_user_group_num int unsigned not null default 0,
				chat_user_group_user_type int unsigned not null default 0,
				chat_user_group_user_num int unsigned not null default 0,
                chat_user_group_unread_messages_number int unsigned not null default 0,
			    primary key (chat_user_group_num, chat_user_group_user_type, chat_user_group_user_num)
			)";
		    echo traite_rqt($rqt,"create table chat_users_groups");

			echo "</table>";
			$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
			$res = pmb_mysql_query($rqt, $dbh) ;
			echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
			$action=$action+$increment;
			//echo form_relance ("v5.32");
		//case "v5.32":
		    echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
		    // +-------------------------------------------------+
		    
		    // PLM & DG - DSI > Veilles : Langue d'indexation par défaut en création de notice
		    $rqt = "ALTER TABLE docwatch_watches ADD watch_record_default_index_lang varchar(20) not null default '' after watch_record_default_status";
		    echo traite_rqt($rqt,"ALTER TABLE docwatch_watches ADD watch_record_default_index_lang");
		    
		    // PLM & DG - DSI > Veilles : Langue par défaut en création de notice
		    $rqt = "ALTER TABLE docwatch_watches ADD watch_record_default_lang varchar(20) not null default '' after watch_record_default_index_lang";
		    echo traite_rqt($rqt,"ALTER TABLE docwatch_watches ADD watch_record_default_lang");
		    
		    // PLM & DG - DSI > Veilles : Nouveauté par défaut en création de notice
		    $rqt = "ALTER TABLE docwatch_watches ADD watch_record_default_is_new tinyint(1) unsigned not null default 0 after watch_record_default_lang";
		    echo traite_rqt($rqt,"ALTER TABLE docwatch_watches ADD watch_record_default_is_new");
		    
		    // AP / CC - Table des champs calculés de scénarios
		    // id_computed_fields : Identifiant du champ calculé
		    // computed_fields_area_num : Identifiant de l'espace de contribution
		    // computed_fields_field_num : Identifiant unique dans l'arbre DOJO du champ calculé
		    // computed_fields_template : Template à utiliser pour renseigner le champ
		    $rqt = "CREATE TABLE IF NOT EXISTS contribution_area_computed_fields (
		    		id_computed_fields int unsigned not null auto_increment primary key,
					computed_fields_area_num int unsigned not null default 0,
					computed_fields_field_num varchar(255) not null default '',
					computed_fields_template text
        		)";
		    echo traite_rqt($rqt,"create table contribution_area_computed_fields");
		    
		    // AP / CC - Table des champs utilisés dans les champs calculés de scénarios
		    // id_computed_fields_used : Identifiant du champ à utiliser pour renseigner un champ calculé
		    // computed_fields_used_origine_field_num: Clé étrangère, identifiant du champ calculé
		    // computed_fields_used_label: Libellé du champ à utiliser pour renseigner un champ calculé
		    // computed_fields_used_num: Identifiant unique dans l'arbre DOJO du champ à utiliser pour renseigner un champ calculé
		    // computed_fields_used_alias: Alias du champ à utiliser pour renseigner un champ calculé
		    $rqt = "CREATE TABLE IF NOT EXISTS contribution_area_computed_fields_used (
		    		id_computed_fields_used int unsigned not null auto_increment primary key,
					computed_fields_used_origine_field_num int unsigned not null default 0,
					computed_fields_used_label text,
					computed_fields_used_num varchar(255) not null default '',
					computed_fields_used_alias varchar(255) not null default ''
        		)";
		    echo traite_rqt($rqt,"create table contribution_area_computed_fields_used");
		    
		    // NG - Ajout de l'identifiant ISNI des auteurs
		    $rqt = "alter table authors add author_isni varchar(255) not null default '' ";
		    echo traite_rqt($rqt,"alter table empr add author_isni");
		    
		    // VT - Ajout d'un paramètre permettant de renseigner une URL interne (valeur par défaut mise à pmb_url_base)
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='url_internal' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param)
		          VALUES (0, 'pmb', 'url_internal', '".$pmb_url_base."', 'URL interne utilisée quand le serveur doit s\'appeler lui même. Ne pas oublier le / final','')";
		        echo traite_rqt($rqt,"insert pmb_url_internal=pmb_url_base into parametres");
		    }
		    
		    // DG - Durée de validité (en heures) du lien de connexion automatique
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='connexion_auto_duration' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'opac','connexion_auto_duration','0','Durée de validité (en heures) du lien de connexion automatique','l_dsi',0)" ;
		        echo traite_rqt($rqt,"insert opac_connexion_auto_duration into parametres") ;
		    }
		    
		    // DG - Ajout du champ permettant la pré-selection du connecteur en gestion
		    $rqt = "ALTER TABLE connectors_sources ADD gestion_selected int(1) unsigned not null default 0 after opac_selected";
		    echo traite_rqt($rqt,"ALTER TABLE connectors_sources ADD gestion_selected");
		    
		    // AP - Changement des constantes utilisées dans les vedettes composées
		    
		    // AP - Création du paramètre qui va permettre de faire le traitement qu'une fois
		    if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param='pmb' AND sstype_param='vedette_objects_id_updated' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param, gestion)
		    	 VALUES (0, 'pmb', 'vedette_objects_id_updated', '0', 'La mise à jour des constantes de vedette a-t-elle été faite ?\n 0: Non\n 1: En cours\n 2: Terminée','','1')";
		        echo traite_rqt($rqt,"INSERT pmb_vedette_objects_id_updated=0 INTO parametres");
		    }
		    
		    if (pmb_mysql_result(pmb_mysql_query("SELECT valeur_param FROM parametres WHERE type_param='pmb' AND sstype_param='vedette_objects_id_updated'"), 0, 0) == 0) {
		        // On passe le paramètre à 1
		        $rqt = "UPDATE parametres SET valeur_param = '1' WHERE type_param= 'pmb' AND sstype_param='vedette_objects_id_updated'";
		        echo traite_rqt($rqt,"UPDATE parametres SET valeur_param = '1' WHERE type_param='pmb' AND sstype_param='vedette_objects_id_updated'");
		        
		        // Copie de la table vedette_object
		        $rqt = "CREATE TABLE vedette_object_copy AS SELECT * FROM vedette_object";
		        echo traite_rqt($rqt,"COPY TABLE vedette_object TO vedette_object_copy");
		        
		        // Concepts 9 => 17
		        $rqt = "UPDATE vedette_object SET object_type = '17' WHERE object_type = '9'";
		        echo traite_rqt($rqt,"(Concepts) UPDATE vedette_object SET object_type = '17' WHERE object_type = '9'");
		        
		        // Index. décimales 8 => 9
		        $rqt = "UPDATE vedette_object SET object_type = '9' WHERE object_type = '8'";
		        echo traite_rqt($rqt,"(Index. décimales) UPDATE vedette_object SET object_type = '9' WHERE object_type = '8'");
		        
		        // Titres uniformes 7 => 8
		        $rqt = "UPDATE vedette_object SET object_type = '8' WHERE object_type = '7'";
		        echo traite_rqt($rqt,"(Titres uniformes) UPDATE vedette_object SET object_type = '8' WHERE object_type = '7'");
		        
		        // Séries 6 => 7
		        $rqt = "UPDATE vedette_object SET object_type = '7' WHERE object_type = '6'";
		        echo traite_rqt($rqt,"(Séries) UPDATE vedette_object SET object_type = '7' WHERE object_type = '6'");
		        
		        // Sous-collections 5 => 6
		        $rqt = "UPDATE vedette_object SET object_type = '6' WHERE object_type = '5'";
		        echo traite_rqt($rqt,"(Sous-collections) UPDATE vedette_object SET object_type = '6' WHERE object_type = '5'");
		        
		        // Collections 4 => 5
		        $rqt = "UPDATE vedette_object SET object_type = '5' WHERE object_type = '4'";
		        echo traite_rqt($rqt,"(Collections) UPDATE vedette_object SET object_type = '5' WHERE object_type = '4'");
		        
		        // Editeurs 3 => 4
		        $rqt = "UPDATE vedette_object SET object_type = '4' WHERE object_type = '3'";
		        echo traite_rqt($rqt,"(Editeurs) UPDATE vedette_object SET object_type = '4' WHERE object_type = '3'");
		        
		        // Catégories 2 => 3
		        $rqt = "UPDATE vedette_object SET object_type = '3' WHERE object_type = '2'";
		        echo traite_rqt($rqt,"(Catégories) UPDATE vedette_object SET object_type = '3' WHERE object_type = '2'");
		        
		        // Auteurs 1 => 2
		        $rqt = "UPDATE vedette_object SET object_type = '2' WHERE object_type = '1'";
		        echo traite_rqt($rqt,"(Auteurs) UPDATE vedette_object SET object_type = '2' WHERE object_type = '1'");
		        
		        // Notices 10 => 1
		        $rqt = "UPDATE vedette_object SET object_type = '1' WHERE object_type = '10'";
		        echo traite_rqt($rqt,"(Notices) UPDATE vedette_object SET object_type = '1' WHERE object_type = '10'");
		        
		        // On passe le paramètre à 2
		        $rqt = "UPDATE parametres SET valeur_param = '2' WHERE type_param= 'pmb' AND sstype_param='vedette_objects_id_updated'";
		        echo traite_rqt($rqt,"UPDATE parametres SET valeur_param = '2' WHERE type_param='pmb' AND sstype_param='vedette_objects_id_updated'");
		    }
		    
		    // AP - Suppression de la table copiée
		    if (pmb_mysql_result(pmb_mysql_query("SELECT valeur_param FROM parametres WHERE type_param='pmb' AND sstype_param='vedette_objects_id_updated'"), 0, 0) == 2) {
		        $rqt = "DROP TABLE IF EXISTS vedette_object_copy";
		        echo traite_rqt($rqt,"DROP TABLE vedette_object_copy");
		    }
		    
		    // DG - Type de données de tri des résultats de facettes
		    $rqt = "ALTER TABLE facettes ADD facette_datatype_sort varchar(255) NOT NULL DEFAULT 'alpha' after facette_order_sort";
		    echo traite_rqt($rqt,"alter table facettes add facette_datatype_sort ");
		    
		    // DG - Type de données de tri des résultats de facettes externes
		    $rqt = "ALTER TABLE facettes_external ADD facette_datatype_sort varchar(255) NOT NULL DEFAULT 'alpha' after facette_order_sort";
		    echo traite_rqt($rqt,"alter table facettes_external add facette_datatype_sort ");
		    
		    // NG - Prendre en compte les diacritiques dans le dédoublonnage des autorités auteurs et éditeurs
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='controle_doublons_diacrit' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'pmb', 'controle_doublons_diacrit', '0', 'Prendre en compte les diacritiques dans le dédoublonnage des autorités auteurs et éditeurs? \n 0 : Non \n 1 : Oui', '', 0)" ;
		        echo traite_rqt($rqt,"insert pmb_controle_doublons_diacrit=0 into parametres");
		    }
		    
		    // NG - Paramètre d'activation de la recherche avancée dans les autorités en OPAC
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='allow_extended_search_authorities' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
                    VALUES (0, 'opac', 'allow_extended_search_authorities', '0', 'Autorisation ou non de la recherche avancée dans les autorités\n 0 : Non \n 1 : Oui', 'c_recherche', '0')";
		        echo traite_rqt($rqt,"insert opac_allow_extended_search_authorities='0' into parametres");
		    }
		    
		    // DG - Grilles sur les articles et rubriques du contenu éditorial éditables
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'cms' and sstype_param='editorial_form_editables' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			     VALUES (0, 'cms', 'editorial_form_editables', '1', 'Grilles éditables sur les articles et rubriques du contenu éditorial ? \n 0 non \n 1 oui','',0)";
		        echo traite_rqt($rqt,"insert cms_editorial_form_editables into parametres");
		    }
		    
		    // TS - Ajout du commentaire et de la visibilité OPAC sur l'indexation des entités
		    $rqt = "ALTER TABLE index_concept ADD comment text NOT NULL" ;
		    echo traite_rqt($rqt,"ALTER TABLE index_concept ADD comment ");
		    $rqt = "ALTER TABLE index_concept ADD comment_visible_opac tinyint( 1 ) UNSIGNED NOT NULL default 0" ;
		    echo traite_rqt($rqt,"ALTER TABLE index_concept ADD comment_visible_opac ");
		    
		    // DG - Gestion des fichiers substituables
		    $rqt = "CREATE TABLE IF NOT EXISTS subst_files (
		    	id_subst_file int unsigned not null auto_increment primary key,
				subst_file_path varchar(255) not null default '',
				subst_file_filename varchar(255) not null default '',
				subst_file_data mediumtext not null
			)";
		    echo traite_rqt($rqt,"create table subst_files");
		    
		    // AP - Grammaires de vedette composée à utiliser par entité
		    $rqt = "CREATE TABLE IF NOT EXISTS vedette_grammars_by_entity (
		    	entity_type int UNSIGNED NOT NULL default 0,
		    	grammar varchar(255) NOT NULL default '',
		    	PRIMARY KEY(entity_type, grammar)
			)";
		    echo traite_rqt($rqt,"create table vedette_grammars_by_entity");
		    
		    // NG - chat, mémorisation des discussions, recréé car chat_message_date datetime DEFAULT CURRENT_TIMESTAMP ne passe pas avant MYSQL V5.6.5
		    $rqt = "create table if not exists chat_messages (
	    		id_chat_message int unsigned not null auto_increment primary key,
				chat_message_from_user_type int unsigned not null default 0,
				chat_message_from_user_num int unsigned not null default 0,
				chat_message_to_user_type int unsigned not null default 0,
				chat_message_to_user_num int unsigned not null default 0,
				chat_message_text text not null,
				chat_message_file blob,
				chat_message_read int unsigned not null default 0,
				chat_message_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
				INDEX i_from_user_num (chat_message_from_user_num, chat_message_from_user_type)
			)";
		    echo traite_rqt($rqt,"create table chat_messages");
		    
		    // AP - Ajout d'une colonne dans vedette_object pour stocker le numéro correspondant au champ disponible associé
		    if (pmb_mysql_num_rows(pmb_mysql_query("SHOW COLUMNS FROM vedette_object LIKE 'num_available_field'"))==0) {
		        $rqt = "ALTER TABLE vedette_object ADD num_available_field int(11) NOT NULL default 0";
		        echo traite_rqt($rqt, "ALTER TABLE vedette_object ADD num_available_field");
		        
		        // AP - On le préremplit avec la valeur d'object_type
		        $rqt = "UPDATE vedette_object SET num_available_field = object_type";
		        echo traite_rqt($rqt, "UPDATE vedette_object SET num_available_field = object_type");
		    }
		    
		    // DG - Paramètre d'activation de la localisation d'une demande de numérisation à l'OPAC
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='scan_request_location_activate' "))==0){
		        $rqt = "INSERT INTO parametres (type_param,sstype_param,valeur_param,comment_param,section_param,gestion)
		    	VALUES ('opac','scan_request_location_activate','0','Activer la localisation d\'une demande de numérisation','f_modules',0)";
		        echo traite_rqt($rqt,"insert opac_scan_request_location_activate into parametres");
		    }
		    
		    // NG - Mémorisation des modes de paiement
		    $rqt = "CREATE TABLE IF NOT EXISTS transaction_payment_methods (
    			transaction_payment_method_id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    			transaction_payment_method_name varchar(255) not null default ''
			)";
		    echo traite_rqt($rqt,"CREATE TABLE transaction_payment_methods");
		    
		    // NG - Mémorisation du mode de paiement d'une transaction
		    $rqt = "ALTER TABLE transactions ADD transaction_payment_method_num int NOT NULL default 0";
		    echo traite_rqt($rqt,"alter table transactions add transaction_payment_method_num ");
		    
		    // DG - Ajout d'un paramètre utilisateur permettant d'activer par défaut le bulletinage en OPAC en création de périodique
		    $rqt = "alter table users add deflt_opac_visible_bulletinage int not null default 1";
		    echo traite_rqt($rqt,"alter table users add deflt_opac_visible_bulletinage");
		    
		    
		    // NG - Export des emprises cartographiques des notices
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'exportparam' and sstype_param='export_map' "))==0){
		        $rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			  		VALUES (NULL, 'exportparam', 'export_map', '0', 'Exporter les emprises cartographiques des notices', '', '1')";
		        echo traite_rqt($rqt,"insert exportparam_export_map='0' into parametres ");
		    }
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='exp_export_map' "))==0){
		        $rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			  		VALUES (NULL, 'opac', 'exp_export_map', '0', 'Exporter les emprises cartographiques des notices', '', '1')";
		        echo traite_rqt($rqt,"insert opac_exp_export_map='0' into parametres ");
		    }
		    
		    // NG - Ajout d'un commentaire dans la table contribution_area_forms
		    if (!pmb_mysql_num_rows(pmb_mysql_query("SHOW COLUMNS FROM contribution_area_forms LIKE 'form_comment'"))){
		        $rqt = "alter table contribution_area_forms add form_comment text not null";
		        echo traite_rqt($rqt,"alter table contribution_area_forms add form_comment");
		    }
		    
		    // DG - Ajout de la personnalisation des filtres par utilisateur
		    $rqt = "ALTER TABLE lists ADD list_selected_filters text AFTER list_pager" ;
		    echo traite_rqt($rqt,"ALTER TABLE lists ADD list_selected_filters");
		    
		    // NG - Ajout du mode de prêt (borne_rfid, bibloto, pret_opac, gestion_rfid, gestion_standard) dans la table pret_archive:
		    $rqt = "ALTER TABLE pret_archive ADD arc_pret_source_device varchar(255) not null default '' ";
		    echo traite_rqt($rqt,"alter table pret_archive add arc_pret_source_device");
		    
		    // NG - Ajout du mode de retour de prêt (borne_rfid, bibloto, pret_opac, gestion_rfid, gestion_standard) dans la table pret_archive:
		    $rqt = "ALTER TABLE pret_archive ADD arc_retour_source_device varchar(255) not null default '' ";
		    echo traite_rqt($rqt,"alter table pret_archive add arc_retour_source_device");
		    
		    // DG - Ajout dans les bannettes la possibilité de définir un expéditeur
		    $rqt = "ALTER TABLE bannettes ADD bannette_num_sender INT( 5 ) UNSIGNED NOT NULL default 0 ";
		    echo traite_rqt($rqt,"alter table bannettes add bannette_num_sender");
		    
		    // DG - Paniers - Visible pour tous ?
		    $rqt = "ALTER TABLE caddie ADD autorisations_all INT(1) NOT NULL DEFAULT 0 AFTER autorisations";
		    echo traite_rqt($rqt,"ALTER TABLE caddie add autorisations_all AFTER autorisations");
		    $rqt = "ALTER TABLE empr_caddie ADD autorisations_all INT(1) NOT NULL DEFAULT 0 AFTER autorisations";
		    echo traite_rqt($rqt,"ALTER TABLE empr_caddie add autorisations_all AFTER autorisations");
		    $rqt = "ALTER TABLE authorities_caddie ADD autorisations_all INT(1) NOT NULL DEFAULT 0 AFTER autorisations";
		    echo traite_rqt($rqt,"ALTER TABLE authorities_caddie add autorisations_all AFTER autorisations");
		    
		    // DG - Procédures - Visible pour tous ?
		    $rqt = "ALTER TABLE procs ADD autorisations_all INT(1) NOT NULL DEFAULT 0 AFTER autorisations";
		    echo traite_rqt($rqt,"ALTER TABLE procs add autorisations_all AFTER autorisations");
		    $rqt = "ALTER TABLE caddie_procs ADD autorisations_all INT(1) NOT NULL DEFAULT 0 AFTER autorisations";
		    echo traite_rqt($rqt,"ALTER TABLE caddie_procs add autorisations_all AFTER autorisations");
		    $rqt = "ALTER TABLE empr_caddie_procs ADD autorisations_all INT(1) NOT NULL DEFAULT 0 AFTER autorisations";
		    echo traite_rqt($rqt,"ALTER TABLE empr_caddie_procs add autorisations_all AFTER autorisations");
		    $rqt = "ALTER TABLE authorities_caddie_procs ADD autorisations_all INT(1) NOT NULL DEFAULT 0 AFTER autorisations";
		    echo traite_rqt($rqt,"ALTER TABLE authorities_caddie_procs add autorisations_all AFTER autorisations");
		    
		    // DG - Paniers - Couleur associée au panier
		    $rqt = "ALTER TABLE caddie ADD favorite_color VARCHAR(255) NOT NULL DEFAULT '' AFTER acces_rapide";
		    echo traite_rqt($rqt,"ALTER TABLE caddie add favorite_color");
		    $rqt = "ALTER TABLE empr_caddie ADD favorite_color VARCHAR(255) NOT NULL DEFAULT '' AFTER acces_rapide";
		    echo traite_rqt($rqt,"ALTER TABLE empr_caddie add favorite_color");
		    $rqt = "ALTER TABLE authorities_caddie ADD favorite_color VARCHAR(255) NOT NULL DEFAULT '' AFTER acces_rapide";
		    echo traite_rqt($rqt,"ALTER TABLE authorities_caddie add favorite_color");
		    
		    // DG - maj Colonnes exemplaires affichées en gestion - ajout du nombre de prêts
		    $rqt = "update parametres set comment_param='Colonnes des exemplaires, dans l\'ordre donné, séparé par des virgules : expl_cb,expl_cote,location_libelle,section_libelle,statut_libelle,tdoc_libelle,groupexpl_name,nb_prets #n : id des champs personnalisés \r\n expl_cb est obligatoire et sera ajouté si absent' where type_param= 'pmb' and sstype_param='expl_data' ";
		    echo traite_rqt($rqt,"update pmb_expl_data into parametres");
		    
		    // AP & BT - Ajout d'une table pour la gestion des champs à afficher dans le formulaire de réabonnement
		    // empr_renewal_form_field_code : Code du champ
		    // empr_renewal_form_field_display : Afficher le champ ? 0 ou 1
		    // empr_renewal_form_field_mandatory : Le champ est-il obligatoire ? 0 ou 1
		    // empr_renewal_form_field_alterable : Le champ est-il modifiable ? 0 ou 1
		    // empr_renewal_form_field_explanation : Texte explicatif
		    $rqt="CREATE TABLE IF NOT EXISTS empr_renewal_form_fields (
				empr_renewal_form_field_code VARCHAR(255) NOT NULL PRIMARY KEY,
				empr_renewal_form_field_display TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
				empr_renewal_form_field_mandatory TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
				empr_renewal_form_field_alterable TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
				empr_renewal_form_field_explanation VARCHAR(255) NOT NULL DEFAULT ''
			)";
		    echo traite_rqt($rqt, "CREATE TABLE empr_renewal_form_fields");
		     
		    // DG - maj du commentaire sur la consultation et l'ajout d'un avis
		    $rqt = "update parametres set comment_param='Permet de consulter/ajouter un avis pour les notices \n 0 : non \n 1 : sans être identifié : consultation possible, ajout impossible \n 2 : identification obligatoire pour consulter et ajouter \n 3 : consultation et ajout anonymes possibles' where type_param= 'opac' and sstype_param='avis_allow' ";
		    echo traite_rqt($rqt,"update opac_avis_allow into parametres");
		    
		    // DG - Paramètre pour utiliser la localisation des plans de classement
		    if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'thesaurus' and sstype_param='classement_location' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param, section_param)
					VALUES (0, 'thesaurus', 'classement_location', '0', '0', 'Utiliser la gestion des plans de classement localisés?\n 0: Non\n 1: Oui', 'classement') ";
		        echo traite_rqt($rqt,"INSERT thesaurus_classement_location INTO parametres") ;
		    }
		    
		    // DG - Localisations visibles par plan de classement
		    $rqt = "ALTER TABLE pclassement ADD locations varchar(255) NOT NULL DEFAULT ''";
		    echo traite_rqt($rqt,"alter table pclassement add locations ");
		    
		    // DG - Paramètre pour l'affichage des notices dans les flux RSS partagés
		    if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'opac' and sstype_param='short_url_rss_records_format' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param, section_param)
					VALUES (0, 'opac', 'short_url_rss_records_format', '1', '0', 'Format d\'affichage des notices du flux RSS de recherche\n 1: Défaut\n H 1 = id d\'un template de notice', 'd_aff_recherche') ";
		        echo traite_rqt($rqt,"INSERT opac_short_url_rss_records_format INTO parametres") ;
		    }
		    
		    // AP & CC - Ajout d'un paramètre pour activer le réabonnement à l'OPAC
		    if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'empr' and sstype_param='active_opac_renewal' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param, section_param)
					VALUES (0, 'empr', 'active_opac_renewal', '0', '0', 'Activer la prolongation d\'abonnement à l\'OPAC', '') ";
		        echo traite_rqt($rqt,"INSERT empr_active_opac_renewal INTO parametres") ;
		    }
		    
		    
		    // TS - Ajout d'un paramètre pour activer la suppression du compte à l'OPAC
		    if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'empr' and sstype_param='opac_account_deleted_status' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param, section_param)
					VALUES (0, 'empr', 'opac_account_deleted_status', '0', '0', '0 : Bouton de suppression du compte lecteur en OPAC invisible\nn : Identifiant du statut pour la suppression du compte lecteur + bouton visible', '') ";
		        echo traite_rqt($rqt,"INSERT empr_opac_account_deleted_status INTO parametres") ;
		    }
		    
		    // AP - Schémas de concepts à utiliser par défaut en création de vedettes composées depuis une entité
		    $rqt = "CREATE TABLE IF NOT EXISTS vedette_schemes_by_entity (
		    	entity_type INT UNSIGNED NOT NULL DEFAULT 0,
		    	scheme INT NOT NULL DEFAULT 0,
		    	PRIMARY KEY(entity_type, scheme)
			)";
		    echo traite_rqt($rqt,"create table vedette_schemes_by_entity");
		    
		    
		    // NG & BT - Table de liaison entre les listes de lecture et les notices
		    $rqt = "CREATE TABLE IF NOT EXISTS opac_liste_lecture_notices (
		    	opac_liste_lecture_num INT UNSIGNED NOT NULL DEFAULT 0,
		    	opac_liste_lecture_notice_num INT UNSIGNED NOT NULL DEFAULT 0,
		    	opac_liste_lecture_create_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
		    	PRIMARY KEY(opac_liste_lecture_num, opac_liste_lecture_notice_num)
			)";
		    echo traite_rqt($rqt,"create table opac_liste_lecture_notices");
		    
		    if (pmb_mysql_num_rows(pmb_mysql_query("SHOW COLUMNS FROM opac_liste_lecture LIKE 'notices_associees'"))) {
		        $query = "select id_liste, notices_associees from opac_liste_lecture";
		        $result = pmb_mysql_query($query);
		        if (pmb_mysql_num_rows($result)) {
		            while ($row = pmb_mysql_fetch_object($result)) {
		                if ($row->notices_associees) {
		                    $notices_associees = explode(',', $row->notices_associees);
		                    foreach ($notices_associees as $num_notice) {
		                        $query = "INSERT INTO opac_liste_lecture_notices SET opac_liste_lecture_num=" . $row->id_liste . ", opac_liste_lecture_notice_num=" . $num_notice;
		                        pmb_mysql_query($query);
		                    }
		                }
		            }
		        }
		        $query = "ALTER TABLE opac_liste_lecture DROP notices_associees";
		        pmb_mysql_query($query);
		    }
		    
		    // AP - Paramètre de tri par défaut des notices dans les listes de lecture
		    if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'opac' and sstype_param='default_sort_reading' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param, section_param)
					VALUES (0, 'opac', 'default_sort_reading', 'd_text_35', '0', 'Tri par défaut des recherches OPAC.\nDe la forme, c_num_6 (c pour croissant, d pour décroissant, puis num ou text pour numérique ou texte et enfin l\'identifiant du champ (voir fichier xml sort.xml))', 'd_aff_recherche') ";
		        echo traite_rqt($rqt,"INSERT opac_default_sort_reading INTO parametres") ;
		    }
		    
		    // AP - Paramètre de définition du sélecteur de tri des notices dans les listes de lecture
		    if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'opac' and sstype_param='default_sort_reading_list' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param, section_param)
					VALUES (0, 'opac', 'default_sort_reading_list', '1 d_text_14|Trier par date', '0', 'Listes de lecture :\nAfficher la liste déroulante de sélection d\'un tri ?\n 0 : Non\n 1 : Oui\nFaire suivre d\'un espace pour l\'ajout de plusieurs tris sous la forme : c_num_6|Libelle||d_text_7|Libelle 2||c_num_5|Libelle 3\n\nc pour croissant, d pour décroissant\nnum ou text pour numérique ou texte\nidentifiant du champ (voir fichier xml sort.xml)\nlibellé du tri (optionnel)', 'd_aff_recherche') ";
		        echo traite_rqt($rqt,"INSERT opac_default_sort_reading_list INTO parametres") ;
		    }
		    
		    // DG - [Régression] Correction sur la date de création de l'exemplaire
		    $query = "select expl_id from exemplaires where create_date = '0000-00-00 00:00:00'";
		    $result = pmb_mysql_query($query);
		    if(pmb_mysql_num_rows($result)) {
		        while($row = pmb_mysql_fetch_object($result)) {
		            $rqt = "select quand from audit where type_modif = 1 and type_obj = 2 and object_id =".$row->expl_id;
		            $res = pmb_mysql_query($rqt);
		            if(pmb_mysql_num_rows($res) == 1) {
		                pmb_mysql_query("update exemplaires set create_date='".pmb_mysql_result($res, 0, 'quand')."' where expl_id = '".$row->expl_id."'");
		            }
		        }
		    }
		    
		    // BT & NG - Ajout des champs dates dans la table aut_link
		    $rqt = "ALTER TABLE aut_link ADD aut_link_string_start_date varchar(255) NOT NULL DEFAULT ''";
		    echo traite_rqt($rqt,"alter table aut_link add aut_link_string_start_date ");
		    $rqt = "ALTER TABLE aut_link ADD aut_link_string_end_date varchar(255) NOT NULL DEFAULT ''";
		    echo traite_rqt($rqt,"alter table aut_link add aut_link_string_end_date ");
		    $rqt = "ALTER TABLE aut_link ADD aut_link_start_date DATE NOT NULL default '0000-00-00'";
		    echo traite_rqt($rqt,"alter table aut_link add aut_link_start_date ");
		    $rqt = "ALTER TABLE aut_link ADD aut_link_end_date DATE NOT NULL default '0000-00-00'";
		    echo traite_rqt($rqt,"alter table aut_link add aut_link_end_date ");
		    
		    if (pmb_mysql_num_rows(pmb_mysql_query("show columns from aut_link like 'id_aut_link'")) == 0){
		        $info_message = "<font color=\"#FF0000\">ATTENTION ! Il est nécessaire de regénérer les liens entre autorités, en nettoyage de base !</font>";
		        echo "<tr><td><font size='1'>".($charset == "utf-8" ? utf8_encode($info_message) : $info_message)."</font></td><td></td></tr>";
		    }
		    
		    // NG - Paramètre pour ne pas envoyer la DSI aux lecteurs dont la date de fin d'adhésion est dépassée
		    if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'dsi' and sstype_param='send_empr_date_expiration' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param, section_param)
					VALUES (0, 'dsi', 'send_empr_date_expiration', '1', '0', 'Envoyer la D.S.I aux lecteurs dont la date de fin d\'adhésion est dépassée ?\n 0: Non\n 1: Oui', '') ";
		        echo traite_rqt($rqt,"INSERT dsi_send_empr_date_expiration INTO parametres") ;
		    }
		    
		    // NG - Paramètre pour activer l'autocomplétion dans les liens entre autorités
		    if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'pmb' and sstype_param='aut_link_autocompletion' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param, section_param)
					VALUES (0, 'pmb', 'aut_link_autocompletion', '0', '0', 'Activer l\'autocomplétion dans les liens entre autorités ?\n 0: Non\n 1: Oui', '') ";
		        echo traite_rqt($rqt,"INSERT pmb_aut_link_autocompletion INTO parametres") ;
		    }
		    
		    // DB - Ajout d'une clé primaire sur la table sessions
		    $rqt ="alter table sessions drop primary key";
		    echo traite_rqt($rqt,"alter table sessions drop primary key");
		    $rqt = "alter table sessions add primary key(SESSID)";
		    echo traite_rqt($rqt,"alter table sessions add primary key");
		    // DB - modification de la taille du champ login dans la table sessions
		    $rqt = "ALTER TABLE sessions CHANGE login login VARCHAR(255) NOT NULL DEFAULT ''";
		    echo traite_rqt($rqt,"alter table sessions increase login size to 255");
		    
		    // AR - Paramètre de préfixe des index sphinx
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'sphinx' and sstype_param='indexes_prefix' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param)
					VALUES (NULL, 'sphinx', 'indexes_prefix', '', 'Prefixe pour le nommage des index sphinx','')";
		        echo traite_rqt($rqt,"insert sphinx_indexes_prefix = '' into parametres ");
		    }
		    
		    // DG - Attribut BNF Z3950 - ISMN
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from z_attr where attr_bib_id= '3' and attr_libelle='ismn' "))==0){
		        $rqt = "INSERT INTO z_attr (attr_bib_id, attr_libelle, attr_attr) VALUES ('3','ismn','9')";
		        echo traite_rqt($rqt,"insert ismn for BNF into z_attr");
		    }
		    
		    // DG - Attribut BNF Z3950 - EAN
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from z_attr where attr_bib_id= '3' and attr_libelle='ean' "))==0){
		        $rqt = "INSERT INTO z_attr (attr_bib_id, attr_libelle, attr_attr) VALUES ('3','ean','1214')";
		        echo traite_rqt($rqt,"insert ean for BNF into z_attr");
		    }

		    // +-------------------------------------------------+


			/****/

			$rqt = "update parametres set valeur_param='0' where type_param='pmb' and sstype_param='bdd_subversion' " ;
			echo traite_rqt($rqt,"update pmb_bdd_subversion=0 into parametres");
			$pmb_bdd_subversion=0;
			
			if ($pmb_subversion_database_as_it_shouldbe!=$pmb_bdd_subversion) {
				// Info de déconnexion pour passer le add-on
				$rqt = " select 1 " ;
				echo traite_rqt($rqt,"<b><a href='".$base_path."/logout.php' target=_blank>VOUS DEVEZ VOUS DECONNECTER ET VOUS RECONNECTER POUR TERMINER LA MISE A JOUR  / YOU MUST DISCONNECT AND RECONNECT YOU TO COMPLETE UPDATE</a></b> ") ;
			}
			
			
			// +-------------------------------------------------+
			echo "</table>";
			$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
			$res = pmb_mysql_query($rqt, $dbh) ;
			echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
			$action=$action+$increment;
			//echo form_relance ("v5.33");

		//case "v5.33":
		    echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
		    // +-------------------------------------------------+
		    
		    //DG - Ajout du champ pour inclure ou non le flux RSS dans les métadonnées de l'OPAC
		    // Case à cocher pour le rendre accessible ou non par un agrégateur de flux RSS
		    $rqt = "ALTER TABLE rss_flux ADD metadata_rss_flux INT(1) UNSIGNED NOT NULL DEFAULT 1 AFTER descr_rss_flux";
		    echo traite_rqt($rqt,"ALTER TABLE rss_flux ADD metadata_rss_flux INT(1) UNSIGNED NOT NULL DEFAULT 1 ");
		    
		    // NG - Paramètre pour activer le formulaire du changement de profil à l'OPAC. Si des données sont présentes dans empr_renewal_form_fields, le formulaire est actif par défaut      
		    if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param='empr' and sstype_param='renewal_activate' "))==0){
		        $activate = 0;
		        if (pmb_mysql_num_rows(pmb_mysql_query("SELECT * FROM empr_renewal_form_fields LIMIT 1"))) {
		            $activate = 1;
		        }
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
					VALUES (0, 'empr', 'renewal_activate', '" . $activate . "', '1', 'Activer le formulaire changement de profil à l\'OPAC\n 0: Non \n 1: Oui') ";
		        echo traite_rqt($rqt,"INSERT empr_renewal_activate INTO parametres") ;
		    }

		    // DG - Paramètre d'activation des demandes de location
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='rent_requests_activate' "))==0){
		        $rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
			VALUES ( 'acquisition', 'rent_requests_activate', '0', 'Activation des demandes de location:\n 0 : non \n 1 : oui','', 0)";
		        echo traite_rqt($rqt,"insert acquisition_rent_requests_activate into parametres");
		    }
		    
		    // TS - Modification de la taille du champ name de la table titres_uniformes
		    $rqt = "ALTER TABLE titres_uniformes MODIFY tu_name TEXT" ;
		    echo traite_rqt($rqt,"ALTER TABLE titres_uniformes MODIFY tu_name TO TEXT");
		    
		    // DG - Paramètre pour activer/désactiver la circulation des périodiques ?
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='serialcirc_active' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param)
			VALUES (0, 'pmb', 'serialcirc_active', '1','Activer la circulation des périodiques ? \r\n 0: Non \r\n 1: Oui','')";
		        echo traite_rqt($rqt,"insert pmb_serialcirc_active into parametres");
		    }
		    
		    // DG - Modification du champ date_relance de la table lignes_actes_relances
		    $rqt = "ALTER TABLE lignes_actes_relances MODIFY date_relance DATETIME DEFAULT '0000-00-00 00:00:00'" ;
		    echo traite_rqt($rqt,"alter table lignes_actes_relances modify date_relance");
		    
		    // DG - Vues OPAC dans les recherches prédéfinies
		    $rqt = "show fields from search_persopac";
		    $res = pmb_mysql_query($rqt);
		    $exists = false;
		    if(pmb_mysql_num_rows($res)){
		        while($row = pmb_mysql_fetch_object($res)){
		            if($row->Field == "search_opac_views_num"){
		                $exists = true;
		                break;
		            }
		        }
		    }
		    if(!$exists){
		        $rqt = "ALTER TABLE search_persopac ADD search_opac_views_num text NOT NULL";
		        echo traite_rqt($rqt,"alter table search_persopac add search_opac_views_num");
		        
		        $req = "select search_id, search_opac_views_num from search_persopac";
		        $res = pmb_mysql_query($req);
		        if ($res) {
		            $search_persopac = array();
		            while($row = pmb_mysql_fetch_object($res)) {
		                $search_persopac[$row->search_id] = array();
		            }
		            if (count($search_persopac)) {
		                $req = "select opac_filter_view_num, opac_filter_param from opac_filters where opac_filter_path='search_perso'";
		                $myQuery = pmb_mysql_query($req);
		                if ($myQuery) {
		                    while ($row = pmb_mysql_fetch_object($myQuery)) {
		                        $param = unserialize($row->opac_filter_param);
		                        if(is_array($param['selected'])) {
		                            foreach ($param['selected'] as $selected) {
		                                $search_persopac[$selected]['opac_views_num'][] = $row->opac_filter_view_num;
		                            }
		                        }
		                    }
		                    foreach ($search_persopac as $id=>$search_p) {
		                        if(!isset($search_p['opac_views_num']) || !is_array($search_p['opac_views_num'])) {
		                            $search_p['opac_views_num'] = array();
		                        }
		                        $query = "update search_persopac set search_opac_views_num = '".implode(',', $search_p['opac_views_num'])."' where search_id = '".$id."'";
		                        pmb_mysql_query($query);
		                    }
		                }
		            }
		        }
		    }
		    
		    // DG - Ajout du champ abt_name_opac permettant de préciser un libellé OPAC pour l'abonnement
		    $rqt = "ALTER TABLE abts_abts ADD abt_name_opac VARCHAR(255) NOT NULL DEFAULT '' AFTER abt_name";
		    echo traite_rqt($rqt,"ALTER TABLE abts_abts ADD abt_name_opac");
		    
		    // DG - Ajout dans les bannettes la possibilité de choisir un répertoire de templates
		    $rqt = "ALTER TABLE bannettes ADD django_directory VARCHAR( 255 ) NOT NULL default '' AFTER notice_tpl ";
		    echo traite_rqt($rqt,"alter table bannettes add django_directory");
		    
		    // DG - Ajout dans les bannettes la possibilité de choisir un répertoire de templates pour le produit documentaire
		    $rqt = "ALTER TABLE bannettes ADD document_django_directory VARCHAR( 255 ) NOT NULL default '' AFTER document_notice_tpl ";
		    echo traite_rqt($rqt,"alter table bannettes add document_django_directory");
		    
		    // DG - Ajout dans les bannettes la possibilité de choisir entre un template de notices et un répertoire django
		    $rqt = "ALTER TABLE bannettes ADD notice_display_type INT( 1 ) UNSIGNED NOT NULL default 0 AFTER piedpage_mail ";
		    echo traite_rqt($rqt,"alter table bannettes add notice_display_type");
		    
		    // DG - Ajout dans les bannettes la possibilité choisir entre un template de notices et un répertoire django pour le produit documentaire
		    $rqt = "ALTER TABLE bannettes ADD document_notice_display_type INT( 1 ) UNSIGNED NOT NULL default 0 AFTER document_generate ";
		    echo traite_rqt($rqt,"alter table bannettes add document_notice_display_type");
		    
		    // DG - Connecteurs sortants JSON-RPC = 5, Connecteurs sortants Bibloto = 10
		    //Jusqu'à présent le commentaire était utilisée pour crypter la connexion
		    //Transfert de la valeur du commentaire dans un champ dédié à cela
		    $query = "select connectors_out_source_id, connectors_out_source_name, connectors_out_source_comment, connectors_out_source_config from connectors_out_sources where connectors_out_sources_connectornum IN(5,10)";
		    $result = pmb_mysql_query($query);
		    if ($result && pmb_mysql_num_rows($result)) {
		        while ($source = pmb_mysql_fetch_object($result)) {
		            $source_config = unserialize($source->connectors_out_source_config);
		            // !isset pour s'assurer que l'on n'est pas encore passé ici
		            // afin de ne pas écraser la phrase de connexion à chaque passage de l'alter ou de l'add-on
		            if(!isset($source_config['auth_connexion_phrase'])) {
		                $source_config['auth_connexion_phrase'] = $source->connectors_out_source_comment;
		                $query = "update connectors_out_sources set connectors_out_source_config = '".addslashes(serialize($source_config))."' where connectors_out_source_id = ".$source->connectors_out_source_id;
		                pmb_mysql_query($query);
		                echo traite_rqt($rqt,"UPDATE connectors_out_sources ".$source->connectors_out_source_name);
		            }
		        }
		    }
		    
		    // DB - Ajout champ add_to_new_order dans table Frais annexes
		    $rqt = "ALTER TABLE frais ADD add_to_new_order INT(1) NOT NULL DEFAULT 0 AFTER index_libelle" ;
		    echo traite_rqt($rqt,"alter table frais add field add_to_new_order");
		    
		    // NG - Ajout paramètre permettant de bloquer ou pas le prêt lorsqu'une réservation est faites sans validation
		    if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='pret_resa_non_validee' "))==0){
		        $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param)
			    VALUES (0, 'pmb', 'pret_resa_non_validee', '0',' Bloquer le prêt lorsqu\'une réservation sans validation est faite pour un autre lecteur ? \r\n 0: Non \r\n 1: Oui','')";
		        echo traite_rqt($rqt,"insert pmb_pret_resa_non_validee into parametres");
		    }
		    
		    // BT - Suppression des espaces dans le code des instruments
		    $res = pmb_mysql_query("SELECT id_instrument, instrument_code FROM nomenclature_instruments");
		    while ($row = pmb_mysql_fetch_object($res)) {
		        $code = str_replace(' ', '', $row->instrument_code);
		        pmb_mysql_query("UPDATE nomenclature_instruments SET instrument_code='$code' WHERE id_instrument=$row->id_instrument");
		    }
		    echo traite_rqt("SELECT 1", "UPDATE nomenclature_instruments SET instrument_code without spaces");		
			// +-------------------------------------------------+
			echo "</table>";
			
	//---------------------------------------------------------Se deshabilita la edicion de los formularios--------------------------------
			$rqt="Update parametres set valeur_param='0' WHERE type_param='pmb' and sstype_param='form_authorities_editables' and valeur_param='1'";
			$res = pmb_mysql_query($rqt, $dbh);
				
			$rqt="Update parametres set valeur_param='0' WHERE type_param='pmb' and sstype_param='form_editables' and valeur_param='1'";
			$res = pmb_mysql_query($rqt, $dbh);
				
			$rqt="Update parametres set valeur_param='0' WHERE type_param='pmb' and sstype_param='form_expl_editables' and valeur_param='1'";
			$res = pmb_mysql_query($rqt, $dbh);
				
			$rqt="Update parametres set valeur_param='0' WHERE type_param='pmb' and sstype_param='form_explnum_editables' and valeur_param='1'";
			$res = pmb_mysql_query($rqt, $dbh);
//-------------------------------------------------------------------------------------------------------------------------------------------------------			
			
			$rqt = "update parametres set valeur_param='v5.33' where type_param='pmb' and sstype_param='bdd_version' " ;

		//----------FIN LLIUREX 06/03/2018-------	
			
			$res = pmb_mysql_query($rqt, $dbh) ;
			$rqt = "update parametres set valeur_param='0' where type_param='pmb' and sstype_param='bdd_subversion' " ;
			$res = pmb_mysql_query($rqt, $dbh) ;

			echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
			$action=$action+$increment;

			echo "<SCRIPT>alert(\"".$msg[actualizacion_ok]."\");</SCRIPT>";
			//echo("<SCRIPT LANGUAGE='JavaScript'> window.location = \"$base_path/\"</SCRIPT>");
			break;

		default:
			include("$include_path/messages/help/$lang/alter.txt");
			break;
		}
