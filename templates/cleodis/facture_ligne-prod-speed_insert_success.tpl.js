,success:function(form, action) {
	if(action.result.result){
		if(Ext.ComponentMgr.get('{$id}').store && Ext.ComponentMgr.get('{$id}').store.getRange()){
			var grid = Ext.ComponentMgr.get('{$id}');
			var store = grid.getStore();
			var theType = store.recordType;
			var p = new theType({
				{foreach from=$fields item=i}
					{util::extJSEscapeDot($i)}:''{if !$i@last},{/if}
				{/foreach}
			});
			grid.stopEditing();
			var idx = 0; // Numéro de ligne par défaut
			var index = grid.getSelectionModel().getSelectedCell();
			if (index) {
				idx = index[0]; // Numéro de ligne sélectionné
			}
			var records = Ext.ComponentMgr.get('{$id}').store.getRange();		

			records[idx].set('facture_ligne__dot__ref',Ext.util.Format.stripTags(action.result.result.data.ref));
			records[idx].set('facture_ligne__dot__produit',Ext.util.Format.stripTags(action.result.result.data.produit));
			records[idx].set('facture_ligne__dot__id_produit_fk',action.result.result.data.id_produit);
			records[idx].set('facture_ligne__dot__prix_achat',action.result.result.data.prix_achat);			
		}
		
		Ext.ComponentMgr.get('speed_insert{$id}').close();
	}else{
		ATF.extRefresh(action); 
	}
}