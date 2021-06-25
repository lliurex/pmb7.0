<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_parameters_mail_ui.class.php,v 1.1.2.4 2021/03/26 10:29:18 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_parameters_mail_ui extends list_parameters_ui {
	
	public function init_filters($filters=array()) {
		$filters['types_param'] = array(
				'acquisition_pdfcde',
				'mailretard',
				'pdflettreresa',
				'mailrelanceadhesion'
		);
		parent::init_filters($filters);
	}
}