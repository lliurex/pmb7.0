<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_auth_perso.class.php,v 1.1.2.6 2020/12/22 15:39:04 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/cms/cms_editorial_parametres_perso.class.php");
require_once($class_path."/authperso.class.php");

class onto_auth_perso extends onto_parametres_perso{
	
	protected $authpersos;
	
	public function __construct($prefix = 'authperso') {
		$this->prefix = $prefix;
		$this->get_authpersos();
	}
	
	public function build_onto () {
		
		$onto = "
		<!-- Champs perso ".$this->prefix." PMB -->";
		
		foreach ($this->authpersos as $id => $detail) {
			if (is_array($detail['fields'])) {
				foreach ($detail['fields'] as $key => $t_field) {
					$this->init_attributes();
					$this->set_uri_description($t_field["name"]);
					$this->set_datatype_from_field($t_field["id"],$t_field);
					$this->set_restrictions($t_field);
					$onto.= "
						<rdf:Description rdf:about='http://www.pmbservices.fr/ontology#authperso_" . $t_field["name"]. "'>
							<rdfs:label>" . htmlspecialchars(encoding_normalize::utf8_normalize($t_field["label"]), ENT_QUOTES, 'utf-8') . "</rdfs:label>
							<rdfs:isDefinedBy rdf:resource='http://www.pmbservices.fr/ontology#'/>
					       	<rdf:type rdf:resource='http://www.w3.org/1999/02/22-rdf-syntax-ns#Property'/>
							<rdfs:domain rdf:resource='http://www.pmbservices.fr/ontology#authperso_" . $id . "'/>
							<rdfs:range rdf:resource='" . $this->uri_range . "'/>
							<pmb:datatype rdf:resource='" . $this->uri_datatype . "'/>";
								$onto.= $this->optional_properties;
								
								$onto.= "
							<pmb:name>" . $this->uri_description . "</pmb:name>
					    </rdf:Description>
					";
					// On n'oublie pas les noeuds blancs
					$onto.= $this->blank_nodes;
					$this->authpersos[$id]['fields'][$key]['rdf_nodeId'] = $this->rdf_nodeId;
				}
			}
		}
		$onto .= $this->build_onto_class();
		$onto .= $this->build_onto_responsabilities();
		return $onto;
	}
	
	protected function build_onto_class() {
		$onto = '';
		if (!empty($this->authpersos)) {
			foreach ($this->authpersos as $id => $detail) {
				$onto.= '
				<!-- Classe autorite perso '.$detail['name'].' PMB -->
				<rdf:Description rdf:about="http://www.pmbservices.fr/ontology#authperso_'.$id.'">
			        <rdfs:label xml:lang="fr">'.htmlspecialchars(encoding_normalize::utf8_normalize($detail['name']), ENT_QUOTES, 'utf-8').'</rdfs:label>
			        <rdfs:comment>'.htmlspecialchars(encoding_normalize::utf8_normalize($detail['comment']), ENT_QUOTES, 'utf-8').'</rdfs:comment>
                    <rdfs:subClassOf rdf:resource="http://www.pmbservices.fr/ontology#entity"/>
                    <rdfs:subClassOf rdf:resource="http://www.pmbservices.fr/ontology#authority"/>';
				
				foreach ($detail['fields'] as $field) {
				    if (!empty($field['rdf_nodeId'])) {
				        // on ajoute le rdf:nodeID pour prendre en compte les restrictions
    				    $onto.= '
                            <rdfs:subClassOf rdf:nodeID="'.$field['rdf_nodeId'].'"/>';
				    }
                }
                
                $onto.= '
                    <rdfs:isDefinedBy rdf:resource="http://www.pmbservices.fr/ontology#" />
			        <rdf:type rdf:resource="http://www.w3.org/2002/07/owl#Class"/>
			        <pmb:displayLabel rdf:resource="http://www.pmbservices.fr/ontology#isbd"/>
        			<pmb:flag>pmb_entity</pmb:flag>
        			<pmb:flag>auth_perso</pmb:flag>
			        '.($detail['event'] ? '<pmb:flag>is_event</pmb:flag>' : '').'
			        <pmb:name>authperso_'.$id.'</pmb:name>
		    	</rdf:Description>
			    <!-- propriete reliant l autorite perso '.$detail['name'].' a une notice -->
			    <rdf:Description rdf:about="http://www.pmbservices.fr/ontology#has_authperso_'.$id.'">
					<rdfs:label xml:lang="fr">'.htmlspecialchars(encoding_normalize::utf8_normalize($detail['name']), ENT_QUOTES, 'utf-8') .'</rdfs:label>
					<rdfs:comment>'. htmlspecialchars(encoding_normalize::utf8_normalize($detail['comment']), ENT_QUOTES, 'utf-8') .'</rdfs:comment>
					<rdfs:isDefinedBy rdf:resource="http://www.pmbservices.fr/ontology#"/>
			       	<rdf:type rdf:resource="http://www.w3.org/1999/02/22-rdf-syntax-ns#Property"/>
					<rdfs:domain rdf:resource="http://www.pmbservices.fr/ontology#record"/>
        			<rdfs:subClassOf rdf:resource="http://www.pmbservices.fr/ontology#entity_link"/>
					<rdfs:range rdf:resource="http://www.pmbservices.fr/ontology#authperso_'.$id.'"/>
					<pmb:datatype rdf:resource="http://www.pmbservices.fr/ontology#resource_selector"/>
        			<pmb:flag>authperso_'.$id.'</pmb:flag>
					<pmb:name>has_authperso_'.$id.'</pmb:name>
			    </rdf:Description>
			    ';
			}
		}
		return $onto;
	}
	
	protected function get_authpersos() {
		$authperso = new authpersos();		
		$this->authpersos = $authperso->get_data();
	}
	
	protected function build_onto_responsabilities() {
	    global $msg;
	    $onto = "
			<rdf:Description rdf:about='http://www.pmbservices.fr/ontology#has_responsability_authperso'>
				<rdfs:label>".htmlspecialchars(encoding_normalize::utf8_normalize($msg['aut_responsability_form_responsability_authperso']), ENT_QUOTES, 'utf-8')."</rdfs:label>
				<rdfs:isDefinedBy rdf:resource='http://www.pmbservices.fr/ontology#'/>
		       	<rdf:type rdf:resource='http://www.w3.org/1999/02/22-rdf-syntax-ns#Property'/>";
        	    foreach ($this->authpersos as $id => $detail) {
        	        if ($detail['responsability_authperso']) {
        	            $onto .= "<rdfs:domain rdf:resource='http://www.pmbservices.fr/ontology#authperso_" . $id . "'/>";
        	        }
        	    }
	    $onto .= "<rdfs:range rdf:resource='http://www.pmbservices.fr/ontology#responsability'/>
                <rdfs:range rdf:resource='http://www.pmbservices.fr/ontology#author'/>
	            <rdfs:domain rdf:resource='http://www.pmbservices.fr/ontology#record'/>
                <rdfs:domain rdf:resource='http://www.pmbservices.fr/ontology#work'/>
				<rdfs:subClassOf rdf:resource='http://www.pmbservices.fr/ontology#entity_link'/>
                <pmb:datatype rdf:resource='http://www.pmbservices.fr/ontology#responsability_selector'/>
        	    <pmb:name>has_responsability_authperso</pmb:name>
        	    <pmb:flag>author</pmb:flag>
            </rdf:Description>";
	    return $onto;
	}
}