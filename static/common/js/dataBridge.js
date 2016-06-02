/* Author  : Gaetan // Novastream 
*  Project :
*  Class : communication ajax centralisation
*/
(function($) {
Nova.DataBridge = (function() {
var DataBridge = {
  	getFirstChampsFromModule : function(module){
  		return $.ajax({
			dataType: "json",
			type:'post',
			data:{module:module},
			url: module+',champs1.ajax'
		});	
  	},
  	getOperatorFromChamps : function(champs,module){
  		return	$.ajax({
			dataType: "json",
			type:'post',
			data:{champs:champs,module:module},
			url: module+',operator.ajax'
		});
  	},
  	createCollection : function(module , id_filtre){	  	 	
  	 	var res = $.ajax({
			dataType: "json",
			type:'post',
			data:{ module:module , id_filtre:id_filtre },
			url: module+',getFilterCountSimulations.ajax'
		});
		return res;
  	}/*,
  	filterElementUpdated : function(data){
  	alert('filterElementUpdated');
  	return	$.ajax({
			dataType: "json",
			type:'post',
			data:data,
			url: data+',getFilterCountSimulations.ajax'
				
		});
		console.log("filterElementUpdated");	
  	} 	
  */
};
return DataBridge;
})();
}(jQuery));