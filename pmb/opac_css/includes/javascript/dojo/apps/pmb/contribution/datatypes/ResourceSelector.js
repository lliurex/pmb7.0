// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ResourceSelector.js,v 1.6.6.7 2021/01/11 11:39:09 moble Exp $


define([
        'dojo/_base/declare',
        'dijit/_WidgetBase',
        'dojo/on',
        'dojo/_base/lang',
        'dojo/io-query',
        'dojo/request',
        'dojo/dom',
        'dojo/dom-construct'
], function(declare, _WidgetBase, on, lang, ioQuery, request, dom, domConstruct){
	return declare([_WidgetBase], {

		target: './ajax_selector.php',
		
		datalist: [],
		
		datalistNode: null,
		
		valueNode: null,
		
		draftNode: null,
		
		templateNode : null,
		
		lastValue: null,
		
		postCreate: function() {
			this.inherited(arguments);
			this.datalistNode = dom.byId(this.domNode.id + '_list');
			this.valueNode = dom.byId(this.valueNodeId);
			this.draftNode = dom.byId(this.isDraftNodeId);
			this.templateNode = dom.byId(this.templateNodeId);
			on(this.domNode, 'keyup', lang.hitch(this, this.updateDatalist));
			on(this.domNode, 'input', lang.hitch(this, this.updateValue));
			if (this.valueNode.value) {
				this.updateTemplate();
			}
		},
	
		updateDatalist: function(e) {
			if (this.domNode.value == this.lastValue) {
				return false;
			}
			this.lastValue = this.domNode.value; 
			var url = this.target+'?handleAs=json&completion='+this.completion+'&autexclude='+this.autexclude+'&param1='+this.param1+'&param2='+this.param2+'&from_contrib=1';
			url = url + '&datas=' + this.domNode.value;
			request.get(url, {
				handleAs: 'json'
			}).then(lang.hitch(this, function(data) {
				this.setDatalist(data);
			}), function(err){console.log(err);});
		},
		
		setDatalist: function(data) {
			domConstruct.empty(this.datalistNode);
			this.datalist = [];
			for (var element of data) {
				this.datalist[element.value] = element.label;
				domConstruct.create('option', {innerHTML: element.label}, this.datalistNode);
			}
			this.domNode.focus();
		},
		
		updateValue: function(e) {
			for (var id in this.datalist) {
				if (this.datalist[id].trim() == this.domNode.value) {
					this.removeDraft();
					this.valueNode.value = id;
					this.domNode.setCustomValidity("");
					this.domNode.blur();
					this.updateTemplate();
					return true;
				}
			}
			this.valueNode.value = '';
			this.domNode.setCustomValidity("Invalid field.");
			return false;
		},
		
		updateTemplate : function() {
			//recupération du template
			let params = ioQuery.queryToObject(document.location.search.substring(1));
			var url = './ajax.php?module=ajax&categ=contribution&sub=get_resource_template&type='+this.completion+'&id='+encodeURIComponent(this.valueNode.value)+'&area_id='+params.area_id;			
			request.get(url, {
				handleAs: 'text'
			}).then(lang.hitch(this, function(tpl) {
				if (this.templateNode) {
					this.templateNode.innerHTML = tpl;
				}
			}), function(err){console.log(err);});
		},
		
		removeDraft: function () {
			if (this.domNode) {
				var parent = this.domNode.parentNode
				if (parent) {
					var parentContainer = parent.parentNode
					if (parentContainer && parentContainer.classList.value.includes('contribution_draft')) {
						parentContainer.classList.remove('contribution_draft')
					}
				}
			}
			
			if (this.draftNode && this.draftNode != 0) {
				this.draftNode.value = 0;
			}
			
			var editButtonNode = dom.byId(this.baseId+"_edit");
			if (editButtonNode) {
				editButtonNode.type = "hidden";
			}
		},
	})
});