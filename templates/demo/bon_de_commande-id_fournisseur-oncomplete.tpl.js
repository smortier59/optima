/* Sync des lignes */
Ext.ComponentMgr.get('bon_de_commande[produits]').store.setBaseParam('id_fournisseur',record.data.id);
var grid = Ext.ComponentMgr.get('bon_de_commande[produits]');
var firstRecord = grid.store.getAt(0);
if (!firstRecord) {
	/* Soit c'est la première selection de fournisseur */
	Ext.ComponentMgr.get('bon_de_commande[produits]').store.load();
} else {
	/* Soit on ajoute les lignes d'un autre fournisseur */
	var baseParams = grid.store.baseParams;
	baseParams.id_fournisseur = record.data.id;
	Ext.Ajax.request({
		params:baseParams
		,url: 'commande_ligne,selectOnlyNotYetOrderedQuantities.ajax'
		,method:'POST'	
		,success: function (r,e) {
			var result = $.parseJSON(r.responseText).result;
			for (var i=0; i<result.length; i++) {
				
				/* Check same ref, ne pas remettre une ref deja mise ! */
				var range = grid.store.getRange();
				for (var rangeIdx=0;rangeIdx<range.length;rangeIdx++) {				
					if (result[i]["bon_de_commande_ligne.ref"] == range[rangeIdx].data.bon_de_commande_ligne__dot__ref) {
						return ;	
					}
				}
				
				var recordClone = new grid.store.recordType({ 
					bon_de_commande_ligne__dot__prix_achat:result[i]["bon_de_commande_ligne.prix_achat"],
					bon_de_commande_ligne__dot__ref:result[i]["bon_de_commande_ligne.ref"],
					bon_de_commande_ligne__dot__produit:result[i]["bon_de_commande_ligne.produit"],
					bon_de_commande_ligne__dot__quantite:result[i]["bon_de_commande_ligne.quantite"],
					bon_de_commande_ligne__dot__prix:result[i]["bon_de_commande_ligne.prix"],
					bon_de_commande_ligne__dot__prix_achat:result[i]["bon_de_commande_ligne.prix_achat"],
					bon_de_commande_ligne__dot__etat:result[i]["bon_de_commande_ligne.etat"]
				});
				grid.store.add(recordClone);
				grid.store.commitChanges();
				grid.refreshHiddenValues();
			}
		}
	});
}