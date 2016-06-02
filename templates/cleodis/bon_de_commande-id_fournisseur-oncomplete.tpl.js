/* Sync du contact */
Ext.ComponentMgr.get('label_bon_de_commande[id_contact]').setValue(Ext.util.Format.stripTags(record.data.id_contact_signataire));
Ext.ComponentMgr.get('bon_de_commande[id_contact]').setValue(record.data.id_contact_signataire_fk);
Ext.ComponentMgr.get('label_bon_de_commande[id_contact]').store.proxy=new Ext.data.HttpProxy({
	url: 'contact,autocompleteAvecMail.ajax,condition_field=contact.id_societe&condition_value='+record.data.id
	,method:'POST'
});
Ext.ComponentMgr.get('label_bon_de_commande[id_contact]').store.reload();

{$id_commande=$requests.id_commande|default:$requests.devis.id_commande}
Ext.getCmp('commandes_tree').loader.dataUrl = 'commande,getCommande_ligne.ajax,id_commande={$id_commande}&id_fournisseur='+record.data.id;
Ext.getCmp('commandes_tree').root.reload();
Ext.getCmp('commandes_tree').show();

Ext.getCmp('bon_de_commande[prix_cleodis]').setValue('0.00');
Ext.getCmp('bon_de_commande[prix]').setValue('0.00');

if(record.data.id=="246" || record.data.id=="5374"){
	Ext.getCmp('panel_total_cleodis').show();
	Ext.getCmp('panel_total').hide();
}else{
	Ext.getCmp('panel_total_cleodis').hide();
	Ext.getCmp('panel_total').show();
}

