<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_sphinx_authperso.class.php,v 1.9.6.1 2020/11/04 11:04:42 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;

require_once "$class_path/searcher/searcher_sphinx_authorities.class.php";

class searcher_sphinx_authperso extends searcher_sphinx_authorities {
	protected $index_name = 'authperso';

	public function __construct($user_query, $id_authperso = 0) {
		global $include_path;
		
		$this->champ_base_path = "$include_path/indexation/authorities/authperso/champs_base.xml";
		parent::__construct($user_query);
		$this->index_name = 'authperso';
		$this->authority_type = AUT_TABLE_AUTHPERSO;
		$this->authperso_id = $id_authperso;
		$this->object_table = "authperso_authorities";
		$this->object_table_key = "id_authperso_authority";
	}
	
	protected function get_filters() {
		$filters = parent::get_filters();
		return $filters;
	}
	
	protected function get_search_indexes() {
		global $lang, $id_authperso, $authperso_id, $sphinx_indexes_prefix;
		
		// AR - C'est quand m�me pas super pratique...
		$current_authperso_id = $this->authperso_id;
		if (empty($current_authperso_id)) {
		    $current_authperso_id = ($id_authperso ? $id_authperso : $authperso_id);
		}
		if (!empty($current_authperso_id)) {
		    return $sphinx_indexes_prefix . $this->index_name . '_' . $current_authperso_id . '_' . $lang . ',' . $sphinx_indexes_prefix . $this->index_name . '_' . $current_authperso_id;
		}
		
		// On cherche dans toutes les autorit�s persos
		$indexes = '';
		$result = pmb_mysql_query('select id_authperso from authperso');
		if (pmb_mysql_num_rows($result)) {
			while ($row = pmb_mysql_fetch_object($result)) {
				if (!empty($indexes)) {
					$indexes .= ',';
				}
				$indexes .= $this->index_name . '_' . $row->id_authperso . '_' . $lang . ',' . $this->index_name . '_' . $row->id_authperso;
			}
		}
		return $indexes;
	}
	
	protected function get_full_raw_query() {
		global $id_authperso, $authperso_id;
		
		$current_authperso_id = $this->authperso_id;
		if (empty($current_authperso_id)) {
    		$current_authperso_id = ($id_authperso ? $id_authperso : $authperso_id);
		}
		
		if (!empty($current_authperso_id)) {
		    return "SELECT id_authority AS id, 100 AS weight FROM authorities JOIN authperso_authorities ON num_object = id_authperso_authority WHERE type_object = $this->authority_type AND authperso_authority_authperso_num = $current_authperso_id";
		}
		return "SELECT id_authority AS id, 100 AS weight FROM authorities WHERE type_object = $this->authority_type";
	}
	
	public function get_authority_tri() {
		return 'authperso_index_infos_global';
	}
}