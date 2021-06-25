<?php
// +-------------------------------------------------+
//  2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_segment_external_search_result.class.php,v 1.1.2.6 2020/09/14 13:28:54 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once $class_path."/search_universes/search_segment_search_result.class.php";

class search_segment_external_search_result extends search_segment_search_result {
	private $undisplayed_search_index = [];
	private $json_search;
    
	protected function prepare_segment_search(){
	    global $user_query;
	    global $universe_query;
	    global $search;
	    global $segment_json_search;
	    global $deleted_search_nb;
	    global $es;
	    
	    if(!is_object($es)){
    	    $es = new search('search_fields_unimarc_gestion');
	    }

	    if (!is_array($search)) {
	    	$search = array();
	    }

	    search_universes_history::update_json_search_with_history();
	    if (!empty($segment_json_search)) {
	        //$es->json_decode_search(stripslashes($segment_json_search));
	        $this->json_search = $segment_json_search;
	        $segment_json_search = $this->format_search($segment_json_search);
	        $es->json_decode_search($segment_json_search);
	    }
	    
	    if (!in_array('s_2', $search)) {
	        $es->json_decode_search($this->segment->get_set()->get_data_set());
	        $this->undisplayed_search_index = array_keys($search);
	    	//ajout de l'universe_query dans le cas d'un changement de segment (sans user_query)
	    	search_universes_history::init_universe_query_from_history();
	    	search_universes_history::$undisplayed_search_index = $this->undisplayed_search_index;	    	
	    	if (empty($user_query) && !empty($universe_query)) {
	    	    $universe_query_mc = combine_search::simple_search_to_mc(stripslashes($universe_query), true, $this->get_type_from_segment(), $es);
	    	    $es->json_decode_search($universe_query_mc);
	    	}
	    }
	    
// 	    if (!in_array('s_10', $search)) {
// 	    	$new_index = count($search);
// 		    $search[$new_index] = 's_10';
	    
// 	    	global ${'inter_'.$new_index.'_s_10'};
// 		    global ${'op_'.$new_index.'_s_10'};
// 		    global ${'field_'.$new_index.'_s_10'};
	    
// 	    	${'inter_'.$new_index.'_s_10'} = 'and';
// 		    ${'op_'.$new_index.'_s_10'} = 'EQ';
// 	    	${'field_'.$new_index.'_s_10'} = array($this->segment->get_id());
	    	
// 	    	//ajout de l'universe_query dans le cas d'un changement de segment (sans user_query)
// 	    	search_universes_history::init_universe_query_from_history();
// 	    	if (empty($user_query) && !empty($universe_query)) {
// 	    	    $universe_query_mc = combine_search::simple_search_to_mc(stripslashes($universe_query), true, $this->get_type_from_segment(), $es);
// 	    	    $es->json_decode_search($universe_query_mc);
// 	    	}
// 	    }
	    
	    if (!empty($user_query)) {
	    	$user_query_mc = combine_search::simple_search_to_mc(stripslashes($user_query), true, $this->get_type_from_segment());
	    	
	    	//on stocke la recherche pour l'affichage avant de la formater
	    	$this->json_search = $user_query_mc;
	    	$user_query_mc = $this->format_search($user_query_mc);
	    	$es->json_decode_search($user_query_mc);
	    	unset($user_query);
	    }
	    
	    if (isset($deleted_search_nb)) {
	    	$es->delete_search($deleted_search_nb);
	    }
	    
	    $this->init_global_universe_id();
	}
	
	public function get_display_facets() {
	    global $es, $base_path;
	    
	    $facettes_tpl = '';
	    $tab_result = $this->init_session_facets();
	    $segment_facets = search_segment_facets::get_instance('', $this->segment->get_id());
	    // 		$segment_facets->set_num_segment($this->segment->get_id());
	    //$segment_facets->set_segment_search($es->json_encode_search());
	    $segment_facets->set_segment_search($this->json_search);
	    $es->json_decode_search($this->json_search);
	    $content = $es->make_segment_search_form($base_path.'/index.php?lvl=search_segment&id='.$this->segment->get_id().'&action=segment_results', 'form_values', "", true, $this->undisplayed_search_index);
	    $facettes_tpl .= $segment_facets->call_facets($content);
	    
	    return $facettes_tpl;
	}
	
	private function format_search($json_search) {
	    $format_search = "";
	    $segment_search = encoding_normalize::json_decode($json_search, true);
	    $tab_search = $segment_search["SEARCH"];
	    if (in_array("f_42", $segment_search["SEARCH"])) {
	        foreach ($tab_search as $i => $field) {
	            if ($i == 0) {
	                $segment_search[$i]["INTER"] = "and";
	            }
	            if ($field == "f_42") {
	                $temp_search = $segment_search[$i];
	                unset($segment_search[$i]);
	                array_unshift($segment_search, $temp_search);
	                unset($segment_search["SEARCH"][$i]);
	                array_unshift($segment_search["SEARCH"], "f_42");
	            }
	        }
	    }
	    $format_search = json_encode($segment_search);
	    return $format_search;
	}
	
	public function get_nb_results($ajax_mode = false) {
	    global $search_type;
	    
	    $search_type="search_universes";
	    
	    $this->prepare_segment_search();
	    $this->checked_facette_search();
	    //search_segment_facets::make_facette_search_env();
	    if ($ajax_mode) {
	        // Afin de paralléliser les recherches AJAX, on ferme la session PHP
	        session_write_close();
	    }
	    $this->get_searcher();
	    $nb_results = $this->searcher->get_nb_results();
	    
	    search_universes_history::$segment_json_search = $this->json_search;
	    rec_history();
	    
	    return $nb_results;
	}
}