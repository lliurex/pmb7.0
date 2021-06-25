<?php
// +-------------------------------------------------+
// � 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: entities.class.php,v 1.2.2.5 2021/01/14 09:18:28 jlaurent Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class entities{
    public static $entities;
    
    public static function get_entities() {
    	return array(
    			TYPE_NOTICE,
    			TYPE_AUTHOR,
    			TYPE_CATEGORY,
    			TYPE_PUBLISHER,
    			TYPE_COLLECTION,
    			TYPE_SUBCOLLECTION,
    			TYPE_SERIE,
    			TYPE_TITRE_UNIFORME,
    			TYPE_INDEXINT,
    			TYPE_EXPL,
    			TYPE_EXPLNUM,
    			TYPE_AUTHPERSO,
    			TYPE_CMS_SECTION,
    			TYPE_CMS_ARTICLE,
    			TYPE_CONCEPT
    	);
    }
    
	public static function get_entities_labels() {
	    global $msg;
	    
	    $entities = array(    
	    		TYPE_NOTICE => $msg['288'],
	    		TYPE_AUTHOR => $msg['isbd_author'],
	    		TYPE_CATEGORY => $msg['isbd_categories'],
	    		TYPE_PUBLISHER => $msg['isbd_editeur'],
	    		TYPE_COLLECTION => $msg['isbd_collection'],
	    		TYPE_SUBCOLLECTION => $msg['isbd_subcollection'],
	    		TYPE_SERIE => $msg['isbd_serie'],
	    		TYPE_TITRE_UNIFORME => $msg['isbd_titre_uniforme'],
	    		TYPE_INDEXINT => $msg['isbd_indexint'],
	    		TYPE_EXPL => $msg['376'],
	    		TYPE_EXPLNUM => $msg['search_explnum'],
	    		TYPE_AUTHPERSO => $msg['search_by_authperso_title'],
	    		TYPE_CMS_SECTION => $msg['cms_menu_editorial_section'],
	    		TYPE_CMS_ARTICLE => $msg['cms_menu_editorial_article'],
	    		TYPE_CONCEPT => $msg['search_concept_title']
	    );
	    return $entities;
	}
	
	public static function get_entities_options($selected) {
	    global $charset;
	    $entities = static::get_entities_list();
	    $html = '';
	    foreach ($entities as $value => $label) {
	        $html .= '<option value="'.$value.'" '.($value == $selected ? 'selected="selected"' : '').'>'.htmlentities($label, ENT_QUOTES, $charset).'</option>';
	    }
	    return $html;
	}
	
	public static function get_string_from_const_type($type){
	    if(!is_numeric($type)){
	        return $type;
	    }
		switch ($type) {
			case TYPE_NOTICE :
				return 'notices';
			case TYPE_AUTHOR :
				return 'authors';
			case TYPE_CATEGORY :
				return 'categories';
			case TYPE_PUBLISHER :
				return 'publishers';
			case TYPE_COLLECTION :
				return 'collections';
			case TYPE_SUBCOLLECTION :
				return 'subcollections';
			case TYPE_SERIE :
				return 'series';
			case TYPE_TITRE_UNIFORME :
				return 'titres_uniformes';
			case TYPE_INDEXINT :
				return 'indexint';
			case TYPE_CONCEPT_PREFLABEL:
			case TYPE_CONCEPT:
				return 'concepts';
			case TYPE_AUTHPERSO :
				return 'authperso';
			case TYPE_EXTERNAL :
				return 'external';
		}
	}
	
	public static function get_query_from_entity_linked($id, $get_type, $from_type) {
		$query = "";
		switch($get_type){
			case 'publisher':
				$query .= "SELECT ed_id FROM publishers";
				switch($from_type){
					case 'collection':
						$query .= " JOIN collections ON ed_id = collection_parent where collection_id = ".$id;
						break;
					case 'sub_collection':
						$query .= " JOIN collections ON ed_id = collection_parent JOIN sub_collections ON sub_coll_parent = collection_id where sub_coll_id = ".$id;
						break;
				}
				break;
			case 'collection':
				$query .= "SELECT collection_id FROM collections";
				switch($from_type){
					case 'publisher':
						 $query .= " JOIN publishers ON ed_id = collection_parent where ed_id = ".$id;
						break;
					case 'sub_collection':
						$query .= " JOIN sub_collections ON sub_coll_parent = collection_id  where sub_coll_id = ".$id;
						break;
							
				}
				break;
			case 'sub_collection':
				$query = "SELECT sub_coll_id FROM sub_collections";
				switch($from_type){
					case 'publisher':
						$query .= " JOIN collections ON sub_coll_parent = collection_id WHERE collection_parent = ".$id;
						break;
					case 'collection':
						 $query .= " WHERE sub_coll_parent = ".$id;
						break;
							
				}
				break;
		}
		return $query;
	}
	
	public static function get_aut_table_from_type($type) {
	    switch ($type) {
	        case TYPE_AUTHOR :
	            return AUT_TABLE_AUTHORS;
	        case TYPE_CATEGORY :
	            return AUT_TABLE_CATEG;
	        case TYPE_PUBLISHER :
	            return AUT_TABLE_PUBLISHERS;
	        case TYPE_COLLECTION :
	            return AUT_TABLE_COLLECTIONS;
	        case TYPE_SUBCOLLECTION :
	            return AUT_TABLE_SUB_COLLECTIONS;
	        case TYPE_SERIE :
	            return AUT_TABLE_SERIES;
	        case TYPE_TITRE_UNIFORME :
	            return AUT_TABLE_TITRES_UNIFORMES;
	        case TYPE_INDEXINT :
	            return AUT_TABLE_INDEXINT;
	        case TYPE_CONCEPT:
	            return AUT_TABLE_CONCEPT;
	        case TYPE_AUTHPERSO :
	        default: 
	            return AUT_TABLE_AUTHPERSO;
	    }
	}
	    
    public static function get_table_prefix_from_const($authority_type) {
        switch ($authority_type) {
            case AUT_TABLE_AUTHORS :
                return 'author';
            case AUT_TABLE_CATEG :
                return 'categorie';
            case AUT_TABLE_PUBLISHERS :
                return 'publisher';
            case AUT_TABLE_COLLECTIONS :
                return 'collection';
            case AUT_TABLE_SUB_COLLECTIONS :
                return 'sub_coll';
            case AUT_TABLE_SERIES :
                return 'serie';
            case AUT_TABLE_TITRES_UNIFORMES :
                return 'tu';
            case AUT_TABLE_INDEXINT :
                return 'indexint';
            case AUT_TABLE_CONCEPT :
                return 'concept';
            case AUT_TABLE_AUTHPERSO :
                return 'authperso';
        }
    }
    
    public static function get_authority_table_from_const($authority_type) {
        switch ($authority_type) {
            case AUT_TABLE_AUTHORS :
                return 'authors';
            case AUT_TABLE_CATEG :
                return 'categories';
            case AUT_TABLE_PUBLISHERS :
                return 'publishers';
            case AUT_TABLE_COLLECTIONS :
                return 'collections';
            case AUT_TABLE_SUB_COLLECTIONS :
                return 'sub_collections';
            case AUT_TABLE_SERIES :
                return 'series';
            case AUT_TABLE_TITRES_UNIFORMES :
                return 'titres_uniformes';
            case AUT_TABLE_INDEXINT :
                return 'indexint';
            case AUT_TABLE_AUTHPERSO :
                return 'authperso_authorities';
            case AUT_TABLE_CONCEPT :
            default :
                return '';
        }
    }
    
    public static function get_authority_id_from_const($authority_type) {
        switch ($authority_type) {
            case AUT_TABLE_AUTHORS :
                return 'author_id';
            case AUT_TABLE_CATEG :
                return 'num_noeud';
            case AUT_TABLE_PUBLISHERS :
                return 'ed_id';
            case AUT_TABLE_COLLECTIONS :
                return 'collection_id';
            case AUT_TABLE_SUB_COLLECTIONS :
                return 'sub_coll_id';
            case AUT_TABLE_SERIES :
                return 'serie_id';
            case AUT_TABLE_TITRES_UNIFORMES :
                return 'tu_id';
            case AUT_TABLE_INDEXINT :
                return 'indexint_id';
            case AUT_TABLE_AUTHPERSO :
                return 'id_authperso_authority';
            case AUT_TABLE_CONCEPT :
            default :
                return '';
        }
    }
    
    public static function get_searcher_mode_from_type(string $type){
        switch($type){
            case 'authors' :
            case 'author' :
            case 'auteur' :
            case 'auteurs' :
                return 'authors';
            case 'categories' :
            case 'categorie' :
            case 'category' :
                return 'categories';
            case 'editeur' :
            case 'publisher' :
            case 'publishers' :
                return 'publishers';
            case 'collection' :
            case 'collections' :
                return 'collections';
            case 'subcollections' :
            case 'subcollection' :
                return 'subcollections';
            case 'serie' :
            case 'series' :
                return 'series';
            case 'indexint' :
                return 'indexint';
            case 'titres_uniformes':
            case 'titre_uniforme':
            case 'work':
            case 'works':
                return 'titres_uniformes';
            case 'skos_concepts' :
                return 'skos_concepts';
            case 'concepts':
            case 'concept':
                return 'concepts';
            case 'authpersos' :
            case 'authperso' :
                return 'authpersos';
            case 'record' :
            case 'records' :
            case 'notices' :
            case 'notice' :
                return 'records';
            default :
                if(strpos($type,'authperso') !== false){
                    return $type;
                }
                return '';
        }
    }
    
    public static function get_label_from_entity(int $id, string $type) {
        switch ($type) {
            case "record" :
                return notice::get_notice_title($id);
            case "authority" :
            default:
                $authority = authorities_collection::get_authority($type, $id);
                return $authority->get_isbd();
        }
    }
}