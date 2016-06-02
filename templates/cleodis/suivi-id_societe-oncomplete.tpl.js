if(Ext.ComponentMgr.get('label_suivi[id_affaire]') != "undefined"){
	Ext.ComponentMgr.get('label_suivi[id_affaire]').clearValue();
	Ext.ComponentMgr.get('label_suivi[id_affaire]').store.proxy=new Ext.data.HttpProxy({
		url: 'affaire,autocomplete.ajax,condition_field=affaire.id_societe&condition_value='+record.data.id
		,method:'POST'
	});
	Ext.ComponentMgr.get('label_suivi[id_affaire]').store.reload();
}


if(Ext.ComponentMgr.get('label_suivi[id_formation_devis]') != "undefined" ){
	Ext.ComponentMgr.get('label_suivi[id_formation_devis]').clearValue();
	Ext.ComponentMgr.get('label_suivi[id_formation_devis]').store.proxy=new Ext.data.HttpProxy({
		url: 'formation_devis,autocomplete.ajax,condition_field=formation_devis.id_societe&condition_value='+record.data.id
		,method:'POST'
	});
	Ext.ComponentMgr.get('label_suivi[id_formation_devis]').store.reload();
}