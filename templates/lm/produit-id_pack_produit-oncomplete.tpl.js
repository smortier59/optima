/* Sync des produits pour un pack selectionné */
Ext.ComponentMgr.get('label_produit[id_produit_principal]').clearValue();
Ext.ComponentMgr.get('label_produit[id_produit_principal]').store.proxy = new Ext.data.HttpProxy({
	url: 'produit,autocompleteProduitPack.ajax,condition_field=pack_produit.id_pack_produit&condition_value='+record.data.id
	,method:'POST'
});
Ext.ComponentMgr.get('label_produit[id_produit_principal]').store.reload();