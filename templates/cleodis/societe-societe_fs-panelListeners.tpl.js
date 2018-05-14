{
	afterrender: function (el,t,b) {
		/* ATTENTION, si un jour l'ordre des éléments change, alors ce code ne fonctionnera plus !! */
		{if ATF::famille()->select($requests.societe.id_famille, "famille")=="Foyer"}
			if (Ext.getCmp("panel_societe_fs")) Ext.getCmp("panel_societe_fs").hide();
			if (Ext.getCmp("panel_codes_fs")) Ext.getCmp("panel_codes_fs").hide();
			if (Ext.getCmp("panel_caracteristiques")) Ext.getCmp("panel_caracteristiques").hide();
			if (Ext.getCmp("panel_deploiement")) Ext.getCmp("panel_deploiement").hide();

			if (Ext.getCmp("panel_coordonnees_supplementaires_fs")) Ext.getCmp("panel_coordonnees_supplementaires_fs").hide();

			if (Ext.getCmp('facturation_rib')) Ext.getCmp('facturation_rib').hide();
			if (Ext.getCmp('logo')) Ext.getCmp('logo').hide();

			if (Ext.getCmp("panel_particulier_fs")) Ext.getCmp("panel_particulier_fs").show();
			if (Ext.getCmp("panel_fidelite")) Ext.getCmp("panel_fidelite").show();
			if (Ext.getCmp("panel_optin")) Ext.getCmp("panel_optin").show();
		{else}
			if (Ext.getCmp("panel_societe_fs")) Ext.getCmp("panel_societe_fs").show();
			if (Ext.getCmp("panel_codes_fs")) Ext.getCmp("panel_codes_fs").show();
			if (Ext.getCmp("panel_caracteristiques")) Ext.getCmp("panel_caracteristiques").show();
			if (Ext.getCmp("panel_deploiement")) Ext.getCmp("panel_deploiement").show();

			if( Ext.getCmp("panel_coordonnees_supplementaires_fs")) Ext.getCmp("panel_coordonnees_supplementaires_fs").show();

			if( Ext.getCmp('facturation_rib')) Ext.getCmp('facturation_rib').show();
			if( Ext.getCmp('logo')) Ext.getCmp('logo').show();

			if( Ext.getCmp("panel_particulier_fs")) Ext.getCmp("panel_particulier_fs").hide();
			if( Ext.getCmp("panel_fidelite")) Ext.getCmp("panel_fidelite").hide();
			if( Ext.getCmp("panel_optin")) Ext.getCmp("panel_optin").hide();
		{/if}
	}
}