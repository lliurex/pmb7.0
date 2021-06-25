<?php
// +-------------------------------------------------+

// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sel_authperso.tpl.php,v 1.4.2.1 2021/01/04 10:05:09 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

require_once($base_path."/selectors/templates/sel_authorities.tpl.php");

//-------------------------------------------
//	$jscript : script de m.a.j. du parent
//-------------------------------------------

/**
 * Script de mise � jour des champs vedette compos�e authperso
 */

global $p1, $p2, $p3, $p4, $p5, $p6;
global $param1, $param2, $field_id, $field_name_id;
global $dyn;
global $jscript;
global $jscript_common_selector_simple;
global $authperso_form_all;
global $infield;

if($dyn == 1){
    $jscript = $jscript_common_selector_simple;
}else if ($dyn==3) {
    $jscript ="
<script type='text/javascript'>
	function set_parent(f_caller, id_value, libelle_value){
	
		var w=window.parent;
		w.document.getElementById('$field_id').value = id_value;
		w.document.getElementById('$field_name_id').value = reverse_html_entities(libelle_value);
		
	}
</script>";
}elseif ($dyn==2) { // Pour les liens entre autorit�s
    $jscript = $jscript_common_authorities_link;
    
}elseif ($dyn==4) { // aut_pperso
    $jscript = "
	<script type='text/javascript'>
	<!--
	function set_parent(f_caller, id_value, libelle_value, type_value, callback){
		
		let w=window;
		let n_aut = eval('w.parent.document.'+f_caller+'.n_".$param1.".value');	
		let add = true;
		if ( 'function' != typeof(w.parent.add_".$param1.") ){
			add = false;
		};
		let first_empty_place = null;
		let i=0;
						
		for (i=0; i<n_aut; i++) {

			//Si l'autorite est deja selectionnee, on le dit et on s'en va
			if (id_value==w.parent.document.getElementById('".$param1."_'+i).value) {
				alert('".$msg["term_already_in_use"]."');
				return;
			}
			//Si l'emplacement est vide on le note pour eviter de refaire un tour
			if ( null == first_empty_place && ((0==w.parent.document.getElementById('".$param1."_'+i).value)||(''==w.parent.document.getElementById('".$param1."_'+i).value)) ) {
				first_empty_place = i;
			}
		}
		//Un emplacement vide 
		if(null != first_empty_place) {
			window.parent.document.forms[f_caller].elements['".$param1."_'+first_empty_place].value = id_value;
			window.parent.document.forms[f_caller].elements['".$param2."_'+first_empty_place].value = reverse_html_entities(libelle_value);
			if(!add) {
				closeCurrentEnv();
			}
			return;
		}
		if(!add) {
			i=0;
		} else {
			w.parent.add_".$param1."();
		} 
		window.parent.document.forms[f_caller].elements['".$param1."_'+i].value = id_value;
		window.parent.document.forms[f_caller].elements['".$param2."_'+i].value = reverse_html_entities(libelle_value);
		if(!add) {
			closeCurrentEnv();
		}
		return;
	}
	-->
	</script>";
}elseif($dyn==5){
    $jscript ="
<script type='text/javascript'>
	function set_parent(f_caller, id_value, libelle_value){
	
		var w=window;
		
		var n_auth=w.parent.document.forms[f_caller].elements['$max_field'].value;
		var flag = 1;
		//V�rification pas d�j� s�lectionn�e
		for (var i=0; i<n_auth; i++) {
			if (w.parent.document.getElementById('$p1'+i).value==id_value) {
				alert('".$msg["term_already_in_use"]."');
				flag = 0;
				break;
			}
		}
		
		if (flag) {
			for (i=0; i<n_auth; i++) {
				if ((w.parent.document.getElementById('$p1'+i).value==0)||(w.parent.document.getElementById('$p1'+i).value=='')||(w.parent.document.getElementById('$p1'+i).value=='0')){
					break;
				}
			}
			if (i==n_auth) w.parent.add_authperso('$p3');
			w.parent.document.getElementById('$p1'+i).value = id_value;
			w.parent.document.getElementById('$p2'+i).value = reverse_html_entities(libelle_value);
		}
		
	}
</script>";
}else {
    
    $jscript = "
<script type='text/javascript'>
<!--
function set_parent(f_caller, id_value, libelle_value,callback){
	var p1 = '$p1';
	var p2 = '$p2';
	//on enl�ve le dernier _X
	var tmp_p1 = p1.split('_');
	var tmp_p1_length = tmp_p1.length;
	tmp_p1.pop();
	var p1bis = tmp_p1.join('_');
	
	var tmp_p2 = p2.split('_');
	var tmp_p2_length = tmp_p2.length;
	tmp_p2.pop();
	var p2bis = tmp_p2.join('_');
	
	var max_aut = window.parent.document.getElementById(p1bis.replace('id','max_aut'));
	if(max_aut){
		var trouve=false;
		var trouve_id=false;
		for(i_aut=0;i_aut<=max_aut.value;i_aut++){
			if(window.parent.document.getElementById(p1bis+'_'+i_aut).value==0){
				window.parent.document.getElementById(p1bis+'_'+i_aut).value=id_value;
				window.parent.document.getElementById(p2bis+'_'+i_aut).value=reverse_html_entities(libelle_value);
				trouve=true;
				break;
			}else if(window.parent.document.getElementById(p1bis+'_'+i_aut).value==id_value){
				trouve_id=true;
			}
		}
		if(!trouve && !trouve_id){
			window.parent.add_line(p1bis.replace('_id',''));
			window.parent.document.getElementById(p1bis+'_'+i_aut).value=id_value;
			window.parent.document.getElementById(p2bis+'_'+i_aut).value=reverse_html_entities(libelle_value);
		}
		if(callback)
			window.parent[callback](p1bis.replace('_id','')+'_'+i_aut);
	}else{
		set_parent_value(f_caller, '$p1', id_value);
		set_parent_value(f_caller, '$p2', reverse_html_entities(libelle_value));
		set_parent_value(f_caller, '$p3', '0');
		set_parent_value(f_caller, '$p4', '');
		set_parent_value(f_caller, '$p5', '0');
		set_parent_value(f_caller, '$p6', '');
		if(callback)
			window.parent[callback]('$infield');
		closeCurrentEnv();
	}
}
-->
</script>
";
}
// ------------------------------------------
// 	$authperso_form : form saisie
// ------------------------------------------
$authperso_form_all = "";
