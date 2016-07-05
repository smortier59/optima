{strip}
{
	text: '{ATF::$usr->trans(creditsafeButton,"societe")|escape:javascript}',
	id:'creditsafe _{$current_class->table}',
	disabled : false,
	handler: function(){
		var societe = Ext.getCmp('comite[id_societe]').getValue();
		if (!societe) {
			Ext.Msg.show({
			   title:'Champ manquant',
			   msg: 'Vous devez renseigner la societe pour interroger CreditSafe',
			   buttons: Ext.Msg.OK,
			   animEl: 'elId',
			   icon: Ext.MessageBox.WARNING
			});
			return false;
		}
		ATF.loadMask.show();
		ATF.ajax(
			"comite,getInfosFromCREDITSAFE,ajax"
			,"societe="+societe			, {
				onComplete: function (r) {
					Ext.iterate(r.result, function(key, value) {
					  	if (r.result[key]) {
							Ext.getCmp('comite['+key+']').setValue(value);
						} else {
							Ext.getCmp('comite['+key+']').reset();
						}
					});

					ATF.loadMask.hide();
				}
			}
		);
	}
},
{/strip}