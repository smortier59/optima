{strip}
{* Ajout du champ nécessaire pour ce renderer *}
{util::push($fieldsKeys,"isResponsable")}

ATF.renderer.actionsCommission=function(table,field) {
	return function(filetype, meta, record, rowIndex, colIndex, store) {
		if(record.json){
			var idDivActionsCommission = Ext.id();
			var id = record.data[table+'__dot__id_'+table];
			var isResponsable = record.data['isResponsable'];
			var btnValide = {
				xtype:'button',
				id:"valide",
				buttonText: '',
				buttonOnly: true,
				iconCls: 'valid',
				cls:'floatLeft',
				disabled:!isResponsable,
				tooltip: '{ATF::$usr->trans("valider","commission")}',
				tooltipType:'title',
				listeners: {
					'click': function(fb, v){
						if (confirm('{ATF::$usr->trans(Etes_vous_sur)}')) {
							ATF.ajax('commission,setInfos.ajax','id_commission='+id+'&etat=valide');
							store.reload();
							return false;
						}
					}
				}
			};
			var allowRefuse = {
				xtype:'button',
				id:"refus",
				buttonText: '',
				buttonOnly: true,
				iconCls: 'refus',
				cls:'floatLeft',
				disabled:!isResponsable,
				tooltip: '{ATF::$usr->trans("refuser","commission")}',
				tooltipType:'title',
				listeners: {
					'click': function(fb, v){
						if (confirm('{ATF::$usr->trans(Etes_vous_sur)}')) {
							ATF.ajax('commission,setInfos.ajax','id_commission='+id+'&etat=refuse');
							store.reload();
							return false;
						}
					}
				}
			};
			var allowPaye = {
				xtype:'button',
				id:"paye",
				buttonText: '',
				buttonOnly: true,
				iconCls: 'btnFacture',
				cls:'floatLeft',
				disabled:!isResponsable,
				tooltip: '{ATF::$usr->trans("payer","commission")}',
				tooltipType:'title',
				listeners: {
					'click': function(fb, v){
						if (confirm('{ATF::$usr->trans(Etes_vous_sur)}')) {
							ATF.ajax('commission,setInfos.ajax','id_commission='+id+'&etat=paye');
							store.reload();
							return false;
						}
					}
				}
			};
			(function(){					
					var params = {
						renderTo: idDivActionsCommission,
						items:[btnValide,allowRefuse,allowPaye]
					};
					
				var p = new Ext.Container(params);
			}).defer(25);
			return '<div class="left" id="'+idDivActionsCommission+'"></div>';
		}
	}
};

ATF.rowEditor.setInfos=function(table,field) {
	return new Ext.form.TextField({
		value: 0,
		id:table+'_'+field+'_'+Ext.id(),
		fieldLabel: '',
		listeners:{
			change:function(f) {
				ATF.ajax(table+',setInfos.ajax','id_'+table+'='+this.gridEditor.record.data[table+'__dot__id_'+table]+'&'+field+'='+this.getValue());
			}
			,specialkey: function(tf, e){
				if (e.getKey() == e.ENTER) {
					ATF.ajax(table+',setInfos.ajax','id_'+table+'='+this.gridEditor.record.data[table+'__dot__id_'+table]+'&'+field+'='+this.getValue());
				}
			}
		}
	});
};


{/strip}