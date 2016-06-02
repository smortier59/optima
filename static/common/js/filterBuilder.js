/* Author  : Gaetan // Novastream
*  Project :
*  Class : Filter Element
*/
(function($) {
	Nova.FilterBuilder = (function() {

	  //------ Membres privés ---------
	  var defaults = {
	  	$el : $('#new-element-builder'),
	  	$opener : $('.add-condition-btn')
	  };

	  var FilterBuilder = function(config){
	  	this.params = $.extend({},defaults,config);
	  	this.init();
	  };

	  //------ Prototype ---------
	  FilterBuilder.prototype={
	  	constructor:FilterBuilder,

	  	/**
	  	* plug des events
	  	*/
	  	init:function(){
	  		var that = this;
	  		$(document).delegate('#add-element','click',$.proxy(this.createNewElement,this));
	  		this.params.$opener.on('click',$.proxy(this.openBuilder,this));
	  		$.subscribe('filterAddToInterface', function(e){
	  			that.closeBuilder();
	  		});
	  		$.subscribe('filterElementDestroyed',function(e,data){
	  			that.updateCounters();
	  		});
	  	},

	  	/**
	  	* ouverture du builder , recupération des data pour les premiers champs
	  	*/
	  	openBuilder : function(e){
	  		e.preventDefault();

	  		var that = this,
	  			$this = $(e.currentTarget),
	  			module = $this.attr('data-module'),
	  			$wrapper = $this.parent('header').siblings('.condition-list'),
	  			$addBtn 	= this.params.$el.find('[type="submit"]'),
				$firstChamps = this.params.$el.find('.first-champs').find(".select-box");


			$addBtn.prop('disabled',true);

	  		// -- recup des champs

	  		var call = Nova.DataBridge.getFirstChampsFromModule(module);
	  		call.done(function(d){
	  			/* Ajout par Morgan car notre result en JSON est different de celui de gaetan */
	  			var result = JSON.parse(d.result);
	  			d = result;

	  			//var html ='<option value="">&nbsp</option>';
	  			var html ='';
	  			for (var i = d.length - 1; i >= 0; i--) {
	  				html+='<option value="'+d[i].module+'.'+d[i].name+'">'+d[i].trad+'</option>';
				};

	  			$firstChamps.html(html);
	  			$firstChamps.selectbox('detach');
	  			$firstChamps.selectbox({
	  				onChange : function(value,inst){
	  					that.updateOperatorField(value,module);
	  				}
	  			});
	  		});


	  		// -- update de l'interface

	  		this.resetValueField();
	  		this.resetOperatorField();

	  		$this.fadeOut(200,function(){
	  			if ($('#new-element-builder').is(':visible')){$('#new-element-builder').hide();}
	  			$wrapper.prepend($('#new-element-builder'));
	  			$('#new-element-builder').attr('data-module',module).fadeIn(200);
	  			that.params.$opener.not($this).show();
	  			$.publish('openBuilder');
	  		});
	  	},

	  	/**
	  	* fermeture du builder
	  	*/

	  	closeBuilder : function(){
	  		var that = this;
	  		this.params.$el.hide()
	  		that.params.$opener.fadeIn(200);
	  		this.resetValueField();
	  		this.resetOperatorField();
	  	},

	  	/**
	  	* creation d'un filterElement et ajout à la collection
	  	*/
	  	createNewElement : function(e){
	  		e.preventDefault();
	  		var module = $(e.target).parents('#new-element-builder').attr('data-module');
	  		var champs1 = $("#chps1").val();
	  		if(!champs1){
	  			champs1 = $("#chps1").value;
	  		}

	  		var  valeur = "";
	  		var  valueTrad = "";

	  		if($(".dynamic-champs2 input").val()){
	  			valeur = $(".dynamic-champs2 input").val();
	  		}else{
	  			valeur = $(".dynamic-champs2 option:selected").val();
	  			valueTrad = $(".dynamic-champs2 option:selected").text();
	  		}

	  		for(var i=0; i<$("#chps1 option").length; i++){
	  			if($("#chps1").val() == $("#chps1 option")[i].value){
	  				ATF.log($("#chps1 option")[i].text);
	  				var traduction = $("#chps1 option")[i].text;
	  			}
	  		}

	  		// Ajout de l'élément à la collection
	  		var id = 'Element-'+Math.floor((Math.random()*10000)+1).toString();
	  		var newFilterElement = new Nova.FilterElement({
		 			id : id,
		 			module : module,
		 			field : champs1,
		 			traduction : traduction,
		 			operator : {
		 				type:'input',
		 				value : $("#chps2").val(),
		 				name : $("#chps2 option:selected").text()
		 			},
		 			value : valeur,
		 			valueTrad : valueTrad,
		 			result: 0
		 	});
		 	this.params.collection.addToCollection(newFilterElement);

		 	this.updateCounters(id);
	  	},

	  	/**
	  	* reset de l'opérateur de comparaison
	  	*/
	  	updateCounters : function(id){
	  		var moduleGeneral = $("#conditions-container header:first").attr("data-module");
	  		var conditions = new Array();
	  		var elements = this.params.collection.params.elements;
    		for(var i=0; i<elements.length; i++){
				if(elements[i]){
					conditions.push({
					  "field"    : elements[i].params.field,
					  "operand"  : elements[i].params.operator.value,
					  "value"    : elements[i].params.value
					});
				}
    		}

	  		$.ajax({
				dataType: "json",
				type:'post',
				data:{ conditions:conditions },
				url: moduleGeneral+',getFilterCountSimulations.ajax?num=1'
			}).done(function(data) {
				//data = JSON.parse(data.result);
				if (data && data.result) {
					if (id) {
						var r = data.result.elements.slice(-1);
						$("#"+id+" div.item-count span").text(r[0].result);
					}

					// Relancer avec toutes les conditions
					$("#nb-result").text(data.result.total);
				}
			});
	  	},

	  	/**
	  	* reset de l'opérateur de comparaison
	  	*/
	  	resetOperatorField : function(){
	  		var $operatorSelect = this.params.$el.find('.dynamic-champs1').find('.select-box');
	  		$operatorSelect.html('<option data-type="" value="">&nbsp</option>');
	  		$operatorSelect.selectbox('detach');
	  		$operatorSelect.selectbox('attach');
	  	},

	  	/**
	  	* reset du champs valeur
	  	*/

	  	resetValueField : function(){
	  		this.params.$el.find('.dynamic-champs2').html('<input disabled name="chps3" type="text" value="">');
	  	},

	  	/**
	  	* Udate du champs d'opérateur de comparaison
	  	*/
	  	updateOperatorField : function(champs, module){
	  		var $dynamicZone = this.params.$el.find('.dynamic-champs1').find('.select-box'),
	  			$dynamicZone2 = this.params.$el.find('.dynamic-champs2'),
	  			$addBtn 	 = this.params.$el.find('[type="submit"]'),
	  			that = this;

	  		if(!champs || champs == ""){
	  			var html='';
	  			$dynamicZone.html(html);
	  			$dynamicZone.selectbox('detach');
	  			$dynamicZone.selectbox('attach');
	  		}else{
	  			//appel ajax pour recup les opérateurs de comparaison et les type attendus pour le troisieme champs
	  			var call = Nova.DataBridge.getOperatorFromChamps(champs, module);
	  			call.done(function(d){
	  				var result = JSON.parse(d.result);
	  				data = result;
	  				var html ='';
	  				if(data.length){
	  					d = data;
	  				}else
	  					d = data[0];
	  				/* pourquoi il est a l'envers le for ? */
	  				for (var i = d.length - 1; i >= 0; i--) {
	  					html+='<option data-type="'+d[i].type+'" value="'+d[i].value+'">'+d[i].name+'</option>';
	  				};

	  				//Si c'est une liste on enverra les données de la liste
	  				var donnee = Array();
	  				if(data.donnees){
	  					donnee = data.donnees;
	  				}

	  				$dynamicZone.html(html);
					$dynamicZone.selectbox('detach');
					$dynamicZone.selectbox({
						onChange : function(value,inst){
							var type = $("#"+inst.id+" [value='"+value+"']").attr('data-type');
							that.updateValueField(value,type,donnee);
						}
					});
	  			});
	  		}

	  		this.resetValueField();
	  		$addBtn.prop('disabled',true);
	  	},

	  	/**
	  	* Udate du champs valeur
	  	*/
	  	updateValueField : function(value,type,donnee){
	  		var $dynamicZone = this.params.$el.find('.dynamic-champs2'),
	  			$addBtn 	 = this.params.$el.find('[type="submit"]'),
	  			that = this;

	  		//appel ajax à ajouter pour récuper les données des listes
	  		if(type&&value){
	  			$addBtn.prop('disabled',false);

				html = Nova.Templates[type];
				$dynamicZone.html(html);

				if(type == "date"){
					var $datepicker= $dynamicZone.find('.picker');
					var $select = $dynamicZone.find('.select-box');
					var $op = this.params.$el.find('.dynamic-champs1').find('.select-box')
					$datepicker.glDatePicker({
						cssName: 'flatwhite',
						onClick: function(target, cell, date, data) {

							var h = "";
							switch ($op.val()) {
								case "<=":
								case "<":
									h = "23:59:59";
								break;

								case ">=":
								case ">":
									h = "00:00:00";
								break;
							}


				            target.val(
				            	date.getFullYear() + '-' +
				                ("0" + (date.getMonth()+1)).slice(-2) + '-' +
				                ("0" + date.getDate()).slice(-2) + ' ' +
				                h
				            );

				        }
					});
				}

				if(type == "list"){

					var $select = $dynamicZone.find('.select-box');
					for (var i = donnee.length - 1; i >= 0; i--) {
	  					$select.append('<option value="'+donnee[i][0]+'">'+donnee[i][1]+'</option>');
	  				};
					$select.selectbox('detach');
					$select.selectbox();
				}
	  		} else {
	  			// si on selectionne quelque chose sans value ni type, on disable le champs de valeur et on cache le bouton de validation
	  			$addBtn.prop('disabled',true);
	  			this.resetValueField();
	  		}
	  	}
	  };
	  return FilterBuilder;
	})();
}(jQuery));