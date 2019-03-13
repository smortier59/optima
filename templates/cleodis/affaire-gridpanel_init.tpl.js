ATF.renderer.validateOrderRenderer=function(table,field) {
	return function(filetype, meta, record, rowIndex, colIndex, store) {
		var id = record.data[table+'__dot__id_'+table];
		if (record.json.ref_externe != null && record.json.etat_cmd_externe == "attente" && record.json.site_associe == "boulangerpro") {
			return '<a href="javascript:;" onclick="if (confirm(\''+ATF.usr.trans('Etes_vous_sur')+'\')) ATF.tpl2div(\'affaire,validateOrderPartenaire.ajax\',\'id_affaire='+id+'\');"><img src="{ATF::$staticserver}images/logos/partenaire/'+record.json.site_associe+'.png" height="50"  alt="" /></a>';
		} else {
			return '<img src="{ATF::$staticserver}images/icones/no_2.png" />';
		}
	}
};

{* Ajout du champ nécessaire pour ce renderer *}
ATF.renderer.etatAffaire=function(table,field) {
	return function(filetype, meta, record, rowIndex, colIndex, store) {
		var idDiv = Ext.id();
		var etat = record.data[table+'__dot__etat'];
		html = '<img class="smallIcon '+etat+'" title="'+ATF.usr.trans(etat,table)+'"  alt="'+ATF.usr.trans(etat,table)+'" src="'+ATF.blank_png+'" />';
		return '<div class="center" id="'+idDiv+'">'+html+'</div>';
	}
};


ATF.renderer.fileRenderer = function(table, field){
	return function(filetype, meta, record, rowIndex, colIndex, store) {
		var idDiv = Ext.id();
		var id = record.data[table+'__dot__id_'+table];
		field = store.fields.keys[colIndex - 1];
		var html = "";

		if(record.json[field] == true){
			html += '<a href="affaire-select-'+field+'-'+id+'.dl" target="_blank">';
			html += '<img src="{ATF::$staticserver}images/icones/pdf.png" /></a>';
		}else{
			html = '<img src="{ATF::$staticserver}images/icones/no_2.png" />';
		}

		return '<div id="'+idDiv+'">'+html+'</div>';


	}
};