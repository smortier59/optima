{strip}
{* Ajout du champ nécessaire pour ce renderer *}

ATF.renderer.formation_genereBonCommandeF=function(table,field) {
	return function(filetype, meta, record, rowIndex, colIndex, store) {		
		var id = record.data[table+'__dot__id_formation_devis_fk'];
		return '<a href="formation_bon_de_commande_fournisseur-insert.html,id_formation_devis='+id+'" onclick="ATF.goTo(\'formation_bon_de_commande_fournisseur-insert.html,id_formation_devis='+id+'\'); return false;"><img src="{ATF::$staticserver}images/module/16/formation_commande.png" height="16" width="16" alt="" /></a>';
	}
};
{/strip}