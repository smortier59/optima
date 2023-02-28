{strip}
{if ATF::$codename == 'cleodisbe'}
{
	text: '{ATF::$usr->trans(creditsafeButton,$current_class->name())|escape:javascript}',
	id: 'creditsafe _{$current_class->table}',
	disabled : false,
	handler: function(){
		var num_ident = Ext.getCmp('societe[num_ident]').getValue();
 		if (!num_ident) {
			Ext.Msg.show({
			   title:'Champ manquant',
			   msg: 'Vous devez renseigner un numéro d\'identification pour interroger CreditSafe',
			   buttons: Ext.Msg.OK,
			   animEl: 'elId',
			   icon: Ext.MessageBox.WARNING
			});
			return false;
		}
		ATF.loadMask.show();
		ATF.ajax(
			"societe,getInfosFromCREDITSAFE,ajax",
			"siret="+num_ident,
			{
				onComplete: function (r) {
					Ext.iterate(r.result, function(key, value) {
						if(Ext.getCmp('societe['+key+']')){
							if (r.result[key]) {
								Ext.getCmp('societe['+key+']').setValue(value);
							} else {
								Ext.getCmp('societe['+key+']').reset();
							}
						} else {
							if (Ext.getCmp('combosociete['+key+']')) {
								Ext.getCmp('combosociete['+key+']').setValue(value);
							}
						}
					});
					ATF.loadMask.hide();
				}
			}
		);
	}
},
{else}
	{if ATF::$codename == 'itrenting'}
	{
		text: '{ATF::$usr->trans(creditsafeButton,$current_class->name())|escape:javascript}',
		id: 'creditsafe _{$current_class->table}',
		disabled : false,
		handler: function(){
			var cif = Ext.getCmp('societe[CIF]').getValue();
			if (!cif) {
				Ext.Msg.show({
				title:'Champ manquant',
				msg: 'Vous devez renseigner un CIF pour interroger CreditSafe',
				buttons: Ext.Msg.OK,
				animEl: 'elId',
				icon: Ext.MessageBox.WARNING
				});
				return false;
			}
			ATF.loadMask.show();
			ATF.ajax(
				"societe,getInfosFromCREDITSAFE,ajax",
				"siret="+cif,
				{
					onComplete: function (r) {
						Ext.iterate(r.result, function(key, value) {
							if(Ext.getCmp('societe['+key+']')){
								if (r.result[key]) {
									Ext.getCmp('societe['+key+']').setValue(value);
								} else {
									Ext.getCmp('societe['+key+']').reset();
								}
							} else {
								if (Ext.getCmp('combosociete['+key+']')) {
									Ext.getCmp('combosociete['+key+']').setValue(value);
								}
							}
						});
						ATF.loadMask.hide();
					}
				}
			);
		}
	},
	{else}
	{
		text: '{ATF::$usr->trans(creditsafeButton,$current_class->name())|escape:javascript}',
		id: 'creditsafe _{$current_class->table}',
		disabled : false,
		handler: function(){

			var siret = Ext.getCmp('societe[siret]').getValue();
			if (!siret) {
				Ext.Msg.show({
				title:'Champ manquant',
				msg: 'Vous devez renseigner un SIRET pour interroger CreditSafe',
				buttons: Ext.Msg.OK,
				animEl: 'elId',
				icon: Ext.MessageBox.WARNING
				});
				return false;
			}
			ATF.loadMask.show();
			ATF.ajax(
				"societe,getInfosFromCREDITSAFE,ajax"
				,"siret="+siret		, {
					onComplete: function (r) {
						Ext.iterate(r.result, function(key, value) {
							if(Ext.getCmp('societe['+key+']')){
								if (r.result[key]) {
									Ext.getCmp('societe['+key+']').setValue(value);
								} else {
									Ext.getCmp('societe['+key+']').reset();
								}
							} else {
								if (Ext.getCmp('combosociete['+key+']')) {
									Ext.getCmp('combosociete['+key+']').setValue(value);
								}
							}
						});

						/* On ouvre les panels pour l'envoi du formulaire */
						if(Ext.getCmp('panel_facturation_fs').collapsed == true ) Ext.getCmp('panel_facturation_fs').toggleCollapse(true);
						if(Ext.getCmp('panel_coordonnees_supplementaires_fs').collapsed == true ) Ext.getCmp('panel_coordonnees_supplementaires_fs').toggleCollapse(true);
						if(Ext.getCmp('panel_caracteristiques').collapsed == true ) Ext.getCmp('panel_caracteristiques').toggleCollapse(true);



						ATF.loadMask.hide();
					}
				}
			);
		}
	},
	{/if}
{/if}
{/strip}