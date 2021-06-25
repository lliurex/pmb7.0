<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_concepts_datasource_used_in_authperso_custom_fields.class.php,v 1.1 2019/01/14 15:52:36 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_concepts_datasource_used_in_authperso_custom_fields extends frbr_entity_common_datasource_used_in_custom_fields {
	
	public function __construct($id=0){
		$this->entity_type = 'authperso';
		$this->origin_entity = 'concepts';
		$this->prefix = 'authperso';
		parent::__construct($id);
	}
}