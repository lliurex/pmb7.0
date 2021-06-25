// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: FormConceptContainer.js,v 1.5 2018/10/08 16:41:52 arenou Exp $

/*****
 * 
 * C'est cette classe qui aura la lourde responsabilite de mettree en place
 * l'ensemble des onnnnnglet permettant de representer un selecteur
 * 
 * 
 * *Cette classe devra pouvoir être utilisée dans les selecteur comme dans le module
 * de gestion des formulaire. prévoir l'utilisation d'un mod permettant de définir
 * le contexte dans lequel nous nous trouvons 
 * 
 * 
 * 
 */

define([
        'dojo/_base/declare',
        'dijit/layout/TabContainer',
        'apps/pmb/form/form_concept/SubTabSearchConcept',
        'apps/pmb/form/form_concept/SubTabConceptHierarchized',
        'apps/pmb/form/form_concept/SubTabConceptNavigate'
], function(declare, TabContainer, SubTabSearchConcept, SubTabConceptHierarchized, SubTabConceptNavigate){
		return declare([TabContainer], {
			simpleSearchTab: null,   //Onglet rech simple
			extendedSearchTab: null, //Onglet rech multicritere
			resultTab: null,		 //Onglet affichage des résultats de recherche
			newTab: null,
			entity: '',
			constructor: function(parameters) {
				this.parameters = parameters;
			},
			postCreate: function() {
				this.inherited(arguments);
				this.createTabs();
			},
			createTabs: function(){
		  		this.simpleSearchTab = new SubTabSearchConcept({title: pmbDojo.messages.getMessage('selector', 'selector_tab_search_concept'), style: 'width:95%; height:100%;', parameters: this.parameters});
				this.simpleSearchTab.href = this.parameters.selectorURL+'&action=authority_searcher';

				this.hierarchizedTab = new SubTabConceptHierarchized({title: pmbDojo.messages.getMessage('selector', 'selector_tab_hierarchical_search'), style: 'width:95%; height:100%;', loadScripts: true, parameters: this.parameters});
				this.hierarchizedTab.href = this.parameters.selectorURL+'&action=list';
				

				this.navigationTab = new SubTabConceptNavigate({title: pmbDojo.messages.getMessage('selector', 'selector_tab_navigate'), style: 'width:95%; height:100%;', loadScripts: true, parameters: this.parameters});
				this.navigationTab.href = this.parameters.selectorURL+'&action=navigate';
				
				this.simpleSearchTab.resize();
				this.simpleSearchTab.startup();
				
				this.hierarchizedTab.resize();
				this.hierarchizedTab.startup();
				
				this.navigationTab.resize();
				this.navigationTab.startup();
				
				this.addChild(this.simpleSearchTab);
				this.addChild(this.hierarchizedTab);
				this.addChild(this.navigationTab);
				
				
				this.startup();
				this.resize();
			},
			selectChild : function(page,animate) {
				this.inherited(arguments);
				page.resizeIframe();
			},
		})
});