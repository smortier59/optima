{strip}
{* Ajout du champ nécessaire pour ce renderer *}
{util::push($fieldsKeys,"allowTermine")}


ATF.renderer.livraisonTermine=function(table,field) {
	return function(filetype, meta, record, rowIndex, colIndex, store) {

		var idDivLivraisonTermine = Ext.id();
		var id = record.data[table+'__dot__id_'+table];
		var allowTermine = record.data['allowTermine'];

		var btnLivraisonTermine = {
			xtype:'button',
			id:"livraisonTermine",
			buttonText: '',
			buttonOnly: true,
			iconCls: 'valid',
			disabled:!allowTermine,
			tooltip: '{ATF::$usr->trans("passerEnTermine")}',
			tooltipType:'title',
			listeners: {
				'click': function(fb, v){
					ATF.ajax('livraison,delivery_Complete.ajax','id_livraison='+id);
					return false;
				}
			}
		};
		
		(function(){
			var params = {
				renderTo: idDivLivraisonTermine,
				layout:'fit',
				items:[btnLivraisonTermine]
				
			};
			var p = new Ext.Container(params);
		}).defer(25);

		
		return '<div class="center" id="'+idDivLivraisonTermine+'"></div>';
			
	}
};

{/strip}