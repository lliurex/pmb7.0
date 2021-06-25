// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ContributionFormEdit.js,v 1.4.8.4 2021/02/24 09:20:17 qvarin Exp $

define(	[
    'dojo/_base/declare', 
    'dojo/_base/lang', 
    'dojo/topic',
    'apps/pmb/gridform/FormEdit',
    'dojo/dom',
    'dojo/dom-attr',
    'dojo/query',
    'dojo/dom-style',
    'dojo/dom-construct'
], function(declare,lang, topic, FormEdit, dom, domAttr, query, domStyle, domConstruct){
	return declare(FormEdit, {
		initialized: null,
		resetGrid: false,
		constructor: function(){
			this.initialized = false;
			domConstruct.destroy(this.btnEdit);
		},
		saveCallback: function(response){
			if(response.status == true){
				if (this.btnSave  && !this.resetGrid) {
					var returnURL = dom.byId('return_url').value;
					if (returnURL) {
						window.location = returnURL; 
					}
				}
				
				if (this.resetGrid) {
					this.resetGrid = false;
				}
			}else{
				alert(pmbDojo.messages.getMessage('grid', 'grid_js_move_saved_error'));
			}
		},
		initializationCallback : function() {
			this.btnEditCallback();	
			this.initialized = true;
		},
		btnEditCallback : function(evt) {
			switch (this.state) {
				case 'std':
					this.state = 'inedit';
					var disableButtonsForm = query('#form-contenu input[type=button]');
					if (disableButtonsForm.length) {
						for (var i = 0; i < disableButtonsForm.length; i++) {
							if (domAttr.get(disableButtonsForm[i], 'onclick')) {
								domAttr.remove(disableButtonsForm[i], 'onclick');
							}
							domStyle.set(disableButtonsForm[i], 'color', '#aaa');
						}
					}
					
					var disableImgsForm = query('#form-contenu img[class=img_plus]');
					if (disableImgsForm.length) {
						for (var i = 0; i < disableImgsForm.length; i++) {
							disableImgsForm[i].remove();
						}
					}
					this.parseDom();
					domAttr.set(this.btnEdit, 'value', pmbDojo.messages.getMessage('grid', 'grid_js_move_back'));
					break;
				case 'inedit':
					this.state = 'std';
					this.unparseDom();
					this.getDatas();
					domAttr.set(this.btnEdit, 'value', pmbDojo.messages.getMessage('grid', 'grid_js_move_edit_format'));
					break;
			}
		},
		btnOriginFormatCallback: function(evt) {
			this.state = 'std';
			this.resetGrid = true;
			const promise = new Promise((resolve, reject) => {
				resolve(this.inherited(arguments));
			});
			
			promise.then(() => {
				this.initialized = false;
				this.initializationCallback();
			});
		},
		removeJsDom: function(){
			  var cleanElts = query('#zone-container > div');
			  for(var i=0; i<this.originalFormat.length ; i++){
				  domConstruct.place(dom.byId(this.originalFormat[i].id), dom.byId('zone-container'), 'last');
				  dom.byId(this.originalFormat[i].id).className = this.originalFormat[i].class;
			  }

			  for(var i=0; i<cleanElts.length ; i++){
			  	if(cleanElts[i].getAttribute('movable') == null){
			  		domConstruct.destroy(cleanElts[i]);	
			  	}
			  }
			  this.zones = new Array();
			  if(this.btnEdit) this.signalEditFormat = on(this.btnEdit,'click', lang.hitch(this, this.btnEditCallback));
		},
		
		buildGrid: async function(datas) {
			
			// Suppression des noeuds contenant l'editeur HTML
			var text_areas_with_tinymce = this.removeTinyEditor();
			
			var activeElement = document.activeElement;
			
			// On stocke le premier niveau des enfants de zone-container des div non movable (nettoye apres traitement)
			var cleanElts = query('#zone-container > div:not([movable="yes"])', this.context);
			
			// Prefixe des proprietes
			var prefixNode = document.getElementById('prefix');
			const prefix = prefixNode.value;
			
			// Grille sauvegarder en base
			const gridSaved = new Object();
			if(typeof datas != 'undefined' && datas != ""){
				this.savedScheme = JSON.parse(datas);
			}
			
			// Liste des champs ordonnee du formulaire
			const gridForm = new Object();
			
			// Tableau qui vas contenir la liste des div de chaque propriete 
			const elementsNode = new Object();
			
			// On extrait la liste des el0Child_X de la grille
			if (this.savedScheme) {
				for(var i=0 ; i < this.savedScheme.length ; i++) {
					for(var j=0 ; j < this.savedScheme[i].elements.length ; j++) {
						let propertyName = j;
						if (this.savedScheme[i].elements[j].propertyName) {
							propertyName = this.savedScheme[i].elements[j].propertyName;
						}
						gridSaved[this.savedScheme[i].elements[j].propertyName] = this.savedScheme[i].elements[j].nodeId;
					}
				}
			}
			
			// On extrait la liste des el0Child_X du formulaire
			var currentElts = query('div[movable="yes"]', this.context);
			for(var i=0 ; i < currentElts.length ; i++) {
				// On récupère la première div de l'element
				var div = query('#' + currentElts[i].id + '>div');
				let propertyName = "";
				if (div && div[0]) {
					// On recupere le type de propriete
					let idNode = div[0].id.toLowerCase();
					let values = idNode.split(prefix.toLowerCase()+'_');
					propertyName = values[1];
				}
				elementsNode[propertyName] = currentElts[i];
				domAttr.set(currentElts[i], 'data-pmb-propertyName', propertyName);
				gridForm[propertyName] = currentElts[i].id;
			}
			
			// Grille Finale avec les elements dans l'ordre
			var grid = new Object();
			
			// Tableau des el0Child_X qu'on a déjà sauvegarder
			if (Object.values(gridSaved).length < 0) {
				var elementIdsSaved = Object.values(gridSaved);
			} else {
				var elementIdsSaved = new Array();
			}
			
			// Tableau des el0Child_X present dans le formulaire
			var elementIdsForm = Object.values(gridForm);
			// Tableau des el0Child_X qu'on ne connais pas 
			var newElementIds = new Array();
			
			if (elementIdsSaved.length > 0) {
				for(var i=0 ; i < elementIdsForm.length ; i++) {
					if (elementIdsSaved.indexOf(elementIdsForm[i]) === -1) {
						newElementIds.push(elementIdsForm[i])
					}
				}
			}
			
			// Liste des element mal place
			var elements = new Object();
			
			if (Object.values(gridSaved).length < 0) {
				for (key in gridForm) {
					if (useGridSave) {
						if (gridForm[key] != gridSaved[key]) {
							// L'element est mal place.
							elements[key] = gridForm[key];
						} else {
							// L'element est dans la bonne div.
							grid[key] = gridSaved[key];
						}
					}
				}
			} else {
				// On fait un format d'origine
				grid = gridForm;
			}

			// On repositionne les elements
			if (elements.length > 0) {
				for (propertyName in elements) {
					if (gridSaved[propertyName]) {
						grid[propertyName] = gridSaved[propertyName];
						delete elements[propertyName];
					} else {
						// Nouveau champs on lui donne n'importe quel el0Child_X
						if (newElementIds.length != 0) {
							grid[propertyName] = newElementIds[newElementIds.length-1];
							newElementIds.splice(newElementIds.length-1, 1);
							delete elements[propertyName];
						} else {
							let temp = await Object.values(grid);
							let number = temp.length+1;
							grid[propertyName] = 'el0Child_' + number;
							delete elements[propertyName];
						}
					}
				}
			}
			
			// On redéfinis les id des noeuds
			for (propertyName in grid) {
				if (elementsNode[propertyName].id != grid[propertyName]) {
					elementsNode[propertyName].id = grid[propertyName];
				}
			}
			
			// On la place les elements que l'on connais
			if (this.savedScheme) {
				for(var i=0 ; i < this.savedScheme.length ; i++){
					 await this.buildZone(this.savedScheme[i], currentElts);
				}
			}
			
			await this.getDefaultZones();
			
			// Il nous reste des elements a placer
			if(currentElts.length) {
				
				if(!this.existsDefaultZone('el0')) {
					this.addDefaultDOMZone('el0');
				}
				
				if (this.originalZones.length) {
					for(var i=0 ; i < this.originalZones.length ; i++){
						this.addDefaultDOMZone(this.originalZones[i].id);
					}
				}
				
				var nbColumn=1;
				var lastNbColumn = 1;
				var columnInProgress = 0;
				
				for(var i=0; i < currentElts.length; i++){
					
					var domNode = this.getDOMZoneFromDOMElement(currentElts[i]);
					if(columnInProgress == 0) {
						var parentDiv = domConstruct.create('div', {class:'container-div row'}, domNode, 'last');
					}
					
					var result = /colonne([2-5]|_suite)/.exec(currentElts[i].className);
					if(result) {
						if (result[1] == '_suite') {
							nbColumn = lastNbColumn;
						} else {
							nbColumn = result[1];
						}
					} else {
						nbColumn = 1;
					}
					
					if (parentDiv) {
						domConstruct.place(currentElts[i], parentDiv, 'last');
					}
					domStyle.set(currentElts[i], 'display', 'block');
					
					columnInProgress++;
					lastNbColumn = nbColumn;
					if(nbColumn == columnInProgress){
						columnInProgress = 0;
						nbColumn = 1;
					}
				}
			}
			
			// Ajout des noeuds contenant l'editeur HTML
			this.addTinyEditor(text_areas_with_tinymce);
			// Traitement terminé, on nettoye
			this.cleanRecursiveElts(cleanElts, ' > div:not([movable="yes"])');
			
			activeElement.focus();
			
			if(!this.initialized){
				this.initializationCallback();	
			}
			
		},
		getStruct: function(){
			var JSONInformations = new Array();
			if(this.zones.length) {
				for(var i=0; i < this.zones.length ; i++) {
					var zoneInformations = this.zones[i].getJSONInformations();
					for(var j=0; j < this.zones[i].elements.length ; j++) {
						let node = document.getElementById(this.zones[i].elements[j].nodeId);
						zoneInformations.elements[j].propertyName = domAttr.get(node, 'data-pmb-propertyName');
					}
					JSONInformations.push(zoneInformations);
				}
			}
			if(!this.type) {
				var currentUrl = window.location;
				this.type = /categ=(\w+)&?/g.exec(currentUrl)[1];
				if(this.type == 'authperso'){
					var authPerso = /id_authperso=(\w+)&?/g.exec(currentUrl)[1];
					this.type += '_'+authPerso;
				}
			}
			var returnedInfos = {zones: JSONInformations, genericType: this.type};
			return returnedInfos;
		}
	});
});