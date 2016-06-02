/**
 * @author qjanon
 */

{
	text: '{ATF::$usr->trans(fiche_casting,personnel)|escape:javascript}',
	handler: function(btn, ev) {
		var grid = Ext.getCmp('{$id}');
		var records = grid.getSelectionModel().getSelections();
		var ids= "";
		var fields = "";
		if (records.length) {

			for(var i=0,j=records.length; i<j; i++){
				ids += records[i].data.personnel__dot__id_personnel+",";
			}
			var d = new Date();
			ATF.basicInfo = new Ext.FormPanel({
				renderTo: "{$renderer}",
				frame: true,
				items: [
					{
						xtype: "textfield",
						fieldLabel: '{ATF::$usr->trans(nom_client,personnel)|escape:javascript}',
					    itemCls: 'x-check-group-alt',
					    id: "nom_client"
					},{
						xtype: "textfield",
						fieldLabel: '{ATF::$usr->trans(date,personnel)|escape:javascript}',
					    itemCls: 'x-check-group-alt',
					    value: d.getDate()+"-"+(d.getMonth()+1)+"-"+d.getFullYear(),
					    id: "date"
					},{
						xtype: "textfield",
						fieldLabel: '{ATF::$usr->trans(lieu,personnel)|ucfirst}',
					    itemCls: 'x-check-group-alt',
					    id: "lieu"
					},{
						xtype: "checkboxgroup",
						fieldLabel: '{ATF::$usr->trans(colonne_dispo,personnel)|escape:javascript}',
					    itemCls: 'x-check-group-alt',
					    id: "colonne_choisis",
					    // Put all controls in a single column with width 100%
					    columns: 1,
					    items: [
					        { boxLabel: 'Age uniquement', name: 'cb-col-1', checked: true, inputValue: "age" },
					        { boxLabel: 'Date', name: 'cb-col-2', inputValue: "date_naisance" },
					        { boxLabel: 'Lieu de naissance', name: 'cb-col-3', inputValue: "lieu_naisance" },
					        { boxLabel: 'Taille', name: 'cb-col-4', checked: true, inputValue: "taille" },
					        { boxLabel: 'Mensurations', name: 'cb-col-5', checked: true, inputValue: "mensurations" },
					        { boxLabel: 'Adresse complète', name: 'cb-col-6', inputValue: "adresse_full" },
					        { boxLabel: 'CP', name: 'cb-col-7', inputValue: "cp" },
					        { boxLabel: 'Ville', name: 'cb-col-8', inputValue: "ville" },
					        { boxLabel: 'Numéro de sécurité sociale', name: 'cb-col-9', inputValue: "num_secu" },
					        { boxLabel: 'Nationalité', name: 'cb-col-10', inputValue: "nationalite" },
					        { boxLabel: 'Email', name: 'cb-col-11', inputValue: "email" },
					        { boxLabel: 'Téléphone', name: 'cb-col-12', inputValue: "tel" },
					        { boxLabel: 'Permis', name: 'cb-col-13', checked: true, inputValue: "permis" },
					        { boxLabel: 'voiture', name: 'cb-col-14', checked: true, inputValue: "voiture" },
					        { boxLabel: 'Anglais', name: 'cb-col-15', inputValue: "anglais" },
					        { boxLabel: 'Autres langues', name: 'cb-col-16', inputValue: "langues" },
					        { boxLabel: '3 derniers clients', name: 'cb-col-17', inputValue: "last_client" }
					    ]
					}
				],
				buttons:[{
					text : "Générer la fiche casting",
					handler : function(){
						var f = Ext.getCmp("colonne_choisis").getValue();
						for(var i=0,j=f.length; i<j; i++){
							fields += f[i].inputValue+",";
						}
						ATF.loadMask.show();
						ATF.ajax("personnel,generateFicheCasting.ajax",{ ids : ids, fields: fields, client: Ext.getCmp("nom_client").getValue(), date: Ext.getCmp("date").getValue(), lieu: Ext.getCmp("lieu").getValue() }, { onComplete: function (result) { 
							ATF.loadMask.hide();
							window.open(result.result, "_self");
						} } );
					}
				}]
			});
			ATF.unsetFormIsActive();
			
			ATF.currentWindow = new Ext.Window({
				title: '{ATF::$usr->trans(choix_des_champs,personnel)|escape:javascript}',
				id:'mywindow',
				width: 600,
				buttonAlign:'center',
				autoScroll:false,
				closable:true,
				items: ATF.basicInfo
			}).show();


		} else {
			Ext.Msg.alert('{ATF::$usr->trans(aucune_selection_title,personnel)|escape:javascript}', '{ATF::$usr->trans(aucune_selection,personnel)|escape:javascript}');
		}


			
	}
	
}
,"-",
{
	text: '{ATF::$usr->trans(Emails)|escape:javascript}',
	handler: function(btn, ev) {
		var grid = Ext.getCmp('{$id}');
		var records = grid.getSelectionModel().getSelections();
				
		var mails = "";
		for(var i=0,j=records.length; i<j; i++){
			ATF.log(records);
			if(i == records.length-1){
				mails += records[i].data.personnel__dot__email;
			}else{
				mails += records[i].data.personnel__dot__email+",";
			}	
						
		};
		
		ATF.sendmail('personnel',mails,'Proposition de mission','Bonjour, \n\nVous trouverez ci-joint une proposition de recrutement pour la mission : \n\n\nCordialement,\nL\'équipe MANALA.');
	}
	
}

