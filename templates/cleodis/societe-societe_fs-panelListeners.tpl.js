{
	afterrender: function (el,t,b) {
		/* ATTENTION, si un jour l'ordre des éléments change, alors ce code ne fonctionnera plus !! */
		{if ATF::famille()->select($requests.societe.id_famille, "famille")=="Foyer"}
			Ext.getCmp("panel_societe_fs").hide();
			Ext.getCmp("panel_codes_fs").hide();
			Ext.getCmp("panel_caracteristiques").hide();
			Ext.getCmp("panel_deploiement").hide();

			Ext.getCmp("panel_coordonnees_supplementaires_fs").hide();

			Ext.getCmp('facturation_rib').hide();
			Ext.getCmp('logo').hide();

			Ext.getCmp("panel_particulier_fs").show();
			Ext.getCmp("panel_fidelite").show();
			Ext.getCmp("panel_optin").show();
		{else}
			Ext.getCmp("panel_societe_fs").show();
			Ext.getCmp("panel_codes_fs").show();
			Ext.getCmp("panel_caracteristiques").show();
			Ext.getCmp("panel_deploiement").show();

			Ext.getCmp("panel_coordonnees_supplementaires_fs").show();

			Ext.getCmp('facturation_rib').show();
			Ext.getCmp('logo').show();

			Ext.getCmp("panel_particulier_fs").hide();
			Ext.getCmp("panel_fidelite").hide();
			Ext.getCmp("panel_optin").hide();
		{/if}
	}
}