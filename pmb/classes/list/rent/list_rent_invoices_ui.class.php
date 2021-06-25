<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_rent_invoices_ui.class.php,v 1.1.2.5 2021/04/07 14:00:30 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_rent_invoices_ui extends list_rent_ui {
		
	protected $marclist_rent_destination;
	
	protected function _get_query_base() {
		$query = "SELECT distinct id_invoice FROM rent_invoices 
			JOIN rent_accounts_invoices ON account_invoice_num_invoice = id_invoice
			JOIN rent_accounts ON id_account = account_invoice_num_account";
		return $query;
	}
	
	protected function get_object_instance($row) {
		return new rent_invoice($row->id_invoice);
	}
	
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		$this->available_filters =
		array('main_fields' =>
				array(
						'entity' => 'acquisition_coord_lib',
						'exercice' => 'acquisition_budg_exer',
						'type' => 'acquisition_account_type_name',
						'num_publisher' => 'acquisition_account_num_publisher',
						'num_supplier' => 'acquisition_account_num_supplier',
						'status' => 'acquisition_invoice_status',
						'date' => 'acquisition_invoice_date',
				)
		);
		$this->available_filters['custom_fields'] = array();
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		$id_entity = entites::getSessionBibliId();
		$query = exercices::listByEntite($id_entity);
		$result = pmb_mysql_query($query);
		$id_exercice = 0;
		if($result && pmb_mysql_num_rows($result)) {
			$id_exercice = pmb_mysql_result($result, 0, 'id_exercice');
		}
		$this->filters = array(
				'entity' => $id_entity,
				'exercice' => $id_exercice,
				'type' => '',
				'num_publisher' => '',
				'num_supplier' => '',
				'num_pricing_system' => '',
				'status' => 0,
				'date_start' => '',
				'date_end' => ''
		);
		parent::init_filters($filters);
	}
	
	protected function init_default_selected_filters() {
		$this->add_selected_filter('entity');
		$this->add_selected_filter('exercice');
		$this->add_selected_filter('type');
		$this->add_selected_filter('num_publisher');
		$this->add_selected_filter('num_supplier');
		$this->add_selected_filter('status');
		$this->add_selected_filter('date');
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns =
		array('main_fields' =>
				array(
						'id' => 'acquisition_invoice_id',
						'num_user' => 'acquisition_invoice_num_user',
						'date' => 'acquisition_invoice_date',
						'num_publisher' => 'acquisition_invoice_num_publisher',
						'num_supplier' => 'acquisition_invoice_num_supplier',
						'status' => 'acquisition_invoice_status',
						'valid_date' => 'acquisition_invoice_valid_date',
						'destination_name' => 'acquisition_invoice_destination_name',
				)
		);
		$this->available_columns['custom_fields'] = array();
	}
	
	protected function init_default_columns() {
		$this->add_column_selection();
		$this->add_column('id');
		$this->add_column('num_user');
		$this->add_column('date');
		$this->add_column('num_publisher');
		$this->add_column('num_supplier');
		$this->add_column('status');
		$this->add_column('valid_date');
		$this->add_column('destination_name');
	}
	
	/**
	 * Initialisation des settings par défaut
	 */
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_column('status', 'align', 'center');
		$this->set_setting_column('valid_date', 'align', 'center');
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		$this->set_filter_from_form('status', 'integer');
		parent::set_filters_from_form();
	}
	
	protected function get_search_filter_status() {
		global $msg;
		
		return '<select name="'.$this->objects_type.'_status">
			<option value= "0" '.($this->filters['status'] == 0 ?  "selected='selected'" : "").'>'.$msg['acquisition_account_type_select_all'].'</option>
			<option value="1" '.($this->filters['status'] == 1 ?  "selected='selected'" : "").'>'.$msg['acquisition_invoice_status_new'].'</option>
			<option value="2" '.($this->filters['status'] == 2 ?  "selected='selected'" : "").'>'.$msg['acquisition_invoice_status_validated'].'</option>
		</select>';
	}
	
	protected function get_search_filter_date() {
		return $this->get_search_filter_interval_date('date');
	}
	
	/**
	 * Filtre SQL
	 */
	protected function _get_query_filters() {
		
		$filter_query = '';
		
		$this->set_filters_from_form();
		
		$filters = array();
		$filters[] = 'account_num_exercice = "'.$this->filters['exercice'].'"';
		
		if($this->filters['type']) {
			$filters [] = 'account_type = "'.addslashes($this->filters['type']).'"';
		}
		if($this->filters['num_publisher']) {
			$filters [] = 'account_num_publisher = "'.$this->filters['num_publisher'].'"';
		}
		if($this->filters['num_supplier']) {
			$filters [] = 'account_num_supplier = "'.$this->filters['num_supplier'].'"';
		}
		if($this->filters['num_pricing_system']) {
			$filters [] = 'account_num_pricing_system = "'.$this->filters['num_pricing_system'].'"';
		}
		if($this->filters['status']) {
			$filters [] = 'invoice_status = "'.$this->filters['status'].'"';
		}
		if($this->filters['date_start']) {
			$filters [] = 'invoice_date >= "'.$this->filters['date_start'].'"';
		}
		if($this->filters['date_end']) {
			$filters [] = 'invoice_date <= "'.$this->filters['date_end'].' 23:59:59"';
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
				case 'num_user' :
					return strcmp($a->get_user()->prenom.' '.$a->get_user()->nom, $b->get_user()->prenom.' '.$b->get_user()->nom);
					break;
				case 'num_publisher' :
					return strcmp((count($a->get_accounts()) ? $a->get_accounts()[0]->get_publisher()->display : ''), (count($b->get_accounts()) ? $b->get_accounts()[0]->get_publisher()->display : ''));
					break;
				case 'num_supplier' :
					return strcmp((count($a->get_accounts()) ? $a->get_accounts()[0]->get_supplier()->raison_sociale : ''), (count($b->get_accounts()) ? $b->get_accounts()[0]->get_supplier()->raison_sociale : ''));
					break;
				case 'id' :
					return $this->intcmp($a->get_id(), $b->get_id());
					break;
				default :
					return strcmp($a->{'get_'.$sort_by}(), $b->{'get_'.$sort_by}());
					break;
			}
		}
		
	}
	
	protected function get_cell_content($object, $property) {
		$content = '';
		switch($property) {
			case 'num_publisher':
				$accounts = $object->get_accounts();
				if(count($accounts)) {
					if(isset($accounts[0]->get_publisher()->display)) {
						$content .= $accounts[0]->get_publisher()->display;
					}
				}
				break;
			case 'num_supplier':
				$accounts = $object->get_accounts();
				if(count($accounts)) {
					if(isset($accounts[0]->get_supplier()->raison_sociale)) {
						$content .= $accounts[0]->get_supplier()->raison_sociale;
					}
				}
				break;
			case 'status':
				$content .= $object->get_status_label();
				break;
			case 'destination_name':
				if(!isset($this->marclist_rent_destination)) {
					$this->marclist_rent_destination = new marc_list('rent_destination');
				}
				$content .= $this->marclist_rent_destination->table[$object->get_destination()];
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}

	protected function _get_query_human_status() {
		global $msg;
		if($this->filters['status'] == 1) {
			return $msg['acquisition_invoice_status_new'];
		} elseif($this->filters['status'] == 2){
			return $msg['acquisition_invoice_status_validated'];
		}
		return '';
	}
	
	protected function _get_query_human() {
		$humans = $this->_get_query_human_main_fields();
		return $this->get_display_query_human($humans);
	}
	
	protected function get_display_cell($object, $property) {
		global $id_bibli;
		
		$attributes = array();
		$attributes['onclick'] = "window.location=\"".static::get_controller_url_base()."&action=edit&id_bibli=".$id_bibli."&id=".$object->get_id()."\"";
		$content = $this->get_cell_content($object, $property);
		$display = $this->get_display_format_cell($content, $property, $attributes);
		return $display;
	}
	
	protected function get_selection_actions() {
		global $msg, $base_path;
		
		if(!isset($this->selection_actions)) {
			$this->selection_actions = array();
			$gen_invoices_link = array(
					'openPopUp' => $base_path."/pdf.php?pdfdoc=account_invoice",
					'openPopUpTitle' => 'lettre'
			);
			$this->selection_actions[] = $this->get_selection_action('gen_invoices', $msg['acquisition_invoice_generate'], '', $gen_invoices_link);
			
			$validate_invoices_link = array(
					'href' => static::get_controller_url_base()."&action=validate"
			);
			$this->selection_actions[] = $this->get_selection_action('validate_invoices', $msg['acquisition_invoice_validate'], '', $validate_invoices_link);
		}
		return $this->selection_actions;
	}
	
	protected function get_selection_mode() {
		return 'button';
	}
	
	protected function get_name_selected_objects() {
	    return "invoices";
	}
}