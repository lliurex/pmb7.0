<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_resa_planning_circ_ui.class.php,v 1.1.2.5 2020/11/09 09:11:30 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_resa_planning_circ_ui extends list_resa_planning_ui {
	
    protected function get_form_title() {
        global $msg;
        
        return $msg['edit_resa_planning_menu'];
    }
    
    protected function init_default_selected_filters() {
        global $pmb_lecteurs_localises, $pmb_location_resa_planning;
        
        $this->add_selected_filter('montrerquoi');
        $this->add_empty_selected_filter();
        $this->add_empty_selected_filter();
        if($pmb_lecteurs_localises) {
            $this->add_selected_filter('empr_location');
        }
        if($pmb_location_resa_planning) {
            $this->add_selected_filter('resa_loc_retrait');
        }
    }
    
	protected function init_default_columns() {
	    global $pmb_lecteurs_localises;
	    global $pmb_location_resa_planning;
		
		$this->add_column('record');
		$this->add_column('empr');
		if($pmb_lecteurs_localises) {
		    $this->add_column('empr_location');
		}
		$this->add_column('resa_date');
		$this->add_column('resa_date_debut');
		$this->add_column('resa_date_fin');
		$this->add_column('resa_qty');
		$this->add_column('resa_validee');
		$this->add_column('resa_confirmee');
		if ($pmb_location_resa_planning=='1') {
		    $this->add_column('resa_loc_retrait');
		}
		$this->add_column_selection();
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'export_icons', false);
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('empr');
	    $this->add_applied_sort('record');
	    $this->add_applied_sort('resa_date');
	}
	
	protected function get_js_sort_script_sort() {
	    $display = parent::get_js_sort_script_sort();
	    $display = str_replace('!!categ!!', 'resa_planning', $display);
	    return $display;
	}
	
	protected function get_js_func_callback() {
		return "<script type='text/javascript'>
    		var ajax_func_to_call=new http_request();
    		var f_caller='';
    		var param1='';
    		var param2='';
    		var id;
    		function func_callback(p_caller,p_id,p_date,p_param1,p_param2) {
    			f_caller = p_caller;
    			param1 = p_param1;
    			param2 = p_param2;
    			id = p_id;
    			var url_func = './ajax.php?module=circ&categ=resa_planning&sub=update_resa_planning&id='+p_id+'&date='+p_date+'&param1='+p_param1;
    			ajax_func_to_call.request(url_func,0,'',1,func_callback_ret,0,0);
    		}
				
    		function func_callback_ret() {
    			if (param1 == '1') {
                    document.getElementById('".$this->objects_type."_resa_date_debut_'+id).value = ajax_func_to_call.get_text();
    			}
                if (param1 == '2') {
                    document.getElementById('".$this->objects_type."_resa_date_debut_'+id).value = ajax_func_to_call.get_text();
    			}
                document.getElementById(param2).value = ajax_func_to_call.get_text();
    		}
    	</script>";
	}
	
	protected function get_cell_content($object, $property) {
	    $content = '';
	    switch($property) {
	        case 'resa_date_debut':
	            if($object->resa_validee) {
	                $content .= $object->aff_resa_date_debut;
	            } else {
	                $content .= "<input type='hidden' id='".$this->objects_type."_resa_date_debut_".$object->id_resa."' name='".$this->objects_type."_resa_date_debut[".$object->id_resa."]' value='".$object->aff_resa_date_debut."' />";
	                $resa_date_debut = str_replace("-", "", $object->resa_date_debut);
	                $content .= "<input type='hidden' id='".$this->objects_type."_form_resa_date_debut_".$object->id_resa."' name='".$this->objects_type."_form_resa_date_debut_".$object->id_resa."' value='".$resa_date_debut."' />";
	                $content .= "<input type='button' class='bouton' onclick=\"openPopUp('./select.php?what=calendrier&caller=check_resa_planning&date_caller=".$resa_date_debut."&param1=".$this->objects_type."_form_resa_date_debut_".$object->id_resa."&param2=".$this->objects_type."_form_resa_date_debut_lib_".$object->id_resa."&auto_submit=NO&date_anterieure=YES&func_to_call=func_callback&id=".$object->id_resa."&sub_param1=1', 'calendar'); \" value='".$object->aff_resa_date_debut."' id='".$this->objects_type."_form_resa_date_debut_lib_".$object->id_resa."' name='".$this->objects_type."_form_resa_date_debut_lib_".$object->id_resa."'>";
	            }
	            break;
	        case 'resa_date_fin':
	            if($object->resa_validee) {
	                $content .= $object->aff_resa_date_fin;
	            } else {
	                $content .= "<input type='hidden' id='".$this->objects_type."_resa_date_fin_".$object->id_resa."' name='".$this->objects_type."_resa_date_fin[".$object->id_resa."]' value='".$object->aff_resa_date_fin."' />";
	                $resa_date_fin = str_replace("-", "", $object->resa_date_fin);
	                $content .= "<input type='hidden' id='".$this->objects_type."_form_resa_date_fin_".$object->id_resa."' name='".$this->objects_type."_form_resa_date_fin_".$object->id_resa."' value='".$resa_date_fin."' />";
	                $content .= "<input type='button' class='bouton' onclick=\"openPopUp('./select.php?what=calendrier&caller=check_resa_planning&date_caller=".$resa_date_fin."&param1=".$this->objects_type."_form_resa_date_fin_".$object->id_resa."&param2=".$this->objects_type."_form_resa_date_fin_lib_".$object->id_resa."&auto_submit=NO&date_anterieure=YES&func_to_call=func_callback&id=".$object->id_resa."&sub_param1=2', 'calendar')\" value='".$object->aff_resa_date_fin."' id='".$this->objects_type."_form_resa_date_fin_lib_".$object->id_resa."' name='".$this->objects_type."_form_resa_date_fin_lib_".$object->id_resa."'>";
	            }
	            break;
	        default :
	            $content .= parent::get_cell_content($object, $property);
	            break;
	    }
	    return $content;
	}
	
	/**
	 * Affiche la liste
	 */
	public function get_display_list() {
		$display = $this->get_js_func_callback();
		$display .= parent::get_display_list();
		return $display;
	}
	
	protected function get_display_html_content_selection() {
	    return "<div class='center'><input type='checkbox' id='resa_check_!!id!!' data-resa-id-empr='!!resa_idempr!!' name='resa_check[!!id!!]' class='".$this->objects_type."_selection' value='!!id!!'></div>";
	}
	
	protected function get_selection_actions() {
	    global $msg;
	    
	    if(!isset($this->selection_actions)) {
	        $validate_link = array(
	            'href' => static::get_controller_url_base()."&resa_action=val_resa"
	        );
	        $unvalidate_link = array(
	            'href' => static::get_controller_url_base()."&resa_action=raz_val_resa"
	        );
	        $send_confirmation_link = array(
	            'href' => static::get_controller_url_base()."&resa_action=conf_resa"
	        );
	        $raz_confirmation_link = array(
	            'href' => static::get_controller_url_base()."&resa_action=raz_conf_resa"
	        );
	        $to_resa_link = array(
	            'href' => static::get_controller_url_base()."&resa_action=to_resa"
	        );
	        $delete_link = array(
	            'href' => static::get_controller_url_base()."&resa_action=suppr_resa",
	            'confirm' => $msg['resa_valider_suppression_confirm']
	        );
	        $this->selection_actions = array(
	            $this->get_selection_action('validate', $msg['resa_planning_bt_val'], 'tick.png', $validate_link),
	            $this->get_selection_action('unvalidate', $msg['resa_planning_bt_raz_val'], 'cross.png', $unvalidate_link),
	            $this->get_selection_action('send_confirmation', $msg['resa_planning_bt_conf'], 'mail.png', $send_confirmation_link),
	            $this->get_selection_action('raz_confirmation', $msg['resa_planning_bt_raz_conf'], 'cross.png', $raz_confirmation_link),
	            $this->get_selection_action('to_resa', $msg['resa_planning_bt_to_resa'], 'doc.gif', $to_resa_link),
	            $this->get_selection_action('delete', $msg['63'], 'interdit.gif', $delete_link)
	        );
	    }
	    return $this->selection_actions;
	}
	
	protected function get_selection_mode() {
	    return 'button';
	}
	
	protected function get_name_selected_objects() {
	    return "resa_check";
	}
	
	protected function add_event_on_selection_action($action=array()) {
	    global $msg;
	    
	    $display = "
			on(dom.byId('".$this->objects_type."_selection_action_".$action['name']."_link'), 'click', function() {
				var selection = new Array();
				query('.".$this->objects_type."_selection:checked').forEach(function(node) {
					selection.push(node);
				});
				if(selection.length) {
					var confirm_msg = '".(isset($action['link']['confirm']) ? addslashes($action['link']['confirm']) : '')."';
					if(!confirm_msg || confirm(confirm_msg)) {
						".(isset($action['link']['href']) && $action['link']['href'] ? "
							var selected_objects_form = domConstruct.create('form', {
								action : '".$action['link']['href']."',
								name : '".$this->objects_type."_selected_objects_form',
								id : '".$this->objects_type."_selected_objects_form',
								method : 'POST'
							});
							selection.forEach(function(selected_option) {
								var id_resa = selected_option.value;
								var id_empr = selected_option.getAttribute('data-resa-id-empr');
								var selected_objects_hidden = domConstruct.create('input', {
									type : 'hidden',
									name : '".$this->get_name_selected_objects()."[]',
									value : id_resa
								});
								domConstruct.place(selected_objects_hidden, selected_objects_form);

                                var empr_ids_hidden = domConstruct.create('input', {
									type : 'hidden',
                                    id : 'empr_ids['+id_resa+']',
									name : 'empr_ids['+id_resa+']',
									value : id_empr
								});
								domConstruct.place(empr_ids_hidden, selected_objects_form);
						    
                                if(dom.byId('".$this->objects_type."_resa_date_debut_'+id_resa)) {
                                    var resa_date_debut_hidden = domConstruct.create('input', {
    									type : 'hidden',
    									name : 'resa_date_debut['+id_resa+']',
    									value : dom.byId('".$this->objects_type."_resa_date_debut_'+id_resa).value
    								});
    								domConstruct.place(resa_date_debut_hidden, selected_objects_form);
                                }

                                if(dom.byId('".$this->objects_type."_resa_date_fin_'+id_resa)) {
                                    var resa_date_fin_hidden = domConstruct.create('input', {
    									type : 'hidden',
    									name : 'resa_date_fin['+id_resa+']',
    									value : dom.byId('".$this->objects_type."_resa_date_fin_'+id_resa).value
    								});
    								domConstruct.place(resa_date_fin_hidden, selected_objects_form);
                                }
							});
							domConstruct.place(selected_objects_form, dom.byId('list_ui_selection_actions'));
							dom.byId('".$this->objects_type."_selected_objects_form').submit();
							"
						    : "")."
						".(isset($action['link']['openPopUp']) && $action['link']['openPopUp'] ? "openPopUp('".$action['link']['openPopUp']."&selected_objects='+selection.join(','), '".$action['link']['openPopUpTitle']."'); return false;" : "")."
						".(isset($action['link']['onClick']) && $action['link']['onClick'] ? $action['link']['onClick']."(selection); return false;" : "")."
					}
				} else {
					alert('".addslashes($msg['list_ui_no_selected'])."');
				}
			});
		";
	    return $display;
	}
	
	public static function get_controller_url_base() {
		global $base_path;
	
		return $base_path.'/circ.php?categ=resa_planning';
	}
}