{strip}
ATF.renderer.actif=function(table,field) {
	return function(val, meta, record, rowIndex, colIndex, store) {
		var idDivE = Ext.id();
		var id = record.data[table+'__dot__id_'+table];
		if (record.data[table+'__dot__'+field]) {
			var etat = record.data[table+'__dot__'+field];
		} else {
			var etat = record.data[field];
		}

		if (etat) {
			var defaut = false;
			html = '<img class="smallIcon ';
			switch (etat) {
				case 'mort':
					html += 'perdu';
				break;	
				case 'ok':
					html += 'valid';
				break;	
				default:
					defaut = true;
					html = '<span style="font-weight:bold">'+ATF.usr.trans(etat,table)+'</span>';
				break;
			}
			if (!defaut) {
				html += '" title="'+ATF.usr.trans(etat,table)+'"  alt="'+ATF.usr.trans(etat,table)+'" src="'+ATF.blank_png+'" />';
			}
			return '<div class="center" id="'+idDivE+'">'+html+'</div>';
		}
	}
};
{/strip}