ATF.renderer.AttacherMission=function(table,field) {
	return function(filetype, meta, record, rowIndex, colIndex, store) {
		var idDiv = Ext.id();
		var id = record.data[table+'__dot__id_candidat'];
						
		var btnTransfert = {
			xtype:'button',
			id:"transfert"+id,
			buttonText: '',
			buttonOnly: true,
			iconCls: 'btnRelance',
			cls:'floatLeft',
			tooltip: '{ATF::$usr->trans("transfert")}',
			tooltipType:'title',
			listeners: {
				'click': function(fb, v){
						var store = new Ext.data.ArrayStore({
							        proxy   : new Ext.data.MemoryProxy(),
							        fields  : ['id', 'name'],
							        sortInfo: {
							            field    : 'name',
							            direction: 'ASC'
							        }
							    });							    
								
					    store.loadData([
					        {foreach name=affaire from=ATF::candidat()->getAffaires() key=module item=infos}								                    
		                        ["{$infos['affaire.id_affaire']}" , "{ATF::affaire()->nom($infos['affaire.id_affaire'])}"]
		                        {if !$infos@last} , {/if}
					        {/foreach}
					    ]);

					    console.log(store);
						
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
									 	,fieldLabel: "Affaire"
									 	,name:"transfert"
									 	,store: store
									 	,displayField: "name"
									 	,mode: "local"
									 	,listeners: {
									        select: function(combo, record) {									        	
									            hiddenField.setValue(record.data['id']);
									        }
									    }
									 }
									,{
										xtype: 'textfield'
										,name: 'id_candidat' 
										,id: 'id_candidat'
										,value: id
										,hidden:true
									},	
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
												'extAction':'scanner'
												,'extMethod':'transfert'												
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
								title: '{ATF::$usr->trans("Lier à une affaire : ")}',
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
				items:[btnTransfert]
				
			};
			var p = new Ext.Container(params);
		}).defer(25);

		
		return '<div class="left" id="'+idDiv+'"></div>';
	}
};