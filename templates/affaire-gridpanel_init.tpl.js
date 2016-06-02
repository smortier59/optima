{strip}
{* Ajout du champ nécessaire pour ce renderer *}
ATF.renderer.etatAffaire=function(table,field) {
	return function(filetype, meta, record, rowIndex, colIndex, store) {
		var idDiv = Ext.id();
		var etat = record.data[table+'__dot__etat'];
		html = '<img class="smallIcon '+etat+'" title="'+ATF.usr.trans(etat,table)+'"  alt="'+ATF.usr.trans(etat,table)+'" src="'+ATF.blank_png+'" />';
		return '<div class="center" id="'+idDiv+'">'+html+'</div>';
	}
};
ATF.renderer.margeBrute=function(table,field) {
	return function(filetype, meta, record, rowIndex, colIndex, store) {
		var html = ATF.renderer.money(table,field)(filetype, meta, record, rowIndex, colIndex, store);
		var reste = 0.0 + record.data['marge'] - record.data['marge_commandee'];
		if (reste>0) {
			html = html.replace('class="right"','class="right highLightGreen"');
			html = '<img class="smallIcon iconGreenFlag" title="Bravo : La marge est supérieure à celle prévue !" src="'+ATF.blank_png+'" />' + html;
		} else if (reste<0) {
			html = html.replace('class="right"','class="right highLightRed"');
			html = '<img class="smallIcon iconRedFlag" title="La marge brute doit normalement atteindre celle prévue, tout est facturé ? et payé ?" src="'+ATF.blank_png+'" />' + html;
		}
		return html;
	}
};
{/strip}