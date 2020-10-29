{* Ajout du bouton duplicate  *}

ATF.renderer.duplicate=function(table,field) {
	return function(filetype, meta, record, rowIndex, colIndex, store) {
		
		console.log('record',record.data);

        var idDiv = Ext.id();
		
		var idPanier = record.data.panier__dot__id_panier;

		console.log('id_panier',idPanier);


		console.log('id',id);

		var btnDuplicate = {
			xtype:'button',
			id:"duplicate"+id,
			buttonText: '',
			buttonOnly: true,
			iconCls: 'iconMailOK',
			cls:'floatLeft',
			tooltip: 'DUpliquer',
			tooltipType:'title',
			listeners: {
				'click': function(fb, v){
					console.log('button clicked');
					Ext.Ajax.request({
						url: 'panier,duplicate.ajax',
						method:"POST",
						params:{
						   'id_panier':idPanier,
						},
						success: function (response, opts) {
							location.reload();
							ATF.ajax_refresh(opts.result,true);
							ATF.extRefresh(opts);
							store.reload();
						 },

						failure: function(response, opts) {
							console.log('faillure' + response.status)
						}

					});
				}
			}
		};
		
		(function(){
			var params = {
				renderTo: idDiv,
				items:[btnDuplicate]

			};
			var p = new Ext.Container(params);
		}).defer(25);
	
		return '<div class="left" id="'+idDiv+'"></div>';
	}
};



ATF.renderer.url_direct_souscription = function(table, field){
	return function(filetype, meta, record, rowIndex, colIndex, store) {
		
		console.log ('records',record.data);

		var idDiv = Ext.id();
		var id = record.data[table+'__dot__id_'+table];
		
		var html = "";

		
			html += 'test url souscription direct';
			
	
		return '<div id="'+idDiv+'">'+html+'</div>';


	}
};