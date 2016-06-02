{strip}	
Ext.getCmp('menu_vue_{$pager}').removeAll();

{foreach from=ATF::vue()->genererVue($current_class->table) key=module item=panels}
Ext.getCmp('menu_vue_{$pager}').add({ 
		text: '{ATF::$usr->trans($module,"module")|escape:javascript}'
		,menu: 
		new Ext.menu.Menu({ items:[
			{foreach from=$panels.panel key=nom_panel item=champs}
				{if !$champs@first},{/if}
				{ 
					text: '{ATF::$usr->trans("cadre_$nom_panel",$module)|escape:javascript}'
					,menu: new Ext.menu.Menu({ items:[
						{foreach from=$champs key=nom item=donnees}
							{if !$donnees@first},{/if}
							new Ext.menu.CheckItem({
								text       : '{ATF::$usr->trans($nom,$module)|escape:javascript}',
								itemId     : 'col-{$donnees@index}',
								checked    : ATF.gridCheckCols(Ext.getCmp("{$id}").colModel.config,"{$panels.alias}","{$nom}"),
								hideOnClick: false
								, listeners :{ 
									checkchange : function(elem,check){
										if(check==true){
											ATF.loadMask.show();
											Ext.Ajax.request({
											 url: 'vue,update.ajax',
											 method:"POST",
											   params:{
												   'vue':'{$current_class->table}',
												   'table':'{$current_class->table}',
												   'ordre_colonne':ATF.gridGetCols(Ext.getCmp("{$id}").colModel.config,true),
												   'ajout_champs':'{$nom}',
												   'tab_col':'{$module}',
												   'id_filtre':'{$id_filtre}',
												   'pager':'{$pager}',
												   'pager_parent':'{$pager_parent}',
												   'id':'{$id}',
												   'limit':((ATF.currentpage["{$pager}"]+1)*30)
											   },
											   success: function (response, opts) {
												   elem.addClass('bold');
												   ATF.toolbarAddBold(elem);
												   ATF.currentpage["{$pager}"]=0;
												   ATF.gridRaffraichissement({$name},'{$id}',response.responseText,'{$pager}',searchBox.{$pager},"{$current_class->table}");
											   }
											 });
										}else{
											ATF.loadMask.show();
											Ext.Ajax.request({
											 url: 'vue,update.ajax',
											 method:"POST",
											   params:{
												   'vue':'{$current_class->table}',
												   'table':'{$current_class->table}',
												   'ordre_colonne':ATF.gridGetCols(Ext.getCmp("{$id}").colModel.config,true),
												   'sup_champs':'{util::extJSEscapeDot($nom)}',
												   'id_filtre':'{$id_filtre}',
												   'pager':'{$pager}',
												   'pager_parent':'{$pager_parent}',
												   'id':'{$id}',
												   'limit':((ATF.currentpage["{$pager}"]+1)*30)
											   },
											   success: function (response, opts) {
												   elem.removeClass('bold');
												   ATF.toolbarRemoveBold(elem);
												   ATF.currentpage["{$pager}"]=0;
												   ATF.gridRaffraichissement({$name},'{$id}',response.responseText,'{$pager}',searchBox.{$pager},"{$current_class->table}");
											   }
											 });
										}
									}
								}
							})
						{/foreach}
					]})								  
				}
			{/foreach}
		]})
		,listeners:{
			render:function(){
				{* permet de mettre en valeur les parents des éléments cochés (colonnes affichées dans le grid) *}
				var parents=this.menu.items.items;
				for(cle_parent in parents){
					if(parents[cle_parent].menu){
						var enfants=parents[cle_parent].menu.items.items;
						for(cle_enfant in enfants){
							if(enfants[cle_enfant].checked==true){
								enfants[cle_enfant].addClass("bold");
								parents[cle_parent].addClass("bold");
								this.addClass("bold");
							}
						}
					}
				}
			}
		}
	});	
{/foreach}
Ext.getCmp('menu_vue_{$pager}').add('-');
Ext.getCmp('menu_vue_{$pager}').add({
	text:'{ATF::$usr->trans(reinit_vue)}'
	,icon:'{ATF::$staticserver}images/icones/columns_grise.png'
	,handler:function(){
		ATF.loadMask.show();																	  
		Ext.Ajax.request({
		 url: 'vue,update.ajax',
		 method:"POST",
		   params:{
			   'vue':'{$current_class->table}',
			   'sup':true,
			   'id_filtre':'{$id_filtre}',
			   'function':'{$function}',
			   'table':'{$current_class->table}',
			   'pager':'{$pager}',
			   'pager_parent':'{$pager_parent}',
			   'id':'{$id}'
			   
		   },
		   success: function (response, opts) {
				ATF.gridRaffraichissement({$name},'{$id}',response.responseText,'{$pager}',searchBox.{$pager});
				{* regénération du menu de vue *}
				Ext.Ajax.request({
				 url: '{$current_class->name()},genereMenuVue.ajax',
				 method:"POST",
				   params:{
					   'pager':'{$pager}',
					   'table':'{$current_class->table}',
					   'id_filtre':'{$id_filtre}',
					   'pager_parent':'{$pager_parent}',
					   'id':'{$id}',
					   'function':'{$function}',
					   'name':'{$name}'
				   },
				   success: function (response, opts) {
					   eval(response.responseText);
				   }
				});
		   }
		 });
		 
	}
});
{* Gestion de l affichage des aggregats *}
{if $current_class->afficheAggregate($pager)}	
	Ext.getCmp('menu_vue_{$pager}').add('-');
	Ext.getCmp('menu_vue_{$pager}').add({
		text: '{ATF::$usr->trans(affiche_aggregat)}',
		icon:'{ATF::$staticserver}images/icones/listing_truncate.png',
		handler: function(){
			ATF.actionAggregat("{$pager}","{$id}","{$current_class->table}","{$id_filtre}");	
		}
	});
{/if}
Ext.getCmp('menu_vue_{$pager}').add('-');
Ext.getCmp('menu_vue_{$pager}').add({
	text: '{ATF::$usr->trans(tronque)} / {ATF::$usr->trans(non_tronque)}',
	icon:'{ATF::$staticserver}images/icones/listing_truncate.png',
	handler: function(){
		var liste_class=$('#{$id|replace:"[":"\\\\["|replace:"]":"\\\\]"}').className.split(' ');
		var tronque="non";
		for(nom in liste_class){
			if(liste_class[nom]=="gridPanelNoWrapRow"){
				tronque="oui";
			}
		}
		if (tronque=="non") { 
			/* Tronquer (lignes uniformes) */
			Ext.Ajax.request({
				url: 'vue,update.ajax',
				method:"POST",
				params:{
				   'vue':'{$current_class->table}',
				   'tronque':1
				},
				success: function (response, opts) {
					Ext.getCmp('{$id}').addClass('gridPanelNoWrapRow');
				}
			});
		} else {
			/* Ne pas tronquer (lignes hautes) */
			Ext.Ajax.request({
				url: 'vue,update.ajax',
				method:"POST",
				params:{
				   'vue':'{$current_class->table}',
				   'tronque':0
				},
				success: function (response, opts) {
					Ext.getCmp('{$id}').removeClass('gridPanelNoWrapRow');
				}
			});
		}
	}
});
{/strip}