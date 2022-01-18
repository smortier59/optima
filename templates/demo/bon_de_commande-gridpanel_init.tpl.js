{strip}
{* Ajout du champ nécessaire pour ce renderer *}

ATF.renderer.setCompleted=function(table,field) {
	return function(filetype, meta, record, rowIndex, colIndex, store) {
		var idDivBdcValidate = Ext.id();
		var id = record.data[table+'__dot__id_'+table];
		var etat = record.data[table+'__dot__etat'];
		if (etat == 'en_cours') {
			(function(){
				var params = {
					renderTo: idDivBdcValidate,
					layout:'fit',
					items:[{
						xtype:'button',
						id:"complete",
						buttonText: '',
						buttonOnly: true,
						iconCls: 'valid',
						style: {
							width: '20px'
						},
						listeners: {
							'click': function(fb, v){
								if(ATF.currentWindow){
									ATF.currentWindow.close();
								}
								ATF.basicInfo = new Ext.FormPanel({
																  items:[
																		 {
																			 xtype: 'compositefield',
																			 fieldLabel: '{ATF::$usr->trans("date_reception","bon_de_commande")|escape:javascript}',
																			 items:[
																					{
																					xtype:'datefield',
																					
																					id: 'date_reception',
																					width: 120,
																					format:'d-m-Y',
																					value: '{$smarty.now|date_format:"%d-%m-%Y"}'
																					}
																					,
																					{
																					xtype:'timefield',
																					id: 'time_reception',
																					width: 65,
																					format:'H:i',
																					value: '{$smarty.now|date_format:"%H:%M"}'
																					}
																					]
																		 }
																		 ]
																  ,buttons: [
																			 {
																				 text: '{ATF::$usr->trans(ok)|escape:javascript}',
																				 handler: function(){
																						ATF.ajax(table+',setCompleted.ajax','id_bon_de_commande='+id+'&date_reception='+Ext.getCmp('date_reception').value+"{urlencode(" ")}"+Ext.getCmp('time_reception').value);
																						ATF.currentWindow.close();
																						store.reload();
																				 }
																			 }
																			 ]
																  });
								/*ATF.ajax('stock,setReceived.ajax','id_stock='+id);*/
								ATF.currentWindow = new Ext.Window({
									title: '{ATF::$usr->trans("reception_bon_de_commande","bon_de_commande")|escape:javascript}',
									id:'mywindow',
									width: 400,
									buttonAlign:'center',
									closable:true,
									items: ATF.basicInfo
								}).show();
								return false;
							}
						}
					}]
					
				};
				var p = new Ext.Container(params);
			}).defer(25);

			return '<div class="center" id="'+idDivBdcValidate+'"></div>';
			
		} else {
			return '<div id="'+idDivBdcValidate+'" class="center"><img class="smallIcon noAction" src="'+ATF.blank_png+'" /></div>';
		}
	}
};
ATF.renderer.viewCommand=function(table,field) {
	return function(filetype, meta, record, rowIndex, colIndex, store) {
		var idDivViewCommand = Ext.id();
		var id = record.data[table+'__dot__id_'+table];
		var url = record.data['url'];
		html =  '<div id="'+idDivViewCommand+'" class="center"></div>';
		if (url) {
			html += '<a href="'+url+'" target="_blank" >';
			html += '<img class="smallIcon url" src="'+ATF.blank_png+'" />';
			html += '{ATF::$usr->trans("voirCommande")}';
			html += '</a>';
		} else {
			html += '<img class="smallIcon noAction" src="'+ATF.blank_png+'" />';
		}
		
		html += '</div>';
		
		return html;
	}
};
{/strip}