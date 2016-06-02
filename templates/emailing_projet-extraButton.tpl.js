{strip}
{
	text: '{ATF::$usr->trans(saveSendAndStay,$current_class->name())|escape:javascript}',
	id:'saveSendAndStay_{$current_class->table}',
	{if !ATF::_r('id_emailing_projet')}
		disabled : true,
	{/if}
	handler: function(){
		ATF.basicInfo.getForm().submit({
				submitEmptyText:false,
				method  : 'post',
				waitMsg : '{ATF::$usr->trans(updating_element)|escape:javascript}',
				waitTitle : '{ATF::$usr->trans(loading)|escape:javascript}',
				url     : 'extjs.ajax',
				params: {
					'{$current_class->table}[id_{$current_class->table}]':'{$identifiant}'
					,'extAction':'{$current_class->table}'
					,'extMethod':"saveSendAndStay"
				}
				,success:function(form, action) {
					ATF.unsetFormIsActive();
					if(ATF.currentWindow){
						ATF.currentWindow.close();
					}
					ATF.extRefresh(action); 
				}
				,failure:function(form, action) {
					var title='Problème';
					if (action.failureType === Ext.form.Action.CONNECT_FAILURE){
						Ext.Msg.alert(title, 'Server reported:'+action.response.status+' '+action.response.statusText);
					} else if (action.failureType === Ext.form.Action.SERVER_INVALID){
						Ext.Msg.alert(title, action.result.errormsg);
					} else if (action.failureType === Ext.form.Action.CLIENT_INVALID){
						Ext.Msg.alert(title, "Un champs est mal renseigné");
					} else if (action.failureType === Ext.form.Action.LOAD_FAILURE){
						Ext.Msg.alert(title, "Un champs est mal renseigné");
					}
				}
				,timeout:3600
			});	
		}
},
{/strip}