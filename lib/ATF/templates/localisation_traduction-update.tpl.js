{strip}
var {$formName} = {
		autoHeight:true,
		{if $renderTo}
			renderTo:'{$renderTo}',
		{/if}
		frame:true,
		title: '{ATF::$usr->trans($current_class->table,module)|escape:javascript} | Création',
		bodyStyle:'padding:5px 5px 0',
		items: [{
			xtype: 'compositefield',
			hideLabel:true,
			msgTarget: 'under',
			id:'nom_colonne',
			height:25,
			items: [
					{ xtype: 'displayfield', value: "{ATF::$usr->trans('localisation','localisation')}", width:300 }
					,{ xtype: 'displayfield', value: "{ATF::$usr->trans('expression_traduite','localisation')}", width:300 }
					,{ xtype: 'displayfield', value: "{ATF::$usr->trans('codename','localisation')}", width:100 }
			]
		},{
			autoHeight:true,
			autoWidth:true,
			id:'ligne_trad',
			defaults: { anchor: '95%' },
			items: []
			,listeners:{
				render: function(lignes){
					{section name=foo start=0 loop=10}
						lignes.add({include file="localisation_traduction-ajout_ligne_trad.tpl.js" ident="{$smarty.section.foo.index}"});
					{/section}
					lignes.doLayout(true);
				}
			}
		}],buttons : [{
				text: 'Sauvegarder',
				handler: function() {
					ATF.basicInfo.getForm().submit({
						submitEmptyText:false,
						method  : 'post',
						waitMsg : '{ATF::$usr->trans(creating_new_element)|escape:javascript}',
						waitTitle : '{ATF::$usr->trans(loading)|escape:javascript}',
						url     : 'extjs.ajax',
						params: {
							'extAction':'{$current_class->table}'
							,'extMethod':'insert'
						}
						,success:function(form, action) {
							ATF.unsetFormIsActive();
							if(action.result.cadre_refreshed){
								{* pour rediriger sur fiche select de l element ou de l element parent *}
								ATF.ajax_refresh(action.result,true);	
							}else{
								{* pour afficher les notices *}
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
				}
		}]
};
{/strip}
ATF.setFormIsActive();	