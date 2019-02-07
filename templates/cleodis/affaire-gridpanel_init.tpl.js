ATF.renderer.validateOrderRenderer=function(table,field) {
	return function(filetype, meta, record, rowIndex, colIndex, store) {
		var id = record.data[table+'__dot__id_'+table];
		if (record.json.ref_externe != null && record.json.site_associe == "attente" && record.json.site_associe == "boulangerpro") {
			return '<a href="javascript:;" onclick="if (confirm(\''+ATF.usr.trans('Etes_vous_sur')+'\')) ATF.tpl2div(\'affaire,validateOrderPartenaire.ajax\',\'id_affaire='+id+'\');"><img src="{ATF::$staticserver}images/icones/no.png" height="16" width="16" alt="" /></a>';
		} else {
			return '<img src="{ATF::$staticserver}images/icones/no_2.png" />';
		}
	}
};