{* Ajout du bouton duplicate  *}

ATF.renderer.duplicate=function(table,field) {
	return function(filetype, meta, record, rowIndex, colIndex, store) {
        var idDiv = Ext.id();
		var idPanier = record.data.panier__dot__id_panier;
		var btnDuplicate = {
			xtype:'button',
			id:"duplicate"+id,
			buttonText: '',
			buttonOnly: true,
			iconCls: 'iconMailOK',
			cls:'floatLeft',
			tooltip: 'Dupliquer',
			tooltipType:'title',
			listeners: {
				'click': function(fb, v){
					ATF.loadMask.show()
					Ext.Ajax.request({
						url     : 'extjs.ajax',
						params: {
							'extAction':'panier'
							,'extMethod':'duplicatePanier'
						    ,'id_panier':idPanier
						},
						method:"POST",
						waitMsg: '{ATF::$usr->trans(loading_new_page)|escape:javascript}',
                        waitTitle: '{ATF::$usr->trans(loading)|escape:javascript}',
						success: function (r, action) {
							res = {}
							res.response = r;
							ATF.extRefresh(res);
							store.reload();
							ATF.loadMask.hide()
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
