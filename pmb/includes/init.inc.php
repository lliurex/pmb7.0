<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: init.inc.php,v 1.57.2.8 2020/12/24 11:28:01 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// Cet include permet de r�duire consid�rablement les trucs � mettre au d�part d'un script
// Six param�tres � fournir en fixant les valeurs avant l'include de ce fichier
//	$base_path="../.."; par ex : = chemin pour acc�der � la racine de l'applic PMB
//	$base_auth = "SAUV_AUTH|ADMINISTRATION_AUTH"; les droits du user � tester
//	$base_title = "Titre de la fen�tre"; le titre de la page : facultatif
//		si besoin d'une variable : $base_title = "\$msg[28]";
//	$base_noheader = 0; par d�faut, pas obligatoire, si non vide : pas d'envoi du d�but de page (header & co)
//	$base_nocheck = 0; par d�faut, pas obligatoire : si non vide : pas de checkuser ( session, droits )
//	$base_nobody = 0; par d�faut, pas obligatoire : si non vide : pas de <body> apr�s le header envoy�
//  $base_nosession =0; par d�faut, pas obligatoire, si non vide pas d'envoi du cookie de session dans global_vars.inc.php
//
//	l'exemple ci-dessus correspond � l'inclusion dans le fichier : admin/sauvegarde/launch.php :
//		$base_path="../.."; 
//		$base_auth = "SAUV_AUTH|ADMINISTRATION_AUTH";
//		$base_title = "Lancement d'une sauvegarde"; 
//		require_once ("$base_path/includes/init.inc.php");
//	l'exemple ci-dessus correspond � l'inclusion dans le fichier : catalog/z3950/z_progession_main.php :
//		J'ai besoin du header mais pas du <body> � cause des frames
//		$base_path="../..";
//		$base_auth = "CIRCULATION_AUTH";  
//		$base_title = "";    
//		$base_nobody = 1;    
//		require_once ("$base_path/includes/init.inc.php");  

if (!$base_path) $base_path=".";

include_once ("$base_path/includes/error_report.inc.php") ;

//include_once ("$base_path/includes/global_vars.inc.php") ;
require_once ("$base_path/includes/config.inc.php");

// prevents direct script access
if(preg_match('/init\.inc\.php/', $REQUEST_URI)) {
	include('forbidden.inc.php'); forbidden();
	}

$include_path      = $base_path."/".$include_path; 
$class_path        = $base_path."/".$class_path;
$javascript_path   = $base_path."/".$javascript_path;
$styles_path       = $base_path."/".$styles_path;

if (!defined('TYPE_NOTICE')) 		define('TYPE_NOTICE',1);
if (!defined('TYPE_AUTHOR')) 		define('TYPE_AUTHOR',2);
if (!defined('TYPE_CATEGORY'))		define('TYPE_CATEGORY',3);
if (!defined('TYPE_PUBLISHER')) 	define('TYPE_PUBLISHER',4);
if (!defined('TYPE_COLLECTION')) 	define('TYPE_COLLECTION',5);
if (!defined('TYPE_SUBCOLLECTION')) define('TYPE_SUBCOLLECTION',6);
if (!defined('TYPE_SERIE')) 		define('TYPE_SERIE',7);
if (!defined('TYPE_TITRE_UNIFORME')) define('TYPE_TITRE_UNIFORME',8);
if (!defined('TYPE_INDEXINT'))		define('TYPE_INDEXINT',9);
if (!defined('TYPE_EXPL'))			define('TYPE_EXPL',10);
if (!defined('TYPE_EXPLNUM')) 		define('TYPE_EXPLNUM',11);
if (!defined('TYPE_AUTHPERSO')) 	define('TYPE_AUTHPERSO',12);
if (!defined('TYPE_CMS_SECTION')) 	define('TYPE_CMS_SECTION',13);
if (!defined('TYPE_CMS_ARTICLE')) 	define('TYPE_CMS_ARTICLE',14);
if (!defined('TYPE_LOCATION'))		define('TYPE_LOCATION',15);
if (!defined('TYPE_SUR_LOCATION'))	define('TYPE_SUR_LOCATION',16);
if (!defined('TYPE_CONCEPT'))		define('TYPE_CONCEPT',17);
if (!defined('TYPE_ONTOLOGY'))		define('TYPE_ONTOLOGY',18);
if (!defined('TYPE_DOCWATCH'))		define('TYPE_DOCWATCH',19);
if (!defined('TYPE_EXTERNAL'))		define('TYPE_EXTERNAL',20);

// A n'utiliser QUE dans le contexte des MAP
if (!defined('TYPE_RECORD')) 		define('TYPE_RECORD',11);

if (!defined('TYPE_CONCEPT_PREFLABEL')) 					            define('TYPE_CONCEPT_PREFLABEL', 1);
if (!defined('TYPE_TU_RESPONSABILITY')) 					            define('TYPE_TU_RESPONSABILITY', 2);
if (!defined('TYPE_NOTICE_RESPONSABILITY_PRINCIPAL')) 	                define('TYPE_NOTICE_RESPONSABILITY_PRINCIPAL', 3);
if (!defined('TYPE_NOTICE_RESPONSABILITY_AUTRE')) 		                define('TYPE_NOTICE_RESPONSABILITY_AUTRE', 4);
if (!defined('TYPE_NOTICE_RESPONSABILITY_SECONDAIRE')) 	                define('TYPE_NOTICE_RESPONSABILITY_SECONDAIRE', 5);
if (!defined('TYPE_TU_RESPONSABILITY_INTERPRETER')) 		            define('TYPE_TU_RESPONSABILITY_INTERPRETER', 6);
if (!defined('TYPE_AUTHPERSO_RESPONSABILITY')) 			                define('TYPE_AUTHPERSO_RESPONSABILITY', 7);

// define pour diff�rent flags de situation document
define ('EX_OK', 1);
define ('EX_INCONNU', 2);
define ('HAS_RESA_GOOD', 4); // l'exemplaire est r�serv� pour ce lecteur
define ('NON_PRETABLE', 8);
define ('HAS_NOTE', 16);
define ('HAS_RESA_FALSE', 32); // l'exemplaire est r�serv� pour un autre lecteur
define ('ALREADY_LOANED', 64); // cet emprunteur a d�j� emprunt� ce document
define ('ALREADY_BORROWED', 128); // ce document est emprunt� par un autre emprunteur
define ('HAS_RESA_PLANNED_FALSE', 256); //Les pr�visions sur le document sont �gales ou sup�rieures au nb d'exemplaires disponibles
define ('IS_TRUSTED',512); //l'exemplaire est monopolis�
define ('IS_GROUP',1024); //l'exemplaire fait parti d'un groupe d'exemplaires

require_once("$class_path/XMLlist.class.php");

// fichier de d�f. pour gestion des erreurs
require_once("$include_path/error_handler.inc.php");

require_once("$include_path/db_param.inc.php");

if (isset($_tableau_databases[1]) && isset($base_title)) {
	// multi-databases
	$database_window_title=$_libelle_databases[array_search(LOCATION,$_tableau_databases)].": ";
} else $database_window_title="" ; 

require_once("$include_path/mysql_connect.inc.php");
$dbh = connection_mysql();

require_once("$include_path/sessions.inc.php");
require_once("$include_path/misc.inc.php");
require_once("$javascript_path/misc.inc.php");
require_once($class_path."/pmb_error.class.php");
require_once("$include_path/user_error.inc.php");

// Chargement de l'autoload des librairies externes
require_once $base_path.'/vendor/autoload.php';

if(!isset($_SESSION['CURRENT'])) $_SESSION['CURRENT'] = '';
if(!isset($_SESSION['ext_type'])) $_SESSION['ext_type'] = '';
if(!isset($_SESSION['opac_view'])) $_SESSION['opac_view'] = '';
if(!isset($_SESSION['id_empr_session'])) $_SESSION['id_empr_session'] = '';
if(!isset($_SESSION['user_code'])) $_SESSION['user_code'] = '';
if(!isset($_SESSION['tri'])) $_SESSION['tri'] = '';

if(!isset($sub)) $sub = '';
if(!isset($action)) $action = '';

// Get current page...  pour marquer l'onglet...
if (!isset($current_alert)) {
	$current = current_page();
	$current_module=str_replace(".php","",$current);
} else  {
	$current = '';
	$current_module = $current_alert ;
}
if(in_array($current_module, array('select', 'cart', 'print', 'print_cart', 'download')) && isset($_SERVER["HTTP_REFERER"])) {
	$short_referer = substr($_SERVER["HTTP_REFERER"], strrpos($_SERVER["HTTP_REFERER"], "/")+1);
	$current_module .= " ".substr($short_referer, 0, strpos($short_referer, '.'));
}
if (!$current_module) $current_module = "index" ;

include("$include_path/start.inc.php");

require_once("$include_path/clean_pret_temp.inc.php");
if(isset($categ) && ($categ=='pret' || $categ=='retour')) {
	if (!isset($clean_pret_tmp)) clean_pret_temp();
}

if(!isset($base_auth)) $base_auth = '';
if ($base_auth) eval("\$auth=".$base_auth.";"); 
	else $auth="";
	
// dur�e depuis le dernier rafraichissement
if(!defined('SESSION_REACTIVATE')) {
	if(!empty($pmb_session_reactivate)) {
		define('SESSION_REACTIVATE', $pmb_session_reactivate);
	} else {
		define('SESSION_REACTIVATE', 7200); // refresh max = 120 minutes
	}
}

// dur�e depuis le d�but de la session
if(!defined('SESSION_MAXTIME')) {
	if(!empty($pmb_session_maxtime)) {
		define('SESSION_MAXTIME', $pmb_session_maxtime);
	} else {
		define('SESSION_MAXTIME', 86400);	// dur�e de vie maximum d'une session = 24h
	}
}

if (!isset($base_nocheck)) {
	$base_nocheck = 0;
}
if (!$base_nocheck) {
	if(!checkUser('PhpMyBibli', $auth)) {
		// localisation (fichier XML) (valeur par d�faut)
		$messages = new XMLlist("$include_path/messages/$lang.xml", 0);
 		$messages->analyser();
		$msg = $messages->table;
		
		//Inclusion/initialisation du syst�me de plugins
		require_once $class_path.'/plugins.class.php';
		$plugins = plugins::get_instance();
		
		//Inclusion/initialisation du syst�me d'�venements !
		require_once $class_path.'/event/events_handler.class.php';
		$evth = events_handler::get_instance();
		$evth->discover();
		$requires = $evth->get_requires();
		for($i=0 ; $i<count($requires) ; $i++){
			require_once $requires[$i];
		}
		
		include("$include_path/templates/common.tpl.php");
		
		if(!isset($base_is_http_request) || !$base_is_http_request) {
 			header ("Content-Type: text/html; charset=$charset");
			print $std_header;
		}
		print "<body class='$current_module $pmb_dojo_gestion_style' id='body_current_module' page_name='$current_module'>";
		require_once($class_path."/pmb_error.class.php");
		require_once("$include_path/user_error.inc.php");
		switch ($checkuser_type_erreur) {
			case CHECK_USER_NO_SESSION :
				print "<div id='login-box'>".return_error_message($msg[11], $msg['checkuser_no_session'], 1, './index.php',basename($_SERVER['REQUEST_URI']))."</div>";
				break;
			case CHECK_USER_SESSION_DEPASSEE :
				print "<div id='login-box'>".return_error_message($msg[11], $msg['checkuser_session_depassee'], 1, './index.php', basename($_SERVER['REQUEST_URI']))."</div>";
				break;
			case CHECK_USER_SESSION_INVALIDE :
				print "<div id='login-box'>".return_error_message($msg[11], $msg['checkuser_session_invalide'], 1, './index.php', basename($_SERVER['REQUEST_URI']))."</div>";
				break;
			case CHECK_USER_AUCUN_DROITS :
				print "<div id='login-box'>".return_error_message($msg[11], $msg['checkuser_aucun_droit'], 1)."</div>";
				break;
			case CHECK_USER_PB_ENREG_SESSION :
				print "<div id='login-box'>".return_error_message($msg[11], $msg['checkuser_pb_enreg_session'], 1, './index.php')."</div>";
				break;
			case CHECK_USER_PB_OUVERTURE_SESSION :
				print "<div id='login-box'>".return_error_message($msg[11], $msg['checkuser_pb_ouverture_session'], 1, './index.php')."</div>";
				break;
			default :
				print "<div id='login-box'>".return_error_message($msg[11], $msg[12], 1)."</div>";
				break;
			}
		print $footer;
		exit;
	}
	
	if(SESSlang) {
		$lang=SESSlang;
		$helpdir = $lang;
	}

	if (!$pmb_indexation_lang) $pmb_indexation_lang = $lang; 

	// localisation (fichier XML)
	$messages = new XMLlist("$include_path/messages/$lang.xml", 0);
 	$messages->analyser();
	$msg = $messages->table;
	
	//Inclusion/initialisation du syst�me de plugins
	require_once $class_path.'/plugins.class.php';
	$plugins = plugins::get_instance();
	
	//Inclusion/initialisation du syst�me d'�venements !
	require_once $class_path.'/event/events_handler.class.php';
	$evth = events_handler::get_instance();
	$evth->discover();
	$requires = $evth->get_requires();
	
	for($i=0 ; $i<count($requires) ; $i++){
		require_once $requires[$i];
	}
	
	require("$include_path/templates/common.tpl.php");  
	
	//
	$champs_base=array();
}

if (!isset($base_noheader)) {
	$base_noheader = 0;
}
if (!$base_noheader) {
 	header ("Content-Type: text/html; charset=$charset");
	print $std_header;
	if (!isset($base_nobody)) {
		$base_nobody = 0;
	}
	if (!$base_nobody) print "<body class='$current_module $pmb_dojo_gestion_style' id='body_current_module' page_name='$current_module'>";
	if (isset($base_title)) {
		eval ("\$base_title_temp=\"".$database_window_title.$base_title."\";") ;
		echo window_title($base_title_temp);
	}
}

// Param�trage de la RFID, en fonction �ventuellement de la localisation
require_once($class_path."/parameters_subst.class.php");
if (file_exists($include_path."/parameters_subst/rfid_per_localisations_subst.xml")){
	$parameter_subst = new parameters_subst($include_path."/parameters_subst/rfid_per_localisations_subst.xml", (isset($deflt2docs_location) ? $deflt2docs_location : 0));
} else {
	$parameter_subst = new parameters_subst($include_path."/parameters_subst/rfid_per_localisations.xml", (isset($deflt2docs_location) ? $deflt2docs_location : 0));
}
$parameter_subst->extract();

// Activation RFID selon les prefs user
if (!isset($param_rfid_activate)) $param_rfid_activate = '';
if($pmb_rfid_activate)	$pmb_rfid_activate=$param_rfid_activate;
// Pr�paration des js sripts pour la RFID
if($pmb_rfid_activate) {	
	require_once($include_path."/rfid_config.inc.php");
	get_rfid_js_header();
} else {
	$rfid_js_header = "";
}

require_once $class_path.'/event/events_handler.class.php';
require_once $class_path.'/event/event.class.php';
$evth = events_handler::get_instance();
$evth->send(new event('init', 'finished'));

require_once($class_path.'/interface/interface_form.class.php');
require_once($class_path.'/interface/interface_date.class.php');

require_once($class_path."/autoloader.class.php");
$autoloader = new autoloader();
$autoloader->add_register("lists_class",true);