/**
* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
*/
Ext.ComponentMgr.get('label_facture[id_facture_parente]').clearValue();
Ext.ComponentMgr.get('label_facture[id_facture_parente]').store.proxy=new Ext.data.HttpProxy({
	url: 'facture,autocomplete.ajax,condition_field=facture.id_affaire&condition_value='+record.data.id
	,method:'POST'
});
Ext.ComponentMgr.get('label_facture[id_facture_parente]').store.reload();