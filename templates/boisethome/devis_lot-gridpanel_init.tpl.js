{*strip*}
{* Ajout du champ nécessaire pour ce renderer *}
{util::push($fieldsKeys,"allowFacture")}

ATF.renderer.actionsDevis_lot=function(table,field) {
	return function(filetype, meta, record, rowIndex, colIndex, store) {
		if(record.json){
			var idDivActionsDevis_lot = Ext.id();
			var id = record.data[table+'__dot__id_'+table];
			var etat = record.data[table+'__dot__etat'];
			var allowFacture = record.data['allowFacture'];

			var btnFacture = {
				xtype:'button',
				id:"btnFacture",
				buttonText: '',
				buttonOnly: true,
				iconCls: 'btnFacture',
				cls:'floatLeft',
				disabled: !allowFacture,
				tooltip: '{ATF::$usr->trans("creerFacture")}',
				tooltipType:'title',
				listeners: {
					'click': function(fb, v){
						ATF.goTo("facture-insert.html,id_devis_lot="+id);
						return false;
					}
				}
			};

			(function(){
				var params = {
					renderTo: idDivActionsDevis_lot,
					items:[btnFacture]
				};
				var p = new Ext.Container(params);
			}).defer(25);

			return '<div class="left" id="'+idDivActionsDevis_lot+'"></div>';
		}
	}
};

{*/strip*}