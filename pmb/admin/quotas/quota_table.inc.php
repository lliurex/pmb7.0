<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: quota_table.inc.php,v 1.7.26.1 2021/02/19 08:57:56 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $include_path, $categ, $sub;
global $qt, $first, $elements, $elements_quota_form, $query_compl;
//Affichage d'un tableau de quota

require_once($include_path."/templates/quotas.tpl.php");

if (!$first) {
	$elements_quota_form=str_replace("!!quota_table!!",$qt->show_quota_table($elements),$elements_quota_form);
	$ids=$qt->get_table_ids_from_elements_id_ordered($elements);
	$elements_quota_form=str_replace("!!ids_order!!",implode(",",$ids),$elements_quota_form);
	print "<br /><br />\n".$elements_quota_form;
} else {
	$qt->rec_quota($elements);
	print "<script>document.location='./admin.php?categ=$categ&sub=$sub$query_compl';</script>";
}
?>