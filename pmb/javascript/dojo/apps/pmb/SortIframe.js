// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SortIframe.js,v 1.1.2.1 2021/02/09 10:58:35 qvarin Exp $

define([
	"dojo/_base/declare",
	"dijit/_WidgetBase",
	"dojo/on",
	"dojo/_base/lang",
	"dojo/query",
	"dojo/topic",
	"dojo/request/xhr",
	"dojo/dom-attr",
	"dojox/widget/DialogSimple",
    "dojox/widget/Standby",
    "dijit/registry",
], function(declare, WidgetBase, on, lang, query, topic, xhr, domAttr, DialogSimple, Standby, registry) {
	return declare(WidgetBase, {
		currentDialog: null,
		sortLink: null,
		standby: null,
		msgSuppr: null,
		btnNode: null,
		id: 'sortIframe',
		current_module: null,
		event: [],
		
		buildRendering: function() {
			this.current_module = domAttr.get(document.body, 'page_name');
			if (!this.current_module) {
				this.current_module = "catalog";
			}
			if (this.sortLink.indexOf('?') == -1) {
				this.sortLink += "?module="+ encodeURI(this.current_module)
			} else {
				this.sortLink += "&module="+ encodeURI(this.current_module)
			}
			
			window.agitTri = function(actionTri, idTri) {
				document.sort_form.action_tri.value = actionTri;
				document.sort_form.id_tri.value = idTri;
				topic.publish('agitTri', 'agitTri', 'submitForm', { form: document.sort_form });
			}
			
			window.suppr = function(idTri) {
				if (confirm(this.msgSuppr)) {
					agitTri('supp', idTri);
				}
			}
			
			this.initEvents();
			on(this.btnNode, 'click', lang.hitch(this, this.eventClick));
		},
		
		handleEvents: function(evtClass, evtType, evtArgs) {
			switch (evtClass) {
				case 'agitTri':
					switch (evtType) {
						case 'submitForm':
							this.formSubmit(evtArgs.form);
							break;
					}
					break;
			}
		},
		
		initEvents: function () {
			this.own(
				topic.subscribe('agitTri', lang.hitch(this, this.handleEvents))
			);
		},
		
		eventClick: function() {
			this.currentDialog = new DialogSimple({
				style: "width: 50%;min-heigth:25%;",
				'class': this.current_module,
				executeScripts: true,
				id: "test",
				onHide: lang.hitch(this, this.onHideDialog),
				href: this.sortLink
			});
			this.currentDialog.startup();
			this.currentDialog.show();
			on(this.currentDialog, 'load', lang.hitch(this, this.parseDialog));
		},
		
		onHideDialog: function() {
			this.standby.destroy();
			this.standby = null
			this.currentDialog.destroyRecursive();
			this.currentDialog.destroy();
			this.currentDialog = null
		},

		initStandby: function() {
			if (this.currentDialog) {
				if (!this.standby) {
					this.standby = new Standby({
						target: this.currentDialog.containerNode
					});
					document.body.appendChild(this.standby.domNode);
					this.standby.startup();
				}
				this.standby.show();
			}
		},
		
		shutStandby: function() {
			if (this.standby) {
				this.standby.hide();
			}
		},

		parseDialog: function() {
			var sortList = query(".sort", this.currentDialog.containerNode);
			for (var i = 0; i < sortList.length; i++) {
				on(sortList[i], 'click', lang.hitch(this, this.applySort, sortList[i]));
			}

			var btnCancel = query(".cancel", this.currentDialog.containerNode);
			for (var i = 0; i < btnCancel.length; i++) {
				on(btnCancel[i], 'click', lang.hitch(this, this.resetContentDialog));
			}

			var btnSave = query(".save", this.currentDialog.containerNode);
			for (var i = 0; i < btnCancel.length; i++) {
				on(btnSave[i], 'click', lang.hitch(this, this.saveSort));
			}
		},

		saveSort: function() {
			var form = query("form[name='sort_form']", this.currentDialog.containerNode);
			if (form && form[0]) {
				topic.publish('agitTri', 'agitTri', 'submitForm', { form: form[0] });
			}
		},

		applySort: function(node) {
			var link = domAttr.get(node, 'data-sort_link');
			if (link) {
				if (link.indexOf('?') == -1) {
					link += "?base_noheader=1&module="+ encodeURI(this.current_module)
				} else {
					link += "&base_noheader=1&module="+ encodeURI(this.current_module)
				}
				this.initStandby();
				xhr.get(link).then(lang.hitch(this, function(response) {
					response = JSON.parse(response);
					this.shutStandby();
					if (response.success) {
						this.currentDialog.hide().then(lang.hitch(this, function () {
							topic.publish('SortIframe', 'SortIframe', 'reloadResult');
							this.remove();
						}))
					}
				}))
			}
		},

		formSubmit: function(form) {
			if (form.action_tri.value == "supp") {
				// après la suppression, il faut réafficher la liste des tris
				var resolve = lang.hitch(this, this.resetContentDialog);
			} else {
				var resolve = lang.hitch(this, this.setContentDialog);
			}

			var link = domAttr.get(form, 'action');
			if (link.indexOf('?') == -1) {
				link += "?base_noheader=1&module="+ encodeURI(this.current_module)
			} else {
				link += "&base_noheader=1&module="+ encodeURI(this.current_module)
			}

			this.initStandby();
			xhr.post(link, {
				data: new FormData(form)
			}).then(resolve)
		},

		setContentDialog: function(content) {
			if (this.currentDialog) {
				this.currentDialog.set("content", content);
			}
			this.shutStandby();
		},

		resetContentDialog: function() {
			if (this.currentDialog) {
				this.currentDialog.set("content", "");
				this.currentDialog.set("href", this.sortLink);
			}
			this.shutStandby();
		},
		
		remove: function () {
			this.destroyRecursive();
			this.destroy();
		}

	});
});