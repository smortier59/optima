{strip}
{* Ajout du champ nécessaire pour ce renderer *}
{util::push($fieldsKeys,"canValid")}

ATF.renderer.ndfValidation = function(table,field) {
	return function(val, meta, record, rowIndex, colIndex, store) {
		var idDivNdfValidation = Ext.id();
		var id = record.data[table+'__dot__id_'+table];
		var etat = record.data[table+'__dot__etat'];

		if (record.data['canValid'] && etat=='en_cours') {

			(function(){
				var params = {
					renderTo: idDivNdfValidation,
					layout:'fit',
					items:[{
						xtype:'button',
						id:"Valid",
						buttonText: '',
						buttonOnly: true,
						iconCls: 'valid',
						style: {
							width: '20px',
							height: '20px',
							float: 'left'
						},
						listeners: {
							'click': function(fb, v){
								if (confirm('{ATF::$usr->trans(Etes_vous_sur)}')) {
									ATF.ajax(table+',valid.ajax','id_note_de_frais='+id);
									store.reload();
								}
							}
						}
					},{
						xtype:'button',
						id:"Refus",
						buttonText: '',
						buttonOnly: true,
						iconCls: 'refus',
						style: {
							width: '20px',
							height: '20px',
							float: 'left'
						},
						listeners: {
							'click': function(fb, v){
								ATF.ajax(table+',refus.ajax','id_note_de_frais='+id);
								store.reload();
							}
						}
					}]
					
				};
				var p = new Ext.Container(params);
			}).defer(25);
			
			return '<div id="'+idDivNdfValidation+'" class="center"></div>';

		} else {
			return '<div id="'+idDivNdfValidation+'" class="center"><img class="smallIcon noAction" src="'+ATF.blank_png+'" /></div>';
		}


	}
};


{/strip}