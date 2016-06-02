var test=record.data.id;
ATF.log(record);
/* Sync du contact */
if (Ext.ComponentMgr.get('label_facture_paiement[id_facture_avoir]')) {
	Ext.ComponentMgr.get('label_facture_paiement[id_facture_avoir]').setDisabled(false);
	Ext.ComponentMgr.get('label_facture_paiement[id_facture_avoir]').clearValue();
	Ext.ComponentMgr.get('label_facture_paiement[id_facture_avoir]').store.proxy=new Ext.data.HttpProxy({
		url: 'contact,autocomplete.ajax,condition_field=contact.id_societe&condition_value='+test
		,method:'POST'
	});
	Ext.ComponentMgr.get('label_facture_paiement[id_facture_avoir]').store.reload();
}
