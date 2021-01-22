ATF.renderer.licence_prise=function(table,field) {
	return function(filetype, meta, record, rowIndex, colIndex, store) {
		var id = record.data[table+'__dot__id_'+table];
		if (record.json.deja_pris) {
			return '<img src="{ATF::$staticserver}images/icones/form-valid.png" height="16" width="16" alt="" />';
		} else {
			return '<img src="{ATF::$staticserver}images/icones/no_2.png" />';
		}

	}
};

ATF.renderer.deleteDateEnvoi=function(table, field) {
	return function(filetype, meta, record, rowIndex, colIndex, store) {


		var idDiv = Ext.id();
		var id = record.data[table+'__dot__id_'+table];

		if (record.json["licence.date_envoi"]) {

				var btndecision = {
					xtype:'button',
					id:'btnResetDateEnvoi',
					scale: 'small',
					split: true,
					tooltip:'{ATF::$usr->trans("Supprimer date envoi")}',
					iconCls: 'iconFiltreDelete',
					handler: function () {
						Ext.Msg.confirm(
							"{ATF::$usr->trans(Etes_vous_sur)}",
							"{ATF::$usr->trans(Supprimer_date_envoi)}",
							function (btn) {
								if (btn=="yes") {
									ATF.loadMask.show();
									ATF.tpl2div('{$current_class->table},deleteDateEnvoi.ajax','id_licence=' + id,{ onComplete: function() { ATF.loadMask.hide(); }});
								}
							}
						);
					},
					style: {
						marginLeft: "5px",
						float: 'left'
					},
				};

				(function(){
					var params = {
						renderTo: idDiv,
						items:[btndecision]

					};
					var p = new Ext.Container(params);
				}).defer(25);
				return '<div class="left" id="'+idDiv+'"></div>';
		} else {
			return '<img src="{ATF::$staticserver}images/icones/no_2.png" />';
		}
	}
};


