/* Sync de l'affaire */
Ext.ComponentMgr.get('label_comite[id_affaire]').clearValue();
Ext.ComponentMgr.get('label_comite[id_affaire]').store.proxy=new Ext.data.HttpProxy({
	url: 'affaire,autocomplete.ajax,condition_field=affaire.id_societe&condition_value='+record.data.id
	,method:'POST'
});
Ext.ComponentMgr.get('label_comite[id_affaire]').store.reload();