/* Author  : Gaetan // Novastream
*  Project :
*  Class :
*/
(function($) {
	Nova.Page = (function() {

	  //------ Membres privés ---------
	  var defaults = {
	  	module:null,
	  	id_filtre: Nova.id_filtre || null
	  }

	  //------ Constructeur ---------
	  var Page = function(config){
	  	this.params = $.extend({},defaults,config);
	  	this.loadTemplates();
	  };

	  //------ Prototype ---------
	  Page.prototype={
	  	constructor:Page,

	  	/**
	  	* creation des instances d'objet contenu dans la page
	  	*/
	  	init:function(){
	  		var that = this;
	  		if (this.params.module){
	  			var call = Nova.DataBridge.createCollection(this.params.module, this.params.id_filtre);
	  			call.done(function(data){
	  				//cas d'un update
	  				if (data.result){
	  					that.filterCollection = new Nova.FilterCollection();
	  					that.filterCollection.buildCollection(data.result);
	  					that.filterBuilder = new Nova.FilterBuilder({collection:that.filterCollection});
	  				//cas nouveau filtre "sans titre"
	  				}else{
	  					that.filterCollection = new Nova.FilterCollection();
	  					that.filterBuilder = new Nova.FilterBuilder({collection:that.filterCollection});
	  				}
	  			});
	  		}
	  	},

	  	/**
	  	* Chargement des templates
	  	*/
	  	loadTemplates : function(){
	  		var that = this;
	  		Nova.Templates = {};
	  		var XHRs = [],
	  			$link = $("link[type='application/x-handlebars-template']");

			$link.each(function() {
				var url = $(this).attr("src");
				var templateName = $(this).data('template');
				XHRs.push($.get(url, function(data) {
					var template = Handlebars.compile(data);
					Nova.Templates[templateName] = template;
				}));
			});
			$.when.apply($,XHRs).then(function(){
				that.init();
				$.publish('templateLoaded');
			});
	  	}
	  };
	  return Page;
	})();

	//------DOM IS FUCCKING READY !!!
	$(function(){
		Nova.myPage = new Nova.Page({module:Nova.module});
	});

}(jQuery));