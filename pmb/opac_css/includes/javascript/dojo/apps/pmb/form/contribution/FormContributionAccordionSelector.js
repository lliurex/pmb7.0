// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: FormContributionAccordionSelector.js,v 1.1.2.1 2020/07/23 09:17:16 moble Exp $

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
        'dojo/dom',
        'dojo/on',
        'dojo/_base/lang',
        'dojo/request/xhr',
        'dojo/dom-form',
        'dijit/layout/ContentPane',
        'dojo/query',
        'dojo/ready',
        'dojo/topic',
        'dijit/registry',
        'dojo/dom-attr',
        'dojo/dom-geometry',
        'dojo/dom-construct',
        'dojo/dom-style',
        'dijit/layout/LayoutContainer',
        'apps/pmb/form/FormTab',
        'dojox/layout/ContentPane',
        'apps/pmb/form/SubTabAdd',
        'apps/pmb/form/SubTabAdvancedSearch',
        'apps/pmb/form/SubTabSimpleSearch',
        'apps/pmb/form/SubTabResults',
        'apps/pmb/form/form_concept/SubTabConceptResults',
        'dojo/request',
        'dojo/io-query',
        'dojox/widget/Standby',
        'dijit/layout/AccordionContainer',
        'apps/pmb/form/contribution/FormContributionSelector'
        ], function(declare, dom, on, lang, xhr, domForm, ContentPane, query, ready, topic, registry, domAttr, 
        		geometry, domConstruct, domStyle, LayoutContainer, FormTab, ContentPaneDojox, SubTabAdd,
        		SubTabAdvancedSearch, SubTabSimpleSearch, SubTabResults, SubTabConceptResults, request, ioQuery, Standby, AccordionContainer, FormContributionSelector){
		return declare([AccordionContainer, FormContributionSelector], {
			
		})
});