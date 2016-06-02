;var Nova = Nova || {};


if (typeof Handlebars !== 'undefined'){
	Handlebars.registerHelper('equal', function(lvalue, rvalue, options) {
	    if (arguments.length < 3)
	        throw new Error("Handlebars Helper equal needs 2 parameters");
	    if( lvalue!=rvalue ) {
	        return options.inverse(this);
	    } else {
	        return options.fn(this);
	    }
	});
}



(function($) {
	
	/*****
	* Methode de log sans bug iE
	* @param event[object]
	*/
	Nova.log = function(message){
		if (window["console"] && console["log"]) {
			console.log(message);
		}
	};

	/*****
	* Vider et remplir avec un text placeholder un champs input ou textarea
	* @param 
	*/
	Nova.focusBlur = function(){
		$('body').delegate('.focusblur','focus',function() {
	  		if ($(this).val()==$(this).attr("title")) { $(this).val(""); }
		});
		$('body').delegate('.focusblur','blur',function() {
	  		if ($(this).val()=="") { $(this).val($(this).attr("title")); }
		});

	};

	/*****
	* Ajout dynamique de balise script dans le head de la page (JSONP)
	* @param id{string} identifiant de la balise script
	* @param url{string} l'url du script à executer
	* @param callback {string} le nom de la function de callback
	*/
	Nova.addScriptTag = function (id, url, callback) {
	var scriptTag = document.createElement("script");
   	scriptTag.setAttribute("type", "text/javascript");
   	scriptTag.setAttribute("charset", "utf-8");
   	scriptTag.setAttribute("src", url + "&callback=" + callback);
   	scriptTag.setAttribute("id", id);
	var head = document.getElementsByTagName("head").item(0);
	head.appendChild(scriptTag); 
	};


	/*****
	* Méthode pour truncate des string ajout au type natif string
	* @param n{int} nb max de caractères
	* @param useWordBoundary{Bolean} si true alors on coupe pas dans un mot
	*/
	String.prototype.trunc = function(n,useWordBoundary){
        var toLong = this.length>n,
            s_ = toLong ? this.substr(0,n-1) : this;
         	s_ = useWordBoundary && toLong ? s_.substr(0,s_.lastIndexOf(' ')) : s_;
         return  toLong ? s_ + '&hellip;' : s_;
    };
	
 
}(jQuery));


 /* jQuery Tiny Pub/Sub - v0.7 - 10/27/2011
 * http://benalman.com/
 * Copyright (c) 2011 "Cowboy" Ben Alman; Licensed MIT, GPL */
 
(function($) {
 
  var o = $({});
 
  $.subscribe = function() {
    o.on.apply(o, arguments);
  };
 
  $.unsubscribe = function() {
    o.off.apply(o, arguments);
  };
 
  $.publish = function() {
    o.trigger.apply(o, arguments);
  };
 
}(jQuery));
