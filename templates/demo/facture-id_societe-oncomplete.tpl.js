var test=record.data.id;

/*
Ext.ComponentMgr.get('label_facture[id_affaire]').clearValue();
*/
Ext.ComponentMgr.get('facture[email]').setValue(record.json.email_facturation);

Ext.ComponentMgr.get('label_facture[id_affaire]').store.proxy=new Ext.data.HttpProxy({
	url: 'affaire,autocomplete.ajax,condition_field[]=affaire.id_societe&condition_value[]='+test
	,method:'POST'
});
Ext.ComponentMgr.get('label_facture[id_affaire]').store.reload();
Ext.ComponentMgr.get('facture[tva]').setValue(record.data.tva);

