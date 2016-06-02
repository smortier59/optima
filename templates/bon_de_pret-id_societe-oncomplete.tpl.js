var test=record.data.id;

/* Sync du contact */
if (Ext.ComponentMgr.get('label_bon_de_pret[id_contact]')) {
	Ext.ComponentMgr.get('label_bon_de_pret[id_contact]').clearValue();
	Ext.ComponentMgr.get('label_bon_de_pret[id_contact]').store.proxy=new Ext.data.HttpProxy({
		url: 'contact,autocomplete.ajax,condition_field=contact.id_societe&condition_value='+test
		,method:'POST'
	});
	Ext.ComponentMgr.get('label_bon_de_pret[id_contact]').store.reload();
}