var test=record.data.id;

/* Sync du contact */
if (Ext.ComponentMgr.get('label_devis[id_contact]')) {
	Ext.ComponentMgr.get('label_devis[id_contact]').clearValue();
	Ext.ComponentMgr.get('label_devis[id_contact]').store.proxy=new Ext.data.HttpProxy({
		url: 'contact,autocompleteAvecMail.ajax,condition_field=contact.id_societe&condition_value='+test
		,method:'POST'
	});
	Ext.ComponentMgr.get('label_devis[id_contact]').store.reload();
}
/* Sync de l'opportunite */
if (Ext.ComponentMgr.get('label_devis[id_opportunite]')) {
	Ext.ComponentMgr.get('label_devis[id_opportunite]').clearValue();
	Ext.ComponentMgr.get('label_devis[id_opportunite]').store.proxy=new Ext.data.HttpProxy({
		url: 'opportunite,autocomplete.ajax,condition_field=opportunite.id_societe&condition_value='+test
		,method:'POST'
	});
	Ext.ComponentMgr.get('label_devis[id_opportunite]').store.reload();
}
if (Ext.ComponentMgr.get('devis[id_termes]')) {
	/* Sélectionner la valeur */
	Ext.ComponentMgr.get('devis[id_termes]').setValue(record.data.id_termes_fk);
	Ext.ComponentMgr.get('label_devis[id_termes]').setValue(record.data.id_termes);
}
/* Sync des devis */
if (Ext.ComponentMgr.get('label_id_devis')) {
	if (Ext.ComponentMgr.get('label_id_devis').disabled) {
		Ext.ComponentMgr.get('label_id_devis').setDisabled(false);
	}
	Ext.ComponentMgr.get('label_id_devis').store.proxy=new Ext.data.HttpProxy({
		url: 'devis,autocomplete.ajax,condition_field=devis.id_societe&condition_value='+test
		,method:'POST'
	});
	Ext.ComponentMgr.get('label_id_devis').store.reload();
}

Ext.ComponentMgr.get('label_devis[id_termes]').setValue("A réception de facture");

