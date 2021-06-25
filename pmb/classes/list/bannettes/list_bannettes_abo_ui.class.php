<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_bannettes_abo_ui.class.php,v 1.1.2.6 2021/03/26 10:58:57 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_bannettes_abo_ui extends list_bannettes_ui {
		
	protected function init_default_columns() {
		$this->add_column('name');
		$this->add_column('equations');
		$this->add_column('number_records');
		$this->add_column('send_last_date');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'visible', false);
	}
	
	protected function _get_label_cell_header($name) {
		global $msg, $charset;
	
		switch ($name) {
			case 'dsi_ban_form_nom':
				return 
					"<strong>".htmlentities($msg['dsi_ban_form_nom'],ENT_QUOTES, $charset)."</strong><ul>
					</li><li>".htmlentities($msg['dsi_ban_form_com_gestion'],ENT_QUOTES, $charset)."
					</li><li>".htmlentities($msg['dsi_ban_form_com_public'],ENT_QUOTES, $charset)."
					</li></ul>";
			case 'dsi_ban_date_last_envoi':
				return "<strong>".htmlentities($msg['dsi_ban_date_last_envoi'],ENT_QUOTES, $charset)."</strong>
					<br />(".htmlentities($msg['dsi_ban_date_last_remp'],ENT_QUOTES, $charset).")";
			default:
				return "<strong>".parent::_get_label_cell_header($name)."</strong>";
				
		}
		
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
				case 'equations' :
					return strcmp(strip_tags(static::get_equations($a->id_bannette, $a->proprio_bannette)), strip_tags(static::get_equations($b->id_bannette, $b->proprio_bannette)));
					break;
				default :
					return parent::_compare_objects($a, $b);
					break;
			}
		}
	}
	
	protected function get_cell_content($object, $property) {
		global $charset;
	
		$content = '';
		switch($property) {
			case 'name':
				$content .= "
					<strong>".htmlentities($object->nom_bannette,ENT_QUOTES, $charset)."</strong><ul>
					</li><li>".htmlentities($object->comment_gestion,ENT_QUOTES, $charset)."
					</li><li>".htmlentities($object->comment_public,ENT_QUOTES, $charset)."
					</li></ul>";
				break;
			case 'equations':
				$content .= static::get_equations($object->id_bannette, $object->proprio_bannette);
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_display_cell($object, $property) {
		$onclick="";
		switch($property) {
			case 'name':
				$onclick = "document.location=\"".static::get_controller_url_base()."&id_bannette=".$object->id_bannette."&suite=modif&id_empr=".$object->proprio_bannette."\";";
				break;
			case 'equations':
				$query = "select id_equation from equations, bannette_equation where num_equation=id_equation and proprio_equation='".$object->proprio_bannette."' and num_bannette='".$object->id_bannette."' order by nom_equation " ;
				$result = pmb_mysql_query($query);
				if(pmb_mysql_result($result, 0, 'id_equation')) {
					$onclick = "document.modif_requete_form_".pmb_mysql_result($result, 0, 'id_equation').".submit();";
				}
				break;
			case 'send_last_date':
				$onclick = "document.location=\"./dsi.php?categ=diffuser&sub=lancer\";";
				break;
		}
		$attributes = array(
				'style' => 'vertical-align:top;',
				'onclick' => $onclick,
		);
		$content = $this->get_cell_content($object, $property);
		$display = $this->get_display_format_cell($content, $property, $attributes);
		return $display;
	}
}