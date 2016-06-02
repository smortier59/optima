var test=record.data.id;

/* Sync du contact */
if (Ext.ComponentMgr.get('label_gep_projet[id_contact_facturation]')) {
	Ext.ComponentMgr.get('label_gep_projet[id_contact_facturation]').clearValue();
	Ext.ComponentMgr.get('label_gep_projet[id_contact_facturation]').store.proxy=new Ext.data.HttpProxy({
		url: 'contact,autocomplete.ajax,condition_field=contact.id_societe&condition_value='+test
		,method:'POST'
	});
	Ext.ComponentMgr.get('label_gep_projet[id_contact_facturation]').store.reload();
}
/* Sync de l'opportunite */
if (Ext.ComponentMgr.get('label_gep_projet[id_affaire]')) {
	Ext.ComponentMgr.get('label_gep_projet[id_affaire]').clearValue();
	Ext.ComponentMgr.get('label_gep_projet[id_affaire]').store.proxy=new Ext.data.HttpProxy({
		url: 'affaire,autocomplete.ajax,condition_field=affaire.id_societe&condition_value='+test
		,method:'POST'
	});
	Ext.ComponentMgr.get('label_gep_projet[id_affaire]').store.reload();
}

