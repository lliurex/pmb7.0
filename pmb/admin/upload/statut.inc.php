<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: statut.inc.php,v 1.7.4.5 2021/01/12 13:00:31 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $msg, $id;

require_once($class_path."/explnum_statut.class.php");
require_once($class_path."/configuration/configuration_controller.class.php");

// gestion des codes statut documents numériques
?>
<script type="text/javascript">
function test_form(form) {
	if(form.form_gestion_libelle.value.length == 0) {
		alert("<?php echo $msg[98] ?>");
		return false;
	}
	return true;
}
</script>

<?php
configuration_controller::set_model_class_name('explnum_statut');
configuration_controller::set_list_ui_class_name('list_configuration_explnum_statut_ui');
configuration_controller::proceed($id);