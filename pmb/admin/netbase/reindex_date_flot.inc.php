<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: reindex_date_flot.inc.php,v 1.1.2.1 2021/03/16 16:21:21 moble Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");
global $class_path;
require_once $class_path.'/entities.class.php';
require_once $class_path.'/parametres_perso.class.php';

// initialisation de la borne de départ
if (empty($start)) {
	$start=0;
	//remise a zero de la table au début
}

$v_state=urldecode($v_state);
$fields_date_flot = array();
if (empty($count)) {
    $count = 0;
	//nombre de CP date flottante
    $prefixes = entities::get_prefixes();
    foreach ($prefixes as $prefix) {
        $p_perso = new parametres_perso($prefix);
        $fields = $p_perso->get_t_fields();
        foreach ($fields as $id_field => $field) {
            if ('date_flot' == $field['TYPE']) {
                if (!isset($fields_date_flot[$prefix])) {
                    $fields_date_flot[$prefix] = array();
                }
                $fields_date_flot[$prefix][] = $id_field;
                $count++;
            }
        }
    }
}

print "<br /><br /><h2 class='center'>".htmlentities($msg["nettoyage_reindex_date_flot"], ENT_QUOTES, $charset)."</h2>";

$NoIndex = 1;
$counter = 0;
foreach ($fields_date_flot as $prefix=>$fields_id) {
    foreach ($fields_id as $field_id) {
        $requete = "delete from ".$prefix."_custom_dates where ".$prefix."_custom_champ=$field_id";
        $query = 'SELECT '.$prefix.'_custom_small_text, '. $prefix.'_custom_text, '.$prefix.'_custom_origine, '.$prefix.'_custom_order 
                  FROM '.$prefix.'_custom_values 
                  WHERE '.$prefix.'_custom_champ = '.$field_id;
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            while ($row = pmb_mysql_fetch_array($result)) {
                $value = $row[0];
                if (empty($row[0])) {
                    $value = $row[1];
                }
                
                $interval = explode("|||", $value);
                $date_type = $interval[0];
                
                $date_start_signe = 1;
                $date_end_signe = 1;
                if (substr($interval[1], 0, 1) == '-') {
                    // date avant JC
                    $date_start_signe = -1;
                    $interval[1] = substr($interval[1], 1);
                }
                if (substr($interval[2], 0, 1) == '-') {
                    // date avant JC
                    $date_end_signe = -1;
                    $interval[2] = substr($interval[2], 1);
                }
                // années saisie inférieures à 4 digit
                if (strlen($interval[1]) < 4) {
                    $interval[1] = str_pad($interval[1], 4, '0', STR_PAD_LEFT);
                }
                if ($interval[2] && strlen($interval[2]) < 4) {
                    $interval[2] = str_pad($interval[2], 4, '0', STR_PAD_LEFT);
                }
                
                $date_start = detectFormatDate($interval[1], 'min');
                $date_end = detectFormatDate($interval[2], 'max');
                
                if ($date_start == '0000-00-00') {
                    $date_start = '';
                }
                if ($date_end == '0000-00-00') {
                    $date_end = '';
                }
                
                if ($date_start || $date_end) {
                    if (!$date_end) {
                        $date_end = detectFormatDate($interval[1], 'max');
                        $date_end_signe = $date_start_signe;
                    }
                    // format en integer
                    $date_start = str_replace('-', '', $date_start) * $date_start_signe;
                    $date_end = str_replace('-', '', $date_end) * $date_end_signe;
                    if ($date_end < $date_start) {
                        $date = $date_start;
                        $date_start = $date_end;
                        $date_end = $date;
                    }
                    $requete = "INSERT INTO ".$prefix."_custom_dates (".$prefix."_custom_champ,".$prefix."_custom_origine,
								".$prefix."_custom_date_type,".$prefix."_custom_date_start,".$prefix."_custom_date_end,".$prefix."_custom_order)
								VALUES($field_id,$row[2],$date_type,'".$date_start."','".$date_end."',$row[3])";
                    pmb_mysql_query($requete);
                }
            }//end while
        }
        $counter++;
        print netbase::get_display_progress($counter, $count);
    }
}




$spec = $spec - INDEX_DATE_FLOT;
$v_state .= "<br /><img src='".get_url_icon('d.gif')."' hspace=3>".htmlentities($msg["nettoyage_reindex_date_flot"], ENT_QUOTES, $charset)." :";
$v_state .= $count." ".htmlentities($msg["nettoyage_res_reindex_date_flot"], ENT_QUOTES, $charset);

print netbase::get_process_state_form($v_state, $spec);
