{strip}
{util::push($fieldsKeys,"showModif")}
{util::push($fieldsKeys,"showAvModif")}

ATF.renderer.traceModif=function(table,field) {
	return function(filetype, meta, record, rowIndex, colIndex, store) {
		var idDivModif = Ext.id();
		var id = record.data[table+'__dot__id_'+table];
		var showModif = record.data['showModif'];
		var showAvModif = record.data['showAvModif'];
		
		var btnModif = {
			xtype:'button',
			id:"modif",
			buttonText: '',
			buttonOnly: true,
			iconCls: 'valid',
			cls:'floatLeft',
			disabled:!showModif,
			tooltip: '{ATF::$usr->trans(modification,tracabilite)}',
			tooltipType:'title',
			listeners: {
				'click': function(fb, v){
					Modalbox.show('tracabilite_modification.dialog', { scrollTo:0, width: 700, title: '{ATF::$usr->trans(modification,tracabilite)}', params: 'table={$current_class->table}&id_trace='+id, method:'post' });
				}
			}
		};
		
		var btnAvModif = {
			xtype:'button',
			id:"avmodif",
			buttonText: '',
			buttonOnly: true,
			iconCls: 'valid',
			cls:'floatLeft',
			disabled:!showAvModif,
			tooltip: '{ATF::$usr->trans(avant_modification,tracabilite)}',
			tooltipType:'title',
			listeners: {
				'click': function(fb, v){
					Modalbox.show('tracabilite_avant_modification.dialog', { scrollTo:0, width: 700, height: 500, title: '{ATF::$usr->trans(avant_modification,tracabilite)}', params: 'table={$current_class->table}&id_trace='+id, method:'post' });
				}
			}
		};
		
		(function(){
			var params = {
				renderTo: idDivModif,
				items:[btnModif,btnAvModif]
			};
			var p = new Ext.Container(params);
		}).defer(25);

		return '<div id="'+idDivModif+'"></div>';
	}
};
{/strip}