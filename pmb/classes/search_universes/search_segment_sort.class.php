<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_segment_sort.class.php,v 1.1.4.9 2021/04/07 14:45:04 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path.'/templates/search_universes/search_segment_sort.tpl.php');
require_once "$class_path/fields/sort_fields.class.php";

class search_segment_sort {
	
	protected $num_segment;
	
	protected $human_query;
	
	protected $sort;
	
	protected $type;
	
	protected $table_tempo;
	
	protected $sort_fields;
	
	protected $principal_fields;
	
	protected $pperso_fields;
		
	public function __construct($num_segment = 0){
		$this->num_segment = $num_segment+0;
		$this->fetch_data();
	}
	
	protected function fetch_data() {
	    $this->type = '';
		if ($this->num_segment) {
			$query = '
			    SELECT search_segment_sort, search_segment_type
			    FROM search_segments 
			    WHERE id_search_segment = "'.$this->num_segment.'"
			';
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				$row = pmb_mysql_fetch_assoc($result);
				$this->sort = stripslashes($row['search_segment_sort']);
				$this->type = $row['search_segment_type'];
			}
		}
	}

	public function get_sort() {
	    return $this->sort;
	}

	public function get_human_query() {
	    if (isset($this->human_query)) {
	        return $this->human_query;
	    }
	    if (empty($this->sort)) {
	        return '';
	    }
	    $this->get_sort_fields();
	    $fields = encoding_normalize::json_decode($this->sort, true);
	    $this->human_query = $this->sort_fields->get_human_query($fields);
	    return $this->human_query;
	}
	
	public function get_form() {
	    global $search_segment_sort_form;

	    $i = 0;
	    $select_fields = '';
	    $sorts = explode('||', $this->get_sort());
	    $nb_sorts = count($sorts);
	    foreach ($sorts as $key => $sort) {
	        $exploded_sort = explode('|', $sort);
            $sort_name = '';
	        if (!empty($exploded_sort[1])) {
	            $sort_name = $exploded_sort[1];
	        }
	        
            // Si pas de tri définit on met a la place un message et on envoi des valeurs par défaut
            $display = "block";
	        if ($sorts[0] === "" || strpos ($sorts[0] ,"segment_sort_name_default")) {
	            $select_fields .= $this->get_segment_sort_default();
                $display = "none";
	        }
	        
	        $select_fields .= "<div class='row' style='display:$display'>";
	        $select_fields .= "<input id='segment_sort_name_$i' name='segment_sort_name[]' class='saisie-30em' type='text' value='" . clean_string($sort_name) . "'/>";
	        $select_fields .= $this->get_select_direction_sort($exploded_sort[0], $i);
	        $select_fields .= $this->get_select_fields_sort($exploded_sort[0], $i);
	        $select_fields .= "<input id='segment_sort_delete_$i' class='bouton' type='button' value='X' onclick='document.getElementById(\"segment_sort_name_$i\").value = \"\"' />";
	        if ($key == ($nb_sorts - 1)) {
	            $select_fields .= "<input id='segment_sort_add_$i' class='bouton' type='button' value='+' onclick='add_sort_field($i)' />";
	        }
	        $select_fields .= "</div>";
	        $i++;
	    }
	    
	    $search_segment_sort_form = str_replace('!!segment_sort_select_fields!!', $select_fields, $search_segment_sort_form);
	    $search_segment_sort_form = str_replace('!!segment_sort_fields_javascript!!', $this->get_sort_fields_javascript(), $search_segment_sort_form);

	    return $search_segment_sort_form;
	}
		
	public function set_properties_from_form(){
	    $this->get_sort_fields();
	    $this->sort = encoding_normalize::json_encode($this->sort_fields->format_fields());
	    $this->human_query = $this->get_human_query();
	}
	
	public function get_sort_from_form() {
	    global $segment_sort_name, $segment_sort_direction, $segment_sort_fields;

	    $sort = '';
	    for ($i = 0; $i < count($segment_sort_name); $i++) {
	        if ($segment_sort_name[$i] === "segment_sort_name_default") {
	            continue;
	        }
	        if (!empty($segment_sort_name[$i])) {
	            if (!empty($sort)) {
	                $sort .= ' || ';
	            }
	            $sort .= $segment_sort_direction[$i] . '_' . $segment_sort_fields[$i] . ' | ' . $segment_sort_name[$i];
	        }
	    }
	    
	    // Si on a pas de tri renseigne on enregistre un tri par default
	    // Si tous les champs on ete vide du coup on applique un tri par defaut decroissant sur la pert
	    // On test quel type on a car la pert n'est pas defini pareil dans le xml
	    if (empty($sort)) {
    	    if (TYPE_NOTICE == $this->type) {
    	        $sort = 'd_num_6 | segment_sort_name_default';
    	    } elseif (TYPE_EXTERNAL == $this->type){
    	        $sort = '';
    	    } else {
    	        $sort = 'd_num_1 | segment_sort_name_default';
    	    }
	    }
	    return $sort;
	}
	
	public function update() {
	    if (!$this->num_segment ) {
	        return false;
	    }
		$query = '
		    UPDATE search_segments 
		    SET search_segment_sort = "'.addslashes($this->sort).'"
		    WHERE id_search_segment = "'.$this->num_segment.'"';
		return pmb_mysql_query($query);
	}
	
	public function delete_sort(){
	    $this->sort = "";
	    $this->human_query = "";
	}
	
	private function get_sort_fields() {
	    if (!isset($this->sort_fields)) {
	        $this->sort_fields = new sort_fields($this->get_indexation_type(), $this->get_indexation_path());
	    }
	    return $this->sort_fields;
	}
	
	private function get_indexation_path() {
	    global $include_path;
	    $string_type = entities::get_string_from_const_type($this->type);
	    switch ($string_type) {
	        case 'ontology' :
	            break;
	        case 'notices' :
	            return $include_path."/indexation/notices/champs_base.xml";
	        default :
	            return $include_path."/indexation/authorities/$string_type/champs_base.xml";
	    }
	}
	
	private function get_indexation_type() {
	    switch ($this->type) {
	        case TYPE_NOTICE :
	            return "notices";
	        default :
	            return "authorities";
	    }
	}
	
	private function get_sub_type() {
	    return entities::get_aut_table_from_type($this->type);
	}
	
	public function sort_data($data, $offset = 0, $limit = 0, $query_searcher) {
	    $query = $this->appliquer_tri($this->num_segment,$query_searcher,$this->params['REFERENCEKEY'],$offset,$limit);
	    $res = pmb_mysql_query($query);
	    if($res && pmb_mysql_num_rows($res)){
	        $this->result=array();
	        while($row = pmb_mysql_fetch_object($res)){
        	    $this->result[] = $row->{ $this->params["REFERENCEKEY"] };
	        }
	    }	
	    return $this->result;
	}
	
	public function add_session_currentSegment($id){
	    $_SESSION['sort_segment_'.$this->num_segment.'currentSort'] = $id;   
	    return true;
	}

	public function show_tris_selector_segment() {
        global $search_index, $msg;
        
        if (!empty($_SESSION['sort_segment_'.$this->num_segment.'currentSort'])){
            $sort_seg = $_SESSION['sort_segment_'.$this->num_segment.'currentSort'];
        } else {
            $sort_seg = 0;
        }
	    $sorts = array();
	    $sorts = explode('||',$this->sort);
        $html = '<label for="segment_sort">' . $msg['list_applied_sort'] . '</label>
                 <select onChange=applySort(this.options[this.selectedIndex].value) name="segment_sort" id="segment_sort">';
        foreach ($sorts as $sort_id => $sort){
            if (!empty(explode('|',$sort)[1])){
                $sort_name = explode('|',$sort)[1];
            } else {
                $sort_name = '';
            }
            $html .= '<option  value="'.$sort_id.'"'.  (($sort_seg == $sort_id) ? " selected" : "").'" >'.$sort_name.'</option>';
        }
	    $html .= "</select></span>
            <script>
            function applySort(value){
                dojo.xhrPost({
					url : './ajax.php?module=ajax&categ=search_segment&action=add_session_currentSegment&num_segment=".$this->num_segment."&segment_sort='+value,
				});	                
                document.location = 'index.php?lvl=search_segment&id=".$this->num_segment."&search_index=".$search_index."&segment_sort='+value;
            }

            </script><span class=\"espaceResultSearch\">&nbsp;</span>";
	    return $html; 
	}
	
	/**
	 * Ajoute les tris par défaut éventuellement saisis en paramètre
	 */
	public function add_default_sort(){
	    if ($this->sort) {
	        if (empty($_SESSION['sort_segment_'.$this->num_segment.'_list']) || $_SESSION['sort_segment_'.$this->num_segment.'_list'] != $this->sort) {
	            $_SESSION['sort_segment_'.$this->num_segment.'_list'] = $this->sort;
	            $_SESSION['sort_segment_'.$this->num_segment.'flag'] = 0;
	        }
	        //on vérifie l'existence d'un flag : que la recherche par défaut ne revienne pas si l'utilisateur l'a supprimée par le formulaire
	        if(empty($_SESSION['sort_segment_'.$this->num_segment.'flag'])){
	            $tmpArray = explode("||",$this->sort);
	            foreach($tmpArray as $tmpElement){
	                if(trim($tmpElement)){
	                    if (strstr($tmpElement,'|')) {
	                        $tmpSort=explode("|",$tmpElement);
	                        $this->add_session_sort($tmpSort[0],$tmpSort[1]);
	                    } else {
	                        $this->add_session_sort($tmpElement);
	                    }
	                }
	            }
	            $_SESSION['sort_segment_'.$this->num_segment.'flag']=1;
	        }
	    }
	}

	private function add_session_sort($sortDes, $sortName =''){
	    global $charset;
	    $_SESSION["sort_segment_".$this->num_segment][]= [
	        "name" => htmlentities($sortName,ENT_QUOTES,$charset),
	        "des"  => htmlentities($sortDes,ENT_QUOTES,$charset)  
	    ];
	}
	
	public function get_select_fields_sort($sort, $i) {
	    global $msg, $charset;
	    
	    $sort_principal_fields = '';
	    $sort_pperso = '';
	    
	    $field_id = 0;
	    if (isset(explode('_', $sort)[2])) {
    	    $field_id = clean_string(explode('_', $sort)[2]);
	    }
	    
	    $type = $this->type;
	    if ($this->type > 1000) {
	        $type = TYPE_AUTHPERSO;
	    }
	    
	    $this->get_principal_fields(entities::get_string_from_const_type($type));
	    if (!empty($this->principal_fields)) {
	        $sort_principal_fields .= "<optgroup label='" . $msg['champs_principaux_query'] . "'>";
	        foreach ($this->principal_fields['FIELD'] as $field) {
	            $selected = ($field['ID'] == $field_id ? 'selected' : '');
	            $label = (isset($msg[$field['NAME']]) ? $msg[$field['NAME']] : '');
	            $sort_principal_fields .= "<option value='" . $field['TYPE'] . "_" . $field['ID'] . "' $selected>" . htmlentities($label, ENT_QUOTES, $charset) . "</option>";
	        }
	        $sort_principal_fields .= "</optgroup>";
	    }
	    
	    $this->get_pperso_fields(parametres_perso::get_pperso_prefix_from_type($type));
	    if (!empty($this->pperso_fields->t_fields)) {
	        $options = '';
	        foreach ($this->pperso_fields->t_fields as $id => $field) {
	            if (!empty($field['OPAC_SORT'])) {
        	        $selected = ("cp$id" == $field_id ? 'selected' : '');
        	        $value = $field['OPTIONS'][0]['FOR'] . "_cp$id";
        	        if ($field['OPTIONS'][0]['FOR'] == 'date_box') {
        	            $value = "date_cp$id";
        	        }
        	        $options .= "<option value='$value' $selected>" . htmlentities($field['TITRE'], ENT_QUOTES, $charset) . "</option>";
	            }
    	    }
    	    if (!empty($options)) {
    	        $sort_pperso .= "<optgroup label='" . htmlentities($msg['authority_champs_perso'], ENT_QUOTES, $charset) . "'>$options</optgroup>";
    	    }
	    }
	    
	    return "<select id='segment_sort_fields_$i' name='segment_sort_fields[]' class='saisie-30em'>
	               $sort_principal_fields
	               $sort_pperso
	            </select>";
	}
	
	public function get_select_direction_sort($sort, $i) {
	    global $msg;
	    
	    $direction_sort = clean_string(explode('_', $sort)[0]);
	    
	    return "<select id='segment_sort_direction_$i' name='segment_sort_direction[]' class='saisie-10em'>
	               <option value='c' " . ($direction_sort != 'd' ? 'selected' : '') . ">" . $msg['list_applied_sort_asc'] . "</option>
	               <option value='d' " . ($direction_sort == 'd' ? 'selected' : '') . ">" . $msg['list_applied_sort_desc'] . "</option>
	            </select>";
	}
	
	public function get_principal_fields($type) {
	    global $include_path;
	    
	    if (isset($this->principal_fields)) {
	        return;
	    }
	    
	    $nomfichier = "$include_path/sort/$type/sort.xml";
	    
	    if (file_exists("$include_path/sort/$type/sort_subst.xml")) {
	        $nomfichier = "$include_path/sort/$type/sort_subst.xml";
	        $fp = fopen($nomfichier, "r");
	    } elseif (file_exists($nomfichier)) {
	        $fp = fopen($nomfichier, "r");
	    }
	    
	    if ($fp) {
	        $xml = fread($fp, filesize($nomfichier));
	        fclose($fp);
	        $params = _parser_text_no_function_($xml, "SORT", $nomfichier);
	        $this->principal_fields = $params;
	    }
	    return $this->principal_fields;
	}
	
	public function get_pperso_fields($type) {
	    if (!isset($this->pperso_fields)) {
    	    $this->pperso_fields = new parametres_perso($type);
	    }
	    return $this->pperso_fields;
	}
	
	public function get_sort_fields_javascript() {
	    return "
	        <script type='text/javascript'>
	            function add_sort_field(sort_index) {
	               let target_index = parseInt(sort_index) + 1;
	               let container = document.getElementById('sort_fields');
	               let row = document.createElement('div');
	               row.class = 'row';

	               let input = document.getElementById('segment_sort_name_' + sort_index).cloneNode(true);
	               input.id = 'segment_sort_name_' + target_index;
	               input.value = '';

	               let direction = document.getElementById('segment_sort_direction_' + sort_index).cloneNode(true);
	               direction.id = 'segment_sort_direction_' + target_index;

	               let fields = document.getElementById('segment_sort_fields_' + sort_index).cloneNode(true);
	               fields.id = 'segment_sort_fields_' + target_index;

	               let del_button = document.getElementById('segment_sort_delete_' + sort_index).cloneNode(true);
	               del_button.id = 'segment_sort_delete_' + target_index;
	               del_button.removeAttribute('onclick');
	               del_button.onclick = function() { document.getElementById(\"segment_sort_name_\" + target_index).value = \"\"; };

	               let old_add_button = document.getElementById('segment_sort_add_' + sort_index);
	               let add_button = old_add_button.cloneNode();
	               old_add_button.remove();
	               add_button.id = 'segment_sort_add_' + target_index;
	               add_button.removeAttribute('onclick');
	               add_button.onclick = function() { add_sort_field(target_index) };
	               
	               row.appendChild(input);
	               row.appendChild(direction);
	               row.appendChild(fields);
	               row.appendChild(del_button);
	               row.appendChild(add_button);
	               container.appendChild(row);
                   
                   if(sort_index == 0){
                       let input_default = document.getElementById('segment_sort_name_default');
                       input_default.remove()
    	               
                       let direction_default = document.getElementById('segment_sort_direction_default');
                       direction_default.remove()
    
    	               let fields_default = document.getElementById('segment_sort_fields_default');
                       fields_default.remove()

    	               let old_add_button_default = document.getElementById('segment_sort_add_default');
                       old_add_button_default.remove()

    	               let segment_sort_label_default = document.getElementById('segment_sort_label_default');
                       segment_sort_label_default.remove()

    	               let segment_sort_default = document.getElementById('segment_sort_default');
                       segment_sort_default.remove()
                   }
	            }
	        </script>";
	}
	
	private function get_segment_sort_default() {
	    global $msg;
	    
	    // On test quel type on a car la pert n'est pas defini pareil dans le xml
	    // Par defaut on met une pert DESC
	    $sort = 'd_num_1';
	    $message = $msg['segment_sort_label_default'];
	    if (TYPE_NOTICE == $this->type) {
	        $sort = 'd_num_6';
	    }
	    if (TYPE_EXTERNAL == $this->type){
	        $sort = '';
	        $message = $msg['segment_sort_label_no_default'];
	    }

	    return "<div id='segment_sort_default' class='row'>
            	   <label id='segment_sort_label_default'> " . $message ." </label>
            	   <input id='segment_sort_name_default' name='segment_sort_name[]' type='hidden' value='segment_sort_name_default'/>
            	   <input id='segment_sort_direction_default' name='segment_sort_direction[]' type='hidden' value='d'>
            	   <input id='segment_sort_fields_default' name='segment_sort_fields[]' type='hidden' value='" . $sort . "'>
            	   <input id='segment_sort_add_default' class='bouton' type='button' value='+' onclick='add_sort_field(0)' />
        	   </div>";
	}
}