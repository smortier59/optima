
ATF.renderer.actif=function(table,field) {
	return function(val, meta, record, rowIndex, colIndex, store) {
		var idDivE = Ext.id();
		var id = record.data[table+'__dot__id_'+table];
		if (record.data[table+'__dot__'+field]) {
			var etat = record.data[table+'__dot__'+field];
		} else {
			var etat = record.data[field];
		}

		if (etat) {
			var defaut = false;
			html = '<img class="smallIcon ';
			switch (etat) {
				case 'mort':
					html += 'perdu';
				break;	
				case 'ok':
					html += 'valid';
				break;	
				default:
					defaut = true;
					html = '<span style="font-weight:bold">'+ATF.usr.trans(etat,table)+'</span>';
				break;
			}
			if (!defaut) {
				html += '" title="'+ATF.usr.trans(etat,table)+'"  alt="'+ATF.usr.trans(etat,table)+'" src="'+ATF.blank_png+'" />';
			}
			return '<div class="center" id="'+idDivE+'">'+html+'</div>';
		}
	}
};


ATF.renderer.deblocage = function(table, field){
	return function(filetype, meta, record, rowIndex, colIndex, store) {
		var idDiv = Ext.id();
		var id_societe = record.data[table+'__dot__id_'+table];
		var field = store.fields.keys[colIndex - 1];

		var date_blocage = record.data.societe__dot__date_blocage
		
		var btnBlocage = {
			xtype:'button',
			id:"deblocage"+id_societe,
			//buttonText: '<img class="smallIcon update"/>',
			text:'<img class="smallIcon update"/>',
			buttonOnly: true,
			// iconCls: 'iconMailOK',
			cls:'center',
			tooltip: 'deblocage',
			tooltipType:'title',
			listeners: {
				'click': function(fb, v){
					ATF.loadMask.show()
					Ext.Ajax.request({
						url     : 'extjs.ajax',
						params: {
							'extAction':'societe'
							,'extMethod':'deblocageSociete'
						    ,'id_societe':id_societe
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
				items:[btnBlocage]

			};
			var p = new Ext.Container(params);
		}).defer(25);
	
		if(date_blocage){
			return '<div class="center" id="'+idDiv+'"></div>';
		}
	}
};
