ATF.renderer.licence_prise=function(table,field) {
	return function(filetype, meta, record, rowIndex, colIndex, store) {
		var id = record.data[table+'__dot__id_'+table];
		if (record.json.deja_pris) {
			return '<img src="{ATF::$staticserver}images/icones/form-valid.png" height="16" width="16" alt="" />';
		} else {
			return '<img src="{ATF::$staticserver}images/icones/no_2.png" />';
		}

	}
};