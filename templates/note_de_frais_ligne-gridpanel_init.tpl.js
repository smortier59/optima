{strip}
{* Ajout du champ nécessaire pour ce renderer *}
{util::push($fieldsKeys,"canValid")}

ATF.renderer.ndflValidation = function(table,field) {
	return function(val, meta, record, rowIndex, colIndex, store) {
		var idDivNdflValidation = Ext.id();
		var id = record.data[table+'__dot__id_'+table];
		var etat = record.data[table+'__dot__etat'];
		if (record.data['canValid'] && etat=='en_cours') {

			(function(){
				var params = {
					renderTo: idDivNdflValidation,
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
									ATF.ajax(table+',valid.ajax','id_note_de_frais_ligne='+id);
									store.reload();
								}
							}
						}
					},{
						xtype:'button',
						id:"Refus"+id,
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
								if (!Ext.getCmp('myForm'+id)) {
									new Ext.FormPanel({
										frame:true,
										autoHeight:true,
										id:'myForm'+id,
										name:'myFormName'+id,
										title: '',
										bodyStyle:'padding:5px 5px 0',
										items: [{
											html: '<span class="bold">{ATF::$usr->trans("pour_quelle_raison")|addslashes}</span><hr>',
											xtype:'container'
										},{
											xtype: 'textarea'
											,name: 'raison' 
											,id: 'raison'
											,hideLabel:true
											,style: {
												width: '99%',
												height:'120px'
											}
										}],
								
										buttons: [{
											text: 'Ok',
											handler: function(){
												ATF.ajax(table+',refus.ajax','id_note_de_frais_ligne='+id+'&raison='+$('#raison').val());
												Ext.getCmp('myForm'+id).destroy();
												Ext.getCmp('mywindow'+id).hide();
												store.reload();
											}
										},{
											text: 'Annuler',
											handler: function(){
												Ext.getCmp('myForm'+id).destroy();
												Ext.getCmp('mywindow'+id).hide();
											}
										}]
									});
								
								}
			
								if (!Ext.getCmp('mywindow'+id)) {
									new Ext.Window({
										title: '{ATF::$usr->trans("cancelNDFLigne")}',
										id:'mywindow'+id,
										width: 500,
										height:250,
										plain:true,
										bodyStyle:'padding:5px;',
										buttonAlign:'center',
										items: Ext.getCmp('myForm'+id)
									});
								}
								Ext.getCmp('mywindow'+id).show();		
							}
			
							
							
							
							
						}
					}]
					
				};
				var p = new Ext.Container(params);
			}).defer(25);
			
			return '<div id="'+idDivNdflValidation+'" class="center"></div>';

		} else {
			return '<div id="'+idDivNdflValidation+'" class="center"><img class="smallIcon noAction" src="'+ATF.blank_png+'" /></div>';
		}


	}
};

{/strip}