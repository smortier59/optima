{
	expand:function(){
		Ext.getCmp('panel_avenant_lignes').collapse();
		Ext.getCmp('panel_vente').collapse();
	},
	afterrender: function (el,t,b) {
		/* ATTENTION, si un jour l'ordre des éléments change, alors ce code ne fonctionnera plus !! */
		{if $requests.devis.type_contrat=="vente"}
			Ext.getCmp('panel_loyer_lignes').hide();
			Ext.getCmp('panel_loyer_uniques').hide();
			Ext.getCmp('combodevis[loyer_unique]').setValue('non');
			Ext.getCmp('panel_vente').show();
			Ext.getCmp('devis[prix_vente]').enable();
			Ext.getCmp('panel_avenant_lignes').hide();
			Ext.getCmp('panel_AR').hide();
		{/if}
	}
}