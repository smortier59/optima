{strip}
{* Ajout du champ nécessaire pour ce renderer *}
{util::push($fieldsKeys,"allowCmd")}

ATF.renderer.actionsMission=function(table,field) {
	return function(filetype, meta, record, rowIndex, colIndex, store) {
		if(record.json){
			var idDivActionsMission = Ext.id();
			var id = record.data[table+'__dot__id_'+table];
			var etat = record.data[table+'__dot__etat'];
			
			{* En attente, on peut tout faire *}
			if (etat=="en_attente") {
				var allowValid = true;
				var allowCancel = true;		
				var allowFacture = false;		
			{* Valide, on ne peut pas valider encore une fois	 *}	
			} else if (etat=="validee") {
				var allowValid = false;
				var allowCancel = true;				
				var allowFacture = true;		
			} else {
				var allowValid = false;
				var allowCancel = false;				
				var allowFacture = false;		
			}

			var btnValid = {
				xtype:'button',
				id:"btnValid"+id,
				buttonText: '',
				buttonOnly: true,
				iconCls: 'valid',
				cls:'floatLeft',
				disabled:!allowValid,
				tooltip: '{ATF::$usr->trans("valid_mission",$current_class->table)}',
				tooltipType:'title',
				listeners: {
					'click': function(fb, v){
						if (confirm('{ATF::$usr->trans(Etes_vous_sur)}')) {
							ATF.ajax("mission,valid.ajax,id="+id,null,{ onComplete : function() {
								store.reload();
							} });
							return false;
						}
					}
				}
			};

			var btnCancel = {
				xtype:'button',
				id:"btnCancel"+id,
				buttonText: '',
				buttonOnly: true,
				iconCls: 'btnCancel',
				cls:'floatLeft',
				disabled:!allowCancel,
				tooltip: '{ATF::$usr->trans("cancel_mission",$current_class->table)}',
				tooltipType:'title',
				listeners: {
					'click': function(fb, v){
						if (confirm('{ATF::$usr->trans(Etes_vous_sur)}')) {
							ATF.ajax("mission,cancel.ajax,id="+id,null,{ onComplete : function() {
								store.reload();
							} });
							return false;
						}
					}
				}
			};

			var btnFacture = {
				xtype:'button',
				id:"btnFacture"+id,
				buttonText: '',
				buttonOnly: true,
				iconCls: 'btnFacture',
				cls:'floatLeft',
				disabled:!allowFacture,
				tooltip: '{ATF::$usr->trans("create_facture",$current_class->table)}',
				tooltipType:'title',
				listeners: {
					'click': function(fb, v){
						if (confirm('{ATF::$usr->trans(Etes_vous_sur)}')) {
							ATF.goTo("facture-insert.html,id_mission="+id);
							return false;
						}
					}
				}
			};

			
			(function(){
				var params = {
					renderTo: idDivActionsMission,
					items:[btnValid,btnFacture]
					
				};
				var p = new Ext.Container(params);
			}).defer(25);
	
			
			return '<div class="left" id="'+idDivActionsMission+'"></div>';
		}
	}
};

{/strip}