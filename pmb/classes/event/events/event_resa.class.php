<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: event_resa.class.php,v 1.2.4.1 2020/05/07 10:26:52 jlaurent Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once $class_path.'/event/event.class.php';

class event_resa extends event {
    protected $resa;
    protected $result;
    protected $empr_id;
    
    public function get_resa() {
        return $this->resa;
    }
    
    public function set_resa($resa){
        $this->resa = $resa;
    }
    
    public function get_empr_id() {
        return $this->empr_id;
    }
    
    public function set_empr_id($id) {
        $this->empr_id= $id;
    }
    
    public function get_result() {
        return $this->result;
    }
    
    public function set_result($result){
        $this->result = $result;
    }
}
