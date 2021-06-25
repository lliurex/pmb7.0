<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_reservations_ui.class.php,v 1.1.6.38 2021/02/15 14:30:16 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/emprunteur.class.php");
require_once($class_path."/resa.class.php");
require_once($class_path."/resa_situation.class.php");
require_once($class_path."/notice.class.php");
require_once($class_path."/expl.class.php");
require_once($class_path."/resa_loc.class.php");

class list_reservations_ui extends list_ui {
	
	protected $location_reservations;
	
	protected $precedenteresa_idnotice;
	
	protected $precedenteresa_idbulletin;
	
	protected $no_aff;
	
	protected $resa_situation;
	
	protected static $info_gestion = NO_INFO_GESTION;
	
	protected $lien_deja_affiche;
	
	protected $resa_loc;
	
	protected $on_expl_location;
	
	protected function _get_query_base() {
		$query = "SELECT id_resa, resa_idempr, resa_idnotice, resa_idbulletin, resa_cb, resa_confirmee, resa_loc_retrait,
			ifnull(expl_cote,'') as expl_cote, expl_cb,
			trim(concat(if(series_m.serie_name <>'', if(notices_m.tnvol <>'', concat(series_m.serie_name,', ',notices_m.tnvol,'. '), concat(series_m.serie_name,'. ')), if(notices_m.tnvol <>'', concat(notices_m.tnvol,'. '),'')),
			if(series_s.serie_name <>'', if(notices_s.tnvol <>'', concat(series_s.serie_name,', ',notices_s.tnvol,'. '), series_s.serie_name), if(notices_s.tnvol <>'', concat(notices_s.tnvol,'. '),'')),
			ifnull(notices_m.tit1,''),ifnull(notices_s.tit1,''),' ',ifnull(bulletin_numero,''), if (mention_date, concat(' (',mention_date,')') ,''))) as tit
			FROM ((((resa LEFT JOIN notices AS notices_m ON resa_idnotice = notices_m.notice_id LEFT JOIN series AS series_m ON notices_m.tparent_id = series_m.serie_id )
			LEFT JOIN bulletins ON resa_idbulletin = bulletins.bulletin_id)
			LEFT JOIN notices AS notices_s ON bulletin_notice = notices_s.notice_id
			LEFT JOIN series AS series_s ON notices_s.tparent_id = series_s.serie_id)
			LEFT JOIN exemplaires ON resa_cb = exemplaires.expl_cb) ";
		return $query;
	}
	
	protected function is_visible_object($empr_location, $resa_loc_retrait) {
		global $pmb_transferts_actif, $transferts_choix_lieu_opac, $transferts_site_fixe;
		global $pmb_location_reservation, $pmb_lecteurs_localises;
		
		if($this->filters['available_location']) {
		    if ($pmb_lecteurs_localises && !$this->filters['id_empr']){
		        $this->on_expl_location = true;
		    }
		}
		if(!empty($this->selected_filters['removal_location']) && $this->filters['removal_location']) {
		    $filter_location_retrait = $this->filters['removal_location'];
		} else {
		    $filter_location_retrait = $this->filters['resa_loc_retrait'];
		}
		if($filter_location_retrait) {
			if ($pmb_transferts_actif=="1" && $filter_location_retrait) {
				switch ($transferts_choix_lieu_opac) {
					case "1":
						//retrait de la resa sur lieu choisi par le lecteur
						if($resa_loc_retrait != $filter_location_retrait) {
							return false;
						}
						break;
					case "2":
						//retrait de la resa sur lieu fix�
						if ($filter_location_retrait != $transferts_site_fixe) {
							return false;
						}
						break;
					case "3":
						//retrait de la resa sur lieu exemplaire
						// On affiche les r�sa que peut satisfaire la loc
						// respecter les droits de r�servation du lecteur
						if($pmb_location_reservation) {
							$resa_loc = $this->get_resa_loc();
							$data = $resa_loc->get_data();
							if(!in_array($filter_location_retrait, $data[$empr_location])) {
								return false;
							}
						}
						if(!$this->filters['id_empr']) {
						    $this->on_expl_location = true;
						}
						break;
					default:
						//retrait de la resa sur lieu lecteur
						if($empr_location != $filter_location_retrait) {
							return false;
						}
						if(!$this->filters['id_empr']) {
						    $this->on_expl_location = true;
						}
						break;
				}
			}elseif($pmb_location_reservation && $filter_location_retrait) {
				$resa_loc = $this->get_resa_loc();
				$data = $resa_loc->get_data();
				if(!in_array($filter_location_retrait, $data[$empr_location])) {
					return false;
				}
			}
		}
		return true;
	}
	
	protected function is_filter_exemplaire() {
	    if($this->filters['expl_codestat']
	        || count($this->filters['expl_codestats'])
	        || $this->filters['expl_section']
	        || count($this->filters['expl_sections'])
	        || $this->filters['expl_statut']
	        || count($this->filters['expl_statuts'])
	        || $this->filters['expl_type']
	        || count($this->filters['expl_types'])
	        || $this->filters['expl_cote']
	        || $this->filters['expl_location']
	        || count($this->filters['expl_locations'])
	        ) {
	        return true;
	    }
	    return false;
	}
	    
	/**
	 * @param exemplaire $exemplaire
	 * @return boolean
	 */
	protected function is_visible_exemplaire($exemplaire) {
	    if($this->filters['expl_codestat'] && ($exemplaire->codestat_id != $this->filters['expl_codestat'])) {
	        return false;
	    }
	    if(count($this->filters['expl_codestats']) && (!in_array($exemplaire->codestat_id, $this->filters['expl_codestats']))) {
	        return false;
	    }
	    if($this->filters['expl_section'] && ($exemplaire->section_id != $this->filters['expl_section'])) {
	        return false;
	    }
	    if(count($this->filters['expl_sections']) && (!in_array($exemplaire->section_id, $this->filters['expl_sections']))) {
	        return false;
	    }
	    if($this->filters['expl_statut'] && ($exemplaire->statut_id != $this->filters['expl_statut'])) {
	        return false;
	    }
	    if(count($this->filters['expl_statuts']) && (!in_array($exemplaire->statut_id, $this->filters['expl_statuts']))) {
	        return false;
	    }
	    if($this->filters['expl_type'] && ($exemplaire->typdoc_id != $this->filters['expl_type'])) {
	        return false;
	    }
	    if(count($this->filters['expl_types']) && (!in_array($exemplaire->typdoc_id, $this->filters['expl_types']))) {
	        return false;
	    }
	    if($this->filters['expl_cote'] && ($exemplaire->cote != $this->filters['expl_cote'])) {
	        return false;
	    }
	    if($this->filters['expl_location'] && ($exemplaire->location_id != $this->filters['expl_location'])) {
	        return false;
	    }
	    if(count($this->filters['expl_locations']) && (!in_array($exemplaire->location_id, $this->filters['expl_locations']))) {
	        return false;
	    }
	    return true;
	}
	
	protected function get_object_instance($row) {
		$resa = new reservation($row->resa_idempr, $row->resa_idnotice, $row->resa_idbulletin, $row->resa_cb);
		$resa->get_resa_cb();
		return $resa;
	}
	
	protected function add_object($row) {
		global $f_loc;
		
		$no_aff=0;
		if(!($this->filters['id_notice'] || $this->filters['id_bulletin']))
			if($this->filters['f_loc'] && !$this->filters['id_empr'] && $row->resa_cb && $row->resa_confirmee){
				// Dans la liste des r�sa � traiter, on n'affiche pas la r�sa qui a �t� affect� par un autre site
				$query = "SELECT expl_location FROM exemplaires WHERE expl_cb='".$row->resa_cb."' ";
				$res = @pmb_mysql_query($query);
				if(($data_expl = pmb_mysql_fetch_array($res))){
					if($data_expl['expl_location']!=$this->filters['f_loc']) {
						$no_aff=1;
					}
				}
		}
		if(!$no_aff || ($this->filters['id_notice'] || $this->filters['id_bulletin'])) {
			if($this->filters['id_empr']) {
				$this->filters['f_loc']=0;
				$f_loc = 0;
				$this->filters['removal_location']=0;
			}
			$empr_location = emprunteur::get_location($row->resa_idempr)->id;
			if($this->is_visible_object($empr_location, $row->resa_loc_retrait)) {
			    $is_filter_exemplaire = $this->is_filter_exemplaire();
			    if($is_filter_exemplaire || !empty($this->on_expl_location)) {
			        $object_instance = $this->get_object_instance($row);
			        if($is_filter_exemplaire && !$this->is_visible_exemplaire($object_instance->get_exemplaire())) {
			            return false;
			        }
			        if(!empty($this->on_expl_location)) {
			            if(!$this->filters['id_notice'] && !$this->filters['id_bulletin']) {
			                $resa_situation = $this->get_resa_situation($object_instance);
			                if($this->is_deffered_load()) {
			                    $resa_situation->initialize_no_aff();
			                } else {
			                    $resa_situation->get_display();
			                }
			                $no_aff = $resa_situation->get_no_aff();
			            } else {
			                $no_aff = 0;
			            }
			            if($no_aff) {
			                return false;
			            }
			        }
			        $this->objects[] = $object_instance;
			    } else {
			        parent::add_object($row);
			    }
				$this->location_reservations[$row->id_resa] = $empr_location;
			}
		}
	}
	
	/**
	 * On ne limite pas la requ�te SQL du fait des restrictions dans add_object
	 */
	protected function _get_query() {
		$query = $this->_get_query_base();
		$query .= $this->_get_query_filters();
		$query .= $this->_get_query_order();
		return $query;
	}
	
	protected function fetch_data() {
		parent::fetch_data();
		$this->pager['nb_results'] = count($this->objects);
	}
	
	/**
	 * Tri SQL
	 */
	protected function _get_query_order() {
		
		if($this->applied_sort[0]['by']) {
			$order = '';
			$sort_by = $this->applied_sort[0]['by'];
			switch($sort_by) {
				case 'index_sew':
					$order .= 'notices_m.index_sew, resa_date';
					break;
				case 'record':
					$order .= 'tit, resa_date';
					break;
				case 'resa_validee' :
					$order .= 'resa_cb';
					break;
				case 'expl_cote' :
				case 'resa_date' :
				case 'resa_date_debut' :
				case 'resa_date_fin' :
				case 'resa_confirmee' :
				case 'expl_cb' :
					$order .= $sort_by;
					break;
				default :
					$order .= parent::_get_query_order();
					break;
			}
			if($order) {
				$this->applied_sort_type = 'SQL';
				$order_sql = $this->_get_query_order_sql_build($order);
				return " group by resa_idnotice, resa_idbulletin, resa_idempr ".$order_sql;
			} else {
				return " group by resa_idnotice, resa_idbulletin, resa_idempr";
			}
		}
	}
	
	/**
	 * limite SQL retir�e dans get_query
	 * On l'applique ici
	 */
	protected function _limit() {
		$this->applied_sort_type = 'OBJECTS';
		parent::_limit();
	}
	
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		global $pmb_transferts_actif, $pmb_location_reservation;
		
		$this->available_filters =
		array('main_fields' =>
				array(
						'montrerquoi' => 'empr_etat_resa_query',
						'removal_location' => 'transferts_circ_resa_lib_localisation',
						'expl_codestat' => 'editions_datasource_expl_codestat',
						'expl_codestats' => 'editions_datasource_expl_codestats',
						'expl_section' => 'editions_datasource_expl_section',
						'expl_sections' => 'editions_datasource_expl_sections',
						'expl_statut' => 'editions_datasource_expl_statut',
						'expl_statuts' => 'editions_datasource_expl_statuts',
						'expl_type' => 'editions_datasource_expl_type',
						'expl_types' => 'editions_datasource_expl_types',
						'expl_cote' => '296',
						'groups' => 'dsi_ban_form_groupe_lect',
//						'resa_condition' => 'resa_condition'
				)
		);
		if ($pmb_transferts_actif=="1" || $pmb_location_reservation) {
			$this->available_filters['main_fields']['available_location'] = 'edit_resa_expl_available_filter';
			$this->available_filters['main_fields']['resa_loc_retrait'] = 'edit_resa_expl_location_filter';
		}
		$this->available_filters['custom_fields'] = array();
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		global $deflt_resas_location;
		global $pmb_transferts_actif, $transferts_choix_lieu_opac, $pmb_location_reservation;
		global $f_loc;
		
		$this->filters = array(
    		    'id_notice' => 0,
    		    'id_bulletin' => 0,
    		    'id_empr' => 0,
    		    'montrerquoi' => 'all',
				'f_loc' => ($f_loc == '' ? $deflt_resas_location : $f_loc),
				'removal_location' => '',
				'available_location' => '',
				'resa_state' => '',
				'expl_codestat' => '',
				'expl_codestats' => array(),
				'expl_section' => '',
				'expl_sections' => array(),
				'expl_statut' => '',
				'expl_statuts' => array(),
				'expl_type' => '',
				'expl_types' => array(),
				'expl_cote' => '',
    		    'expl_location' => '',
    		    'expl_locations' => array(),
				'groups' => array(),
                'resa_condition' => ''
		);
		$this->filters['resa_loc_retrait'] = '';
		$this->filters['resa_loc'] = 0;
		if ($pmb_transferts_actif=="1" && $this->filters['f_loc'] && empty($filters['id_empr'])) {
			switch ($transferts_choix_lieu_opac) {
				case "1":
					//retrait de la resa sur lieu choisi par le lecteur
					$this->filters['resa_loc_retrait'] = $this->filters['f_loc'];
					break;
				case "2":
					//retrait de la resa sur lieu fix�
					break;
				case "3":
					//retrait de la resa sur lieu exemplaire
					// On affiche les r�sa que peut satisfaire la loc
					// respecter les droits de r�servation du lecteur
					if($pmb_location_reservation) {
						$this->filters['resa_loc'] = $this->filters['f_loc'];
					}
					break;
				default:
					//retrait de la resa sur lieu lecteur
					break;
			}
		}elseif($pmb_location_reservation && $this->filters['f_loc'] && empty($filters['id_empr'])) {
			$this->filters['resa_loc'] = $this->filters['f_loc'];
		}
		if($this->filters['id_notice'] || $this->filters['id_bulletin']) {
			$this->filters['f_loc'] = 0;
			$f_loc = 0;
			$this->filters['removal_location'] = 0;
		} else {
		    $this->filters['removal_location'] = $this->filters['f_loc'];
		}
		parent::init_filters($filters);
	}
	
	protected function init_no_sortable_columns() {
	    $this->no_sortable_columns = array(
	        'resa_delete'
	    );
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		global $pmb_transferts_actif;
		
		$this->available_columns =
		array('main_fields' =>
				array(
						'record' => '233',
						'expl_cote' => '296',
						'empr' => 'empr_nom_prenom',
						'empr_location' => 'editions_datasource_empr_location',
						'rank' => '366',
						'resa_date' => '374',
						'resa_condition' => 'resa_condition',
						'resa_date_debut' => 'resa_date_debut_td',
						'resa_date_fin' => 'resa_date_fin_td',
						'resa_validee' => 'resa_validee',
						'resa_confirmee' => 'resa_confirmee',
						'expl_location' => 'edit_resa_expl_location',
						'section' => '295',
						'statut' => '297',
						'support' => '294',
						'expl_cb' => '232',
						'codestat' => '299',
						'groups' => 'groupe_empr'
				)
		);
		if ($pmb_transferts_actif=='1') {
			$this->available_columns['main_fields']['resa_loc_retrait'] = 'resa_loc_retrait';
			$this->available_columns['main_fields']['transfert_location_source'] = 'transfert_location_source';
		}
	}
	
	/**
	 * Initialisation des settings par d�faut
	 */
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'unfolded_filters', true);
		$this->set_setting_display('objects_list', 'deffered_load', true);
		$this->set_setting_filter('expl_codestats', 'visible', false);
		$this->set_setting_filter('expl_sections', 'visible', false);
		$this->set_setting_filter('expl_statuts', 'visible', false);
		$this->set_setting_filter('expl_types', 'visible', false);
		$this->set_setting_column('expl_cb', 'text', array('bold' => true));
		$this->set_setting_column('record', 'align', 'left');
		$this->set_setting_column('record', 'text', array('bold' => true));
		$this->set_setting_column('resa_validee', 'text', array('strong' => true));
		$this->set_setting_column('resa_confirmee', 'text', array('strong' => true));
	}
	
	/**
	 * Initialisation de la pagination par d�faut
	 */
	protected function init_default_pager() {
		parent::init_default_pager();
		$this->pager['nb_per_page'] = 40;
		$this->pager['allow_force_all_on_page'] = true;
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		$this->set_filter_from_form('montrerquoi');
		$this->set_filter_from_form('removal_location');
		$this->set_filter_from_form('available_location');
		$this->set_filter_from_form('resa_loc_retrait');
		
		$this->set_filter_from_form('expl_codestat');
		$this->set_filter_from_form('expl_codestats');
		$this->set_filter_from_form('expl_section');
		$this->set_filter_from_form('expl_sections');
		$this->set_filter_from_form('expl_statut');
		$this->set_filter_from_form('expl_statuts');
		$this->set_filter_from_form('expl_type');
		$this->set_filter_from_form('expl_types');
		$this->set_filter_from_form('expl_cote');
		$this->set_filter_from_form('expl_location');
		$this->set_filter_from_form('expl_locations');
		$this->set_filter_from_form('groups');
		$this->set_filter_from_form('resa_condition');
		parent::set_filters_from_form();
	}
	
	/**
	 * Sauvegarde de la pagination en session
	 */
	public function set_pager_in_session() {
	    global $sub;
	    
	    parent::set_pager_in_session();
	    if($this->pager['nb_results'] >= ($this->pager['page']*$this->pager['nb_per_page'])) {
	        $_SESSION['list_'.$this->objects_type.'_pager']['page'] = $this->pager['page'];
	    } else {
	        unset($_SESSION['list_'.$this->objects_type.'_pager']['page']);
	    }
	}
	
	protected function get_selector_query($type) {
		$query = '';
		switch ($type) {
			case 'docs_codestat':
				$query = 'select idcode as id, codestat_libelle as label from docs_codestat order by label';
				break;
			case 'docs_section':
				$query = 'select idsection as id, section_libelle as label from docs_section order by label';
				break;
			case 'docs_statut':
				$query = 'select idstatut as id, statut_libelle as label from docs_statut order by label';
				break;
			case 'docs_type':
				$query = 'select idtyp_doc as id, tdoc_libelle as label from docs_type order by label';
				break;
			case 'docs_location':
			    $query = 'select idlocation as id, location_libelle as label from docs_location order by label';
			    break;
			case 'groups':
				$query = 'select id_groupe as id, libelle_groupe as label from groupe order by label';
				break;
		}
		return $query;
	}
	
	protected function get_search_filter_montrerquoi() {
		global $msg, $charset;
		
		//Selecteur r�servations validees/confirmees
		$search_filter = "
            <span class='list_ui_montrerquoi_checkbox ".$this->objects_type."_montrerquoi_checkbox'>
                <input type='radio' name='".$this->objects_type."_montrerquoi' value='all' id='all' ".($this->filters['montrerquoi'] == 'all' ? "checked='checked'" : "")." />
                <label for='all'>".htmlentities($msg['resa_show_all'], ENT_QUOTES, $charset)."</label>
            </span>&nbsp;
            <span class='list_ui_montrerquoi_checkbox ".$this->objects_type."_montrerquoi_checkbox'>
                <input type='radio' name='".$this->objects_type."_montrerquoi' value='validees' id='validees' ".($this->filters['montrerquoi'] == 'validees' ? "checked='checked'" : "")." />
                <label for='validees'>".htmlentities($msg['resa_show_validees'], ENT_QUOTES, $charset)."</label>
            </span>&nbsp;
            <span class='list_ui_montrerquoi_checkbox ".$this->objects_type."_montrerquoi_checkbox'>
                <input type='radio' name='".$this->objects_type."_montrerquoi' value='invalidees' id='invalidees' ".($this->filters['montrerquoi'] == 'invalidees' ? "checked='checked'" : "")." />
                <label for='invalidees'>".htmlentities($msg['resa_show_invalidees'], ENT_QUOTES, $charset)."</label>
            </span>&nbsp;
            <span class='list_ui_montrerquoi_checkbox ".$this->objects_type."_montrerquoi_checkbox'>
                <input type='radio' name='".$this->objects_type."_montrerquoi' value='valid_noconf' id='valid_noconf' ".($this->filters['montrerquoi'] == 'valid_noconf' ? "checked='checked'" : "")." />
                <label for='valid_noconf'>".htmlentities($msg['resa_show_non_confirmees'], ENT_QUOTES, $charset)."</label>
            </span>";
		return $search_filter;
	}
	
	protected function get_search_filter_removal_location() {
		global $msg, $charset;
		
		$query = 'select idlocation, location_libelle FROM docs_location order by location_libelle';
		$result = pmb_mysql_query($query);
		$search_filter = '<select name="'.$this->objects_type.'_removal_location">';
		$search_filter.='<option value="0"'.((!$this->filters['removal_location'])?' selected="selected"':'').'>'.htmlentities($msg["all_location"], ENT_QUOTES, $charset).'</option>';
		if(pmb_mysql_num_rows($result)) {
			while($o=pmb_mysql_fetch_object($result)) {
				$search_filter.= '<option value="'.$o->idlocation.'"'.(($this->filters['removal_location'] == $o->idlocation)?' selected="selected"':'').'>'.htmlentities($o->location_libelle,ENT_QUOTES,$charset).'</option>';
			}
		}
		$search_filter.= '</select>';
		return $search_filter;
	}
	
	protected function get_search_filter_available_location() {
		global $msg, $charset;
		
		$query = 'select idlocation, location_libelle FROM docs_location order by location_libelle';
		$result = pmb_mysql_query($query);
		$search_filter = '<select name="'.$this->objects_type.'_available_location">';
		$search_filter.='<option value="0"'.((!$this->filters['available_location'])?' selected="selected"':'').'>'.$msg['all_location'].'</option>';
		if(pmb_mysql_num_rows($result)) {
			while($o=pmb_mysql_fetch_object($result)) {
				$search_filter.= '<option value="'.$o->idlocation.'"'.(($this->filters['available_location'] == $o->idlocation)?' selected="selected"':'').'>'.htmlentities($o->location_libelle,ENT_QUOTES,$charset).'</option>';
			}
		}
		$search_filter.= '</select>';
		return $search_filter;
	}
	
	protected function get_search_filter_resa_loc_retrait() {
		global $msg, $charset;
		
		$query = 'select idlocation, location_libelle FROM docs_location order by location_libelle';
		$result = pmb_mysql_query($query);
		$search_filter = '<select name="'.$this->objects_type.'_resa_loc_retrait">';
		$search_filter.='<option value="0"'.((!$this->filters['resa_loc_retrait'])?' selected="selected"':'').'>'.$msg['all_location'].'</option>';
		if(pmb_mysql_num_rows($result)) {
			while($o=pmb_mysql_fetch_object($result)) {
				$search_filter.= '<option value="'.$o->idlocation.'"'.(($this->filters['resa_loc_retrait'] == $o->idlocation)?' selected="selected"':'').'>'.htmlentities($o->location_libelle,ENT_QUOTES,$charset).'</option>';
			}
		}
		$search_filter.= '</select>';
		return $search_filter;
	}
	
	protected function get_search_filter_expl_cote() {
		global $charset;
		return "<input type='text' class='saisie-20em' name='".$this->objects_type."_expl_cote' value='".htmlentities($this->filters['expl_cote'], ENT_QUOTES, $charset)."' />";
	}
	
	protected function get_search_filter_expl_codestat() {
		global $msg;
		
		return $this->get_simple_selector($this->get_selector_query('docs_codestat'), 'expl_codestat', $msg['all']);
	}
	
	protected function get_search_filter_expl_codestats() {
		global $msg;
		
		return $this->get_multiple_selector($this->get_selector_query('docs_codestat'), 'expl_codestats', $msg['all']);
	}
	
	protected function get_search_filter_expl_section() {
		global $msg;
		
		return $this->get_simple_selector($this->get_selector_query('docs_section'), 'expl_section', $msg['all']);
	}
	
	protected function get_search_filter_expl_sections() {
		global $msg;
		
		return $this->get_multiple_selector($this->get_selector_query('docs_section'), 'expl_sections', $msg['all']);
	}
	
	protected function get_search_filter_expl_statut() {
		global $msg;
		
		return $this->get_simple_selector($this->get_selector_query('docs_statut'), 'expl_statut', $msg['all']);
	}
	
	protected function get_search_filter_expl_statuts() {
		global $msg;
		
		return $this->get_multiple_selector($this->get_selector_query('docs_statut'), 'expl_statuts', $msg['all']);
	}
	
	protected function get_search_filter_expl_type() {
		global $msg;
		
		return $this->get_simple_selector($this->get_selector_query('docs_type'), 'expl_type', $msg['all']);
	}
	
	protected function get_search_filter_expl_types() {
		global $msg;
		
		return $this->get_multiple_selector($this->get_selector_query('docs_type'), 'expl_types', $msg['all']);
	}
	
	protected function get_search_filter_expl_location() {
	    global $msg;
	    
	    return $this->get_simple_selector($this->get_selector_query('docs_location'), 'expl_location', $msg['all']);
	}
	
	protected function get_search_filter_expl_locations() {
	    global $msg;
	    
	    return $this->get_multiple_selector($this->get_selector_query('docs_location'), 'expl_locations', $msg['all']);
	}
	
	protected function get_search_filter_groups() {
		global $msg;
		
		return $this->get_multiple_selector($this->get_selector_query('groups'), 'groups', $msg['dsi_all_groups']);
	}
	
	protected function get_search_filter_resa_condition() {
	    global $msg, $charset;
	    
	    $options = resa_situation::get_conditions();
	    $selector = "<select name='".$this->objects_type."_resa_condition'>";
	    $selector .= "<option value='' ".(empty($this->filters['resa_condition']) ? "selected='selected'" : "").">".htmlentities($msg['all'], ENT_QUOTES, $charset)."</option>";
	    foreach ($options as $value=>$label) {
	        $selector .= "<option value='".htmlentities($value, ENT_QUOTES, $charset)."' ".(in_array($value, $this->filters['resa_condition']) ? "selected='selected'" : "").">".$label."</option>";
	    }
	    $selector .= "</select>";
	    return $selector;
	}
	
	/**
	 * Filtre SQL
	 */
	protected function _get_query_filters() {
		$filter_query = '';
		
		$this->set_filters_from_form();
		
		$filters = array();
		
		if(get_called_class() == 'list_reservations_edition_treat_ui') {
			$filters [] = '(resa_cb="" or resa_cb is null)';
		}
		if($this->filters['id_notice']) {
			$filters [] = 'resa.resa_idnotice="'.$this->filters['id_notice'].'"';
		}
		if($this->filters['id_bulletin']) {
			$filters [] = 'resa.resa_idbulletin="'.$this->filters['id_bulletin'].'"';
		}
		if($this->filters['id_empr']) {
			$filters [] = 'resa.resa_idempr="'.$this->filters['id_empr'].'"';
		}
		if($this->filters['montrerquoi']) {
			switch ($this->filters['montrerquoi']) {
				case 'validees':
					$filters [] = 'resa_cb<>""';
					break;
				case 'invalidees':
					$filters [] = 'resa_cb=""';
					break;
				case 'valid_noconf':
					$filters [] = 'resa_cb<>""';
					$filters [] = 'resa_confirmee="0"';
					break;
				case 'all':
				default:
					break;
			}
		}
		if($this->filters['resa_state']) {
			switch ($this->filters['resa_state']) {
			    case 'depassee':
			    	$filters [] = '(resa_date_fin < CURDATE() and resa_date_fin<>"0000-00-00")';
			    	break;
				case 'encours':
				default:
					$filters [] = '(resa_date_fin >= CURDATE() or resa_date_fin="0000-00-00")';
			    	break;
			}
// 			$filters [] = $this->filters['resa_dates_restrict'];
		}
		if(is_array($this->filters['groups']) && count($this->filters['groups'])) {
			$filters [] = 'groupe_id IN ('.implode(',', $this->filters['groups']).')';
		}
		if(count($filters)) {
			$filter_query .= ' where '.implode(' and ', $filters);
		}
		return $filter_query;
	}
	
	/**
	 * Fonction de callback
	 * @param object $a
	 * @param object $b
	 */
	protected function _compare_objects($a, $b) {
	    if($this->applied_sort[0]['by']) {
	        $sort_by = $this->applied_sort[0]['by'];
			switch($sort_by) {
				case 'expl_cote':
					return strcmp($a->get_exemplaire()->cote, $b->get_exemplaire()->cote);
					break;
				case 'record' :
					$a_notice_title = reservation::get_notice_title($a->id_notice, $a->id_bulletin);
					$b_notice_title = reservation::get_notice_title($b->id_notice, $b->id_bulletin);
					return strcmp(strip_tags($a_notice_title), strip_tags($b_notice_title));
					break;
				case 'empr':
					return strcmp(emprunteur::get_name($a->id_empr), emprunteur::get_name($b->id_empr));
					break;
				case 'empr_location':
					return strcmp(emprunteur::get_location($a->id_empr)->libelle, emprunteur::get_location($b->id_empr)->libelle);
					break;
				case 'rank':
					$rang_a = recupere_rang($a->id_empr, $a->id_notice, $a->id_bulletin, $this->filters['removal_location']);
					$rang_b = recupere_rang($b->id_empr, $b->id_notice, $b->id_bulletin, $this->filters['removal_location']);
					return strcmp($rang_a, $rang_b);
					break;
				case 'resa_date':
					return strcmp($a->date, $b->date);
					break;
				case 'resa_date_debut':
					return strcmp($a->date_debut, $b->date_debut);
					break;
				case 'resa_date_fin':
					return strcmp($a->date_fin, $b->date_fin);
					break;
				case 'resa_loc_retrait':
					$loc_retrait_a = resa_loc_retrait($a->id);
					$docs_location_a = new docs_location($loc_retrait_a);
					$loc_retrait_b = resa_loc_retrait($b->id);
					$docs_location_b = new docs_location($loc_retrait_b);
					return strcmp($docs_location_a->libelle, $docs_location_b->libelle);
					break;
				case 'resa_condition': //Situation
					return strcmp(strip_tags($this->get_cell_content_resa_condition($a)), strip_tags($this->get_cell_content_resa_condition($b)));
					break;
				case 'expl_location':
					return strcmp($a->get_exemplaire()->location, $b->get_exemplaire()->location);
					break;
				case 'support':
					return strcmp($a->get_exemplaire()->typdoc, $b->get_exemplaire()->typdoc);
					break;
				case 'statut':
					$docs_statut_a = new docs_statut($a->get_exemplaire()->statut_id);
					$docs_statut_b = new docs_statut($b->get_exemplaire()->statut_id);
					return strcmp($docs_statut_a->libelle, $docs_statut_b->libelle);
					break;
				case 'section':
					return strcmp($a->get_exemplaire()->{$sort_by}, $b->get_exemplaire()->{$sort_by});
					break;
				case 'codestat':
					$docs_codestat_a = new docs_codestat($a->get_exemplaire()->codestat_id);
					$docs_codestat_b = new docs_codestat($b->get_exemplaire()->codestat_id);
					return strcmp($docs_codestat_a->libelle, $docs_codestat_b->libelle);
					break;
				case 'groups':
					$cmp_a = '';
					$groupes_a = emprunteur::get_groupes($a->id_empr);
					if(count($groupes_a)) {
						$cmp_a = strip_tags($groupes_a[0]);
					}
					$cmp_b = '';
					$groupes_b = emprunteur::get_groupes($b->id_empr);
					if(count($groupes_b)) {
						$cmp_b = strip_tags($groupes_b[0]);
					}
					return strcmp($cmp_a, $cmp_b);
					break;
				case 'transfert_location_source':
					$cmp_a = '';
					$query = "SELECT num_location_source FROM transferts_demande WHERE resa_trans=".$a->id." AND num_expl=".$a->expl_id;
					$result = pmb_mysql_query($query);
					if(pmb_mysql_num_rows($result)) {
						$docs_location_a = new docs_location(pmb_mysql_result($result, 0, 'num_location_source'));
						$cmp_a = $docs_location_a->libelle;
					}
					$cmp_b = '';
					$query = "SELECT num_location_source FROM transferts_demande WHERE resa_trans=".$b->id." AND num_expl=".$b->expl_id;
					$result = pmb_mysql_query($query);
					if(pmb_mysql_num_rows($result)) {
						$docs_location_b = new docs_location(pmb_mysql_result($result, 0, 'num_location_source'));
						$cmp_b = $docs_location_b->libelle;
					}
					return strcmp($cmp_a, $cmp_b);
					break;
				default :
					return parent::_compare_objects($a, $b);
					break;
			}
		}
	}
	
	/**
	 * Construction dynamique de la fonction JS de tri
	 */
	protected function get_js_sort_script_sort() {
		global $sub;
		
		$display = parent::get_js_sort_script_sort();
		$display = str_replace('!!sub!!', $sub, $display);
		$display = str_replace('!!action!!', 'list', $display);
		return $display;
	}
	
	protected function get_grouped_label($object, $property) {
		global $msg;
		
		$grouped_label = '';
		switch($property) {
			case 'empr_location':
				$grouped_label = emprunteur::get_location($object->id_empr)->libelle;
				break;
			case 'rank':
				$grouped_label = recupere_rang($object->id_empr, $object->id_notice, $object->id_bulletin, $this->filters['removal_location']) ;
				break;
			case 'resa_date':
				$grouped_label = $object->formatted_date;
				break;
			case 'resa_date_debut':
				if($object->date_debut != '0000-00-00') {
					$grouped_label = $object->formatted_date_debut;
				}
				break;
			case 'resa_date_fin':
				if($object->date_fin != '0000-00-00') {
					$grouped_label = $object->formatted_date_fin;
				}
				break;
			case 'resa_validee':
				if($object->expl_cb) {
					$grouped_label = $msg["40"];
				} else {
					$grouped_label = $msg["39"];
				}
				break;
			case 'resa_confirmee':
				if($object->confirmee) {
					$grouped_label = $msg["40"];
				} else {
					$grouped_label = $msg["39"];
				}
				break;
			case 'resa_loc_retrait':
				$loc_retrait = resa_loc_retrait($object->id);
				$docs_location = new docs_location($loc_retrait);
				$grouped_label = $docs_location->libelle;
				break;
			case 'resa_condition': //Situation
				$grouped_label = $this->get_cell_content_resa_condition($object);
				break;
			case 'expl_location':
				$grouped_label = $object->get_exemplaire()->location;
				break;
			case 'support':
				$grouped_label = $object->get_exemplaire()->typdoc;
				break;
			case 'statut':
				$docs_statut = new docs_statut($object->get_exemplaire()->statut_id);
				$grouped_label = $docs_statut->libelle;
				break;
			case 'section':
				$grouped_label = $object->get_exemplaire()->{$property};
				break;
			case 'codestat':
				$docs_codestat = new docs_codestat($object->get_exemplaire()->codestat_id);
				$grouped_label = $docs_codestat->libelle;
				break;
			case 'groups':
				$grouped_label = implode(',', emprunteur::get_groupes($object->id_empr));
				break;
			default:
				$grouped_label = parent::get_grouped_label($object, $property);
				break;
		}
		return $grouped_label;
	}
	
	protected function get_resa_situation($object) {
		if(empty($this->resa_situation)) {
			$this->resa_situation = array();
		}
		if(!isset($this->resa_situation[$object->id])) {
			$rank = recupere_rang($object->id_empr, $object->id_notice, $object->id_bulletin, $this->filters['removal_location']);
			
			$resa_situation = new resa_situation($object->id);
			$resa_situation->set_resa($object)
			->set_resa_cb($object->expl_cb)
			->set_idlocation($this->location_reservations[$object->id])
			->set_precedenteresa_idnotice($this->precedenteresa_idnotice)
			->set_precedenteresa_idbulletin($this->precedenteresa_idbulletin)
			->set_rank($rank)
			->set_no_aff($this->no_aff)
			->set_lien_deja_affiche($this->lien_deja_affiche);
			$this->resa_situation[$object->id] = $resa_situation;
		}
		return $this->resa_situation[$object->id];
	}
	
	protected function get_cell_content_resa_condition($object) {
		if(!isset($this->precedenteresa_idnotice)) $this->precedenteresa_idnotice = 0;
		if(!isset($this->precedenteresa_idbulletin)) $this->precedenteresa_idbulletin = 0;
		if(!isset($this->no_aff)) $this->no_aff = 0;
		if(!isset($this->no_aff)) $this->lien_deja_affiche = 0;
		
		$resa_situation = $this->get_resa_situation($object);
		$situation = $resa_situation->get_display(static::$info_gestion);
		
		$this->precedenteresa_idnotice = $resa_situation->get_precedenteresa_idnotice();
		$this->precedenteresa_idbulletin = $resa_situation->get_precedenteresa_idbulletin();
		$this->no_aff = $resa_situation->get_no_aff();
		$this->lien_deja_affiche = $resa_situation->get_lien_deja_affiche();
		
		return $situation;
	}
	
	protected function get_cell_content($object, $property) {
		global $base_path;
		
		$content = '';
		switch($property) {
			case 'expl_cb':
				$content .= exemplaire::get_cb_link($object->{$property});
				break;
			case 'expl_cote':
				$content .= $object->get_exemplaire()->cote;
				break;
			case 'record':
				if ($object->id_bulletin) {
					$typdoc = "";
				} else {
					$typdoc = notice::get_typdoc($object->id_notice);
				}
				$content .= resa_list_get_column_title($object->id_notice, $object->id_bulletin, $typdoc);
				break;
			case 'empr':
				if (SESSrights & CIRCULATION_AUTH) {
					$content .= "<a href='".$base_path."/circ.php?categ=pret&form_cb=".rawurlencode(emprunteur::get_cb_empr($object->id_empr))."'>".emprunteur::get_name($object->id_empr)."</a>";
				} else {
					$content .= emprunteur::get_name($object->id_empr);
				}
				break;
			case 'empr_location':
				$content .= emprunteur::get_location($object->id_empr)->libelle;
				break;
			case 'rank':
				$content .= recupere_rang($object->id_empr, $object->id_notice, $object->id_bulletin, $this->filters['removal_location']) ;
				break;
			case 'resa_date':
				$content .= $object->formatted_date;
				break;
			case 'resa_date_debut':
				if($object->date_debut != '0000-00-00') {
					$content .= $object->formatted_date_debut;
				}
				break;
			case 'resa_date_fin':
				if($object->date_fin != '0000-00-00') {
					$content .= $object->formatted_date_fin;
				}
				break;
			case 'resa_validee':
				if($object->expl_cb) {
					$content .= "<span style='color:red'>X</span>";
				}
				break;
			case 'resa_confirmee':
				if($object->confirmee) {
					$content .= "<span style='color:red'>X</span>";
				}
				break;
			case 'resa_loc_retrait':
				$loc_retrait = resa_loc_retrait($object->id);
				$docs_location = new docs_location($loc_retrait);
				$content .= $docs_location->libelle;
				break;
			case 'resa_condition': //Situation
				$content .= $this->get_cell_content_resa_condition($object);
				break;
			case 'expl_location':
				$content .= $object->get_exemplaire()->location;
				break;
			case 'support':
				$content .= $object->get_exemplaire()->typdoc;
				break;
			case 'statut':
				$docs_statut = new docs_statut($object->get_exemplaire()->statut_id);
				$content .= $docs_statut->libelle;
				break;
			case 'section':
				$content .= $object->get_exemplaire()->{$property};
				break;
			case 'codestat':
				$docs_codestat = new docs_codestat($object->get_exemplaire()->codestat_id);
				$content .= $docs_codestat->libelle;
				break;
			case 'groups':
				$content .= implode(',', emprunteur::get_groupes($object->id_empr));
				break;
			case 'transfert_location_source':
				$query = "SELECT num_location_source FROM transferts_demande WHERE resa_trans=".$object->id." AND num_expl=".$object->expl_id;
				$result = pmb_mysql_query($query);
				if(pmb_mysql_num_rows($result)) {
					$docs_location = new docs_location(pmb_mysql_result($result, 0, 'num_location_source'));
					$content = $docs_location->libelle;
				}
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function _get_query_property_filter($property) {
		switch ($property) {
			case 'expl_codestat':
				return "select codestat_libelle from docs_codestat where idcode = ".$this->filters[$property];
			case 'expl_codestats':
				return "select codestat_libelle from docs_codestat where idcode IN (".implode(',', $this->filters[$property]).")";
			case 'expl_section':
				return "select section_libelle from docs_section where idsection = ".$this->filters[$property];
			case 'expl_sections':
				return "select section_libelle from docs_section where idsection IN (".implode(',', $this->filters[$property]).")";
			case 'expl_statut':
				return "select statut_libelle from docs_statut where idstatut = ".$this->filters[$property];
			case 'expl_statuts':
				return "select statut_libelle from docs_statut where idstatut IN (".implode(',', $this->filters[$property]).")";
			case 'expl_type':
				return "select tdoc_libelle from docs_type where idtyp_doc = ".$this->filters[$property];
			case 'expl_types':
				return "select tdoc_libelle from docs_type where idtyp_doc IN (".implode(',', $this->filters[$property]).")";
			case 'groups':
				return "select libelle_groupe from groupe where id_groupe IN (".implode(',', $this->filters[$property]).")";
		}
		return '';
	}
	
	protected function _get_query_human_montrerquoi() {
		global $msg;
		
		switch ($this->filters['montrerquoi']) {
			case 'validees':
				return $msg['resa_show_invalidees'];
			case 'invalidees':
				return $msg['resa_planning_show_invalidees'];
			case 'valid_noconf':
				return $msg['resa_show_non_confirmees'];
		}
	}
	
	protected function _get_query_human_removal_location() {
		if($this->filters['removal_location']) {
			$docs_location = new docs_location($this->filters['removal_location']);
			return $docs_location->libelle;
		}
		return '';
	}
	
	protected function _get_query_human_available_location() {
		$docs_location = new docs_location($this->filters['available_location']);
		return $docs_location->libelle;
	}
	
	protected function _get_query_human_resa_loc_retrait() {
		$docs_location = new docs_location($this->filters['resa_loc_retrait']);
		return $docs_location->libelle;
	}
	
	public function get_resa_loc() {
		if(!isset($this->resa_loc)) {
			$this->resa_loc = new resa_loc();
		}
		return $this->resa_loc;
	}
}