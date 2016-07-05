{$id_user = ATF::$usr->getID()}

ATF.renderer.comiteDecision=function(table,field) {
	return function(filetype, meta, record, rowIndex, colIndex, store) {
		var idDiv = Ext.id();
		var id = record.data[table+'__dot__id_comite'];
		
		if (record.data.comite__dot__etat=="en_attente") {					
			var btndecision = {
				xtype:'button',
				id:"decision"+id,
				buttonText: '',
				buttonOnly: true,
				iconCls: 'btnValid',
				cls:'floatLeft',
				tooltip: '{ATF::$usr->trans("Décision")}',
				tooltipType:'title',
				listeners: {
					'click': function(fb, v){
							var decision = new Ext.data.ArrayStore({
								fields: ["id", "text"],
								data: [  ["refus_comite", "Refus comité"]
										,["accord_portage", "Acceptation du comité"]
										
									  ]							
							});
							var hiddenField = new Ext.form.Hidden({
							    name: 'comboDisplay'
							});							
							
							var form = new Ext.FormPanel({
									frame:true,
									autoHeight:true,
									id:'myForm'+id,
									name:'myFormName'+id,
									title: '',
									bodyStyle:'padding:5px 5px 0',
									items: [
										 {
										 	 xtype: "combo"
										 	,fieldLabel: "Décision du comité"
										 	,name:"decision"
										 	,store: decision
										 	,displayField: "text"
										 	,mode: "local"
										 	,listeners: {
										        select: function(combo, record) {									        	
										            hiddenField.setValue(record.data['id']);
										        }
										    }
										 },
										 {
										 	 xtype : 'textfield'
											,fieldLabel: 'Commentaire'
											,name: 'commentaire'
										 }
										,	
											hiddenField								
									],
									buttons: [{
										 text: 'Ok'
										,handler: function(a,b,c,d){
											Ext.getCmp('myForm'+id).getForm().submit({
												submitEmptyText:false,
												method  : 'post',
												waitMsg : '{ATF::$usr->trans(loading_new_page)|escape:javascript}',
												waitTitle :'{ATF::$usr->trans(loading)|escape:javascript}',
												url     : 'extjs.ajax',
												params: {
													'extAction':'comite'
													,'extMethod':'decision'
													,'id':id												
												}
												,success:function(form, action) {
													ATF.ajax_refresh(action.result,true);
													Ext.getCmp('myForm'+id).destroy();
													Ext.getCmp('mywindow'+id).destroy();
													store.reload();
												}
												,timeout:3600
											});								
										}
									},
									{
										text: 'Annuler',
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
									title: '{ATF::$usr->trans("Décision du comite : ")}',
									id:'mywindow'+id,
									plain:true,
									bodyStyle:'padding:5px;',
									buttonAlign:'center'
								});								
								if (form) {
									Ext.getCmp('mywindow'+id).add(form);
									height += 400;
									width = 800;
								}													
							}
							Ext.getCmp('mywindow'+id).setHeight(height);		
							Ext.getCmp('mywindow'+id).setWidth(width);		
							Ext.getCmp('mywindow'+id).show();				
					}
				}
			};
			
			(function(){
				var params = {
					renderTo: idDiv,
					items:[btndecision]
					
				};
				var p = new Ext.Container(params);
			}).defer(25);
			return '<div class="left" id="'+idDiv+'"></div>';

		} else {
			return '<img src="{ATF::$staticserver}images/icones/no_2.png" />';
		}
	}
};
