/* L'url du proxy change, on modifie l'objet store */
Ext.ComponentMgr.get('label_hotline[id_contact]').clearValue();
Ext.ComponentMgr.get('label_hotline[id_contact]').store.proxy=new Ext.data.HttpProxy({
	url: 'contact,autocomplete.ajax,condition_field=contact.id_societe&condition_value='+record.data.id
	,method:'POST'
});
Ext.ComponentMgr.get('label_hotline[id_contact]').store.reload();
/*
/*Id_affaire*/
Ext.ComponentMgr.get('label_hotline[id_affaire]').clearValue();
Ext.ComponentMgr.get('label_hotline[id_affaire]').store.proxy=new Ext.data.HttpProxy({
	url: 'affaire,autocomplete.ajax,order_field=affaire.date&order_sens=desc&condition_field=affaire.id_societe&condition_value='+record.data.id
	,method:'POST'
});
Ext.ComponentMgr.get('label_hotline[id_affaire]').store.reload();

/*Id_gep_projet*/
Ext.ComponentMgr.get('label_hotline[id_gep_projet]').clearValue();
Ext.ComponentMgr.get('label_hotline[id_gep_projet]').store.proxy=new Ext.data.HttpProxy({
	url: 'gep_projet,autocomplete.ajax,condition_field=gep_projet.id_societe&condition_value='+record.data.id
	,method:'POST'
});
Ext.ComponentMgr.get('label_hotline[id_gep_projet]').store.reload();

if("{1|cryptid}"==record.data.id || "{1154|cryptid}"==record.data.id){
	Ext.ComponentMgr.get('combohotline[type_requete]').setValue("charge_absystech",false);
}else{
	Ext.ComponentMgr.get('combohotline[type_requete]').setValue("charge_client",false);
}

/*
ATF.tpl2div('tpl2div.ajax','table=hotline&div=gfi_hotline[id_contact]&template=hotline-id_contact-list&id_societe='+record.data.id+'&id_contact={$requests.hotline.id_contact}');
*/