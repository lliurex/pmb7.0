<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rdf_entities_integrator_authority.class.php,v 1.1.8.1 2020/11/26 13:18:37 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/rdf_entities_integration/rdf_entities_integrator.class.php');

class rdf_entities_integrator_authority extends rdf_entities_integrator {
    
    public function insert_thumbnail_url($authority_type, $values) 
    {
        if (empty($values[0]['value'])) {
            return false;
        }
        
        $query = 'SELECT 1 FROM authorities WHERE type_object = ' . $authority_type .' AND num_object = ' . $this->entity_id;
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            $query = 'UPDATE authorities SET thumbnail_url = "' . $values[0]['value'] .'" WHERE type_object = ' . $authority_type .' AND num_object = ' . $this->entity_id;
        } else {
            $query = 'INSERT INTO authorities (thumbnail_url, type_object, num_object) VALUES ("' . $values[0]['value'] .'", ' . $authority_type .', ' . $this->entity_id . ')';
        }
        pmb_mysql_query($query);
    }
    
}