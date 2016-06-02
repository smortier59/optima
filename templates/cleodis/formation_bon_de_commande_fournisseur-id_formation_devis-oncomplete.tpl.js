var test=record.data.id;

if (Ext.ComponentMgr.get('label_formation_bon_de_commande_fournisseur[id_fournisseur]')) {
	Ext.ComponentMgr.get('label_formation_bon_de_commande_fournisseur[id_fournisseur]').clearValue();
	Ext.ComponentMgr.get('label_formation_bon_de_commande_fournisseur[id_fournisseur]').store.proxy=new Ext.data.HttpProxy({
		url: 'societe,autocompleteFournisseurFormationDevis.ajax,condition_field=formation_devis_fournisseur.id_formation_devis&condition_value='+test
		,method:'POST'
	});
	Ext.ComponentMgr.get('label_formation_bon_de_commande_fournisseur[id_fournisseur]').store.reload();
}