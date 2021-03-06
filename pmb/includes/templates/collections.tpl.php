<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: collections.tpl.php,v 1.49.2.1 2020/02/06 12:59:11 dgoron Exp $

// templates pour gestion des autorités collections

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $collection_form, $sub_collection_form, $collection_replace_form, $sub_coll_rep_form, $pmb_form_authorities_editables, $PMBuserid, $pmb_autorites_verif_js, $base_path, $msg;
global $current_module, $charset, $id;

//	----------------------------------
// $collection_form : form saisie collection

$collection_form = jscript_unload_question();
$collection_form.= $pmb_autorites_verif_js!= "" ? "<script type='text/javascript' src='$base_path/javascript/$pmb_autorites_verif_js'></script>":"";
$collection_form.= "
<script src='javascript/ajax.js'></script>
<script type='text/javascript'>
	require(['dojo/ready', 'apps/pmb/gridform/FormEdit'], function(ready, FormEdit){
	     ready(function(){
	     	new FormEdit();
	     });
	});
</script>
<script type='text/javascript'>
	function test_form(form) {
		if (typeof check_form == 'function') {
			if (!check_form()) {
				return false;
			}
		}
	";
	if ($pmb_autorites_verif_js != "") {
		$collection_form.= "
						if(typeof check_perso_collection_form == 'function'){
							var check = check_perso_collection_form(form);
							if (check == false) return false;
						}";
	}
$collection_form.=
		"if(form.collection_nom.value.length == 0) {
			alert(\"$msg[166]\");
			return false;
		}
		if(form.ed_id.value == 0) {
			alert(\"$msg[172]\");
			return false;
		}
		unload_off();
		return true;
	}
function confirm_delete() {
        result = confirm(\"".$msg['confirm_suppr']."\");
        if(result) {
        	unload_off();
            document.location='!!delete_action!!';
		} else
            document.forms['saisie_collection'].elements['collection_nom'].focus();
    }
function check_link(id) {
	w=window.open(document.getElementById(id).value);
	w.focus();
}
</script>
<script src='javascript/ajax.js'></script>
<script type='text/javascript'>
	document.title='!!document_title!!';
</script>
<form class='form-$current_module' id='saisie_collection' name='saisie_collection' method='post' action='!!action!!' onSubmit=\"return false\" enctype='multipart/form-data'>
<div class='row'>
	<div class='left'><h3>!!libelle!!</h3></div>
	<div class='right'>";

	$collection_form.='
	<!-- Selecteur de statut -->
		<label class="etiquette" for="authority_statut">'.$msg['authorities_statut_label'].'</label>
		!!auth_statut_selector!!
	';

	if ($PMBuserid==1 && $pmb_form_authorities_editables==1){
		$collection_form.="<input type='button' class='bouton_small' value='".$msg["authorities_edit_format"]."' id=\"bt_inedit\"/>";
	}
	if ($pmb_form_authorities_editables==1) {
		$collection_form.="<input type='button' class='bouton_small' value=\"".$msg["authorities_origin_format"]."\" id=\"bt_origin_format\"/>";
	}
	$collection_form .= "
	</div>
</div>
<div class='form-contenu'>
	<div class='row'>
		<a onclick='expandAll();return false;' href='#'><img border='0' id='expandall' src='".get_url_icon('expand_all.gif')."'></a>
		<a onclick='collapseAll();return false;' href='#'><img border='0' id='collapseall' src='".get_url_icon('collapse_all.gif')."'></a>
	</div>
	<div id='zone-container'>
		<!-- nom -->
		<div id='el0Child_0' class='row'>
			<div id='el0Child_0_a' class='colonne2' movable='yes' title=\"".htmlentities($msg[714], ENT_QUOTES, $charset)."\">
				<div class='row'>
					<label class='etiquette' for='form_nom'>$msg[714]</label>
				</div>
				<div class='row'>
					<input type='text' class='saisie-30em' size='40' name='collection_nom' value=\"!!collection_nom!!\" data-pmb-deb-rech='1'/>
				</div>
			</div>
		
			<!-- issn -->
			<div id='el0Child_0_b' class='colonne2' movable='yes' title=\"".htmlentities($msg[165], ENT_QUOTES, $charset)."\">
				<div class='row'>
					<label class='etiquette' for='form_issn'>$msg[165]</label>
				</div>
				<div class='row'>
					<input type='text' class='saisie-20em' name='issn' value=\"!!issn!!\" maxlength='50' />
				</div>
			</div>
			<div class='row'></div>
		</div>
		<!-- edparent -->
		<div id='el0Child_1' class='row' movable='yes' title=\"".htmlentities($msg[164], ENT_QUOTES, $charset)."\">
			<div class='row'>
				<label class='etiquette' for='form_edparent'>$msg[164]</label>
			</div>
			<div class='row'>
				<input type='text' class='saisie-50emr' id='ed_libelle' name='ed_libelle' value=\"!!ed_libelle!!\" completion=\"publishers\" autfield=\"ed_id\" autexclude=\"!!id!!\"
			    onkeypress=\"if (window.event) { e=window.event; } else e=event; if (e.keyCode==9) { openPopUp('./select.php?what=editeur&caller=saisie_collection&p1=ed_id&p2=ed_libelle&p3=dcoll_id&p4=dcoll_lib&p5=dsubcoll_id&p6=dsubcoll_lib', 'selector'); }\" />
			
				<input class='bouton' type='button' onclick=\"openPopUp('./select.php?what=editeur&caller=saisie_collection&p1=ed_id&p2=ed_libelle&p3=dcoll_id&p4=dcoll_lib&p5=dsubcoll_id&p6=dsubcoll_lib', 'selector')\" title='$msg[157]' value='$msg[parcourir]' />
				<input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.ed_libelle.value=''; this.form.ed_id.value='0'; \" />
				<input type='hidden' name='ed_id' id='ed_id' value='!!ed_id!!' />
				<input type='hidden' name='dcoll_id' />
				<input type='hidden' name='dcoll_lib' />
				<input type='hidden' name='dsubcoll_id' />
				<input type='hidden' name='dsubcoll_lib' />
			</div>
		</div>
		
		<!-- web -->
		<div id='el0Child_2' class='row' movable='yes' title=\"".htmlentities($msg[147], ENT_QUOTES, $charset)."\">
			<div class='row'>
				<label class='etiquette' for='form_web'>$msg[147]</label>
			</div>
			<div class='row'>
				<input type='text' class='saisie-80em' name='collection_web' id='collection_web' value=\"!!collection_web!!\" maxlength='255' />
				<input class='bouton' type='button' onClick=\"check_link('collection_web')\" title='$msg[CheckLink]' value='$msg[CheckButton]' />
			</div>
		</div>
		
		<!-- Commentaire -->
		<div id='el0Child_3' class='row' movable='yes' title=\"".htmlentities($msg['collection_comment'], ENT_QUOTES, $charset)."\">
			<div class='row'>
				<label class='etiquette' for='comment'>".$msg['collection_comment']."</label>
			</div>
			<div class='row'>
				<textarea class='saisie-80em' id='comment' name='comment' cols='62' rows='4' wrap='virtual'>!!comment!!</textarea>
			</div>
		</div>
			
		!!concept_form!!
		!!thumbnail_url_form!!
		!!aut_pperso!!
		<!-- aut_link -->
	</div>
</div>
<div class='row'>
	<div class='left'>
		<input type='button' class='bouton' value='$msg[76]' id='btcancel' onClick=\"unload_off();document.location='!!cancel_action!!';\" />
		<input type='button' value='$msg[77]' class='bouton' id='btsubmit' onClick=\"document.getElementById('save_and_continue').value=0; if (test_form(this.form)) this.form.submit();\" />
        <input type='hidden' name='save_and_continue' id='save_and_continue' value='' />
		<input type='button' id='update_continue' class='bouton' value='" . $msg['save_and_continue'] . "' onClick=\"document.getElementById('save_and_continue').value=1;if (test_form(this.form)) this.form.submit();\" />
		!!remplace!!
		!!voir_notices!!
		!!audit_bt!!
		<input type='hidden' name='page' value='!!page!!' />
		<input type='hidden' name='nbr_lignes' value='!!nbr_lignes!!' />
		<input type='hidden' name='user_input' value=\"!!user_input!!\" />
	</div>
	<div class='right'>
		!!delete!!
	</div>
</div>
<div class='row'></div>
</form>
<script type='text/javascript'>
	ajax_parse_dom();
	document.forms['saisie_collection'].elements['collection_nom'].focus();
</script>
";

//	----------------------------------

// $sub_collection_form : form saisie sous collection
$sub_collection_form = jscript_unload_question();
$sub_collection_form.= $pmb_autorites_verif_js!= "" ? "<script type='text/javascript' src='$base_path/javascript/$pmb_autorites_verif_js'></script>":"";
$sub_collection_form.= "
<script src='javascript/ajax.js'></script>
<script type='text/javascript'>
	require(['dojo/ready', 'apps/pmb/gridform/FormEdit'], function(ready, FormEdit){
	     ready(function(){
	     	new FormEdit();
	     });
	});
</script>
<script type='text/javascript'>
function test_form(form) {
	if (typeof check_form == 'function') {
		if (!check_form()) {
			return false;
		}
	}
	";
	if ($pmb_autorites_verif_js != "") {
		$sub_collection_form.= "
			if(typeof check_perso_sub_collection_form == 'function'){
				var check = check_perso_sub_collection_form(form);
				if (check == false) return false;
			}";
	}
	$sub_collection_form.="
	if(form.collection_nom.value.length == 0) {
		alert(\"$msg[166]\");
		return false;
	}
	if(form.coll_id.value == 0) {
		alert(\"$msg[180]\");
		return false;
	}
	unload_off();
	return true;
}

function confirm_delete() {
	result = confirm(\"".$msg['confirm_suppr']."\");
    if(result) {
		unload_off();
		document.location='!!delete_action!!';
	} else
		document.forms['saisie_sub_collection'].elements['collection_nom'].focus();
}

function check_link(id) {
	w=window.open(document.getElementById(id).value);
	w.focus();
}

function f_coll_id_callback() {
    ajax_get_entity('get_publisher', 'collection', document.getElementById('coll_id').value, 'ed_id', 'ed_libelle');
}
</script>
<script type='text/javascript'>
	document.title='!!document_title!!';
</script>
<form class='form-$current_module' name='saisie_sub_collection' method='post' action='!!action!!' enctype='multipart/form-data'>
<div class='row'>
	<div class='left'>
		<h3>!!libelle!!</h3>
	</div>
	<div class='right'>";

	$sub_collection_form.='
	<!-- Selecteur de statut -->
		<label class="etiquette" for="authority_statut">'.$msg['authorities_statut_label'].'</label>
		!!auth_statut_selector!!
	';

	if ($PMBuserid==1 && $pmb_form_authorities_editables==1){
		$sub_collection_form.="<input type='button' class='bouton_small' value='".$msg["authorities_edit_format"]."' id=\"bt_inedit\"/>";
	}
	if ($pmb_form_authorities_editables==1) {
		$sub_collection_form.="<input type='button' class='bouton_small' value=\"".$msg["authorities_origin_format"]."\" id=\"bt_origin_format\"/>";
	}
	$sub_collection_form .= "</div>
</div>
<div class='form-contenu'>
	<div class='row'>
		<a onclick='expandAll();return false;' href='#'><img border='0' id='expandall' src='".get_url_icon('expand_all.gif')."'></a>
		<a onclick='collapseAll();return false;' href='#'><img border='0' id='collapseall' src='".get_url_icon('collapse_all.gif')."'></a>
	</div>
	<div id='zone-container'>
		<div id='el0Child_0' class='row'>
			<!-- nom -->
			<div id='el0Child_0_a' class='colonne2' movable='yes' title=\"".htmlentities($msg[67], ENT_QUOTES, $charset)."\">
				<div class='row'>
					<label class='etiquette' for='form_nom'>$msg[67]</label>
				</div>
				<div class='row'>
					<input type='text' class='saisie-30em' size='40' name='collection_nom' value=\"!!collection_nom!!\" data-pmb-deb-rech='1'/>
				</div>
			</div>
			<!-- issn -->
			<div id='el0Child_0_b' class='colonne2' movable='yes' title=\"".htmlentities($msg[165], ENT_QUOTES, $charset)."\">
				<div class='row'>
					<label class='etiquette' for='form_issn'>$msg[165]</label>
				</div>
				<div class='row'>
					<input type='text' class='saisie-20em' name='issn' value=\"!!issn!!\" maxlength='50' />
				</div>
			</div>
			<div class='row'></div>
		</div>
		<div id='el0Child_1' class='row'>
			<!-- collparent -->
			<div id='el0Child_1_a' class='colonne2' movable='yes' title=\"".htmlentities($msg[179], ENT_QUOTES, $charset)."\">
				<div class='row'>
					<label class='etiquette' for='form_collparent'>$msg[179]</label>
				</div>
				<div class='row'>
					<input type='text' class='saisie-30emr' size='34' name='coll_libelle' id='coll_libelle' value=\"!!coll_libelle!!\"  completion='collections'  autfield='coll_id' linkfield='ed_id' callback='f_coll_id_callback' />
					<input class='bouton' type='button' onclick=\"openPopUp('./select.php?what=collection&caller=saisie_sub_collection&p1=ed_id&p2=ed_libelle&p3=coll_id&p4=coll_libelle&p5=dsubcoll_id&p6=dsubcoll_lib', 'selector')\" title='$msg[157]' value='$msg[parcourir]' />
					<input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.coll_libelle.value=''; this.form.ed_libelle.value=''; this.form.coll_id.value='0'; this.form.ed_id.value='0'; \" />
					<input type='hidden' name='coll_id' id='coll_id' value='!!coll_id!!' />
					<input type='hidden' name='dsubcoll_id' />
					<input type='hidden' name='dsubcoll_lib' />
				</div>
	        </div>        
			<!-- colledparent -->
			<div id='el0Child_1_b' class='colonne2' movable='yes' title=\"".htmlentities($msg[164], ENT_QUOTES, $charset)."\">
				<div class='row'>
					<label class='etiquette' for='form_colledparent'>$msg[164]</label>
				</div>
				<div class='row'>
					<input type='text' class='saisie-30emr' size='34' name='ed_libelle' id='ed_libelle' readonly value=\"!!ed_libelle!!\" />
					<input type='hidden' name='ed_id' id='ed_id' value='!!ed_id!!' />
				</div>
			</div>
		</div>
		<!-- web -->
		<div id='el0Child_2' class='row' movable='yes' title=\"".htmlentities($msg[147], ENT_QUOTES, $charset)."\">
			<div class='row'>
				<label class='etiquette' for='form_web'>$msg[147]</label>
				</div>
			<div class='row'>
				<input type='text' class='saisie-80em' name='subcollection_web' id='subcollection_web' value=\"!!subcollection_web!!\" />
				<input class='bouton' type='button' onClick=\"check_link('subcollection_web')\" title='$msg[CheckLink]' value='$msg[CheckButton]' />
			</div>
		</div>
		<!-- Commentaire -->
		<div id='el0Child_3' class='row' movable='yes' title=\"".htmlentities($msg['subcollection_comment'], ENT_QUOTES, $charset)."\">
			<div class='row'>
				<label class='etiquette' for='comment'>$msg[subcollection_comment]</label>
			</div>
			<div class='row'>
				<textarea class='saisie-80em' id='comment' name='comment' cols='62' rows='4' wrap='virtual'>!!comment!!</textarea>
			</div>
		</div>
		!!concept_form!!
		!!thumbnail_url_form!!
		!!aut_pperso!!
		<!-- aut_link -->
	</div>
</div>
<div class='row'>
	<div class='left'>
		<input type='button' id='btcancel' class='bouton' value='$msg[76]' onClick=\"unload_off();document.location='!!cancel_action!!';\" />
		<input type='submit' id='btsubmit' value='$msg[77]' class='bouton' onClick=\"document.getElementById('save_and_continue').value=0;return test_form(this.form)\" />
		<input type='hidden' name='save_and_continue' id='save_and_continue' value='' />
		<input type='button' id='update_continue' class='bouton' value='" . $msg['save_and_continue'] . "' onClick=\"document.getElementById('save_and_continue').value=1;if(test_form(this.form)) {this.form.submit();}\" />
		!!remplace!!
		!!voir_notices!!
		!!audit_bt!!
		<input type='hidden' name='page' value='!!page!!' />
		<input type='hidden' name='nbr_lignes' value='!!nbr_lignes!!' />
		<input type='hidden' name='user_input' value=\"!!user_input!!\" />
	</div>
	<div class='right'>
		!!delete!!
	</div>
</div>
<div class='row'></div>
</form>
<script type='text/javascript'>
	document.forms['saisie_sub_collection'].elements['collection_nom'].focus();
	ajax_parse_dom();
</script>
";

// $collection_replace_form : form remplacement collection
$collection_replace_form = "
<script src='javascript/ajax.js'></script>
<script type='text/javascript'>
<!--
	function test_form(form) {
		if(form.by.value.length == 0) {
			alert(\"$msg[180]\");
			return false;
		}
		return true;
	}
-->
    function f_coll_id_callback() {
		ajax_get_entity('get_publisher', 'collection', document.getElementById('by').value, 'ed_libelle', 'ed_id');
	}
</script>

<form class='form-$current_module' name='coll_replace' method='post' action='!!controller_url_base!!&sub=replace&id=!!id!!'>
<h3>$msg[159] !!coll_name!! (!!coll_editeur!!)</h3>
<div class='form-contenu'>
	<div class='row'>
		<label class='etiquette' for='par'>$msg[160]</label>
	</div>
	<div class='row'>
		<label class='etiquette' for='par'>$msg[186]</label>
	</div>
	<div class='row'>
		<input type='text' class='saisie-30emr' name='coll_libelle' id='coll_libelle' data-form-name='coll_libelle' value='' completion='collections'  autfield='by' autexclude='!!id!!'  linkfield='ed_id' callback='f_coll_id_callback' />
		<input class='bouton' type='button' onclick=\"openPopUp('./select.php?what=collection&caller=coll_replace&p1=ed_id&p2=ed_libelle&p3=by&p4=coll_libelle&p5=dsubcoll_id&p6=dsubcoll_lib&no_display=!!id!!', 'selector')\" title='$msg[157]' value='$msg[parcourir]' />
		<input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.coll_libelle.value=''; this.form.ed_libelle.value=''; this.form.by.value='0'; this.form.ed_id.value='';\" />
		<input type='hidden' name='by' id='by' value=''>
	</div>
	<div class='row'>
		<label class='etiquette' for='par'>$msg[164]</label>
	</div>
	<div class='row'>
		<input type='text' class='saisie-30emr' name='ed_libelle' id='ed_libelle' readonly value='' />
		<input type='hidden' name='dsubcoll_id'>
		<input type='hidden' name='dsubcoll_lib'>
		<input type='hidden' name='ed_id' id='ed_id' value=''>
	</div>
	<div class='row'>		
		<input id='aut_link_save' name='aut_link_save' type='checkbox' checked='checked' value='1'>".$msg["aut_replace_link_save"]."
	</div>	
</div>
<div class='row'>
	<input type='button' id='btcancel' class='bouton' value='$msg[76]' onClick=\"document.location='!!cancel_action!!'\">
	<input type='submit' id='btsubmit' class='bouton' value='$msg[159]' onClick=\"return test_form(this.form)\">
</div>
</form>

<script type='text/javascript'>
	ajax_parse_dom();
	document.forms['coll_replace'].elements['coll_libelle'].focus();
</script>
";

// $sub_coll_rep_form : form remplacement sous collection
$sub_coll_rep_form = "
<script src='javascript/ajax.js'></script>
<script type='text/javascript'>
<!--
	function test_form(form) {
		if(form.by.value.length == 0) {
			alert(\"$msg[180]\");
			return false;
		}
		return true;
	}
    function f_sub_coll_id_callback() {
		ajax_get_entity('get_collection', 'sub_coll_nom', document.getElementById('by').value, 'coll_id', 'coll_libelle', 'ajax_get_entity_response');
	}
    document.body.addEventListener('ajax_get_entity_response', function(e) {
        ajax_get_entity('get_publisher', 'coll_libelle', document.getElementById('coll_id').value, 'ed_id', 'ed_libelle');
	});
-->
</script>
<form class='form-$current_module' name='saisie_sub_collection' method='post' action='!!controller_url_base!!&sub=replace&id=".(!empty($id) ? $id : 0)."'>
<h3>$msg[159] !!subcoll_name!! </h3>
<div class='form-contenu'>
	<div class='row'>
		<label class='etiquette' for='par'>$msg[160]</label>
	</div>
	<div class='row'>
		<label class='etiquette' for='par'>$msg[192]</label>
	</div>
	<div class='row'>
		<input type='text' name='sub_coll_nom'  id='sub_coll_nom' data-form-name='sub_coll_nom' class='saisie-30emr' value='' completion='subcollections' autfield='by' autexclude='!!id!!'  linkfield='ed_id' callback='f_sub_coll_id_callback'/>
		<input type='hidden' name='by' id='by' value=''>
		<input class='bouton' type='button' onclick=\"openPopUp('./select.php?what=subcollection&caller=saisie_sub_collection&p1=ed_id&p2=ed_libelle&p3=coll_id&p4=coll_libelle&p5=by&p6=sub_coll_nom', 'selector')\" title='$msg[157]' value='$msg[parcourir]' />
		<input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.sub_coll_nom.value=''; this.form.coll_libelle.value=''; this.form.ed_libelle.value=''; this.form.ed_id.value=''; this.form.coll_id.value=''; this.form.by.value='0'; \" />
	</div>
	<div class='row'>
		<label class='etiquette' for='par'>$msg[179]</label>
	</div>
	<div class='row'>
		<input type='text' class='saisie-30emr' name='coll_libelle' id='coll_libelle' readonly value='' />
		<input type='hidden' name='coll_id' id='coll_id' value=''/>
	</div>
	<div class='row'>
		<label class='etiquette' for='par'>$msg[164]</label>
	</div>
	<div class='row'>
		<input type='text' class='saisie-30emr' name='ed_libelle' id='ed_libelle' readonly value='' />
		<input type='hidden' name='ed_id' id='ed_id' value=''>
	</div>
	<div class='row'>		
		<input id='aut_link_save' name='aut_link_save' type='checkbox' checked='checked' value='1'>".$msg["aut_replace_link_save"]."
	</div>	
</div>
<div class='row'>
	<input type='button' id='btcancel' class='bouton' value='$msg[76]' onClick=\"document.location='!!cancel_action!!'\">
	<input type='submit' id='btsubmit' class='bouton' value='$msg[159]' onClick=\"return test_form(this.form)\">
</div>
</form>
<script type='text/javascript'>
	ajax_parse_dom();
	document.forms['saisie_sub_collection'].elements['sub_coll_nom'].focus();
</script>

";

