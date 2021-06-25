<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: users_group.class.php,v 1.1.2.3 2021/02/19 12:50:58 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class users_group {
	
	/* ---------------------------------------------------------------
	 propri�t�s de la classe
	 --------------------------------------------------------------- */
	
	public $id=0;
	public $name='';
	
	public function __construct($id=0) {
		$this->id = intval($id);
		$this->getData();
	}
	
	/* ---------------------------------------------------------------
	 getData() : r�cup�ration des propri�t�s
	 --------------------------------------------------------------- */
	public function getData() {
		if(!$this->id) return;
		
		$requete = 'SELECT * FROM users_groups WHERE grp_id='.$this->id;
		$result = @pmb_mysql_query($requete);
		if(!pmb_mysql_num_rows($result)) {
			pmb_error::get_instance(static::class)->add_message("not_found", "not_found_object");
			return;
		}
		
		$data = pmb_mysql_fetch_object($result);
		$this->name = $data->grp_name;
	}
	
	public function get_form() {
		global $msg, $charset;
		global $admin_group_content_form;
		
		//Evenement publi�
		$evt_handler = events_handler::get_instance();
		$event = new event_users_group("users_group", "group_form");
		$event->set_group_id($this->id);
		$evt_handler->send($event);
		
		$content_form = $admin_group_content_form;
		$content_form = str_replace('!!id!!', $this->id, $content_form);
		
		$interface_form = new interface_admin_form('groupform');
		if(!$this->id){
			$interface_form->set_label($msg['admin_usr_grp_add']);
		}else{
			$interface_form->set_label($msg['admin_usr_grp_mod']);
		}
		$content_form = str_replace('!!libelle!!', htmlentities($this->name, ENT_QUOTES, $charset), $content_form);
		
		$interface_form->set_object_id($this->id)
		->set_confirm_delete_msg($msg['confirm_suppr_de']." ".$this->name." ?")
		->set_content_form($content_form)
		->set_table_name('users_groups')
		->set_field_focus('form_libelle');
		return $interface_form->get_display();
	}
	
	public function set_properties_from_form() {
		global $form_libelle;
		
		$this->name = stripslashes($form_libelle);
	}
	
	public function get_query_if_exists() {
		return "SELECT count(1) FROM users_groups WHERE grp_name='".addslashes($this->name)."' AND grp_id!='".$this->id."'";
	}
	
	public function save() {
		//if item already exists UPDATE else INSERT
		if($this->id) {
			$q = "UPDATE users_groups SET grp_name='".addslashes($this->name)."' WHERE grp_id='".$this->id."' ";
			pmb_mysql_query($q);
		} else {
			$q = "INSERT INTO users_groups (grp_id, grp_name) VALUES (0, '".addslashes($this->name)."') ";
			pmb_mysql_query($q);
			$this->id = pmb_mysql_insert_id();
		}
	}
	
	public static function delete($id) {
		$id = intval($id);
		if($id) {
			$total = 0;
			$total = pmb_mysql_result(pmb_mysql_query("select count(1) from users where grp_num='".$id."' "),0 ,0 );
			if ($total==0) {
				$q = "DELETE FROM users_groups WHERE grp_id='$id' ";
				pmb_mysql_query($q);
				
				//Evenement publi�
				$evt_handler = events_handler::get_instance();
				$event = new event_users_group("users_group", "delete");
				$event->set_group_id($id);
				$evt_handler->send($event);
				
				return true;
			} else {
				pmb_error::get_instance(static::class)->add_message('admin_usr_grp_ges', 'admin_usr_grp_del_imp');
				return false;
			}
		}
		return true;
	}
} /* fin de d�finition de la classe */