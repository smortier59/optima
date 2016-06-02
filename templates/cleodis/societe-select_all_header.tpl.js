{
	text: '{ATF::$usr->trans(import_fichier_prospect)|escape:javascript}',
	handler: function(btn, ev) {		
		ATF.basicInfo = new Ext.FormPanel({
			autoWidth: true,
			autoHeight: true,
			fileUpload: true,
			bodyStyle:'padding: 5px;',
			id: 'formImportProspectCleodis',
			items: [
				{
					xtype: "fileuploadfield",
					fieldLabel: "Fichier prospect",
					name: "file",
					id: "file"
				},{
					xtype: "checkbox",
					fieldLabel: "Ignorer les doublons",
					name: "ignore",
					id: "ignore",
					checked: false
				},
					{include file="generic-field-textfield.tpl.js" est_nul=true key="id_owner" function="autocomplete" id="id_owner" name="id_owner" fieldLabel="Responsable"}
				,
					{include file="generic-field-textfield.tpl.js" est_nul=true key="id_apporteur" function="autocomplete" id="id_apporteur" name="id_apporteur" fieldLabel="Apporteur"}
				,
					{include file="generic-field-textfield.tpl.js" est_nul=true key="id_prospection" function="autocomplete" id="id_prospection" name="id_prospection" fieldLabel="Prospection"}
				,
					{include file="generic-field-textfield.tpl.js" key="relation" id="relation" name="relation" fieldLabel="Relation" xtype="combo" data=ATF::db()->enum2array(ATF::societe(),'relation')}
				
				,{
					xtype: 'panel',
					id:'resultDiv'
				}
			],
			buttons:[{
				text : "{ATF::$usr->trans(valid)|escape:javascript}",
				handler : function(){
					Ext.getCmp('formImportProspectCleodis').getForm().submit({
						method: 'post',									   	
						url: 'extjs.ajax',
						params: {
							'table':'societe'
							, 'extAction':'societe'
							, 'extMethod':'importProspect'
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
							html += "<b>L'export s'est bien déroulé, "+r.societeInserted+" sociétés insérées et "+r.contactInserted+" contacts insérés.</b>";
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
			},{
				text: "{ATF::$usr->trans(close)|escape:javascript}",
				handler: function(){
					ATF.currentWindow.close();
				}
			}]
		});
		
		ATF.currentWindow = new Ext.Window({
			title: '{ATF::$usr->trans("import_fichier_prospect")}',
			id:'mywindow',
			width: 500,
			buttonAlign:'center',
			autoScroll:false,
			closable:true,
			items: ATF.basicInfo
		}).show();
			
	}
	
}
{if ATF::$codename=="cap"}
,"-",
{
	text: 'Editer des courriers',
	handler: function(btn, ev) {		
		var grid = Ext.getCmp('{$id}');
		var records = grid.getSelectionModel().getSelections();
		var ids  = "";
		var libelle = "";
		var fields = "";
		if (records.length) {
			for(var i=0,j=records.length; i<j; i++){
				ids += records[i].data.societe__dot__id_societe+"|";
				libelle += records[i].data.societe__dot__societe+", ";
			}
			var d = new Date();
			ATF.basicInfo = new Ext.FormPanel({
				renderTo: "{$renderer}",
				frame: true,
				id: 'formEnvoiCourrierCAP',
				items: [
					{
						xtype:'hidden',
						name:'ids',
						id:'ids',
						value:ids
					},
					{
						xtype: "label",
						fieldLabel: 'Société selectionné',
					    itemCls: 'x-check-group-alt',
					    html: libelle
					},{
						xtype: "combo",
						fieldLabel: 'Société source',
					    itemCls: 'x-check-group-alt',
					    id: "societe_source",
					    name: "societe_source",
				     	typeAhead: true,
					    triggerAction: 'all',
					    lazyRender:true,
					    mode: 'local',
				     	store: new Ext.data.ArrayStore({
					        id: 0,
					        fields: [
					            'val',
					            'lib'
					        ],
					        data: [["cap", 'Cap-recouvrement'], ["abjuris", 'Abjuris'], ["tlm","Toulemonde"]]
					    }),
					    valueField: 'val',
					    displayField: 'lib'
					}
				],
				buttons:[{
					text : "Générer les courriers",
					handler : function(){

						Ext.getCmp('formEnvoiCourrierCAP').getForm().submit({
							method: 'post',									   	
							url: 'extjs.ajax',
							params: {
								'table':'societe'
								, 'extAction':'societe'
								, 'extMethod':'envoiCourrier'
							},
							waitTitle:'Veuillez patienter',
							waitMsg: 'Chargement ...',
							timeout: 3600
							, success:function(form, action) {

								window.open("{$smarty.const.__ABSOLUTE_WEB_PATH__}/courrierSociete-1.pdf", "_blank");
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
			
			ATF.currentWindow = new Ext.Window({
				title: 'Edition de courrier type',
				id:'mywindow',
				width: 600,
				buttonAlign:'center',
				autoScroll:false,
				closable:true,
				items: ATF.basicInfo
			}).show();


		} else {
			Ext.Msg.alert('{ATF::$usr->trans(aucune_selection_title,societe)|escape:javascript}', '{ATF::$usr->trans(aucune_selection,societe)|escape:javascript}');
		}


	}
}
{/if}