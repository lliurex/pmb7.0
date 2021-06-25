<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: extended_search_dnd.tpl.php,v 1.2.6.1 2020/12/22 15:39:05 btafforeau Exp $

global $extended_search_dnd_tpl, $javascript_path;

$extended_search_dnd_tpl = '
<link rel="stylesheet" type="text/css" href="'.$javascript_path.'/dojo/dojox/grid/resources/Grid.css">
<link rel="stylesheet" type="text/css" href="'.$javascript_path.'/dojo/dojox/grid/resources/claroGrid.css">
<div id="extended_search_dnd_container" data-dojo-type="dijit/layout/BorderContainer" data-dojo-props="splitter:true" style="height:800px;width:100%;">
</div>
<script type="text/javascript">
	require(["apps/search/SearchController", "dojo/domReady!"], function(SearchController){
		var searchController = new SearchController();
	});
</script>';