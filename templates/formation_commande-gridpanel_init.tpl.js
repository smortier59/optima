{strip}
{* Ajout du champ nécessaire pour ce renderer *}
/* Pour les Factures Frounisseurs */
{util::push($fieldsKeys,"factureAllow")}

ATF.renderer.formation_genereBDCF=function(table,field) {
	return function(filetype, meta, record, rowIndex, colIndex, store) {
		var id = record.data[table+'__dot__id_'+table];
		return '<a href="formation_commande_fournisseur-insert.html,id_formation_commande='+id+'" onclick="ATF.goTo(\'formation_commande_fournisseur-insert.html,id_formation_commande='+id+'\'); return false;"><img src="{ATF::$staticserver}images/module/16/formation_commande.png" height="16" width="16" alt="" /></a>';
	}
};

ATF.renderer.formation_genereFacture=function(table,field) {
	return function(filetype, meta, record, rowIndex, colIndex, store) {		
		var idDiv = Ext.id();		
		var html = "";
		var id = record.data[table+'__dot__id_'+table];	
		if (record.data.factureAllow) {
			html += '<a href="formation_facture-insert.html,id_formation_commande='+id+'" onclick="ATF.goTo(\'formation_facture-insert.html,id_formation_commande='+id+'\'); return false;"><img src="{ATF::$staticserver}images/module/16/formation_facture.png" height="16" width="16" alt="" /></a>';
		} else {
			html += '<img src="{ATF::$staticserver}images/icones/no_2.png" />';
		}
		return '<div class="center" id="'+idDiv+'">'+html+'</div>';		
	}
};
{/strip}