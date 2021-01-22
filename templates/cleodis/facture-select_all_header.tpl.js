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
							        allowBlank: true,
							        value: r.result.libelle
							  	};
							  	el.add(cb);

							  	var cb = {
							  		xtype: 'datefield',
							        anchor: '50%',
							        fieldLabel: 'Date de Prélèvement',
							        name: 'date',
							        format: 'Y-m-d',
							        minValue: new Date(),
							        value: r.result.date_prelevement
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

								Ext.iterate(r.result.lignes, function(key, value) {
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

},



{
	text: '{ATF::$usr->trans(import_facture_libre,facture)|escape:javascript}',
	disabled: false,
	handler: function(btn, ev) {
		ATF.panelImport = new Ext.FormPanel({
			frame: true,
			width: 500,
			fileUpload: true,
			id: 'formImportFactureLibre',
			items: [
				{
					xtype: "fileuploadfield",
					fieldLabel: "Fichier d'import",
					name: "file",
					id: "file"
				},{
					xtype: 'panel',
					id:'resultDiv'
				}
			],

			buttons:[{
				text : "Importer",
				handler : function(){
					Ext.getCmp('formImportFactureLibre').getForm().submit({
						submitEmptyText:false,
						method  : 'post',
						waitMsg : '{ATF::$usr->trans(submit)|escape:javascript}',
						waitTitle : '{ATF::$usr->trans(loading)|escape:javascript}',
						url     : 'extjs.ajax',
						params: {
							'extAction':'facture'
							,'extMethod':'import_facture_libre'
						},
						waitTitle:'Veuillez patienter',
						waitMsg: 'Chargement ...',
						timeout: 3600
						, success:function(form, action) {
							var r = Ext.util.JSON.decode(action.response.responseText);

							var html = "";
							if (r.warnings) {
								html += "Certaines erreurs non bloquantes ont été détecté, le détail ci dessous : <br><br><ul>";
								for (var d in r.warnings) {
									html += "<li><b>Ligne(s) "+r.warnings[d]+"</b> : "+d+"</li>";
								}
								html += "</ul><br><br>";
							}
							html += "<b>L'export s'est bien déroulé, "+r.factureInserted+" factures insérées.</b>";
							$('#resultDiv').html(html);

						}
						, failure:function(form, action) {
							var r = Ext.util.JSON.decode(action.response.responseText);
							var html = "Certaines erreurs ont rendu impossible l'import du fichier, veuillez les corriger en suivant le détail ci dessous : <br><br><ul>";
							for (var d in r.errors) {
								html += "<li><b>Ligne(s) "+r.errors[d]+"</b> : "+d+"</li>";
							}
							html += "</ul><br><br>";

							$('#resultDiv').html(html);
						}
					});
				}
			}]
		});

		ATF.unsetFormIsActive();

		ATF.ImportWindow = new Ext.Window({
			title: 'Import de facture libre',
			id:'mymodalimport',
			width: 510,
			buttonAlign:'center',
			autoScroll:false,
			closable:true,
			items: ATF.panelImport
		}).show();

	}
}
