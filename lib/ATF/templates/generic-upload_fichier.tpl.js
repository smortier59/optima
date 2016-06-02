{strip}
{
	{$key=$key|default:$smarty.request.key}
	{$table=$table|default:$smarty.request.table|default:$current_class->table}
	xtype: 'compositefield',
	hideLabel:true,
	msgTarget: 'under',
	items: [
	/*Si l'on ne veut pas que le fichier apparaisse (Ex : revision de devis)*/
	{$no_file=ATF::getClass($table)->default_value($key,$quickMail)}
	
	{capture assign=nom_fichier}
		{if $filename}
			{$filename}
		{elseif ATF::getClass($table)->file_exists($identifiant,$key) && $event=="update" && $identifiant  && !$no_file}
		   {ATF::$usr->trans($key)}                
		{else}
			{ATF::$usr->trans("aucun_fichier")}
		{/if}
	{/capture}

	{ xtype: 'displayfield', id:'oldSchoolUploadLabel-{$table}-{$key}', value: '{$nom_fichier|escape:javascript}', width:200 },
	
	{
		xtype:'button'
		,text:"{ATF::$usr->trans('upload_fichier')}"
		,id:"oldSchoolUpload-{$table}-{$key}"
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
												,'extTpl[{$key}]':'generic-upload_fichier'
												, 'extAction':'{$table}'
												, 'extMethod':'uploadExt'
											},
											waitTitle:'Veuillez patienter',
											waitMsg: 'Chargement ...'
											, success:function(form, action) {
												if (Ext.getCmp("{$table}[{$key}]")) {
													Ext.getCmp("{$table}[{$key}]").setValue(action.result["{$key}"]);
												} else if (Ext.getCmp("{$key}")) {
													Ext.getCmp("{$key}").setValue(action.result["{$key}"]);
												}
												ATF.extRefresh(action);
												win.hide();
											}
											, failure:function(form, action) {
												ATF.extRefresh(action);
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
	{if (($filename && $filename!=ATF::$usr->trans("problem_upload")) || (ATF::getClass($table)->file_exists($identifiant,$key) && $event=="update" && $identifiant)) && !$no_file}
		,{ 
			xtype:'button'
			,id:"oldSchoolUpload-download-{$table}-{$key}"
			,text:"{ATF::$usr->trans('upload')}"
			,handler: function(){
				{if $filename}
					window.location='{$table}-select-{$key}.temp';
				{else}
					window.location='{$table}-select-{$key}-{$identifiant|cryptid}.dl';
				{/if}
			}
		}
		,{ 
			xtype:'button'
			,text:"{ATF::$usr->trans('delete_fichier')}"
			,id:"oldSchoolUpload-delete-{$table}-{$key}"
			,handler: function(){
				ATF.basicInfo.getForm().submit({
					method: 'post',									   	
					url: 'extjs.ajax',
					params: {
						'key':'{$key}'
						,'table':'{$table}'
						,'identifiant':'{$identifiant}'
						,'extTpl[{$key}][0]':'generic-upload_fichier'
						,'extTpl[{$key}][1]':'generic-uploadXHR_fichier'
						, 'extAction':'{$table}'
						, 'extMethod':'delete_uploadExt'
					}
					, success:function(form, action) {
						$("#{$table}[{$key}]").val(action.result["{$key}"]);
						ATF.extRefresh(action);
					}
				});
			}
		}
	{/if}
	]
}
{strip}