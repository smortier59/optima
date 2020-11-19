{* Ajout du bouton details*}

ATF.renderer.loupe=function(table,field) {
	return function(filetype, meta, record, rowIndex, colIndex, store) {
		console.log('loupe', record.data);

        var idDiv = Ext.id();
	
		var loupe = {
			xtype:'button',
			id:"loupe"+id,
			buttonText: '',
			buttonOnly: true,
			iconCls: 'smallIcon select',
			cls:'floatLeft',
			tooltip: 'details',
			tooltipType:'title',
			listeners: {
				'click': function(fb, v){
				  console.log ('suis dans site_associe');
				}
			}
		};
		
		(function(){
			var params = {
				renderTo: idDiv,
				items:[loupe]

			};
			var p = new Ext.Container(params);
		}).defer(25);
	
		return '<div class="left" id="'+idDiv+'"></div>';
	}
};



