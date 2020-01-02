{if $smarty.get.event == "insert"}
	{
		iconCls: 'iconCreditSafe',
		id:'btnCreditSafe{$id}',
		enableToggle: true,
		tooltip:'{ATF::$usr->trans("creditsafeButton","societe")}',
		toggleHandler: function () {
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
				,"siret="+siret			, {
					onComplete: function (r) {
						Ext.iterate(r.result, function(key, value) {
							if(Ext.getCmp('societe['+key+']') !== undefined){
								if (r.result[key]) {
									Ext.getCmp('societe['+key+']').setValue(value);
								} else {
									Ext.getCmp('societe['+key+']').reset();

								}
							}

						});

						ATF.loadMask.hide();
					}
				}
			);
		},
		pressed:true,
	},
{/if}