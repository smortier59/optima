if (record.data.id) {
	ATF.ajax(
		"societe,getGed.ajax",
		"id="+record.data.id, 
		{ 
			onComplete : function (r) {
				var p = Ext.getCmp('panelForChosenDocument');
				p.removeAll();

				if (r.result && r.result.length) {
					p.add({
						xtype: "checkboxgroup",
						fieldLabel : "Documents disponibles : ",
						id: 'chosenDocument',
						items: [r.result]
					});

				} else {
					p.add({
						html: "Aucun fichiers disponibles"
					});
				}
				p.doLayout();
			} 
		}
	);

	/* Sync du contact */
	if (Ext.ComponentMgr.get('label_mission[id_contact]')) {
		Ext.ComponentMgr.get('label_mission[id_contact]').clearValue();
		Ext.ComponentMgr.get('label_mission[id_contact]').store.proxy=new Ext.data.HttpProxy({
			url: 'contact,autocompleteAvecMail.ajax,condition_field=contact.id_societe&condition_value='+record.data.id
			,method:'POST'
		});
		Ext.ComponentMgr.get('label_mission[id_contact]').store.reload();
	}

}