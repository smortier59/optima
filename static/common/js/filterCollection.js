/* Author  : Gaetan // Novastream 
*  Project :
*  Class : Filter Collection 
*/
(function($) {
	Nova.FilterCollection = (function() {

	  //------ Membres privés ---------
	  var defaults = {
	  	$listContainerId : $('#conditions-container'), 		// le container global
	  	name : 'Filtre sans titre',							// le nom du filtre	
	  	elements : [], 										// le tableau d'élément de filtrage (filterElement)
	  	logic : {}, 										// les liaisons logiques entre les éléménts de filtres
	  	result : null, 										// le resultat cumulé des filtres
	  	sharable : false									// le filtre est-il partagé
	  }

	  //------ Constructeur ---------
	  var FilterCollection = function(config){
	  	this.params = $.extend({},defaults,config);
	  	this.init();
	  };

	  //------ Prototype ---------
	  FilterCollection.prototype={
	  	constructor:FilterCollection,

	  	// mise en place des différents écouteurs et subscribe
	  	init : function(){
	  		var that = this;
	  		$.subscribe('filterElementDestroyed',function(e,data){	  			
	  			that.removeToCollection(data);
	  		});
	  	},
	  	
	  	// créer une collection d'élément de filtre ( pour le cas d'un update de filtre)
	  	buildCollection : function(data){	  		
	  		//data = jQuery.parseJSON(data);
	  		this.params.name = data.name;
	  		if (data.name)
	  			$("#titreFiltre").val(data.name);

	  		this.params.sharable = data.sharable;	  	

	  		if(this.params.sharable === false){
	  			$("input#partage").prop('checked', false);
	  		}
	  		$("#nb-result").text(data.total);
	  		var elements = data.elements;
	  		
	  		//boucler sur les elements issus de data (donnees ajax)
	  		for(var i=0; i<elements.length; i++) { 
	  			if(elements[i]){
	  				var newFilterElement = new Nova.FilterElement({
			 			id : 'Element-'+Math.floor((Math.random()*10000)+1).toString(),
			 			module : elements[i].module,
			 			field :  elements[i].field,
			 			traduction : elements[i].traduction,
			 			operator : {
			 				value : elements[i].operator.value,
			 				name : elements[i].operator.name
			 			},
			 			value : elements[i].value,
						valueTrad : elements[i].valueTrad, 
						value_sup : elements[i].value_sup, 
						value_sup_trad : elements[i].value_sup_trad, 
						deprecated : elements[i].deprecated, 
			 			result: elements[i].result		 			
			 		});

		 			this.addToCollection(newFilterElement);	  				
	  			}	  			
	  		}
	  	},

	  	// ajoute un élément à la collection de filtre
	  	addToCollection : function(filterElement){
	  		this.params.elements.push(filterElement);
	  		this.addInInterface(filterElement)
	  	},

	  	// ajoute un élément à l'interface
	  	addInInterface : function(filterElement){

	  		var that = this;
	  		var module = filterElement.params.module;
  			$.publish('filterAddToInterface');   	
  			html = Nova.Templates.filterElement({
  				id:filterElement.params.id , 
				field: filterElement.params.field, 
				value : filterElement.params.value, 
				valueTrad : filterElement.params.valueTrad, 
				value_sup : filterElement.params.value_sup, 
				value_sup_trad : filterElement.params.value_sup_trad, 
				operator: filterElement.params.operator.value, 
				operatorTrad : filterElement.params.operator.name, 
				traduction : filterElement.params.traduction, 
				result:filterElement.params.result,
				deprecated:filterElement.params.deprecated
			});
  			that.params.$listContainerId.find('.condition-list[data-module='+module+']').prepend(html);
  			filterElement.addListener();
	  	},

	  	// enleve un élément à la collection de filtre
	  	removeToCollection : function(filterElement){
	  		this.params.elements = _.without(this.params.elements,filterElement);
	  	},

	  	// Met à jour une collection d'éléments de filtre
	  	update : function(){
	  		$.publish('collectionUpdated');
	  	},
	  	destroy : function(){
	  		$.publish('collectionDestroyed');
	  	}
	  };
	  return FilterCollection;
	})(); 
}(jQuery));