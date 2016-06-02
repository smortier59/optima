/**
 * @author mfleurquin
 */

{
	text: '{ATF::$usr->trans(Emails)|escape:javascript}',
	handler: function(btn, ev) {
		var grid = Ext.getCmp('{$id}');
		var records = grid.getSelectionModel().getSelections();
		
		var donnee = "";
		var soc = "";
		
		for(var i=0,j=records.length; i<j; i++){
			if(i == records.length-1){
				//donnee = donnee+"['"+records[i].data.societe__dot__id_societe +"','"+ records[i].data.societe__dot__societe+"']";
				donnee = donnee + records[i].data.societe__dot__societe;
				soc = soc + records[i].data.societe__dot__id_societe;
			}else{
				donnee = donnee + records[i].data.societe__dot__societe + " , ";
				//donnee = donnee+"['"+records[i].data.societe__dot__id_societe +"','"+ records[i].data.societe__dot__societe+"'],";
				soc = soc + records[i].data.societe__dot__id_societe+ "_";
			}	
						
		};
			
		
		ATF.basicInfo = new Ext.FormPanel({
			renderTo: "{$renderer}",
			frame: true,
			width: 690,
			height: 470,
			items: [
					{
						xtype: "textfield",
						fieldLabel: "To",
						name: "to",
						id: "to",
						value : donnee
					},{
						xtype: "textfield",
						fieldLabel: "Sujet",
						id: "sujet",
						name: "sujet"
					},/*{
						xtype: "textarea",
						fieldLabel: "Message",
						id: "message",
						name: "message",
						anchor: '100%',
						height:350
					}*/
					{
						xtype: "htmleditor",
						fieldLabel: "Message",
						id: "message",
						name: "message",
						anchor: '100%',
						height:350
					}	
			],
			buttons:[{
				text : "Envoyer",
				handler : function(){
					Ext.Msg.show({ 
						title:ATF.usr.trans('Envoi groupé'),
						msg:"Un email va être envoyé à tout les contacts des sociétés : "+donnee,
						buttons:Ext.Msg.YESNO,
						fn:function (buttonId,text,opt) {
							switch (buttonId) {
								case "no":
									break;											
								case "yes":
									ATF.ajax("societe,sendMails.ajax,previsualiser=false"
									,{										
										message:Ext.getCmp('message').getValue()
										,societe: soc
										,sujet : Ext.getCmp('sujet').getValue()									
									});
									Ext.Msg.alert("", "Les mails ont bien été envoyé");
								break;
						}
					}})}
			},{
				text: "Prévisualiser",
				handler: function(){
					//debugger;				
					ATF.ajax("societe,sendMails.ajax,previsualiser=true&",{
										message:Ext.getCmp('message').getValue()
										,societe: soc
										,sujet : Ext.getCmp('sujet').getValue()
									});
					Ext.Msg.alert("Prévisualisation envoyée","La prévisualisation du mail à bien été envoyée sur votre boite mail : ");
				}
			}]
		});
		ATF.unsetFormIsActive();
		
		ATF.currentWindow = new Ext.Window({
			title: '{ATF::$usr->trans("Envoi de mails")}',
			id:'mywindow',
			width: 700,
			height: 500,
			buttonAlign:'center',
			autoScroll:false,
			closable:true,
			items: ATF.basicInfo
		}).show();
			
	}
	
}
