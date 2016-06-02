Ext.namespace("Ext.ux");
Ext.QuickTips.init();

ATF.renderer.seenUnseenMail=function(table,field) {
	return function(val, meta, record, rowIndex, colIndex, store) {
		if (val==1) {
			return '<img class="smallIcon mailSeen" src="'+ATF.blank_png+'" />';	
		} else { 
			return '<img class="smallIcon mailUnseen" src="'+ATF.blank_png+'" />';	
		}
	}
};
 
ATF.formatAttachments = function( attachmentsRealName, message_id, attachments, folder_id ) {
	var attachmentsListRealName = attachmentsRealName.split(",");
	var attachmentsList = attachments.split(",");
	var url = "";
	var img = "";
	attachmentsRealName = "";
	for ( var i = 0; i < attachmentsListRealName.length; i++ ) {
		if ( i != 0 ) {
			attachmentsRealName += ", ";
		}
		url = "messagerie,fetchAttachment.ajax,message_id="+message_id+"&filename="+encodeURI(encodeURI(Ext.util.Format.htmlDecode(attachmentsListRealName[i]))).replace(/=/g,"%3D").replace(/&/g,"%26").replace(/\?/g,"%3F")+"&realFilename="+attachmentsList[i];
		img = "<img src='"+ATF.blank_png+"' class='smallIcon webmail attachment'>";
		attachmentsRealName += "<a href='"+url+"'>"+img+attachmentsList[i]+"</a>";
	}
	return attachmentsRealName;
}


Ext.onReady(function(){
	var xg = Ext.grid;
	
	var currMail;
   
	{$pager="gsa_messagerie_messagerie"}

	{$id="webmailGridID"}
	var insertNow = null;
	
	{include file="generic-gridpanel.tpl.js" 
		current_class=ATF::messagerie()
		pager=$pager
		name='grid' 
		height=200
		region='north'
		split=true
		id="{$id}"
		noUpdate=false
		search=true}

	
	grid.on({
		render:{
			scope: this,
			fn: function() {
				syncBtn.setIcon('{ATF::$staticserver}images/loading/loading3.gif');
				syncBtn.setText('En cours...');
				Ext.Ajax.request({
					url: 'messagerie,sync.ajax',
					timeout:60000,
					success: function(a,b) {
						ATF.ajax_refresh($.parseJSON(a.responseText));
						
						syncBtn.setIcon('{ATF::$staticserver}images/icones/messagerie/sync.png');
						syncBtn.setText('Synchroniser');
						var pager = Ext.getCmp('ptb_{$pager}');
						if (pager) {
							pager.doRefresh();
						}
					},
					failure: function(a,b,c,d) {

					},
					params: {  }
				});
			}
		}
	});

	var syncBtn = new Ext.Button({
		icon: '{ATF::$staticserver}images/icones/messagerie/sync.png' 
		,text: "Synchroniser"  
		,handler: function(a,b,c,d){ 
			if (!syncBtnLoadMask) {
				var syncBtnLoadMask = ATF.createLoadMask(grid.getEl());
				syncBtnLoadMask.enable();
			}

			syncBtnLoadMask.show();	

			Ext.Ajax.request({
				url: 'messagerie,sync.ajax',
				timeout:60000,
				success: function(a,b) {
					var r = $.parseJSON(a.responseText);
					if (r.totalCount!==0) {
						var title = 'Synchronisation terminée';
						var msg= r.totalCount+" mails viennent d'être synchronisés de votre boîte mail vers Optima.";
						var fn = function() {
							grid.store.reload();
							syncBtnLoadMask.hide();
						};
					} else {
						var title = 'Synchronisation terminée';
						var msg= "Aucun éléments a synchroniser.";
						var fn = function() { syncBtnLoadMask.hide(); };
					}					
					Ext.Msg.show({
						title:title,
						msg: msg,
						buttons: Ext.Msg.OK,
						fn: fn,
						animEl: 'elId',
						icon: Ext.MessageBox.INFO
					});
				},
				failure: function(a,b,c,d) {
					if (a.isTimeout) {
						Ext.Msg.show({
							title:'Erreur lors du traitement',
							msg: "Tous les mails n'ont pas été synchronisés car le délai imparti a cette requête est depassé, veuillez recommencer l'opération de synchronisation.",
							buttons: Ext.Msg.OK,
							fn: function() {
								grid.store.reload();
							},
							animEl: 'elId',
							icon: Ext.MessageBox.WARNING
						});
					}
					syncBtnLoadMask.hide();
				},
				params: {  }
			});			
						
		}
		,id:'SuiviButton'
	});
	grid.topToolbar.add(syncBtn);
	
	var fromField = new Ext.form.Label({ hideLabel: true,fieldLabel: ' ' });
	var toField = new Ext.form.Label({ hideLabel: true,fieldLabel: ' '	});
	var ccField = new Ext.form.Label({ hideLabel: true,fieldLabel: ' '	});
	var bccField = new Ext.form.Label({	hideLabel: true,fieldLabel: ' ' });
	var replyToField = new Ext.form.Label({ hideLabel: true	,fieldLabel: ' '	});
	var attachmentsField = new Ext.form.Label({ hideLabel: true,fieldLabel: ' ' });

	var headerContent = new Ext.form.FormPanel({
		labelAlign: 'right'
		,frame: true
		,autoHeight: true
		,hidden: true
		,items: [
			fromField
			,toField
			,ccField
			,bccField
			,replyToField
			,attachmentsField
		]
	});
	
	var headerPanel = new Ext.Panel({
		region: 'north'
		,autoHeight: true
		,border: false
		,layout: 'fit'
		,items: headerContent
	});

	var mailContent = new Ext.Panel({
		region: 'center'
		,layout: 'fit'
		,html: '<div align="center">Please select a message from the mail list.</div>'
		,border: false
	});

	var contentPanel = new Ext.Panel({
		id: 'detailPanel'
		,region: 'center'
		,split: true
		,height: 300
		,bodyStyle: {
			background: '#ffffff',
			padding: '7px'
		}
		,border: true
		,layout: 'border'
		,tbar: [{
			icon: '{ATF::$staticserver}images/module/16/suivi.png' 
			,text: "Suivi"  
			,hidden: true
			,handler: function(a,b,c,d){ 
				if (grid.getSelectionModel().selections.items.length==1) {
					Ext.Ajax.request({
						url: 'suivi,suiviSpeedInsertForWebmail.ajax',
						success: function(a,b) {
							var response = $.parseJSON(a.responseText);
							eval(response.result);
						},
						failure: function(a,b,c,d) {
//							ATF.log("FAILURE");	
//							ATF.log(a);	
//							ATF.log(b);	
						},
						params: { id: grid.getSelectionModel().selections.items[0].json['messagerie.id_messagerie'] }
					});			
				} else {
					
				}
				/* faire un ATF.ajax qui renvoi le javascript, puis l'execute, comme ça on met pas 40000 fois la structure de la fenêtre */
				/* voir aussi si je peux pas utiliserl e speed insert... exemple dans sui-suivi_contact-insert.tpl.js (autoload) 	*/
			}
			,id:'SuiviButton'
		}]
		,items: [
			headerPanel
			,mailContent
		]
	});
	
	var ct = new Ext.Panel({
        renderTo: 'webmailDiv',
		frame: true,
		title: 'Emails',
		id:'panelWebmail',
		height: 800,
		layout: 'border',
		items: [grid,contentPanel]	
	});
	
	grid.getSelectionModel().on('rowselect', function(sm, rowIdx, r) {
		if (sm.selections.items.length==1) {
			headerContent.setVisible(true);
			mailContent.removeAll();
			contentPanel.setTitle('Subject: ');
			Ext.getCmp('SuiviButton').show();

			
			fromField.setText('De : '+r.json['messagerie.from'],false);
			if (r.json['messagerie.to']) {
				toField.setText( 'A : ' + r.json['messagerie.to'], false );
				toField.setVisible(true);
			} else {
				toField.setVisible(false);
			}

			if (r.json['messagerie.attachmentsRealName']) {
				attachmentsField.setVisible( true );
				attachmentsField.setText( 'Pièces jointes : '+ATF.formatAttachments(r.json['messagerie.attachmentsRealName'],r.json['messagerie.uid'],r.json['messagerie.attachments']),false);
			} else {
				attachmentsField.setVisible( false );
			}

			var iframeId = Ext.id();
			// On charge le contenu
			mailContent.add( new Ext.BoxComponent({
				cls: 'contentIframe'
				,autoEl: {
					tag: 'iframe'
					,id: iframeId
					,src: "messagerie,getBody.ajax,display=true&uid="+r.json['messagerie.uid']+"&id="+r.json['messagerie.id_messagerie']
				}
			}));			

			// On enlève le bold sur la ligne
			Ext.get(grid.getView().getRow(rowIdx)).removeClass('unseen-row');

			headerPanel.setHeight( headerContent.height() );
			contentPanel.doLayout( false, true );
			
		}
		
	});	
	


});

