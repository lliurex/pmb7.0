<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_selector_readers_statuses.class.php,v 1.1.2.2 2020/01/02 09:24:54 moble Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_selector_readers_statuses extends cms_module_common_selector{
	
	public function __construct($id=0){
		parent::__construct($id);
		
		if(!is_array($this->parameters) && $this->parameters){
			$temp= array();
			if($this->parameters)$temp[0]=$this->parameters;
			$this->parameters=$temp;
		}
	}
	
	public function get_form(){
		$form = "
			<div class='row'>
				<div class='colonne3'>
					<label for=''>".$this->format_text($this->msg['cms_module_common_selector_readers_statuses_choice'])."</label>
				</div>
				<div class='colonne-suite'>";
		$form.=$this->gen_select();
		$form.="
				</div>
			</div>";
		$form.=parent::get_form();
		return $form;
	}
	
	public function save_form(){
		$this->parameters = $this->get_value_from_form("readers_statuses");
		return parent ::save_form();
	}
	
	protected function gen_select(){
		$query= "select idstatut, statut_libelle from empr_statut order by statut_libelle";
		$result = pmb_mysql_query($query);
		$select = "
					<select name='".$this->get_form_value_name("readers_statuses")."[]' multiple='yes'>";
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_object($result)){
				$select.="
						<option value='".$row->idstatut."' ".((is_array($this->parameters) && in_array($row->idstatut,$this->parameters)) ? "selected='selected'" : "").">".$this->format_text($row->statut_libelle)."</option>";
			}
		}else{
			$select.= "
						<option value ='0'>".$this->format_text($this->msg['cms_module_common_selector_readers_statuses_no_status'])."</option>";
		}
		$select.= "
			</select>";
		return $select;
	}
	
	/*
	 * Retourne la valeur sélectionné
	 */
	public function get_value(){
		if(!$this->value){
			$this->value = $this->parameters;
		}
		return $this->value;
	}
}