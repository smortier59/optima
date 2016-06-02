Ext.ComponentMgr.get('suivi[suivi_contact]').reset();
Ext.ComponentMgr.get('suivi[suivi_contact]').store.proxy=new Ext.data.HttpProxy({
	url: 'contact,autocomplete.ajax,condition_field=contact.id_societe&condition_value='+record.data.id
	,method:'POST'
});
Ext.ComponentMgr.get('suivi[suivi_contact]').store.reload();