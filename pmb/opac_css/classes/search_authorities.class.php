<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_authorities.class.php,v 1.7.6.3 2021/02/01 14:09:08 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

//Classe de gestion des recherches avancees des autorit�s

require_once($class_path."/search.class.php");
require_once($class_path."/searcher/searcher_authorities_authors.class.php");
require_once($class_path."/searcher/searcher_authorities_authpersos.class.php");
require_once($class_path."/searcher/searcher_authorities_categories.class.php");
require_once($class_path."/searcher/searcher_authorities_collections.class.php");
require_once($class_path."/searcher/searcher_authorities_concepts.class.php");
require_once($class_path."/searcher/searcher_authorities_indexint.class.php");
require_once($class_path."/searcher/searcher_authorities_publishers.class.php");
require_once($class_path."/searcher/searcher_authorities_series.class.php");
require_once($class_path."/searcher/searcher_authorities_subcollections.class.php");
require_once($class_path."/searcher/searcher_authorities_tab.class.php");
require_once($class_path."/searcher/searcher_authorities_titres_uniformes.class.php");
require_once($class_path."/searcher/searcher_autorities_skos_concepts.class.php");

class search_authorities extends search {
    
    protected $hidden_form_name;    
	
	public function filter_searchtable_from_accessrights($table) {
		global $dbh;
		
	}
	
	protected function sort_results($table) {
		global $nb_per_page_search;
		global $page;
		 
		$start_page=$nb_per_page_search*$page;
		 
		return $table;
	}
	
	protected function get_display_nb_results($nb_results) {
		global $msg;
		 
		return " => ".$nb_results." ".$msg["search_extended_authorities_found"]."<br />\n";
	}
	
	protected function show_objects_results($table, $has_sort) {
		global $dbh;
		global $search;
		global $nb_per_page;
		global $page;
		$start_page=$nb_per_page*$page;
		
		$query = "select ".$table.".*,authorities.num_object,authorities.type_object from ".$table.",authorities where authorities.id_authority=".$table.".id_authority";
		if(count($search) > 1 && !$has_sort) {
			//Tri � appliquer par d�faut
		}		
		if (!empty($nb_per_page)) {
		    $query .= " limit ".$start_page.",".$nb_per_page;
		}
	
		$result=pmb_mysql_query($query, $dbh);
		$objects_ids = array();
		while ($row=pmb_mysql_fetch_object($result)) {
			$objects_ids[] = $row->id_authority;
		}
		if(count($objects_ids)) {
		    $elements_class_name = $this->get_elements_list_ui_class_name();
		    $elements_instance_list_ui = new $elements_class_name($objects_ids, count($objects_ids), 1);
		    
		    $elements = $elements_instance_list_ui->get_elements_list();
			print $elements;
		}
	}
	
	protected function get_display_actions() {
		return "";
	}
	
	protected function get_display_icons($nb_results, $recherche_externe = false) {
		return "";
	}

	public static function get_join_and_clause_from_equation($type = AUT_TABLE_AUTHORS, $equation) {
		
		$authority_join = '';
		$authority_clause = '';
		$authority_ids = array();
		if($equation) {
			$my_search = new search_authorities('search_fields_authorities_gestion');
			$my_search->unserialize_search(stripslashes($equation));
			$res = $my_search->make_search();
			$req="select * from ".$res ;
			$resultat=pmb_mysql_query($req);
			while($r=pmb_mysql_fetch_object($resultat)) {
				$authority_ids[]=$r->id_authority;
			}
			switch($type) {
				case AUT_TABLE_AUTHORS :
					$aut_id_name = 'author_id'; 
					break;
				case AUT_TABLE_CATEG :
					$aut_id_name = 'id_noeud'; 
					break;
				case AUT_TABLE_PUBLISHERS :
					$aut_id_name = 'ed_id'; 
					break;
				case AUT_TABLE_COLLECTIONS :
					$aut_id_name = 'collection_id'; 
					break;
				case AUT_TABLE_SUB_COLLECTIONS :
					$aut_id_name = 'sub_coll_id'; 
					break;
				case AUT_TABLE_SERIES :
					$aut_id_name = 'serie_id'; 
					break;
				case AUT_TABLE_TITRES_UNIFORMES :
					$aut_id_name = 'tu_id'; 
					break;
				case AUT_TABLE_INDEXINT :
					$aut_id_name = 'indexint_id'; 
					break;
				case AUT_TABLE_CONCEPT :
					$aut_id_name = 'id_item'; 
					break;
				case AUT_TABLE_AUTHPERSO :
					// TODO
					break;
				default:
					$aut_id_name = 'author_id';
					break;	
			}
			$authority_join =' JOIN authorities on num_object = '.$aut_id_name.' and type_object = '.$type.' ';
			if (count($authority_ids)) {
				$authority_clause = ' and authorities.id_authority IN ('.implode(',',$authority_ids).') ';
			}else {
				$authority_clause = ' and authorities.id_authority IN (0) ';
			}
		}
		return array(
			'join' => $authority_join,
			'clause' => $authority_clause
		);
	}
	
	public function show_results($url,$url_to_search_form,$hidden_form=true,$search_target="", $acces=false) {
	    global $dbh;
	    global $begin_result_liste;
	    global $nb_per_page_search;
	    global $page;
	    global $charset;
	    global $search;
	    global $msg;
	    global $opac_nb_max_tri;
	    global $opac_allow_external_search;
	    global $debug;
	    //Y-a-t-il des champs ?
	    if (count($search)==0) {
	        array_pop($_SESSION["session_history"]);
	        error_message_history($msg["search_empty_field"], $msg["search_no_fields"], 1);
	        exit();
	    }
	    $recherche_externe=true;//Savoir si l'on peut faire une recherche externe � partir des crit�res choisis
	    //Verification des champs vides
	    for ($i=0; $i<count($search); $i++) {
	        $op=$this->get_global_value("op_".$i."_".$search[$i]);
	        
	        $field=$this->get_global_value("field_".$i."_".$search[$i]);
	        
	        $field1=$this->get_global_value("field_".$i."_".$search[$i]."_1");
	        
	        $s=explode("_",$search[$i]);
	        $bool=false;
	        if ($s[0]=="f") {
	            $champ=$this->fixedfields[$s[1]]["TITLE"];
	            if ($this->is_empty($field, "field_".$i."_".$search[$i]) && $this->is_empty($field1, "field_".$i."_".$search[$i]."_1")) {
	                $bool=true;
	            }
	        } elseif(array_key_exists($s[0],$this->pp)) {
	            $champ=$this->pp[$s[0]]->t_fields[$s[1]]["TITRE"];
	            if ($this->is_empty($field, "field_".$i."_".$search[$i]) && $this->is_empty($field1, "field_".$i."_".$search[$i]."_1")) {
	                $bool=true;
	            }
	        } elseif($s[0]=="s") {
	            $recherche_externe=false;
	            $champ=$this->specialfields[$s[1]]["TITLE"];
	            $type=$this->specialfields[$s[1]]["TYPE"];
	            for ($is=0; $is<count($this->tableau_speciaux["TYPE"]); $is++) {
	                if ($this->tableau_speciaux["TYPE"][$is]["NAME"]==$type) {
	                    $sf=$this->specialfields[$s[1]];
	                    global $include_path;
	                    require_once($include_path."/search_queries/specials/".$this->tableau_speciaux["TYPE"][$is]["PATH"]."/search.class.php");
	                    $specialclass= new $this->tableau_speciaux["TYPE"][$is]["CLASS"]($s[1],$i,$sf,$this);
	                    $bool=$specialclass->is_empty($field);
	                    break;
	                }
	            }
	        }//elseif (substr($s,0,9)=="authperso") {}
	        if (($bool)&&(!$this->op_empty[$op])) {
	            $query_data = array_pop($_SESSION["session_history"]);
	            error_message_history($msg["search_empty_field"], sprintf($msg["search_empty_error_message"],$champ), 1);
	            print $this->get_back_button($query_data);
	            exit();
	        }
	    }
	    $table=$this->make_search();
	    
	    if ($acces==true) {
	        $this->filter_searchtable_from_accessrights($table);
	    }
	    
	    $requete="select count(1) from $table";
	    if($res=pmb_mysql_query($requete)){
	        $nb_results=pmb_mysql_result($res,0,0);
	    }else{
	        $query_data = array_pop($_SESSION["session_history"]);
	        error_message_history("",$msg["search_impossible"], 1);
	        print $this->get_back_button($query_data);
	        exit();
	    }
	    
	    //gestion du tri
	    $has_sort = false;
	    if ($nb_results <= $opac_nb_max_tri) {
	        if (isset($_SESSION["tri"])) {
	            $table = $this->sort_results($table);
	            $has_sort = true;
	        }
	    }
	    // fin gestion tri
	    //Y-a-t-il une erreur lors de la recherche ?
	    if ($this->error_message) {
	        $query_data = array_pop($_SESSION["session_history"]);
	        error_message_history("", $this->error_message, 1);
	        print $this->get_back_button($query_data);
	        exit();
	    }
	    
	    if ($hidden_form) {
	        print $this->make_hidden_search_form($url,$this->get_hidden_form_name(),"",false);
	        print facette_search_compare::form_write_facette_compare();
	        print "</form>";
	    }
	    
	    $human_requete = $this->make_human_query();
	    print "<strong>".$msg["search_search_extended"]."</strong> : ".$human_requete ;
	    if ($debug) print "<br />".$this->serialize_search();
	    if ($nb_results) {
	        print $this->get_display_nb_results($nb_results);
	        print $begin_result_liste;
	        print $this->get_display_icons($nb_results, $recherche_externe);
	    } else print "<br />".$msg["no_result"]." ";
	    
	    self::get_caddie_link();
	    
	    print "<input type='button' class='bouton' onClick=\"document.".$this->get_hidden_form_name().".action='".$url_to_search_form."'; document.".$this->get_hidden_form_name().".target='".$search_target."'; document.".$this->get_hidden_form_name().".submit(); return false;\" value=\"".$msg["search_back"]."\"/>";
	    print $this->get_display_actions();
	    
	    print $this->get_current_search_map();
	    
	    $this->show_objects_results($table, $has_sort);
	    
	    $this->get_navbar($nb_results, $hidden_form);
	}
	
	protected function get_hidden_form_name(){
	    if(!isset($this->hidden_form_name)){
	        $this->hidden_form_name = 'search_form_'.md5(microtime());
	    }
	    return $this->hidden_form_name;
	}
	
	public function get_field_selector($url, $limit_search){
	    global $pmb_extended_search_auto, $charset, $msg, $filter_group;
	    if ($pmb_extended_search_auto)  {
	        $r ="<script>
                function getFormInfos() {
                    if (search_form.add_field.value != '') {
                        enable_operators();
                        
                        var node = document.getElementById('add_field');
                        if (node && search_form.authperso_id) {
                            var option = node.options.item(node.selectedIndex);
    			            if (option && option.attributes.getNamedItem('data-authperso_id')) {
    			                attribute = option.attributes.getNamedItem('data-authperso_id');
                                if (attribute) {
                                    search_form.authperso_id.value = attribute.value;
                                }
    			            }
                        }
                        
                        search_form.action = '$url';
                        search_form.target = '';
                        if (search_form.launch_search) {
                            search_form.launch_search.value = 0;
                        }
                        $limit_search
                        search_form.submit();
                    } else {
                        alert('".htmlentities($msg["multi_select_champ"],ENT_QUOTES,$charset)."');
                    }
                }
            </script>
            <select name='add_field' id='add_field' onChange=\"getFormInfos()\" >\n";
	    } else {
	        $r="<select name='add_field' id='add_field'>\n";
	    }
	    $r.="<option value='' style='color:#000000'>".htmlentities($msg["multi_select_champ"],ENT_QUOTES,$charset)."</option>\n";
	    
	    /**
	     * if else, si il n'y a pas de groupe d�fini, on conserve le traitement de base
	     * Sinon, ordonnancement via les IDs de groupes
	     */
	    if(!$this->groups_used){
	        //Champs fixes
	        if($this->fixedfields){
	            reset($this->fixedfields);
	            $open_optgroup=0;
	            $open_optgroup_deja_affiche=0;
	            $open_optgroup_en_attente_affiche=0;
	            foreach ($this->fixedfields as $id => $ff) {
	                if ($ff["SEPARATOR"]) {
	                    if ($open_optgroup) $r.="</optgroup>\n";
	                    // $r.="<option disabled style='border-left:0px;border-right:0px;border-top:0px;border-bottom:1px;border-style:solid;'></option>\n";
	                    $r_opt_groupe="<optgroup label='".htmlentities($ff["SEPARATOR"],ENT_QUOTES,$charset)."' class='optgroup_multicriteria'>\n";
	                    $open_optgroup=0;
	                    $open_optgroup_deja_affiche=0;
	                    $open_optgroup_en_attente_affiche=1;
	                }
	                if ($this->visibility($ff)) {
	                    if ($open_optgroup_en_attente_affiche && !$open_optgroup_deja_affiche) {
	                        $r.=$r_opt_groupe ;
	                        $open_optgroup_deja_affiche = 1 ;
	                        $open_optgroup_en_attente_affiche = 0 ;
	                        $open_optgroup = 1;
	                    }
	                    $r.="<option value='f_".$id."' style='color:#000000'>".htmlentities($ff["TITLE"],ENT_QUOTES,$charset)."</option>\n";
	                }
	            }
	        }
	        //Champs dynamiques
	        if ($open_optgroup) $r.="</optgroup>\n";
	        $open_optgroup = 0;
	        // $r.="<option disabled style='border-left:0px;border-right:0px;border-top:0px;border-bottom:1px;border-style:solid;'></option>\n";
	        if(!$this->dynamics_not_visible){
	            foreach ( $this->dynamicfields as $key => $value ) {
	                if(!$this->pp[$key]->no_special_fields && count($this->pp[$key]->t_fields) && ($key != 'a')){
	                    $r.="<optgroup label='".$msg["search_custom_".$value["TYPE"]]."' class='optgroup_multicriteria'>\n";
	                    reset($this->pp[$key]->t_fields);
	                    $array_dyn_tmp=array();
	                    //liste des champs persos � cacher par type
	                    $hide_customfields_array = array();
	                    if ($this->dynamicfields_hidebycustomname[$value["TYPE"]]) {
	                        $hide_customfields_array = explode(",",$this->dynamicfields_hidebycustomname[$value["TYPE"]]);
	                    }
	                    foreach ($this->pp[$key]->t_fields as $id => $df) {
	                        //On n'affiche pas les champs persos cit�s par nom dans le fichier xml
	                        if ((!count($hide_customfields_array)) || (!in_array($df["NAME"],$hide_customfields_array))) {
	                            $array_dyn_tmp[strtolower($df["TITRE"])]="<option value='".$key."_".$id."' style='color:#000000'>".htmlentities($df["TITRE"],ENT_QUOTES,$charset)."</option>\n";
	                        }
	                    }
	                    if (count($array_dyn_tmp)) {
	                        if ($this->dynamicfields_order=="alpha") {
	                            ksort($array_dyn_tmp);
	                        }
	                        $r.=implode('',$array_dyn_tmp);
	                    }
	                    $r.="</optgroup>\n";
	                }
	            }
	        }
	        //Champs autorit�s perso
	        if ($open_optgroup) $r.="</optgroup>\n";
	        $open_optgroup = 0;
	        $r_authperso="";
	        foreach($this->authpersos as $authperso){
	            if(!$authperso['gestion_multi_search'])continue;
	            $r_authperso.="<optgroup label='".htmlentities($msg["authperso_multi_search_by_field_title"]." : ".$authperso['name'], ENT_QUOTES, $charset)."' class='optgroup_multicriteria'>\n";
	            $r_authperso.="<option value='authperso_".$authperso['id']."' style='color:#000000'>".$msg["authperso_multi_search_tous_champs_title"]."</option>\n";
	            foreach($authperso['fields'] as $field){
	                $r_authperso.="<option value='a_".$field['id']."' style='color:#000000'>".htmlentities($field['label'],ENT_QUOTES,$charset)."</option>\n";
	            }
	            $r_authperso.="</optgroup>\n";
	        }
	        $r.=$r_authperso;
	        
	        //Champs speciaux
	        if (!$this->specials_not_visible && $this->specialfields) {
	            foreach ($this->specialfields as $id => $sf) {
	                for($i=0 ; $i<count($this->tableau_speciaux['TYPE']) ; $i++){
	                    if ($this->tableau_speciaux["TYPE"][$i]["NAME"] == $sf['TYPE']) {
	                        global $include_path;
	                        require_once($include_path."/search_queries/specials/".$this->tableau_speciaux["TYPE"][$i]["PATH"]."/search.class.php");
	                        $classname = $this->tableau_speciaux["TYPE"][$i]["CLASS"];
	                        if((isset($sf['VISIBLE']) && $sf['VISIBLE'] && !method_exists($classname, 'check_visibility')) || (method_exists($classname, 'check_visibility') && $classname::check_visibility() == true)){
	                            if ($sf["SEPARATOR"]) {
	                                if ($open_optgroup) $r.="</optgroup>\n";
	                                // $r.="<option disabled style='border-left:0px;border-right:0px;border-top:0px;border-bottom:1px;border-style:solid;'></option>\n";
	                                $r.="<optgroup label='".htmlentities($sf["SEPARATOR"],ENT_QUOTES,$charset)."' class='optgroup_multicriteria'>\n";
	                                $open_optgroup=1;
	                            }
	                            $r.="<option value='s_".$id."' style='color:#000000'>".htmlentities($sf["TITLE"],ENT_QUOTES,$charset)."</option>\n";
	                        }
	                        break;
	                    }
	                }
	            }
	            if ($open_optgroup) $r.="</optgroup>\n";
	            $open_optgroup = 0;
	        }
	    }else{
	        //Traitement des champs fixes
	        $fields_array = array();
	        $lonely_fields = array();
	        if($this->fixedfields){
	            reset($this->fixedfields);
	            foreach ($this->fixedfields as $id => $ff) {
	                if ($this->visibility($ff)) {
	                    if(isset($ff["GROUP"])){
	                        $fields_array[$ff["GROUP"]][]="<option value='f_".$id."' style='color:#000000'>".htmlentities($ff["TITLE"],ENT_QUOTES,$charset)."</option>\n";
	                    }else{
	                        $lonely_fields[] = "<option value='f_".$id."' style='color:#000000'>".htmlentities($ff["TITLE"],ENT_QUOTES,$charset)."</option>\n";
	                    }
	                }
	            }
	        }
	        
	        //Traitement des champs dynamiques (champs persos)
	        if(!$this->dynamics_not_visible){
	            foreach ( $this->dynamicfields as $key => $value ) {
	                if(!$this->pp[$key]->no_special_fields && count($this->pp[$key]->t_fields) && ($key != 'a')){
	                    reset($this->pp[$key]->t_fields);
	                    $array_dyn_tmp=array();
	                    //liste des champs persos � cacher par type
	                    $hide_customfields_array = array();
	                    if (isset($this->dynamicfields_hidebycustomname[$value["TYPE"]])) {
	                        $hide_customfields_array = explode(",",$this->dynamicfields_hidebycustomname[$value["TYPE"]]);
	                    }
	                    foreach ($this->pp[$key]->t_fields as $id => $df) {
	                        //On n'affiche pas les champs persos cit�s par nom dans le fichier xml
	                        if ((!count($hide_customfields_array)) || (!in_array($df["NAME"],$hide_customfields_array))) {
	                            $array_dyn_tmp[strtolower($df["TITRE"])]="<option value='".$key."_".$id."' style='color:#000000'>".htmlentities($df["TITRE"],ENT_QUOTES,$charset)."</option>\n";
	                        }
	                    }
	                    if (count($array_dyn_tmp)) {
	                        if ($this->dynamicfields_order=="alpha") {
	                            ksort($array_dyn_tmp);
	                        }
	                        $reorganized_array = array();
	                        foreach($array_dyn_tmp as $dynamic_option){
	                            $reorganized_array[] = $dynamic_option;
	                        }
	                        if(isset($value["GROUP"])){
	                            if(!isset($fields_array[$value["GROUP"]]) || !is_array($fields_array[$value["GROUP"]])) {
	                                $fields_array[$value["GROUP"]] = array();
	                            }
	                            $fields_array[$value["GROUP"]] = array_merge($fields_array[$value["GROUP"]], $reorganized_array);
	                        }else{
	                            $lonely_fields = array_merge($lonely_fields, $reorganized_array);
	                        }
	                    }
	                }
	            }
	        }
	        
	        //Traitement des champs sp�ciaux
	        if (!$this->specials_not_visible && $this->specialfields) {
	            foreach ($this->specialfields as $id => $sf) {
	                for($i=0 ; $i<count($this->tableau_speciaux['TYPE']) ; $i++){
	                    if ($this->tableau_speciaux["TYPE"][$i]["NAME"] == $sf['TYPE']) {
	                        global $include_path;
	                        require_once($include_path."/search_queries/specials/".$this->tableau_speciaux["TYPE"][$i]["PATH"]."/search.class.php");
	                        $classname = $this->tableau_speciaux["TYPE"][$i]["CLASS"];
	                        if((isset($sf['VISIBLE']) && $sf['VISIBLE'] && !method_exists($classname, 'check_visibility')) || (method_exists($classname, 'check_visibility') && $classname::check_visibility() == true)){
	                            if(isset($sf["GROUP"]) && $sf["GROUP"]){
	                                $fields_array[$sf["GROUP"]][] = "<option value='s_".$id."' style='color:#000000'>".htmlentities($sf["TITLE"],ENT_QUOTES,$charset)."</option>\n";
	                            }else{
	                                $lonely_fields[] = "<option value='s_".$id."' style='color:#000000'>".htmlentities($sf["TITLE"],ENT_QUOTES,$charset)."</option>\n";
	                            }
	                        }
	                    }
	                }
	            }
	        }
	        
	        if(isset($filter_group)){
	            if($filter_group < 1000){
	                $r.= $this->processing_groups($fields_array);
	            }else{ //Is int -> authperso
	                $r.= $this->processing_authpersos();
	            }
	        }else{
	            //cas standard
	            $r.= $this->processing_groups($fields_array);
	            $r.= $this->processing_authpersos();
	        }
	        
	        /**
	         * V�rification de la pr�sence de champs non class�s
	         */
	        if(count($lonely_fields)){
	            if(!count($this->filtered_objects_types)) {
	                $r.= "<optgroup label='".htmlentities($msg["search_extended_lonely_fields"], ENT_QUOTES, $charset)."' class='optgroup_multicriteria'>\n";
	                foreach($lonely_fields as $field){
	                    $r.= $field;
	                }
	                $r.="</optgroup>\n";
	            }
	        }
	    }
	    $r.="</select>";
	    return $r;
	}
	
	//Traitement des autorit�s persos (le champs doit �tre g�n�r� dynamiquement
	protected function processing_authpersos(){
	    global $msg, $charset, $authperso_id, $filter_group;
	    $r_authperso="";
	    foreach($this->authpersos as $authperso){
	        if((isset($filter_group) && (($filter_group-1000) == $authperso['id'])) || !isset($filter_group)){
	            if((!count($this->filtered_objects_types) || (in_array("authperso", $this->filtered_objects_types) && $authperso_id == $authperso['id']))) {
	                if(!$authperso['gestion_multi_search'])continue;
	                $r_authperso.="<optgroup label='".htmlentities($msg["authperso_multi_search_by_field_title"]." : ".$authperso['name'], ENT_QUOTES, $charset)."' class='optgroup_multicriteria'>\n";
	                $r_authperso.="<option value='authperso_".$authperso['id']."' style='color:#000000'>".$msg["authperso_multi_search_tous_champs_title"]."</option>\n";
	                if ($authperso['responsability_authperso']) {
	                    $r_authperso.="<option value='f_11' data-authperso_id='".$authperso['id']."' style='color:#000000'>".$msg['aut_responsability_form_responsability_authperso']."</option>\n";
	                }
	                foreach($authperso['fields'] as $field){
	                    $r_authperso.="<option value='a_".$field['id']."' style='color:#000000'>".htmlentities($field['label'],ENT_QUOTES,$charset)."</option>\n";
	                }
	                $r_authperso.="</optgroup>\n";
	            }
	        }
	    }
	    return $r_authperso;
	}
	
	protected function processing_groups($fields_array){
	    global $charset, $filter_group;
	    /**
	     * On parcourt la propri�t� groups contenant les
	     * groupes ordonn�s selon l'ordre d�fini dans le XML
	     */
	    $r = '';
	    foreach($this->groups as $group_id => $group){
	        if(isset($fields_array[$group_id]) && ((isset($filter_group) && ($filter_group == $group_id)) || (!isset($filter_group)))){ //On a des champs d�finis pour le groupe courant
	            if(!count($this->filtered_objects_types) || in_array($group['objects_type'], $this->filtered_objects_types)) {
	                $r.="<optgroup label='".htmlentities($this->groups[$group_id]['label'],ENT_QUOTES,$charset)."' class='optgroup_multicriteria'>\n";
	                foreach($fields_array[$group_id] as $field){
	                    $r.= $field;
	                }
	                $r.="</optgroup>\n";
	            }
	        }
	    }
	    return $r;
	}
	
	public static function get_caddie_link() {
	    //global $msg;
	    //print "&nbsp;<a href='#' onClick=\"openPopUp('./print_cart.php?current_print=".$_SESSION['CURRENT']."&action=print_prepare&object_type=".self::get_type_from_mode()."&authorities_caddie=1','print_cart'); return false;\"><img src='".get_url_icon('basket_small_20x20.gif')."' style='border:0px' class='center' alt=\"".$msg["histo_add_to_cart"]."\" title=\"".$msg["histo_add_to_cart"]."\"></a>&nbsp;";
	}
	
	public function generate_query_op_and($prefixe = "", $suffixe, $search_table) {
	    if ($prefixe) {
	        return "create temporary table ".$prefixe."and_result_".$suffixe." ENGINE=".$this->current_engine." select ".$search_table.".* from ".$search_table." where exists ( select ".$prefixe."mf_".$suffixe.".* from ".$prefixe."mf_".$suffixe." where ".$search_table.".id_authority=".$prefixe."mf_".$suffixe.".id_authority)";
	    } else {
	        return "create temporary table and_result_".$suffixe." ENGINE=".$this->current_engine." select ".$search_table.".* from ".$search_table." where exists ( select mf_".$suffixe.".* from mf_".$suffixe." where ".$search_table.".id_authority=mf_".$suffixe.".id_authority)";
	    }
	}
	
	/**
	 * Retourne le template pour appliquer un tri
	 * @param int|string $nb_results nombre de r�sultat de la recherce
	 * @param string $entity_type type de l'entit�
	 * @return string
	 */
	public static function get_sort_link($nb_results, $entity_type) {
	    print entities_authorities_controller::get_sort_link($nb_results, $entity_type);
	}
}
?>