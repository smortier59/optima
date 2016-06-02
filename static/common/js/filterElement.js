/* Author  : Gaetan // Novastream
*  Project :
*  Class : Filter Element
*/
(function($) {
	Nova.FilterElement = (function() {
	  //------ Membres privés ---------
	  var defaults = {}

	  /*------ Constructeur ---------
	  * @params config {object}
	  *					- id {string} : filterElement ID
	  *					- module {string} : type de module (affaire, contact, ect...)
	  *					- field {string}: le champs sur lequel on filtre
	  *					- operator {obj} :
	  *								- value {string} : operator de comparaison
	  *								- type {string} : (date, list, input) le type de champs permettant de renseigner valeur de l'élement de filtre
	  *					- value {string}	: valeur de l'élement de filtre
	  *					- result	{int}		: résultat après application de l'élèment de filtre
	  */
		var FilterElement = function(config){
		this.params = $.extend({},defaults,config);
	};

	  //------ Prototype ---------
	  FilterElement.prototype={
	  	constructor:FilterElement,

	  	/**
	  	* Ajout de écouteur et subscribe sur une instance
	  	*/
	  	addListener : function(){
	  		var that = this;
	  		this.$id = $('#'+this.params.id);

	 		//listener
	  		this.$id.delegate('.update-link' ,'click',$.proxy(this.openUpdateForm,this));
	  		this.$id.delegate('.suppr-link' ,'click',$.proxy(this.destroy,this));
	  		this.$id.on('click','.update-ok',$.proxy(this.update,this));
	  		this.$id.on('click','.update-annul',$.proxy(this.annul,this));

	  		//Pub/Sub
	  		$.subscribe('openBuilder', function(e){
	  			that.annul();
	  		});
	  		$.subscribe('openUpdate', function(e,data){
	  			if(data.id != that.params.id){that.annul();}

	  		});
	  	},

	  	/**
	  	* Ouverture du formulaire de modif d'un élément
	  	*/
	  	openUpdateForm : function(e){
	  		e.preventDefault();
	  		var that = this;
	  		// on verifie si l'update de l'element et déja ouvert
	  		if (this.$id.hasClass('updateOnProgress')){
	  			this.annul();
	  		}

	  		// on previent les autres elements
	  		this.$id.addClass('updateOnProgress');
	  		$.publish('openUpdate',{id:this.params.id});
	  		this.buildFieldCol();
	  	},

	  	/**
	  	* création du permier champs ( champs sur lequel on filtre)
	  	*/
	  	buildFieldCol : function(type){
	  		var that = this;
	  		// on injecte la vue la colonn de champs de l'élément
	  		var container =  $('<div class="update-form filter-form input-type clearfix">'),

	  		html = Nova.Templates.updateFilter();
	  		container.html(html);
	  		this.$id.find('.item-padding').append(container);

	  		//init des event sur les listes déroulantes de premier niveau
	  		this.$id.find(".select-box").selectbox({
	  			onChange : function(value,inst){
	  			}
	  		});
	  	},

	  	/**
	  	* création du deuxième champs ( opérateur de comparaison)
	  	*/
	  	buildOperatorCol : function(value){},

	  	/**
	  	* création du troisième champs ( valeur)
	  	*/
	  	buildValueCol : function(type){},

	  	/**
	  	* mise à jour d"un element
	  	*/
	  	update : function(){
	  		var that = this;
	  		var call = Nova.DataBridge.filterElementUpdated();
	  		call.done(function(data){
	  			//recuperation du resultats du filtre
	  			$.publish('filterElementUpdated',data);
	  			that.annul();
	  		});
	  	},

	  	/**
	  	* anulation de la modification d'un élèment
	  	*/
	  	annul : function(){
	  		$('#'+this.params.id).find('.update-form').remove();
	  	},

	  	/**
	  	* suppression d'un élément
	  	*/
	  	destroy : function(e){
	  		e.preventDefault();
	  		this.$id.remove();
	  		$.publish('filterElementDestroyed', this);
	  	}
	  };
	  return FilterElement;
	})();
}(jQuery));