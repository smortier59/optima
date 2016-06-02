{if $requests.id_affaire || $requests.devis.id_affaire}
	{$id_affaire=$requests.id_affaire|default:$requests.devis.id_affaire}
{/if}
/* Sync du contact */
Ext.ComponentMgr.get('label_devis[id_contact]').clearValue();
Ext.ComponentMgr.get('label_devis[id_contact]').store.proxy=new Ext.data.HttpProxy({
	url: 'contact,autocompleteAvecMail.ajax,condition_field=contact.id_societe&condition_value='+record.data.id
	,method:'POST'
});
Ext.ComponentMgr.get('label_devis[id_contact]').store.reload();

/* Sync de l'opportunite */
Ext.ComponentMgr.get('label_devis[id_opportunite]').clearValue();
Ext.ComponentMgr.get('label_devis[id_opportunite]').store.proxy=new Ext.data.HttpProxy({
	url: 'opportunite,autocomplete.ajax,condition_field=opportunite.id_societe&condition_value='+record.data.id
	,method:'POST'
});
Ext.ComponentMgr.get('label_devis[id_opportunite]').store.reload();

ATF.ajax("devis,majMail.ajax","id_societe="+record.data.id,{ onComplete:function(obj){ Ext.ComponentMgr.get('devis[emailTexte]').setValue(obj.result); } });

Ext.getCmp('avenant_tree').loader.dataUrl = 'societe,getParc.ajax,type=avenant{if $id_affaire}&id_affaire={$id_affaire}{/if}&id_societe='+record.data.id;
Ext.getCmp('avenant_tree').root.reload();

Ext.getCmp('label_devis[AR_societe]').setValue(record.json.raw_1);

Ext.getCmp('AR_tree').loader.dataUrl = 'societe,getParc.ajax,id_societe='+record.data.id{if $id_affaire}+'&id_affaire={$id_affaire}'{/if};
Ext.getCmp('AR_tree').root.reload();

Ext.getCmp('label_devis[vente_societe]').setValue(record.json.raw_1);

Ext.getCmp('vente_tree').loader.dataUrl = 'societe,getParcVente.ajax,id_societe='+record.data.id{if $id_affaire}+'&id_affaire={$id_affaire}'{/if};
Ext.getCmp('vente_tree').root.reload();