<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_selector_article_type_from.class.php,v 1.1.20.1 2021/02/19 14:41:43 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_selector_article_type_from extends cms_module_common_selector {
    
	public function __construct($id = 0) {
		parent::__construct($id);
	}
	
	public function get_form() {
		$form = parent::get_form();
		$form .= "
			<div class='row'>
                <div class='colonne3'>
                    &nbsp;
                </div>
                <div class='colonne-suite'>
				    <label for='cms_module_common_selector_article_type_from'>" . $this->format_text($this->msg['cms_module_common_selector_article_type_from']) . "</label>
                </div>
			</div>";
		
		return $form;
	}
	public function save_form() {
		$this->parameters = array();
		
		return parent::save_form();
	} 
	
	/*
	 * Retourne la valeur sélectionné
	 */
	public function get_value() {
		return $this->value;
	}
}