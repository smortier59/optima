{strip}
{if $identifiant}
	ATF.basicInfo.getForm().submit({
		submitEmptyText:false,
		method  : 'post',
		waitMsg : '{ATF::$usr->trans(updating_element)|escape:javascript}',
		waitTitle : '{ATF::$usr->trans(loading)|escape:javascript}',
		url     : 'extjs.ajax',
		params: {
			'{$current_class->table}[id_{$current_class->table}]':'{$identifiant}'
			,'extAction':'{$current_class->table}'
			,'extMethod':"{if $event=='cloner'}cloner{else}update{/if}"
		}
		,success:function(form, action) {
			ATF.unsetFormIsActive();
			if(ATF.currentWindow){
				ATF.currentWindow.close();
			}
			if(redirect){
				ATF.goTo(redirect+action.result.result);
			}else if(action.result.cadre_refreshed){
				ATF.ajax_refresh(action.result,true);
			}else if(action.result.result){
				ATF.goTo("{$current_class->table}-select-"+action.result.result+".html");
			}else{
				ATF.extRefresh(action); 
			}
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
{else}
	ATF.basicInfo.getForm().submit({
		submitEmptyText:false,
		method  : 'post',
		waitMsg : '{if $current_class->table=="preferences"}{ATF::$usr->trans(updating_pref,preferences)|escape:javascript}{else}{ATF::$usr->trans(creating_new_element)|escape:javascript}{/if}',
		waitTitle : '{ATF::$usr->trans(loading)|escape:javascript}',
		url     : 'extjs.ajax',
		params: {
			'extAction':'{$current_class->table}'
			,'extMethod':'insert'
		}
		,success:function(form, action) {
			ATF.unsetFormIsActive();
			if(ATF.currentWindow){
				ATF.currentWindow.close();
			}

			if(redirect && !action.result.error.length){
				{* si besoin d une redirection speciale *}
				ATF.goTo(redirect+action.result.result);
			}else if(action.result.cadre_refreshed){
				{* pour rediriger sur fiche select de l element ou de l element parent *}
				ATF.ajax_refresh(action.result,true);	
			}else if(action.result.result){
				{* redirection sur fiche select (parachute) *}
				ATF.goTo("{$current_class->table}-select-"+action.result.result+".html");
			}else{
				{* pour afficher les notices *}
				ATF.extRefresh(action); 
			}
			if (ATF.windowPreference) {
				ATF.windowPreference.hide();
			}					
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
{/if}
{/strip}