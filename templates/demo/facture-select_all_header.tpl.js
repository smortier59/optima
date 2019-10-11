/**
 * @author mfleurquin
 */
{$info = ATF::_r()}

{* Si on est bien sur une societe on affiche le bouton de genration, sinon rien *}
{if $info["id_societe"]}
	{
		text: '{ATF::$usr->trans("Lettre_change")|escape:javascript}',
		handler: function(btn, ev) {	
			
			var fp = new Ext.FormPanel({
			    width: 500,
			    frame: true,
			    id : "formulaire",
			    title: 'Lettre de change',
			    autoHeight: true,
			    bodyStyle: 'padding: 10px 10px 0 10px;',
			    labelWidth: 200,
			    items: [{
		            xtype: 'datefield',
		            fieldLabel: 'Date echeance',
		            name: 'echeance',
		            id: 'echeance',
		            format: 'd/m/Y',   
		            autoHeight: true,
		            width: 117,
		            allowBlank: false,
		            maxLength: 11
		        }
			    
			    ]
			});
			
			var win = new Ext.Window({
                width:500,
                height:250,
                closeAction:'hide',
                items: [fp],
                buttons : [
                	{
                		text : 'Envoyer par mail',
                		handler : function(btn){
                			var grid = Ext.getCmp('{$id}');
							var records = grid.getSelectionModel().getSelections();
							
							var donnee = "";
							var factures = "";
							var date = Ext.getCmp("formulaire").form.items.items[0].value;
														
								for(var i=0,j=records.length; i<j; i++){
									if(i == records.length-1){
										donnee = donnee + records[i].data.facture__dot__id_facture;
										factures = factures + records[i].data.facture__dot__id_facture;
									}else{
										donnee = donnee + records[i].data.facture__dot__id_facture + " , ";
										factures = factures + records[i].data.facture__dot__id_facture+ ",";
									}	
												
								};
							
							if(factures){								
								ATF.ajax("facture,lettre_change.ajax",{									
									facture : factures,
									date : date			
								} ,{ onSuccess:function (obj) {					
										Ext.Msg.alert("Lettre de change envoyée","La lettre de change vous a été envoyée sur votre boite mail : ");
										win.hide();
									}
								});	
							}else{
								Ext.Msg.alert("Aucune facture","Il faut selectionner vos factures !");
							}
                			
                		}
                	}
                ]
            });
            win.show(this);
			
			
		}
	}
{else}
	{}
{/if}
