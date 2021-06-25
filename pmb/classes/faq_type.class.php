<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: faq_type.class.php,v 1.1.2.3 2021/01/14 09:24:35 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($class_path."/indexation.class.php");
require_once($include_path."/templates/liste_simple.tpl.php");

class faq_type {

	/* ---------------------------------------------------------------
		propriétés de la classe
   --------------------------------------------------------------- */

	public $id=0;
	public $libelle='';

	public function __construct($id=0) {
		$this->id = intval($id);
		$this->getData();
	}

	/* ---------------------------------------------------------------
		getData() : récupération des propriétés
   --------------------------------------------------------------- */
	public function getData() {
		if(!$this->id) return;
	
		$requete = 'SELECT * FROM faq_types WHERE id_type='.$this->id;
		$result = pmb_mysql_query($requete);
		if(!pmb_mysql_num_rows($result)) {
			pmb_error::get_instance(static::class)->add_message("not_found", "not_found_object");
			return;
		}
		$data = pmb_mysql_fetch_object($result);
		$this->libelle = $data->libelle_type;
	}

	public function get_form() {
		global $liste_simple_content_form, $msg, $charset;
		
		$content_form = $liste_simple_content_form;
		$content_form = str_replace('!!id!!', $this->id, $content_form);
		
		$interface_form = new interface_admin_form('simple_list_form');
		if(!$this->id){
			$interface_form->set_label($msg['faq_ajout_type']);
		}else{
			$interface_form->set_label($msg['faq_modif_type']);
		}
		$content_form = str_replace('!!libelle!!', htmlentities($this->libelle, ENT_QUOTES, $charset), $content_form);
		
		$interface_form->set_object_id($this->id)
		->set_confirm_delete_msg($msg['faq_del_type'])
		->set_content_form($content_form)
		->set_table_name('faq_types')
		->set_field_focus('libelle');
		return $interface_form->get_display();
	}
	
	public function set_properties_from_form() {
		global $libelle;
		
		$this->libelle = stripslashes($libelle);
	}
	
	public function save() {
		if($this->id) {
			$requete = "UPDATE faq_types set libelle_type='".addslashes($this->libelle)."' where id_type='".$this->id."'";
			pmb_mysql_query($requete);
		} else {
			$requete = "INSERT INTO faq_types set libelle_type='".addslashes($this->libelle)."'";
			pmb_mysql_query($requete);
			$this->id = pmb_mysql_insert_id();
		}
		$this->update_index();
	}
	
	public function update_index(){
		global $include_path;
		$query = "select id_faq_question from faq_questions where faq_question_num_type = ".$this->id;
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			$index = new indexation($include_path."/indexation/faq/question.xml", "faq_questions");
			while($row = pmb_mysql_fetch_object($result)){
				$index->maj($row->id_faq_question,"type");
			}
		}
	}

	public static function check_data_from_form() {
		global $libelle;
		
		if(empty($libelle)) {
			return false;
		}
		return true;
	}
	
	public static function delete($id) {
		$id = intval($id);
		if ($id) {
			$total = pmb_mysql_num_rows(pmb_mysql_query("select * from faq_questions where faq_question_num_type = '".$id."'"));
			if ($total==0) {
				$requete = "DELETE FROM faq_types where id_type='".$id."'";
				pmb_mysql_query($requete);
				return true;
			} else {
				pmb_error::get_instance(static::class)->add_message("321", 'faq_used_type');
				return false;
			}
		}
		return true;
	}
} /* fin de définition de la classe */