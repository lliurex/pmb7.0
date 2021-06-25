<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: authority_page_author.class.php,v 1.3.6.1 2020/05/14 07:42:53 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");


require_once($class_path."/authorities/page/authority_page.class.php");

/**
 * class authority_page_author
 * Controler d'une page d'une autorit� auteur
 */
class authority_page_author extends authority_page {
	
	/**
	 * Constructeur
	 * @param int $id Identifiant de l'auteur
	 */
	public function __construct($id) {
		$this->id = $id*1;
		$query = "select author_id from authors where author_id = ".$this->id;
		$result = pmb_mysql_query($query);
		if($result && pmb_mysql_num_rows($result)) {
			//$this->authority = new authority(0, $this->id, AUT_TABLE_AUTHORS);
			$this->authority = authorities_collection::get_authority('authority', 0, ['num_object' => $this->id, 'type_object' => AUT_TABLE_AUTHORS]);
		}
	}
	
	protected function get_title_recordslist() {
		global $msg, $charset;
		if($this->authority->get_object_instance()->type == 72) {
			//Congr�s
			return htmlentities($msg['documents_disponibles_meme_congres'], ENT_QUOTES, $charset);
		} else {
			return htmlentities($msg['documents_disponibles_meme_auteur'], ENT_QUOTES, $charset);
		}
	}
	
	protected function get_join_recordslist() {
		return "JOIN responsability ON notice_id=responsability_notice";
	}
	
	protected function get_clause_authority_id_recordslist() {
	    if($this->id) {
    		$rqt_auteurs = "select author_id as aut from authors where author_see='".$this->id."' and author_id!=0 ";
    		$rqt_auteurs .= "union select author_see as aut from authors where author_id='".$this->id."' and author_see!=0 " ;
    		$res_auteurs = pmb_mysql_query($rqt_auteurs);
    		$clause_auteurs = " in ('".$this->id."' ";
    		while(($id_aut=pmb_mysql_fetch_object($res_auteurs))) {
    			$clause_auteurs .= ", '".$id_aut->aut."' ";
    			$rqt_auteursuite = "select author_id as aut from authors where author_see='".$id_aut->aut."' and author_id!=0 ";
    			$res_auteursuite = pmb_mysql_query($rqt_auteursuite);
    			while(($id_autsuite=pmb_mysql_fetch_object($res_auteursuite))) $clause_auteurs .= ", '".$id_autsuite->aut."' ";
    		}
    		$clause_auteurs .= " ) " ;
	    } else {
	        $clause_auteurs = " in (0) ";
	    }
		return "responsability_author ".$clause_auteurs;
	}
	
	protected function get_mode_recordslist() {
		return "author_see";
	}
}