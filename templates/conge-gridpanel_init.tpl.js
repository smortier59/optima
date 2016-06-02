{strip}
{* Ajout du champ nécessaire pour ce renderer *}
{util::push($fieldsKeys,"allowValid")}
{util::push($fieldsKeys,"allowRefus")}
{util::push($fieldsKeys,"allowCancel")}


{* Renderer de l état des congés avec bouton pour valider *}
ATF.renderer.congeActions = function(table,field) {
	return function(val, meta, record, rowIndex, colIndex, store) {
		
		var idDivCongeAction = Ext.id();
		var id = record.data[table+'__dot__id_'+table];
		var allowValid = record.data['allowValid'];
		var allowRefus = record.data['allowRefus'];
		var allowCancel = record.data['allowCancel'];

		var btnValid = {
			xtype:'button',
			id:"valid",
			buttonText: '',
			buttonOnly: true,
			iconCls: 'valid',
			cls:'floatLeft',
			disabled:!allowValid,
			tooltip: '{ATF::$usr->trans("valider")}',
			tooltipType:'title',
			listeners: {
				'click': function(fb, v){
					ATF.ajax('conge,CongesDispo.ajax'
							,'id_conge='+id 
							,{ 
								onComplete: function (result) { 
									var nb = parseFloat(result.result);								
									nb = nb + parseFloat(record.data["duree"]);
									if(nb>30){
										if (!Ext.getCmp('alert'+id)) {
											new Ext.FormPanel({
												frame:true,
												autoHeight:true,
												id:'alert'+id,
												name:'alertName'+id,
												title: '',
												bodyStyle:'padding:5px 5px 0',
												items: [{
													html: '<span class="bold">En validant ce congés, vous acceptez que cet utilisateur prendra '+nb+' jours de congé cette année</span><hr>',
													xtype:'container'
												}],
										
												buttons: [{
													text: 'Ok',
													handler: function(){
														ATF.ajax('conge,validation.ajax,etat=ok&id_conge='+id);
														Ext.getCmp('alert'+id).destroy();
														Ext.getCmp('mywindowalert'+id).hide();
														store.reload();
													}
												},{
													text: 'Annuler',
													handler: function(){
														Ext.getCmp('alert'+id).destroy();
														Ext.getCmp('mywindowalert'+id).hide();
													}
												}]
											});										
										}

										if (!Ext.getCmp('mywindowalert'+id)) {
											new Ext.Window({
												title: '{ATF::$usr->trans("cancelConge")}',
												id:'mywindowalert'+id,
												width: 400,
												height:150,
												plain:true,
												bodyStyle:'padding:5px;',
												buttonAlign:'center',
												items: Ext.getCmp('alert'+id)
											});
										}
										Ext.getCmp('mywindowalert'+id).show();	



									}else{
										ATF.ajax('conge,validation.ajax,etat=ok&id_conge='+id);
										store.reload();
									}
							 	}	
							}
					);					
				}
			}
		};
		
		var btnRefus = {
			xtype:'button',
			id:"refus",
			buttonText: '',
			buttonOnly: true,
			iconCls: 'btnCancel',
			cls:'floatLeft',
			disabled:!allowRefus,
			tooltip: '{ATF::$usr->trans("refus")}',
			tooltipType:'title',
			listeners: {
				'click': function(fb, v){
					if (!Ext.getCmp('myForm'+id)) {
						new Ext.FormPanel({
							frame:true,
							autoHeight:true,
							id:'myForm'+id,
							name:'myFormName'+id,
							title: '',
							bodyStyle:'padding:5px 5px 0',
							items: [{
								html: '<span class="bold">{ATF::$usr->trans("pour_quelle_raison")|addslashes}</span><hr>',
								xtype:'container'
							},{
								xtype: 'textarea'
								,name: 'raison' 
								,id: 'raison'
								,fieldLabel:'{ATF::$usr->trans("raison")|addslashes}'
								,style: {
									width: '99%',
									height:'120px'
								}
							}],
					
							buttons: [{
								text: 'Ok',
								handler: function(){
									ATF.ajax('conge,validation.ajax,etat=nok&raison='+$('#raison').val()+'&id_conge='+id);
									Ext.getCmp('myForm'+id).destroy();
									Ext.getCmp('mywindow'+id).hide();
									store.reload();
								}
							},{
								text: 'Annuler',
								handler: function(){
									Ext.getCmp('myForm'+id).destroy();
									Ext.getCmp('mywindow'+id).hide();
								}
							}]
						});
					
					}

					if (!Ext.getCmp('mywindow'+id)) {
						new Ext.Window({
							title: '{ATF::$usr->trans("cancelConge")}',
							id:'mywindow'+id,
							width: 500,
							height:250,
							plain:true,
							bodyStyle:'padding:5px;',
							buttonAlign:'center',
							items: Ext.getCmp('myForm'+id)
						});
					}
					Ext.getCmp('mywindow'+id).show();		
				}
			}
		};
		
		var btnCancel = {
			xtype:'button',
			id:"cancel",
			buttonText: '',
			buttonOnly: true,
			iconCls: 'btnSup',
			cls:'floatLeft',
			disabled:!allowCancel,
			tooltip: '{ATF::$usr->trans("annuler")}',
			tooltipType:'title',
			listeners: {
				'click': function(fb, v){
					if (!Ext.getCmp('myForm'+id)) {
						new Ext.FormPanel({
							frame:true,
							autoHeight:true,
							id:'myForm'+id,
							name:'myFormName'+id,
							items: [{
								html: '<span class="bold">{ATF::$usr->trans("pour_quelle_raison")|addslashes}</span><hr>',
								xtype:'container'
							},{
								xtype: 'textarea'
								,name: 'raison' 
								,id: 'raison'
								,fieldLabel:'{ATF::$usr->trans("raison")|addslashes}'
								,style: {
									width: '90%',
									height:'120px'
								}
							}],
					
							buttons: [{
								text: 'Ok',
								handler: function(){
									ATF.ajax('conge,annulation.ajax,raison='+$('#raison').val()+'&id_conge='+id);
									Ext.getCmp('myForm'+id).destroy();
									Ext.getCmp('mywindow'+id).hide();
									store.reload();
								}
							},{
								text: 'Annuler',
								handler: function(){
									Ext.getCmp('myForm'+id).destroy();
									Ext.getCmp('mywindow'+id).hide();
								}
							}]
						});
					
					}

					if (!Ext.getCmp('mywindow'+id)) {
						new Ext.Window({
							title: '{ATF::$usr->trans("cancelConge")}',
							id:'mywindow'+id,
							width: 550,
							height:235,
							items: Ext.getCmp('myForm'+id)
						});
					}
					Ext.getCmp('mywindow'+id).show();		
				}
			}
		};
		
		(function(){
			var params = {
				renderTo: idDivCongeAction,
				items:[btnValid,btnRefus,btnCancel]
			};
			var p = new Ext.Container(params);
		}).defer(25);

		return '<div id="'+idDivCongeAction+'"></div>';
	}
};


{/strip}