{strip}
{* Ajout du champ nécessaire pour ce renderer *}
/* Pour les Factures Frounisseurs */
{util::push($fieldsKeys,"factureFournisseurAllow")}

ATF.renderer.generateFFF=function(table,field) {
	return function(filetype, meta, record, rowIndex, colIndex, store) {
		var idDiv = Ext.id();		
		var html = "";
		var id = record.data[table+'__dot__id_'+table];	
		if (record.data.factureFournisseurAllow) {
			html += '<a href="formation_facture_fournisseur-insert.html,id_formation_bon_de_commande_fournisseur='+id+'" onclick="ATF.goTo(\'formation_facture_fournisseur-insert.html,id_formation_bon_de_commande_fournisseur='+id+'\'); return false;"><img src="{ATF::$staticserver}images/module/16/formation_facture_fournisseur.png" height="16" width="16" alt="" /></a>';
		} else {
			html += '<img src="{ATF::$staticserver}images/icones/no_2.png" />';
		}
		return '<div class="center" id="'+idDiv+'">'+html+'</div>';		
	}
};
{/strip}