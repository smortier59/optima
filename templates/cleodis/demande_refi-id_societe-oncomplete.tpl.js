/* Sync du contact */
Ext.ComponentMgr.get('label_demande_refi[id_contact]').clearValue();
Ext.ComponentMgr.get('label_demande_refi[id_contact]').store.proxy=new Ext.data.HttpProxy({
	url: 'contact,autocompleteAvecMail.ajax,condition_field=contact.id_societe&condition_value='+record.data.id
	,method:'POST'
});
Ext.ComponentMgr.get('label_demande_refi[id_contact]').store.reload();

/* Sync de l'affaire */
Ext.ComponentMgr.get('label_demande_refi[id_affaire]').clearValue();
Ext.ComponentMgr.get('label_demande_refi[id_affaire]').store.proxy=new Ext.data.HttpProxy({
	url: 'affaire,autocomplete.ajax,condition_field=affaire.id_societe&condition_value='+record.data.id
	,method:'POST'
});
Ext.ComponentMgr.get('label_demande_refi[id_affaire]').store.reload();