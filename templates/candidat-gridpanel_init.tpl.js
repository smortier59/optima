{strip}
{* Ajout du champ nécessaire pour ce renderer *}
ATF.renderer.etatCandidat=function(table,field) {
	return function(val, meta, record, rowIndex, colIndex, store) {
		var idDivE = Ext.id();
		var id = record.data[table+'__dot__id_'+table];
		if (record.data[table+'__dot__'+field]) {
			var etat = record.data[table+'__dot__'+field];
		} else {
			var etat = record.data[field];
		}

		if (etat) {
			switch (etat) {
				case 'oui':
				case 'non':
					html = '<span style="font-weight:bold">'+ATF.usr.trans(etat,table)+'</span>';
				break;
				default:
					html = '<span style="font-weight:bold">'+ATF.usr.trans(etat,table)+'(';
					html+= '<a href="javascript:;" onclick="ATF.afficheModalBox(\''+id+'\',\'oui\')"><img src="{ATF::$staticserver}images/icones/valid.png" height="16" width="16" alt="" /></a>';
					html+= '<a href="javascript:;" onclick="ATF.afficheModalBox(\''+id+'\',\'non\')"><img src="{ATF::$staticserver}images/icones/cancel.png" height="16" width="16" alt="" /></a>';
					html+= ')</span>';
				break;
			}
			return '<div class="center" id="'+idDivE+'">'+html+'</div>';
		}
	}
}; 
ATF.afficheModalBox=function(id_candidat,etat){
	Modalbox.show('candidat.dialog', { scrollTo:0, 
				  						width: 700, 
										title: '{ATF::$usr->trans(candidat)|addslashes}', 
										params: { 'id_candidat':id_candidat, 'etat':etat },  
										method: 'post' 
									}
				);	
};
{/strip}