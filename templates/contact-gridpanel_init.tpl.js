{strip}
ATF.renderer.actionsContact=function(table,field) {
	return function(filetype, meta, record, rowIndex, colIndex, store) {
		if(record.json){
			var idDivActionsContact = Ext.id();
			var id = record.data[table+'__dot__id_'+table];
			var etat = record.data[table+'__dot__etat'];
			var allowSuivi = record.data['allowSuivi'];
			
			var btnSuivi = {
				xtype:'button',
				id:"createSuivi",
				buttonText: '',
				buttonOnly: true,
				iconCls: 'btnSuivi',
				cls:'floatLeft',
				disabled:allowSuivi,
				tooltip: '{ATF::$usr->trans("createSuivi")}',
				tooltipType:'title',
				listeners: {
					'click': function(fb, v){
						ATF.goTo("suivi-insert.html,suivi_contact.id_contact="+id);
						return false;
					}
				}
			};
			
			var btnVCard = {
				xtype:'button',
				id:"createVcard",
				buttonText: '',
				buttonOnly: true,
				iconCls: 'btnVcard',
				cls:'floatLeft',
				tooltip: '{ATF::$usr->trans("exportVCard")}',
				tooltipType:'title',
				listeners: {
					'click': function(fb, v){
						window.location = "contact,export_vcard.ajax,id="+id;
						return false;
					}
				}
			};
						
			(function(){
				var params = {
					renderTo: idDivActionsContact,
					items:[btnSuivi,btnVCard]
				};
				var p = new Ext.Container(params);
			}).defer(25);
			
			return '<div class="left" id="'+idDivActionsContact+'"></div>';
		}
	}
};
{/strip}