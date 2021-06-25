<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_procs_edition_ui.class.php,v 1.1.2.3 2021/03/26 10:08:34 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_procs_edition_ui extends list_procs_ui {
	
	protected function add_object($row) {
		global $PMBuserid;
		
		$rqt_autorisation=explode(" ",$row->autorisations);
		if (($PMBuserid==1 || $row->autorisations_all || array_search ($PMBuserid, $rqt_autorisation)!==FALSE) && pmb_strtolower(pmb_substr(trim($row->requete),0,6))=='select') {
			$this->objects[] = $row;
		}
	}
	
	protected function get_button_add() {
		return "";
	}

	protected function get_display_cell($object, $property) {
		switch ($property) {
			default:
				$attributes = array(
				'onclick' => "document.location=\"".static::get_controller_url_base()."&action=execute&id_proc=".$object->idproc."\""
				);
				break;
		}
		$content = $this->get_cell_content($object, $property);
		$display = $this->get_display_format_cell($content, $property, $attributes);
		return $display;
	}
}