var id_fournisseur = Ext.ComponentMgr.get('formation_bon_de_commande_fournisseur[id_fournisseur]').value;
var id_devis =  Ext.ComponentMgr.get('formation_bon_de_commande_fournisseur[id_formation_devis]').value;


ATF.ajax('formation_devis,getMontantFournisseur.ajax','id_formation_devis='+id_devis+'&id_fournisseur='+id_fournisseur,{ 
	onComplete:function(obj){		
		Ext.ComponentMgr.get('formation_bon_de_commande_fournisseur[montant]').setValue(obj.result);
		
	} 
});

if (Ext.ComponentMgr.get('label_formation_bon_de_commande_fournisseur[id_contact]')) {
	Ext.ComponentMgr.get('label_formation_bon_de_commande_fournisseur[id_contact]').clearValue();
	Ext.ComponentMgr.get('label_formation_bon_de_commande_fournisseur[id_contact]').store.proxy=new Ext.data.HttpProxy({
		url: 'contact,autocompleteAvecMail.ajax,condition_field=contact.id_societe&condition_value='+id_fournisseur
		,method:'POST'
	});
	Ext.ComponentMgr.get('label_formation_bon_de_commande_fournisseur[id_contact]').store.reload();
}