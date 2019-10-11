{if $smarty.request.id_societe}
	{$id_societe=ATF::societe()->decryptId($smarty.request.id_societe)}
{/if}

{strip}ATF.RapprocherFacturesForm = new Ext.FormPanel({
	autoWidth: true,
	frame:true,
	items: [
		{* Panel primaire *}	
		{include file="generic-panel.tpl.js" 
			colonnes=$current_class->colonnes.rapprocher 
			title="Rapprocher factures" 
			readingDirection=true
			requests=$requests 
			colspan=1 
			panel_key=rapprocher}
	]
	,buttons: [
		{
		text: 'Valider',
		handler: function(){
			ATF.RapprocherFacturesForm.getForm().submit({
				method  : 'post',
				waitMsg : 'Rapprocher factures...',
				waitTitle : 'Chargement',
				url     : 'extjs.ajax',
				params: {
					'extAction':'{$current_class->table}'
					,'extMethod':'updateRapprocher'
				}
				,success:function(form, action) {
					if(action.result.result){
						var montant=Ext.getCmp('societe[montant]').getValue();
						Ext.getCmp('rapprocher_{$id_societe}').destroy();
						ATF.getRapprocherWindow(montant);
					}else{
						ATF.extRefresh(action); 
					}
				}
				,failure:function(form, action) {
					ATF.extRefresh(action); 
				}
				,timeout:3600
			});
		}
	}
	]
});
{/strip}
