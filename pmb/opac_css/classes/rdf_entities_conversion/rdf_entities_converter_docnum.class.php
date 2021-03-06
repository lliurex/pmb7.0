<?php
// +-------------------------------------------------+
// ? 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rdf_entities_converter_docnum.class.php,v 1.1.2.2 2021/04/06 09:00:00 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/rdf_entities_conversion/rdf_entities_converter_docnum.class.php');
require_once($class_path.'/explnum.class.php');

class rdf_entities_converter_docnum extends rdf_entities_converter {
    
    protected $table_name = 'explnum';
    
    protected $table_key = 'explnum_id';
    
    protected $ppersos_prefix = 'explnum';
    
    protected $type_constant = TYPE_EXPLNUM;
    
    protected $aut_table_constant = AUDIT_EXPLNUM;
    
    protected function init_map_fields() {
        $this->map_fields = array_merge(parent::init_map_fields(), array(
            'niveau_biblio' => 'http://www.pmbservices.fr/ontology#bibliographical_lvl' ,
            'explnum_nom' => 'http://www.pmbservices.fr/ontology#label',
            'explnum_docnum_statut' => 'http://www.pmbservices.fr/ontology#has_docnum_status',
            'explnum_repertoire' => 'http://www.pmbservices.fr/ontology#upload_directory',
            'explnum_nomfichier' => 'http://www.pmbservices.fr/ontology#docnum_file'
        ));
        return $this->map_fields;
    }
    
    protected function init_foreign_fields() {
        $this->foreign_fields = array_merge(parent::init_foreign_fields(), array(
            'explnum_notice' => array(
                'type' => 'record',
                'property' => 'http://www.pmbservices.fr/ontology#has_record',
            ),
        ));
        return $this->foreign_fields;
    }
    
    protected function init_linked_entities() {
        $this->linked_entities = array_merge(parent::init_linked_entities(), array(
            'http://www.pmbservices.fr/ontology#has_concept' => array(
                'table' => 'index_concept',
                'reference_field_name' => 'num_object',
                'external_field_name' => 'num_concept',
                'other_fields' => array(
                    'type_object' => TYPE_EXPLNUM
                )
            ),
            'http://www.pmbservices.fr/ontology#location' => array(
                'table' => 'explnum_location',
                'reference_field_name' => 'num_explnum',
                'external_field_name' => 'num_location'
            ),
            'http://www.pmbservices.fr/ontology#owner' => array(
                'table' => 'explnum_lenders',
                'reference_field_name' => 'explnum_lender_num_explnum',
                'external_field_name' => 'explnum_lender_num_lender'
            )
        ));
        return $this->linked_entities;
    }
    
    protected function init_special_fields() {
        $this->special_fields = array_merge(parent::init_special_fields(), array(
            'http://www.pmbservices.fr/ontology#thumbnail' => array(
                "method" => array($this,"get_thumbnail_data"),
                "arguments" => array()
            )
        ));
        return $this->special_fields;
    }
    
    public function get_thumbnail_data()
    {
        if (empty($this->entity_id)) {
            return false;
        }
        
        $thumbnail = '';
        
        $query = 'SELECT explnum_data FROM ' . $this->table_name . ' WHERE explnum_id = ' . $this->entity_id;
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            $thumbnail = pmb_mysql_result($result, 0, 0);
        }
        return new onto_assertion($this->uri, "http://www.pmbservices.fr/ontology#thumbnail", $thumbnail, "http://www.w3.org/2000/01/rdf-schema#Literal", array('type'=>"literal"));
    }
}