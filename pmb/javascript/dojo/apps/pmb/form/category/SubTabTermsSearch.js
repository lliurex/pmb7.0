// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SubTabTermsSearch.js,v 1.1.6.2 2021/01/28 11:18:41 dgoron Exp $


define([
        'dojo/_base/declare',
        'dojo/on',
        'dojo/_base/lang',
        'dojo/request',
        'dojo/dom-form',
        'dojo/dom-attr',
        'dojox/layout/ContentPane',
        'dojo/query',
        'dojo/topic',
        ], function(declare, on, lang, request, domForm, domAttr, ContentPane, query, topic){
		return declare([ContentPane], {
			
			constructor: function() {
				
			},
			postCreate: function() {
				this.inherited(arguments);
			},
			onLoad: function(){
				
			},
			onDownloadEnd: function(){
				var searchButton = query('input[id="launch_terms_search_button"]', this.containerNode)[0];
				this.form = searchButton.form;			
				
				on(this.form, 'submit', lang.hitch(this, this.postForm));
				
				this.getParent().resizeIframe();
			},
			destroy: function(){
				this.inherited(arguments);
			},
			postForm: function(e){
				e.preventDefault();
				request(this.parameters.selectorURL+"&action=terms_results_search", {
					data: domForm.toObject(this.form),
					method: 'POST',
					handleAs: 'html',
				}).then(lang.hitch(this, function(data){
					topic.publish('SubTabTermsSearch', 'SubTabTermsSearch', 'printResults', {results: data, origin: this.parameters.selectorURL + "&action=terms_results_search&search_type=term", search_type:'term'});
					this.connectLinks();
				}));
				return false;
			},
			connectLinks: function() {
				let searchLinks = query('a[data-name="term_show"]', this.ownerDocumentBody);
				if (searchLinks.length) {
					//Liens détéctés, application d'un evenement pour la publication des résultats
					searchLinks.forEach(lang.hitch(this, function(searchLink) {
						on(searchLink, 'click', lang.hitch(this, this.searchLinkClicked, searchLink));
					}));
				}
			},
			searchLinkClicked: function(searchLink, e) {
				e.preventDefault();
				request(this.parameters.selectorURL + "&action=terms_show_notice&term=" + encodeURIComponent(searchLink.text), {
					data: '',
					method: 'POST',
					handleAs: 'html',
				}).then(lang.hitch(this, function(data) {
					topic.publish('SubTabTermsSearch', 'SubTabTermsSearch', 'printResults', {results: data, origin: this.parameters.selectorURL+"&action=terms_results_search"});
					this.connectLinks();
				}));
				return false;
			} 	
		})
});