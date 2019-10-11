{strip}
{* Ajout du champ nécessaire pour ce renderer *}
{util::push($fieldsKeys,"allowCmd")}
{util::push($fieldsKeys,"allowUnlockDevis")}
{util::push($fieldsKeys,"allowCancel")}

ATF.renderer.actionsDevis=function(table,field) {
	return function(filetype, meta, record, rowIndex, colIndex, store) {
		if(record.json){
			var idDivActionsDevis = Ext.id();
			var id = record.data[table+'__dot__id_'+table];
			var etat = record.data[table+'__dot__etat'];
			var allowCmd = record.data['allowCmd'];
			var allowUnlockDevis = record.data['allowUnlockDevis'];
			var allowCancel = record.data['allowCancel'];
			
			var btnCmd = {
				xtype:'button',
				id:"btnCommande",
				buttonText: '',
				buttonOnly: true,
				iconCls: 'btnCommande',
				cls:'floatLeft',
				disabled:!allowCmd,
				tooltip: '{ATF::$usr->trans("creerCommande")}',
				tooltipType:'title',
				listeners: {
					'click': function(fb, v){
						if (confirm('{ATF::$usr->trans(Etes_vous_sur)}')) {
							ATF.goTo("commande-insert.html,id_devis="+id);
							return false;
						}
					}
				}
			};
	
			var btnCancel = {
				xtype:'button',
				id:"btnCancel",
				buttonText: '',
				buttonOnly: true,
				iconCls: 'btnCancel',
				cls:'floatLeft',
				disabled:!allowCancel,
				tooltip: '{ATF::$usr->trans("cancelDevis")}',
				tooltipType:'title',
				listeners: {
					'click': function(fb, v){
						if (!Ext.getCmp('myForm'+id)) {
							new Ext.FormPanel({
								frame:true,
								autoHeight:true,
								autoWidth:true,
								id:'myForm'+id,
								name:'myFormName'+id,
								title: '',
								bodyStyle:'padding:5px 5px 0',
								items: [{
									html: '<span class="bold">{ATF::$usr->trans("pour_quelle_raison")|addslashes}</span><hr>',
									xtype:'container'
								},{
									fieldLabel: 'Type'
									,xtype:'radio'
									,boxLabel: '{ATF::$usr->trans("passer_en_perdu","devis")|addslashes}'
									,name: 'action'
									,inputValue: 'perdu' 
									,checked:true
									,listeners:{
										'check':	function (a,b) {
											Ext.ComponentMgr.get('label_id_societe').setDisabled(b);
										}
									}
								},{
									fieldLabel: ''
									,xtype:'radio'
									,boxLabel: '{ATF::$usr->trans("passer_en_annule","devis")|addslashes}'
									,name: 'action' 
									,inputValue: 'annule' 
									,listeners:{
										'check':	function (a,b) {
											Ext.ComponentMgr.get('label_id_societe').setDisabled(b);
										}
									}
								},{
									fieldLabel: ''
									,boxLabel: '{ATF::$usr->trans("passer_en_remplace","devis")|addslashes}'
									,xtype:'radio'
									,name: 'action' 
									,inputValue: 'replace' 
									,listeners:{
										'check':	function (a,b) {
											Ext.ComponentMgr.get('label_id_societe').setDisabled(!b);
										}
									}
								},
								{$item=""}{* pour éviter les conflits d item : actuellement le nom du module courant *}
								{include file="generic-field-textfield.tpl.js" key="id_societe" function="autocomplete" id="id_societe" name="id_societe" fieldLabel="Société" disabled=true}
								,
								{include file="generic-field-textfield.tpl.js" key="id_devis" function="autocomplete" id="id_devis" name="id_devis" fieldLabel="Devis" disabled=true}
								,
								{
									xtype: 'textarea'
									,name: 'raison' 
									,id: 'raison'
									,fieldLabel:'{ATF::$usr->trans("raison")|addslashes}'
									,height:120
									,width:'95%'
								}],
						
								buttons: [{
									text: 'Ok',
									handler: function(){
										Ext.getCmp('myForm'+id).getForm().submit({
											submitEmptyText:false,
											method  : 'post',
											waitMsg : 'Chargement de la nouvelle page ...',
											waitTitle : 'Chargement',
											url     : 'extjs.ajax',
											params: {
												'extAction':'devis'
												,'extMethod':'annulation'
												,'id':id
											}
											,success:function(form, action) {
												Ext.getCmp('myForm'+id).destroy();
												Ext.getCmp('mywindow'+id).hide();
												store.reload();
											}
											,timeout:3600
										});
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
								title: '{ATF::$usr->trans("cancelDevis")} : '+record.data[table+'__dot__ref'],
								id:'mywindow'+id,
								width: 500,
								height:400,
								minWidth: 300,
								minHeight: 200,
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
			
			var btnUnlockDevis = {
				xtype:'button',
				id:"unlockDevis",
				buttonText: '',
				buttonOnly: true,
				iconCls: 'btnUnlock',
				cls:'floatLeft',
				disabled:!allowUnlockDevis,
				tooltip: '{ATF::$usr->trans("unlockDevis")}',
				tooltipType:'title',
				listeners: {
					'click': function(fb, v){
						if (confirm('{ATF::$usr->trans(Etes_vous_sur)}')) {
							ATF.ajax("devis,unlock.ajax,id_devis="+id);
							store.reload();
							return false;
						}
					}
				}
			};
			
			(function(){
				var params = {
					renderTo: idDivActionsDevis,
					items:[btnCmd,btnCancel,btnUnlockDevis]
					
				};
				var p = new Ext.Container(params);
			}).defer(25);
	
			
			return '<div class="left" id="'+idDivActionsDevis+'"></div>';
		}
	}
};

{/strip}