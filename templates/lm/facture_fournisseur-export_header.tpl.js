{strip}
{
	text: 'Export des achats'
	, handler: function(b,e){
		window.location='{$current_class->name()},export_data.ajax,onglet={$pager}';
	}
},
{
	text: 'Export des immobilisations'
	, handler: function(b,e){
		window.location='{$current_class->name()},export_cegid.ajax,onglet={$pager}';
	}
},
{
	text: 'Export des immobilisations (y compris déja exporté)'
	, handler: function(b,e){
		window.location='{$current_class->name()},export_cegid.ajax,onglet={$pager}&force=true';
	}
},{
	text: '<hr />'
},
{
	text: 'Export fichier AP (ADEO)'
	, handler: function(b,e){
		window.location='{$current_class->name()},export_ap.ajax,onglet={$pager}';
	}
},
{
	text: 'Export Immo pour ADEO'
	, /*handler: function(b,e){
		window.location='{$current_class->name()},export_immo_adeo.ajax,onglet={$pager}';
	}*/
	handler: function(btn, ev) {

		ATF.basicInfo = new Ext.FormPanel({
			frame: true,
			width: 900,
			items: [{
				html: "<strong>Export des immo pour les contrats en engagement en date du : </strong>"
			},
			{
				xtype: 'datefield',
		        anchor: '50%',
		        fieldLabel: 'Date',
		        name: 'date',
                id: 'date',
		        format: 'Y-m-d'
			}],
			buttons:[{
				text : "Exporter",
				handler : function(){
					var field = Ext.getCmp('date');
					window.location='{$current_class->name()},export_immo_adeo.ajax,onglet={$pager}&date='+field.value;

				}
			}]
		});
		ATF.unsetFormIsActive();

		ATF.currentWindow = new Ext.Window({
			title: 'Export des immobilisations ADEO',
			id:'mywindow',
			width: 900,
			buttonAlign:'center',
			autoScroll:false,
			closable:true,
			items: ATF.basicInfo
		}).show();

	}
}
{/strip}