<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: html_editor.inc.php,v 1.5.2.2 2021/03/08 16:45:20 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $f_message, $current_module, $msg;

print stripslashes($f_message)."
<form class='form-$current_module' method='post' name='form_message' id='form_message' action='./admin.php?categ=html_editor' />
<h3>".$msg['admin_html_editor']."</h3>
<div class='form-contenu'>
	<div class='row'>					
		<textarea id='f_message' name='f_message' cols='120' rows='40'>".stripslashes($f_message)."</textarea>
	</div>
</div>
</form>";