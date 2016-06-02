{strip}
{* Ajout du champ nécessaire pour ce renderer *}
{util::push($fieldsKeys,"allowValid")}

ATF.renderer.actionsTachesCleodis=function(table,field) {
	return function(filetype, meta, record, rowIndex, colIndex, store) {

		var idDivActionsTaches = Ext.id();
		var id = record.data[table+'__dot__id_'+table];
		var allowValid = record.data['allowValid'];


		var btnValid = {
			xtype:'button',
			id:"valid",
			buttonText: '',
			buttonOnly: true,
			iconCls: 'valid',
			disabled:!allowValid,
			tooltip: '{ATF::$usr->trans("valider")}',
			tooltipType:'title',
			listeners: {				
				'click': function(fb, v){		
					if(record.json.type_tache == "demande_comite"){			
						var decision = new Ext.data.ArrayStore({
							fields: ["id","text"],
							data: [  ["refus_comite", "Refus comité"]
									,["accord_portage" , "Accord de portage"]
									,["accord_reserve_cession" , "Accord reserve de cession"]
									,["accord_cession_portage" , "Accord cession portage"]
									,["attente_retour" , "Attente de retour"]							  	
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
									 	,fieldLabel: "Décision comité"
									 	,name:"decision"
									 	,store: decision
									 	,displayField: "text"
									 	,mode: "local"
									 	,listeners: {
									        select: function(combo, record) {									        	
									            hiddenField.setValue(record.data['id']);
									        }
									    }
									 },{
										xtype: 'textfield'
										,name: 'id_tache' 
										,id: 'id_tache'
										,value: id
										,hidden:true
									},hiddenField
														
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
												'extAction':'tache'
												,'extMethod':'valid'												
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
								title: '{ATF::$usr->trans("Décision de comité : ")}',
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
					}else{
						if(confirm('{ATF::$usr->trans(creer_suivi,tache)}')){
							ATF.ajax(
								'tache,valid.ajax'
								,'id_tache='+id
								,{ onComplete: function (obj) { 
									ATF.goTo('suivi-insert.html,id_tache='+id);
								} }
							);
						}else{	
							ATF.ajax('tache,valid.ajax','id_tache='+id);
							store.reload();
						}
					}
				}				
				
			}
		};
		
		(function(){
			var params = {
				renderTo: idDivActionsTaches,
				layout:'fit',
				items:[btnValid]
				
			};
			var p = new Ext.Container(params);
		}).defer(25);

		
		return '<div class="left" id="'+idDivActionsTaches+'"></div>';
			
	}
};

{/strip}