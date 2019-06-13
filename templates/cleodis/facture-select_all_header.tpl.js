/**
 * @author mfleurquin
 */

{
	text: '{ATF::$usr->trans(prelevement_slimpay,facture)|escape:javascript}',
	disabled: false,
	handler: function(btn, ev) {

		ATF.basicInfo = new Ext.FormPanel({
			frame: true,
			width: 900,
			items: [{
				html: "<strong>Choisissez les factures à prélever : </strong>"
			}],
			listeners: {
				render: function (el) {
					ATF.loadMask.show();
					ATF.ajax(
						"facture,aPrelever.ajax"
						,""
						,{
							onComplete: function (r) {
								var cb = {
							  		xtype: 'textfield',
							        name: 'libelle',
							        fieldLabel: 'Libellé',
							        allowBlank: true
							  	};
							  	el.add(cb);

							  	var cb = {
							  		xtype: 'datefield',
							        anchor: '50%',
							        fieldLabel: 'Date de Prélèvement',
							        name: 'date',
							        format: 'Y-m-d',
							        minValue: new Date()
							  	};
							  	el.add(cb);

								var cb = {
							  		xtype: "checkbox"
							  		,boxLabel: "Tout selectionner"
							  		,value: "all"
							  		,name: ""
							  		,listeners: {
							  			check: function(){
							  				var checkboxes = Ext.query('input[type=checkbox]');

						  					Ext.each(checkboxes, function(obj_item){
												obj_item.checked = checkboxes[0].checked;
											});


							  			}
							  		}
							  	};
							  	el.add(cb);

								Ext.iterate(r.result, function(key, value) {
									var cb = {
								  		xtype: "checkbox"
								  		,boxLabel: "["+key.ref+"]["+key.date+"] "+key.client+" "+key.prix_ttc+" € ("+key.date_periode_debut+" au "+key.date_periode_fin+")"
								  		,value: key.id_facture
								  		,name: "factures["+key.id_facture+"]"
								  	};
								  	el.add(cb);
								});



								el.doLayout();
								ATF.loadMask.hide();
							}
						}
					);
				}
			},
			buttons:[{
				text : "{ATF::$usr->trans(valider_prelevement,hotline)|escape:javascript}",
				handler : function(){
					ATF.basicInfo.getForm().submit({
						submitEmptyText:false,
						method  : 'post',
						waitMsg : '{ATF::$usr->trans(submit)|escape:javascript}',
						waitTitle : '{ATF::$usr->trans(loading)|escape:javascript}',
						url     : 'extjs.ajax',
						params: {
							'extAction':'facture'
							,'extMethod':'massPrelevementSlimpay'
						}
						,success:function(form, action) {
							ATF.unsetFormIsActive();
							ATF.currentWindow.close();
							ATF.extRefresh(action);
							store.reload();
						}
						,failure:function(form, action) {
							var title='Problème';
							if (action.failureType === Ext.form.Action.CONNECT_FAILURE){
								Ext.Msg.alert(title, 'Server reported:'+action.response.status+' '+action.response.statusText);
							} else if (action.failureType === Ext.form.Action.SERVER_INVALID){
								Ext.Msg.alert(title, action.result.errormsg);
							} else if (action.failureType === Ext.form.Action.CLIENT_INVALID){
								Ext.Msg.alert(title, "Un champs est mal renseigné");
							} else if (action.failureType === Ext.form.Action.LOAD_FAILURE){
								Ext.Msg.alert(title, "Un champs est mal renseigné");
							}
						}
						,timeout:3600
					});
				}
			}]
		});
		ATF.unsetFormIsActive();

		ATF.currentWindow = new Ext.Window({
			title: 'Prélèvement automatique via SLIMPAY',
			id:'mywindow',
			width: 900,
			buttonAlign:'center',
			autoScroll:false,
			closable:true,
			items: ATF.basicInfo
		}).show();

	}

}
