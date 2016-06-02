/* Sync du contact */
if (Ext.ComponentMgr.get('label_devis[id_contact]')) {
	Ext.ComponentMgr.get('label_devis[id_contact]').clearValue();
	Ext.ComponentMgr.get('label_devis[id_contact]').store.proxy=new Ext.data.HttpProxy({
		url: 'contact,autocompleteAvecMail.ajax,condition_field=contact.id_societe&condition_value='+record.data.id
		,method:'POST'
	});
	Ext.ComponentMgr.get('label_devis[id_contact]').store.reload();
}