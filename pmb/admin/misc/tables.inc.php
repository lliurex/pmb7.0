<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: tables.inc.php,v 1.15.4.1 2021/03/10 12:33:17 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// on récupére la liste des tables
print "<div class='div-contenu'><div class='row tableListe'>";
print list_misc_tables_ui::get_instance()->get_display_list();
print "</div></div>";