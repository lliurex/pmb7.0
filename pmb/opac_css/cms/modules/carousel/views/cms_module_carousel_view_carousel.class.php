<?php
// +-------------------------------------------------+
// � 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_carousel_view_carousel.class.php,v 1.31.2.4 2021/02/12 11:00:54 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
require_once($include_path."/h2o/h2o.php");

class cms_module_carousel_view_carousel extends cms_module_common_view_django{
	
	
	public function __construct($id=0){
		$this->use_jquery = true;
		$this->default_template = "
<ul id='carousel_{{id}}'>
	{% for record in records %}
		<li class='{{id}}_item'>
			<a href='{{record.link}}' title='{{record.title}}'>
				<img src='{{record.vign}}' alt=''/>
				<br />
			</a>
		</li>
	{% endfor %}
</ul>
";
		parent::__construct($id);
	}
	
	
	public function get_form(){
		if(!isset($this->parameters['css']) || !$this->parameters['css']){
			$this->parameters['css'] = (isset($this->managed_datas['css']) ? $this->managed_datas['css'] : '');
		}
		if (!isset($this->parameters["mode"]))				$this->parameters["mode"] = "";
		if (!isset($this->parameters["speed"]))				$this->parameters["speed"] = "";
		if (!isset($this->parameters["pause"]))				$this->parameters["pause"] = "";
		if (!isset($this->parameters["display_quantity"]))	$this->parameters["display_quantity"] = "";
		if (!isset($this->parameters["slide_quantity"]))	$this->parameters["slide_quantity"] = "";
		if (!isset($this->parameters["autostart"]))			$this->parameters["autostart"] = "";
		if (!isset($this->parameters["autohover"]))			$this->parameters["autohover"] = "";
		if (!isset($this->parameters["pager"]))				$this->parameters["pager"] = "";
		if (!isset($this->parameters["used_template"]))		$this->parameters["used_template"] = "";
		
		$form = "
			<div class='row'>
				<div class='row'>
					<div class='colonne3'>
						<label for='cms_module_carousel_view_carousel_mode'>".$this->format_text($this->msg['cms_module_carousel_view_carousel_mode'])."</label>
					</div>
					<div class='colonne-suite'>
						<select name='cms_module_carousel_view_carousel_mode'>
							<option value='horizontal' ".($this->parameters['mode'] == "horizontal" ? "selected='selected'" : "").">".$this->format_text($this->msg['cms_module_carousel_view_carousel_mode_horizontal'])."</option>
							<option value='vertical' ".($this->parameters['mode'] == "vertical" ? "selected='selected'" : "").">".$this->format_text($this->msg['cms_module_carousel_view_carousel_mode_vertical'])."</option>
							<option value='fade' ".($this->parameters['mode'] == "fade" ? "selected='selected'" : "").">".$this->format_text($this->msg['cms_module_carousel_view_carousel_mode_fade'])."</option>
						</select>
					</div>
				</div>
				<div class='row'>
					<div class='colonne3'>
						<label for='cms_module_carousel_view_carousel_speed'>".$this->format_text($this->msg['cms_module_carousel_view_carousel_speed'])."</label>
					</div>
					<div class='colonne-suite'>
						<input type='text' name='cms_module_carousel_view_carousel_speed' value='".$this->format_text($this->parameters['speed'])."'/>
					</div>
				</div>
				<div class='row'>
					<div class='colonne3'>
						<label for='cms_module_carousel_view_carousel_pause'>".$this->format_text($this->msg['cms_module_carousel_view_carousel_pause'])."</label>
					</div>
					<div class='colonne-suite'>
						<input type='text' name='cms_module_carousel_view_carousel_pause' value='".$this->format_text($this->parameters['pause'])."'/>
					</div>
				</div>
				<div class='row'>
					<div class='colonne3'>
						<label for='cms_module_carousel_view_carousel_display_quantity'>".$this->format_text($this->msg['cms_module_carousel_view_carousel_display_quantity'])."</label>
					</div>
					<div class='colonne-suite'>
						<input type='text' name='cms_module_carousel_view_carousel_display_quantity' value='".$this->format_text($this->parameters['display_quantity'])."'/>
					</div>
				</div>
				<div class='row'>
					<div class='colonne3'>
						<label for='cms_module_carousel_view_carousel_slide_quantity'>".$this->format_text($this->msg['cms_module_carousel_view_carousel_slide_quantity'])."</label>
					</div>
					<div class='colonne-suite'>
						<input type='text' name='cms_module_carousel_view_carousel_slide_quantity' value='".$this->format_text($this->parameters['slide_quantity'])."'/>
					</div>
				</div>
				<div class='row'>
					<div class='colonne3'>
						<label for='cms_module_carousel_view_carousel_autostart'>".$this->format_text($this->msg['cms_module_carousel_view_carousel_autostart'])."</label>
					</div>
					<div class='colonne-suite'>
						<input type='radio' name='cms_module_carousel_view_carousel_autostart' value='1' ".($this->parameters['autostart'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_carousel_view_carousel_yes'])."
				  &nbsp;<input type='radio' name='cms_module_carousel_view_carousel_autostart' value='0' ".(!$this->parameters['autostart'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_carousel_view_carousel_no'])."
					</div>
				</div>
				<div class='row'>
					<div class='colonne3'>
						<label for='cms_module_carousel_view_carousel_autohover'>".$this->format_text($this->msg['cms_module_carousel_view_carousel_autohover'])."</label>
					</div>
					<div class='colonne-suite'>
						<input type='radio' name='cms_module_carousel_view_carousel_autohover' value='1' ".($this->parameters['autohover'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_carousel_view_carousel_yes'])."
				  &nbsp;<input type='radio' name='cms_module_carousel_view_carousel_autohover' value='0' ".(!$this->parameters['autohover'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_carousel_view_carousel_no'])."
					</div>
				</div>
				<div class='row'>
					<div class='colonne3'>
						<label for='cms_module_carousel_view_carousel_pager'>".$this->format_text($this->msg['cms_module_carousel_view_carousel_pager'])."</label>
					</div>
					<div class='colonne-suite'>
						<input type='radio' name='cms_module_carousel_view_carousel_pager' value='1' ".($this->parameters['pager'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_carousel_view_carousel_yes'])."
				  &nbsp;<input type='radio' name='cms_module_carousel_view_carousel_pager' value='0' ".(!$this->parameters['pager'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_carousel_view_carousel_no'])."
					</div>
				</div>".
				parent::get_form()
				."
				<div class='row'>
					<div class='colonne3'>
						<label for='cms_module_common_view_django_template_record_content'>".$this->format_text($this->msg['cms_module_common_view_django_template_record_content'])."</label>
					</div>
					<div class='colonne-suite'>
						".notice_tpl::gen_tpl_select("cms_module_common_view_django_template_record_content",$this->parameters['used_template'])."
					</div>
				</div>
				<div class='row'>
					<div class='colonne3'>
						<label for='cms_module_carousel_view_carousel_css'>".$this->format_text($this->msg['cms_module_carousel_view_carousel_css'])."</label>
					</div>
					<div class='colonne-suite'>
						<textarea name='cms_module_carousel_view_carousel_css'>".$this->format_text($this->parameters['css'])."</textarea>
					</div>
				</div>
			</div>
		";
		return $form;
	}
	
	
	public function save_form(){
		global $cms_module_carousel_view_carousel_mode;
		global $cms_module_carousel_view_carousel_speed;
		global $cms_module_carousel_view_carousel_pause;
		global $cms_module_carousel_view_carousel_display_quantity;
		global $cms_module_carousel_view_carousel_slide_quantity;
		global $cms_module_carousel_view_carousel_autostart;
		global $cms_module_carousel_view_carousel_css;		
		global $cms_module_common_view_django_template_record_content;
		global $cms_module_carousel_view_carousel_autohover;
		global $cms_module_carousel_view_carousel_pager;
				
		$this->parameters['mode'] = $cms_module_carousel_view_carousel_mode;
		$this->parameters['speed'] = $cms_module_carousel_view_carousel_speed+0;
		$this->parameters['pause'] = $cms_module_carousel_view_carousel_pause+0;
		$this->parameters['display_quantity'] = $cms_module_carousel_view_carousel_display_quantity+0;
		$this->parameters['slide_quantity'] = $cms_module_carousel_view_carousel_slide_quantity+0;
		$this->parameters['autostart'] = $cms_module_carousel_view_carousel_autostart==1 ? true : false;
		$this->parameters['css'] = stripslashes($cms_module_carousel_view_carousel_css);
		$this->parameters['used_template'] = $cms_module_common_view_django_template_record_content;
		$this->parameters['autohover'] = $cms_module_carousel_view_carousel_autohover==1 ? true : false;
		$this->parameters['pager'] = $cms_module_carousel_view_carousel_pager==1 ? true : false;
				
		return parent::save_form();	
	}
	
	
	public function get_headers($datas=array()){
		
		global $base_path;
		$headers = parent::get_headers($datas);
		$headers[]= "<script src='".$base_path."/cms/modules/common/includes/javascript/jquery.bxSlider.min.js'></script>";
		$css_file = $this->get_css_file();
		if($css_file) {
			$headers[] = "<link rel='stylesheet' type='text/css' href='".$css_file."'/>";
		}
		return $headers;
	}
	
	
	public function render($datas){
	    global $base_path;
		$html2return = "";
		if(count($datas['records'])){
			try{
				$id = "carousel_".$this->get_module_dom_id();
				$datas['id']=$this->get_module_dom_id();
				if(!isset($datas['get_vars']) || !$datas['get_vars']){
					$datas['get_vars'] = $_GET;
				}
				if(!isset($datas['post_vars']) || !$datas['post_vars']){
					$datas['post_vars'] = $_POST;
				}
				$template_path = $base_path.'/temp/'.LOCATION.'_cms_carousel_view_'.$this->id;
				if(!file_exists($template_path) || (md5($this->parameters['active_template']) != md5_file($template_path))){
				    file_put_contents($template_path, $this->parameters['active_template']);
				}
				$H2o = H2o_collection::get_instance($template_path);
				$html2return.= $H2o->render($datas);
				
				$html2return.= "
			<script type='text/javascript'>
				jQuery(document).ready(function() {";
				if($this->parameters['mode'] == "horizontal"){		
					$html2return.= "
					var item_width = document.getElementById('".$this->get_module_dom_id()."').offsetWidth/".$this->parameters['display_quantity'].";
					var items = document.getElementsByClassName('".$this->get_module_dom_id()."_item');
					for(var i=0 ; i<items.length ; i++){
						items[i].style.width = item_width+'px';
					}";
				}else{
					$html2return.= "
					var item_width = document.getElementById('".$this->get_module_dom_id()."').offsetHeight/".$this->parameters['display_quantity'].";
					var items = document.getElementsByClassName('".$this->get_module_dom_id()."_item');
					for(var i=0 ; i<items.length ; i++){
						items[i].style.height = item_width+'px';
					}";			
				}
				$html2return.= "
					jQuery('#".$id."').bxSlider({
						mode: '".$this->parameters['mode']."',
						speed: '".$this->parameters['speed']."',
						pause: '".$this->parameters['pause']."',
						auto: true,
						autoStart: ".($this->parameters['autostart'] ? "true" : "false").",
						autoHover: ".((isset($this->parameters['autohover']) && $this->parameters['autohover']) ? "true" : "false").",
						autoControls: false,
						controls:true,
						prevImage: '',
						prevText: '',
						nextImage: '',
						nextText: '',
						startImage: '',
						startText: '',
						stopImage: '',
						//stopText:'',
						pager: ".((isset($this->parameters['pager']) && $this->parameters['pager']) ? "true" : "false").",
						randomStart: false,
						displaySlideQty: ".$this->parameters['display_quantity'].",
						moveSlideQty: ".$this->parameters['slide_quantity']."
					});
				});
			</script>";
			} catch(Exception $e){
			    $html2return = '<!-- '.$e->getMessage().' -->';
			    $html2return .= '<div class="error_on_template" title="' .htmlspecialchars($e->getMessage(), ENT_QUOTES). '">';
				$html2return .= $this->msg["cms_module_common_view_error_template"];
				$html2return .= '</div>';
				
			}
		}
		return $html2return;
	}
	
	
	public function execute_ajax(){
		
		return [];
	}
	
	
	protected function generate_css(){
		return str_replace("{{identifiant_dom_du_cadre}}",$this->get_module_dom_id(),$this->parameters['css']);
	}

	
	protected function get_managed_template_form($cms_template){

		$form ="";
		if($cms_template != "new"){
			$infos = $this->managed_datas['templates'][$cms_template];
		}else{
			$infos = array(
				'name' => "Nouveau Template",
				'content' => $this->default_template
			);
		}
		
		if(!$this->managed_datas) $this->managed_datas = array();
		if (!isset($this->managed_datas['css']) || $this->managed_datas['css'] == ""){
			$this->managed_datas['css'] = "
#{{identifiant_dom_du_cadre}} {
	overflow : hidden;
}
		
#{{identifiant_dom_du_cadre}} .bx-wrapper {
	width : inherit !important;
	height : inherit !important;
}
#{{identifiant_dom_du_cadre}} ul li {
	text-align: center;
}

#{{identifiant_dom_du_cadre}} .bx-wrapper {
	width : inherit !important;
	height : inherit !important;
}

#{{identifiant_dom_du_cadre}} ul li a {
	display: block;
	width: 150px;
	margin: 0 auto;
	text-align: center;
	text-decoration: none;
}
/* pour l'image vide...*/
#{{identifiant_dom_du_cadre}} .bx-wrapper ul li a img {
	height: 145px;
	background-repeat: no-repeat;
	-moz-box-shadow: 1px 1px 2px #cccccc;
    -o-box-shadow: 1px 1px 2px #cccccc;
    -webkit-box-shadow: 1px 1px 2px #cccccc;
    box-shadow: 1px 1px 2px #cccccc;
}";
		}
		//nom
		$form.="
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_common_view_django_template_name'>".$this->format_text($this->msg['cms_module_common_view_django_template_name'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='text' name='cms_module_common_view_django_template_name' value='".$this->format_text($infos['name'])."'/>
				</div>
			</div>";
		//contenu	
		$form.="
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_common_view_django_template_content'>".$this->format_text($this->msg['cms_module_carousel_view_template_item'])."</label>
					".$this->get_format_data_structure_tree("cms_module_common_view_django_template_content")."
				</div>
				<div class='colonne-suite'>
					<textarea name='cms_module_common_view_django_template_content'>".$this->format_text($infos['content'])."</textarea>
				</div>
			</div>";		
		// css
		$form.="
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_carousel_view_carousel_manage_default_css'>".$this->format_text($this->msg['cms_module_carousel_manage_default_css'])."</label>
				</div>
				<div class='colonne-suite'>
					<textarea name='cms_module_carousel_view_carousel_manage_default_css'>".$this->format_text($this->managed_datas['css'])."</textarea>
				</div>
			</div>";
		return $form;
	}
		
	
	public function save_manage_form($managed_datas){
		global $cms_template;
		global $cms_template_delete;
		global $cms_module_common_view_django_template_name,$cms_module_common_view_django_template_content;
		global $cms_module_carousel_view_carousel_manage_default_css;
		
		if($cms_template_delete){
			unset($managed_datas['templates'][$cms_template_delete]);
		}else{
			if($cms_template == "new"){
				$cms_template = "template".(cms_module_common_view_django::get_max_template_id($managed_datas['templates'])+1);
			}
			$managed_datas['templates'][$cms_template] = array(
					'name' => stripslashes($cms_module_common_view_django_template_name),
					'content' => stripslashes($cms_module_common_view_django_template_content),
					'css' => stripslashes($cms_module_carousel_view_carousel_manage_default_css)	
			);
		}		
		return $managed_datas;
	}	

	
	public function get_format_data_structure(){
	    if (static::class == "cms_module_carousel_view_carousel") {
			$datas = new cms_module_carousel_datasource_notices();
			$format_datas = $datas->get_format_data_structure();
 			$format_datas[0]['children'][] = array(
 					'var' => "records[i].content",
 					'desc' => $this->msg['cms_module_carousel_view_carousel_record_content_desc']
	 		);
			$format_datas = array_merge($format_datas,parent::get_format_data_structure());
			return $format_datas;
		}
		return parent::get_format_data_structure();
	}
}
