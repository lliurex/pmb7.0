<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_contribution_datatype_thumbnail.class.php,v 1.1.2.1 2021/04/06 09:00:00 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once $class_path.'/onto/common/onto_common_datatype.class.php';
require_once $class_path.'/upload_folder.class.php';
require_once $class_path.'/explnum.class.php';


/**
 * class onto_common_datatype_small_text
 * Les méthodes get_form,get_value,check_value,get_formated_value,get_raw_value
 * sont éventuellement à redéfinir pour le type de données
 */
class onto_contribution_datatype_thumbnail extends onto_common_datatype_file {

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/
	
	
	public function check_value(){
	    if (is_string($this->value) && (strlen($this->value) < 131072)) return true;
		return false;
	}
	
	public static function get_values_from_form($instance_name, $property, $uri_item) {
		$var_name = $instance_name."_".$property->pmb_name;
		global ${$var_name};

		$file = array();
		if (isset($_FILES[$instance_name."_".$property->pmb_name]) && $_FILES[$instance_name."_".$property->pmb_name]["name"][0]["value"] != "" && $_FILES[$instance_name."_".$property->pmb_name]["tmp_name"][0]["value"] != "") {
			$file = $_FILES[$instance_name."_".$property->pmb_name];
		}
		//TODO: Revoir si on ajoute la suppression de la vignette
		if(count($file)) {
			$blob =  base64_encode(construire_vignette("","",$file['tmp_name'][0]['value']));
			$values[] = array(
			    'value' => json_encode(["name"=> $file['name'][0]['value'], "thumbnail" => $blob] ),
				'type' => $_POST[$var_name][0]['type']
			);
			${$var_name} = $values;
		} else if (self::get_properties_from_uri($uri_item)["thumbnail"]) {
			$values[] = array(
			    'value' => self::get_properties_from_uri($uri_item)["thumbnail"],
				'type' => $_POST[$var_name][0]['type']
			);
		    ${$var_name} = $values;
		}
		return parent::get_values_from_form($instance_name, $property, $uri_item);
	}
	
	public static function get_valid_file_path($file_path) {
		$file_path = str_replace('//', '/', $file_path);
		if (!file_exists($file_path)) {
			return $file_path;
		}
		$i = 1;
		$file_info = pathinfo($file_path);
		do {
			$file_path = $file_info['dirname'].'/'.$file_info['filename'].'_'.$i.'.'.$file_info['extension'];
			$i++;
		} while (file_exists($file_path));
		return $file_path;
	}
	
	public function get_value($all = false) {
	    if ($all && $this->value) {
	        return json_decode($this->value);
	    } 
	    
        return json_decode($this->value)->name;
	}
} // end of onto_common_datatype_small_text