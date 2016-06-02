if(ATF.majListWindow){
	ATF.majListWindow.close();
}
var id = "{ATF::emailing_source()->decryptId(ATF::_r('id_emailing_source'))}";
ATF.majListWindow = new Ext.Window({
	title: '{ATF::$usr->trans("majListWindowTitle","emailing_source")}',
	id:'majListWindow'+id,
	width: 900,
	height:600,
	plain:true,
	autoScroll:true,
	layout:'form',
	bodyStyle:'padding:5px; text-align:center; top:200px',
	closeAction:'hide',
	buttonAlign:'center',
	items: [
		{
			cls:'rounded10 marginAuto',
			width: 800,
			html:"<div class='importantRedDiv' style='min-height:50px;'><img src='{ATF::$staticserver}images/icones/erreur404.png' class='medaillon48'>{ATF::$usr->trans('importantMessagePostImport','emailing_source')}</div>"
		},{
			autoEl:{ tag:'hr', cls:'spacer' }
		},{
			xtype:'button'
			,name:'getXlsList'
			,id:'getXlsList'	
			,fieldLabel:"{ATF::$usr->trans('getXlsListButtonFieldLabel','emailing_source')}"
			,labelStyle:"width:400px"
			,itemCls:'marginAuto buttonCTExportXLS'
			,iconCls:'xls'
			,listeners: {
				'click': function(el, v){
					window.location = 'emailing_contact,export_brut.ajax,withID=true&onglet=gsa_emailing_source_emailing_contact_'+id;
					ATF.majListWindow.hide();
					
				}
			}
		},{
			autoEl:{ tag:'hr', cls:'spacer' }
		},new Ext.FormPanel({
			fileUpload: true,
			width: 600,
			id:'formImportFile',
			autoHeight: true,
			bodyStyle: 'padding: 5px; background-color:transparent; float:left; border:0px;',
			labelWidth: 230,
			items: [{
				xtype: 'fileuploadfield',
				id: 'fichier',
				emptyText: "{ATF::$usr->trans('chooseFile','emailing_source')}",
				fieldLabel: "{ATF::$usr->trans('fileToImport','emailing_source')}",
				name: 'fichier',
				buttonText: '',
				buttonCfg: {
					iconCls: 'btnUpload'
				},
				listeners: {
					'fileselected': function (a,b,c) {
						ATF.majListWindow.getComponent('formImportFile').getForm().submit({
							method: 'post',									   	
							url: 'extjs.ajax',
							params: {
								'key':'fichier'
								,'table':'importer'
								, 'field':'fichier'
								,'identifiant':id
								, 'extAction':'importer'
								, 'extMethod':'uploadExt'
							},
							waitTitle:'Veuillez patienter',
							waitMsg: 'Chargement ...'
							, success:function(form, action) {
							}
							, failure:function(form, action) {
							}
						});
					}
				}
			}]
		}),{
			autoEl:{ tag:'hr', cls:'spacer' }
		},new Ext.FormPanel({
			id:'formMajList',
			autoHeight: true,
			bodyStyle: 'padding: 5px; background-color:transparent; border:0px;',
			labelWidth: 100,
			items: [{
				xtype: 'panel',
				id:'resultDiv',
				bodyStyle: 'width:600px;padding: 5px; background-color:transparent; border:0px;',
			},{
				xtype:'button'
				,text:"Commencer la mise a jour"
				,cls: 'marginAuto'
				,handler: function(){
					ATF.majListWindow.getComponent('formMajList').getForm().submit({
						method: 'post',									   	
						url: 'extjs.ajax',
						params: {
							'key':'fichier'
							,'table':'emailing_source'
							,'extTpl[resultDiv]':'emailing_source-rapport-import-contacts'
							, 'extAction':'emailing_source'
							, 'extMethod':'majListContact'
						},
						waitTitle:'Traitement en cours',
						waitMsg: 'Veuillez patienter, cette opération peut prendre plusieurs minutes selon la taille de la source...',
						timeout: 3600
						, success:function(form, action) {
							ATF.extRefresh(action);
						}
						, failure:function(form, action) {
							if (action.failureType === Ext.form.Action.CONNECT_FAILURE) {
								Ext.Msg.alert('Error', 'Status:'+action.response.status+': '+ action.response.statusText);
							}							
							
						}
					});
				}
			}]
		})
	]
});