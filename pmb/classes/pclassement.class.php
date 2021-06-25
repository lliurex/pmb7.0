<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pclassement.class.php,v 1.6.6.1 2021/03/15 09:11:51 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

include_once($include_path."/templates/pclass.tpl.php");

class pclassement {
	
	protected $id;
	
	protected $name;
	
	protected $typedoc;
	
	protected $locations;
	
	public function __construct($id=0) {
		$this->id = intval($id);
		$this->fetch_data();
	}
	
	protected function fetch_data() {
		$this->name = '';
		$this->typedoc = '';
		$this->locations = array();
		// on r�cup�re les donn�es
		$query = "select id_pclass,name_pclass,typedoc,locations from pclassement where id_pclass='".$this->id."' ";
		$result = pmb_mysql_query($query);
		if ($row = pmb_mysql_fetch_object($result)) {
			$this->name = $row->name_pclass;
			$this->typedoc = $row->typedoc;
			$this->locations = explode(',' , $row->locations);
		}
	}
	
	protected function get_locations_form() {
		global $thesaurus_classement_location;
		global $pclassement_locations_form;
		
		$locations_form = '';
		if($thesaurus_classement_location) {
			$locations_form = $pclassement_locations_form;
			
			$locations ="";
			$query = "SELECT idlocation, location_libelle FROM docs_location ORDER BY location_libelle";
			$result = pmb_mysql_query($query);
			while($obj=pmb_mysql_fetch_object($result)) {
				$as=array_search($obj->idlocation,$this->locations);
				$locations .= "
				<input type='checkbox' name='locations_list[]' value='".$obj->idlocation."' ".($as !== null && $as!==false ? "checked='checked'" : "")." class='checkbox' id='location_".$obj->idlocation."' />
				<label for='numloc".$obj->idlocation."'>&nbsp;".$obj->location_libelle."</label>
				<br />";
			}
			$locations_form = str_replace('!!locations!!', $locations, $locations_form);
		}
		return $locations_form;
	}
	
	public function get_form() {
		global $msg, $charset;
		global $pclassement_content_form;
		
		$content_form = $pclassement_content_form;
		$content_form = str_replace('!!id!!', $this->id, $content_form);
		
		$interface_form = new interface_autorites_form('pclass');
		if(!$this->id){
			$interface_form->set_label($msg['pclassement_creation']);
		}else{
			$interface_form->set_label($msg['pclassement_modification']);
		}
		
		if($this->id) {	//modification
			$identifiant = "<div class='row'><label class='etiquette' >".$msg[38]."</label></div>";
			$identifiant.= "<div class='row'>".$this->id."</div>";
		} else {	//creation
			$identifiant = '';
		}
		$content_form = str_replace('!!libelle!!', htmlentities($this->name, ENT_QUOTES, $charset), $content_form);
		$content_form = str_replace('!!identifiant!!', $identifiant, $content_form);
	
		$doctype = new marc_list('doctype');
		$toprint_typdocfield = " <select name='typedoc_list[]' MULTIPLE SIZE=20 >";
		foreach($doctype->table as $value=>$libelletypdoc) {
			if((strpos($this->typedoc, (string) $value)===false)) $tag = "<option value='$value'>";
			else $tag = "<option value='$value' SELECTED>";
			$toprint_typdocfield .= "$tag$libelletypdoc</option>";
		}
		$toprint_typdocfield .= "</select>";
		$content_form = str_replace('!!type_doc!!', $toprint_typdocfield, $content_form);
		
		$content_form = str_replace('!!locations!!', $this->get_locations_form(), $content_form);
		
		$interface_form->set_object_id($this->id)
		->set_confirm_delete_msg($msg['confirm_suppr'])
		->set_content_form($content_form)
		->set_table_name('pclassement')
		->set_field_focus('libelle');
		return $interface_form->get_display();
	}
	
	public function set_properties_from_form() {
		global $libelle;
		global $typedoc_list;
		global $locations_list;
		
		$this->name = stripslashes($libelle);
		$typedoc = '';
		if(is_array($typedoc_list)) {
			foreach($typedoc_list as $doc) {
				$typedoc .=	stripslashes($doc);
			}
		}
		$this->typedoc = $typedoc;
		
		$this->locations = array();
		if(is_array($locations_list)) {
			$this->locations = $locations_list;
		}
	}
	
	public function save() {
		global $msg;
		
		if (trim($this->name) == '') {
			error_form_message($msg["pclassement_libelle_manquant"]);
			exit ;
		}
		if($this->id) {
			$query = "UPDATE pclassement 
				SET name_pclass='".addslashes($this->name)."', 
					typedoc='".addslashes($this->typedoc)."',
					locations='".addslashes(implode(',', $this->locations))."'
				WHERE id_pclass =".$this->id;
		}
		else {
			$query = "INSERT INTO pclassement 
				SET name_pclass='".addslashes($this->name)."', 
					typedoc='".addslashes($this->typedoc)."',
					locations='".addslashes(implode(',', $this->locations))."'";
		}
		pmb_mysql_query($query);
	}
	
	public static function delete($id) {
		global $msg;
		
		$id = intval($id);
		if($id == 1){
			// Interdire l'effacement de l'id 1
			error_form_message($msg["pclassement_suppr_impossible_protege"]);
			exit;
		}
		$query = "SELECT indexint_id FROM indexint WHERE num_pclass='".$id."'";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)) {
			// Il y a des enregistrements. Interdire l'effacement.
			error_form_message($msg["pclassement_suppr_impossible"]);
			exit;
		} else {
			// effacement
			$dummy = "delete FROM pclassement WHERE id_pclass='".$id."'";
			pmb_mysql_query($dummy);
		}
	}
	
	/**
	 * affichage d'un s�lecteur de la liste pclassement
	 */
	public static function get_selector($name, $selected='') {
		global $thesaurus_classement_defaut;
		global $thesaurus_classement_mode_pmb;
		global $thesaurus_classement_location, $deflt_docs_location;
	
		if(!$selected) {
			$selected = $thesaurus_classement_defaut;
		}
		$selector = '';
		$query = "select id_pclass,name_pclass,typedoc, locations from pclassement ";
		$result = pmb_mysql_query($query);
		if ($thesaurus_classement_mode_pmb != 0 && pmb_mysql_num_rows($result) > 1) {
			$selector .= "<select id='".$name."' name='".$name."'>";
			while ($row = pmb_mysql_fetch_object($result)) {
				if(!$thesaurus_classement_location || ($selected == $row->id_pclass) || ($thesaurus_classement_location && in_array($deflt_docs_location, explode(',', $row->locations)))) {
					$selector .= "<option value='".$row->id_pclass."' ".($selected == $row->id_pclass ? "selected='selected'" : "").">".$row->name_pclass."</option>";
				}
			}
			$selector .= "</select>";
		} else {
			$pclassement = new pclassement($selected);
			$selector .= $pclassement->name;
			$selector .= "<input type='hidden' id='".$name."' name='".$name."' value='".$selected."' />";
		}
		return $selector;
	}
	
	public static function is_visible($id_pclass=1) {
		global $thesaurus_classement_location, $deflt_docs_location;
		
		if($thesaurus_classement_location && $deflt_docs_location) {
			$pclassement = new pclassement($id_pclass);
			if(in_array($id_pclass, $pclassement->locations)) {
				return true;
			}
			return false;	
		}
		return true;
	}
	
	public static function get_default_id($id_pclass=1) {
		global $thesaurus_classement_location, $deflt_docs_location;
	
		if($thesaurus_classement_location && $deflt_docs_location) {
			$query = "select id_pclass, locations from pclassement order by id_pclass";
			$result = pmb_mysql_query($query);
			while ($row = pmb_mysql_fetch_object($result)) {
				if(in_array($deflt_docs_location, explode(',', $row->locations))) {
					return $row->id_pclass;
				}
			}
		}
		return $id_pclass;
	}
}

