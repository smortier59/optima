
ATF.renderer.detailRapide=function(table,field) {
	return function(filetype, meta, record, rowIndex, colIndex, store) {

		var idDiv = Ext.id();
		var id = record.data[table+'__dot__id_personnel'];
		var btnDetail = {
			xtype:'button',
			id:id,
			buttonText: '',
			buttonOnly: true,
			iconCls: 'btnExpand',
			cls:'floatLeft',
			tooltip: '{ATF::$usr->trans("detail rapide")}',
			tooltipType:'title',
			listeners: {
				'click': function(fb, v){
					ATF.ajax("personnel,detailPersonnel.ajax,id="+fb.id,null,{ onComplete : function(data) {
						var panel = new Ext.Panel({
							frame:true,
							autoHeight:true,
							id:'myForm'+id,
							name:'myFormName'+id,
							title: '',
							bodyStyle: 'padding: 5px 5px 0',
							items: [
								{
									xtype: 'displayfield',
									fieldLabel: 'mensurations haut',
									name: 'mensuration_haut',
									value: 'Mensurations haut : ' + data.result['personnel.mensuration_haut'],

								},
								{
									xtype: 'displayfield',
									fieldLabel: 'mensurations bas',
									name: 'mensuration_bas',
									value: 'Mensurations bas : ' + data.result['personnel.mensuration_bas'],

								},
								{
									xtype: 'displayfield',
									fieldLabel: 'taille',
									name: 'taille',
									value: 'Taille : ' + data.result['personnel.taille'],
								},
								{
									autoEl : {
										tag:'img',
										src : window.location.protocol+"//"+window.location.hostname+'/personnel-'+fb.id+'-photo_pleine-200.png',
									}
								},
								{
									autoEl : {
										tag:'img',
										src : window.location.protocol+"//"+window.location.hostname+'/personnel-'+fb.id+'-photo_identite-200.png',
									}
								}
							],
							buttons: [
							{
								text: 'fermer',
								handler: function(){
									Ext.getCmp('myForm'+id).destroy();
									Ext.getCmp('mywindow'+id).destroy();
								}
							}]
						});
										
						if (!Ext.getCmp('mywindow'+id)) {
							var height = 500;
							var width = 500;
							new Ext.Window({
								title: '{ATF::$usr->trans("détail rapide : ")}',
								id:'mywindow'+id,
								plain:true,
								bodyStyle:'padding:5px;',
								buttonAlign:'center'
							});								
							if (panel) {
								Ext.getCmp('mywindow'+id).add(panel);
								height += 400;
								width = 800;
							}													
						}
						Ext.getCmp('mywindow'+id).setHeight(height);		
						Ext.getCmp('mywindow'+id).setWidth(width);		
						Ext.getCmp('mywindow'+id).show();	
					} });				
				}
			}
		};
		
		(function(){
			var params = {
				renderTo: idDiv,
				items:[btnDetail]
				
			};
			var p = new Ext.Container(params);
		}).defer(25);

		
		return '<div class="left" id="'+idDiv+'"></div>';
	}
};