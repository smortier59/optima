ATF.renderer.estBAP = function (table, field) {
	return function(filetype, meta, record, rowIndex, colIndex, store) {
		var id = record.data[table+'__dot__id_'+table];

		console.log(record);
		if (record.data.facture_fournisseur__dot__bap==="0") {
			return '<a href="javascript:;" onclick="if (confirm(\''+ATF.usr.trans('Etes_vous_sur de passer cette facture fournisseur en BAP')+'\')) ATF.tpl2div(\''+table+',estBAP.ajax\',\'id_facture_fournisseur='+id+'\');"><img src="{ATF::$staticserver}images/icones/expand2.png" height="16" width="16" alt="" /></a>';
		} else {
			return '<img src="{ATF::$staticserver}images/icones/etats/accepte.png" />';
		}
	}
};