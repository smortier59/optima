{include file="generic-gridpanel_vars.tpl.js"}
{strip}
var insertNow = function() {
	{$id = ATF::note_de_frais()->cryptId(ATF::note_de_frais()->isExists())}
	{if $id}
		ATF.goTo("{$current_class->table}-update-{$id}.html");
	{else}
	
		if(!$("#insertion").length){
			ATF.loadMask.show();
			Ext.Ajax.request({
			   url: '{$current_class->name()},getUpdateForm.ajax',
			   params:{
				   event:'insert',
				   table:'{$current_class->table}',
				   formName:'formulaire'
				   {if $url_extra},'{$url_extra|replace:"=":"':'"|replace:"&":"',"}'{/if} {* L"avoir plutôt en POST *}
			   },
			   success: function (response, opts) {
				   ATF.loadMask.hide();
					eval(response.responseText);
					formulaire.closable=true;
					if (!formulaire.listeners) {
						formulaire.listeners={};
					}
					formulaire.listeners.close=function(){
						ATF.unsetFormIsActive();
					};
					formulaire.id = "insertion";
					ATF.basicInfo = new Ext.FormPanel(formulaire);
					var i = tab.add(ATF.basicInfo);
					tab.setActiveTab(i);
			   },
			   failure: function() {
					ATF.loadMask.hide();
					alert('erreur_inconnue'); 
			   }
			});
		}else{
			alert("{ATF::$usr->trans('form_ouvert')|escape:javascript}");	
		}
	{/if}
};

{/strip}