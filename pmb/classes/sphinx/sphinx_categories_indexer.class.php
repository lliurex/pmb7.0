<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sphinx_categories_indexer.class.php,v 1.2.6.2 2020/04/17 12:42:38 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once $class_path.'/sphinx/sphinx_indexer.class.php';

class sphinx_categories_indexer extends sphinx_authorities_indexer {
	
	public function __construct() {
		global $include_path;
		$this->type = AUT_TABLE_CATEG;
		$this->default_index = "categories";
		parent::__construct();
		$this->filters = ['multi' => ['status', 'num_thesaurus']];
		$this->setChampBaseFilepath($include_path."/indexation/authorities/categories/champs_base.xml");
	}
	
	protected function addSpecificsFilters($id, $filters =array()){
		$filters = parent::addSpecificsFilters($id, $filters);

		//Récupération du statut
		$query = "select num_thesaurus, num_statut from noeuds join authorities on id_noeud = num_object and type_object = ".$this->type." where id_authority = ".$id;
		$result = pmb_mysql_query($query);
		$row = pmb_mysql_fetch_object($result);
		$filters['multi']['num_thesaurus'] = $row->num_thesaurus;
		$filters['multi']['status'] = $row->num_statut;
		return $filters;
	}
}