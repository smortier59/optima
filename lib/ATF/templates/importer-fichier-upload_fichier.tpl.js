{*strip*}
[{
	xtype: 'label',
	tpl: new Ext.XTemplate(
		'<p style="font-weight: bold; font-size: 16px; line-height:30px">{ATF::$usr->trans("some_advice","importer")|addslashes}</p>',
		'<ul>',
		'<li >{ATF::$usr->trans("first_line","importer")|addslashes}</li>',
		'<li>{ATF::$usr->trans("format","importer")|addslashes}</li>',
		'<li>{ATF::$usr->trans("encodage","importer")|addslashes}</li>',
		'</ul>'
	),
	data: []
 }
 ,
 {
	{$key=$key|default:$smarty.request.key}
	{$table=$table|default:$smarty.request.table|default:$current_class->table}
	xtype: 'compositefield',
	id:'commpo',
	hideLabel:true,
	style: 'padding-top:20px;',
	msgTarget: 'under',
	items: [

	{capture assign=nom_fichier}
	{if $filename}
		{$filename}
	{elseif ATF::getClass($table)->file_exists($identifiant,$key) && $event=="update" && $identifiant}
        {ATF::$usr->trans($key)}
	{else}
		{ATF::$usr->trans("aucun_fichier")}
	{/if}
	{/capture}

	{
		xtype: 'displayfield',
		value: '{$nom_fichier|escape:javascript}',
		id:'label_{$table}[{$key}]',
		width:350
	},{
		xtype:'button'
		,text:"{ATF::$usr->trans('upload_fichier')}"
		,id: "validFileBtn"
		,listeners:{
		   'click': function(){
				var win=new Ext.Window({
					layout: 'fit',
					title: "{ATF::$usr->trans('upload_fichier')}",
					width:400,
					height:150,
					items:[
						new Ext.FormPanel({
							fileUpload: true,
							id:'{$key}_form',
							width: 500,
							autoHeight: true,
							labelWidth: 50,
							bodyStyle: 'padding: 10px 10px 10px 10px;',
							items: [{
								xtype: 'fileuploadfield',
								emptyText: "{ATF::$usr->trans('upload_fichier')}",
								fieldLabel: 'Fichier',
								name: '{$key}',
								buttonText: 'Parcourir...',
								width: 300
							},{
								xtype:'textfield',
								name:'field',
								value:'{$key}',
								inputType:'hidden'
							}],
							buttons: [{
								text: 'Valider',
								handler: function(){
									if(Ext.getCmp('{$key}_form').getForm().isValid()){
										Ext.getCmp('{$key}_form').getForm().submit({
											method: 'post',
											url: 'extjs.ajax',
											params: {
												'key':'{$key}'
												,'table':'{$table}'
												,'identifiant':'{$identifiant}'
												, 'extAction':'{$table}'
												, 'extMethod':'uploadExt'
											},
											waitTitle:'Veuillez patienter',
											waitMsg: 'Chargement ...'
											, success:function(form, action) {
												Ext.getCmp("{$table}[{$key}]").setValue(action.result["{$key}"]);
												Ext.getCmp("label_{$table}[{$key}]").setValue(action.result["{$key}"]);
												Ext.getCmp("mappingBtn").setDisabled(false);
												Ext.getCmp("validFileBtn").setDisabled(true);
												Ext.getCmp("mappingBtn").handler();
												win.hide();
											}
											, failure:function(form, action) {
												ATF.log(form);
												ATF.log(action);
												/*ATF.extRefresh(action);*/
											}
										});
									}
								}
							},{
								text: 'Annuler',
								handler: function(){
									Ext.getCmp('{$key}_form').getForm().reset();
									win.hide();
								}
							}]
						})
					]
				}).show();
		   }
		}
	}
	,{
		xtype:'button'
		,text:"Charger le fichier pour la liaison des colonnes"
		,id: "mappingBtn"
		,disabled: true
		,handler: function(){
			if($('#importer\\[id_module\\]').val()){
				ATF.basicInfo.getForm().submit({
					method: 'post',
					url: 'extjs.ajax',
					params: {
						'key':'{$key}'
						,'table':'{$table}'
						,'identifiant':'{$identifiant}'
						,'extTpl[panel_{$key}]':'importer-{$key}-liaison'
						, 'extAction':'{$table}'
						, 'extMethod':'chargerFichier'
					}
					, success:function(form, action) {
						$("#{$table}\\[{$key}\\]").val(action.result["{$key}"]);
						ATF.extRefresh(action);
						if (action.result.extTpl.panel_fichier.indexOf('triggerAction')>0) {
							Ext.getCmp("mappingBtn").setDisabled(true);
						}
					}
					, failure:function(form, action) {
						console.log("failure");
					}
				});
			}else{
				alert("Veuillez préciser le module sur lequel vous souhaitez réaliser l'import");
			}
		}
	}
	]
},{
	xtype:'panel'
	,id:"panel_{$key}"
}
]
{*/strip*}