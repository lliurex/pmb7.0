<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_external_services_esusergroups_ui.class.php,v 1.1.2.2 2021/01/15 13:22:26 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/external_services_esusers.class.php");
require_once($class_path."/list/configuration/external_services/list_configuration_external_services_ui.class.php");

class list_configuration_external_services_esusergroups_ui extends list_configuration_external_services_ui {
	
	protected function _get_query_base() {
		return 'SELECT esgroup_id FROM es_esgroups';
	}
	
	protected function fetch_data() {
		global $msg;
		
		parent::fetch_data();
		//Ajoutons l'utilisateur anonyme
		$ano_sql = "SELECT CONCAT(users.username, ' (', users.nom, ' ', users.prenom,')') AS pmbusercaption FROM `es_esgroups` LEFT JOIN users ON (users.userid = es_esgroups.esgroup_pmbusernum) WHERE `esgroup_id` = -1";
		$ano_res = pmb_mysql_query($ano_sql);
		if (!pmb_mysql_num_rows($ano_res)) {
			$ano_pmbusercaption = pmb_mysql_result(pmb_mysql_query("SELECT CONCAT(users.username, ' (', users.nom, ' ', users.prenom,')') FROM users WHERE userid = 1"), 0, 0);
		} else {
			$ano_pmbusercaption = pmb_mysql_result($ano_res, 0, 0);
		}
		$this->add_object((object) array(
				'esgroup_id' => 0,
				'esgroup_name' => "&lt;".$msg["admin_connecteurs_outauth_anonymgroupname"]."&gt;",
				'esgroup_fullname' => $msg["admin_connecteurs_outauth_anonymgroupfullname"],
				'es_group_pmbuserid' => $ano_pmbusercaption,
				'esgroup_esusers' => array(),
				'esgroup_emprgroups' => array())
		);
	}
	
	protected function get_object_instance($row) {
		if($row->esgroup_id) {
			return new es_esgroup($row->esgroup_id);
		} else {
			return parent::get_object_instance($row);
		}
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('esgroup_name');
	}
	
	protected function _get_query_filters() {
		$filter_query = '';
		
		$this->set_filters_from_form();
		
		$filters = array();
		$filters[] = 'esgroup_id <> -1';
		if(count($filters)) {
			$filter_query .= ' where '.implode(' and ', $filters);
		}
		return $filter_query;
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'esgroup_name' => 'es_group_name',
				'esgroup_fullname' => 'es_group_fullname',
				'es_group_pmbuserid' => 'es_group_pmbuserid',
				'es_group_esusers_count' => 'es_group_esusers_count',
				'es_group_emprgroup_count' => 'es_group_emprgroup_count'
		);
	}
	
	protected function get_cell_content($object, $property) {
		$content = '';
		switch($property) {
			case 'es_group_pmbuserid':
				if($object->esgroup_id) {
					$content .= $object->esgroup_pmbuser_username.' ('.$object->esgroup_pmbuser_lastname.' '.$object->esgroup_pmbuser_firstname.')';
				} else {
					$content .= $object->{$property};
				}
				break;
			case 'es_group_esusers_count':
				if($object->esgroup_id) {
					$content .= count($object->esgroup_esusers);
				}
				break;
			case 'es_group_emprgroup_count':
				if($object->esgroup_id) {
					$content .= count($object->esgroup_emprgroups);
				}
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_edition_link($object) {
		if($object->esgroup_id) {
			return static::get_controller_url_base().'&action=edit&id='.$object->esgroup_id;
		} else {
			return static::get_controller_url_base().'&action=editanonymous';
		}
	}
	
	public function get_error_message_empty_list() {
		global $msg, $charset;
		return htmlentities($msg["es_users_noesgroups"], ENT_QUOTES, $charset);
	}
	
	protected function get_label_button_add() {
		global $msg;
		
		return $msg['es_groups_add'];
	}
}