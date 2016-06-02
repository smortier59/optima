{strip}

{$pager=$current_class->genericSelectAllDivName($div,$parent_class)}

{if $current_class->selectExtjs}
{*if true*}
	var nb=1;
	var tab = new Ext.TabPanel({
		{if $renderTo}renderTo:'{$renderTo}',{/if}
		id:"{$pager}TabPanel",
		frame:false,
		listeners : {
			tabchange:function(a,b){			
				ATF.ajax("filtre_user,saveActiveTab.ajax","module={$current_class->table}&id_filtre_user="+this.getActiveTab().id);			
				for(var i=1; i<nb; i++){
					if(this.items.get(i).id == filtre){
						var tabs = this.items.get(i).tabEl;
						Ext.get(tabs).down('a.x-tab-strip-close').toggleClass('x-hidden',true);
					}				
				}
				
				var tabHeader = this.getActiveTab().tabEl;
				if(Ext.get(tabHeader).down('a.x-hidden')){								
					Ext.get(tabHeader).down('a.x-hidden').toggleClass('x-hidden',false);	
				}							
				filtre = this.getActiveTab().id;
			}		
		}
	});


	{* Fonction d insertion *}
	var insertNow = function() {
		if(!$("insertion").length){
			ATF.loadMask.show();
			ATF.goTo("{$current_class->name()}-insert.html");

		}else{
			alert("{ATF::$usr->trans('form_ouvert')|escape:javascript}");	
		}
	};


	{$id=$pager}
	{include file="generic-gridpanel.tpl.js" 
		renderTo=null 
		div=$div
		name=grid
		height=600
		parent_class=$parent_class
		fromTab=tab
		closable=false}
		
	tab.setActiveTab(0); 	
{else}

	{********* Provenance Graph de stats **********}
	{if ATF::_r(stats)}
		{$filtre_cree=$current_class->statsFiltrage()}
	{/if}

	var nb=1; 
	var tab = new Ext.TabPanel({
		{if $renderTo}renderTo:'{$renderTo}',{/if}
		id:"{$pager}TabPanel",
		frame:false,
		listeners : {
			tabchange:function(a,b){			
				ATF.ajax("filtre_user,saveActiveTab.ajax","module={$current_class->table}&id_filtre_user="+this.getActiveTab().id);			
				for(var i=1; i<nb; i++){
					if(this.items.get(i).id == filtre){
						var tabs = this.items.get(i).tabEl;
						Ext.get(tabs).down('a.x-tab-strip-close').toggleClass('x-hidden',true);
					}				
				}
				
				var tabHeader = this.getActiveTab().tabEl;
				if(Ext.get(tabHeader).down('a.x-hidden')){								
					Ext.get(tabHeader).down('a.x-hidden').toggleClass('x-hidden',false);	
				}							
				filtre = this.getActiveTab().id;
			}		
		}
	});


	{* Fonction d insertion *}
	var insertNow = function() {
		if(!$("insertion").length){
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
					var t = Ext.getCmp("{$pager}TabPanel");
					var i = t.add(ATF.basicInfo);
					t.setActiveTab(i);
			   },
			   failure: function() {
					ATF.loadMask.hide();
					alert('erreur_inconnue'); 
			   }
			});
		}else{
			alert("{ATF::$usr->trans('form_ouvert')|escape:javascript}");	
		}
	};
	
	{$id=$pager}
	{if $parent_class && $parent_class->table!=$current_class->table}
		{$height = false}
	{else}
		{$height = 600}
	{/if}

	{include file="generic-gridpanel.tpl.js" 
		renderTo=null 
		div=$div
		name=grid
		height=$height
		parent_class=$parent_class
		fromTab=tab
		closable=false}




{/if}

	var activeTab = 0;

	var nb=1;
	{if $module = ATF::module()->from_nom($current_class->name())}
		{foreach from=ATF::filtre_user()->getActiveFilters($current_class->name()) key=k item=i}
			{$id=$i.id_filtre_user}
			{if $i.active}
				activeTab = "{$k}";			
				activeTab = parseInt(activeTab)+1;			
			{/if}
			{include file="generic-gridpanel.tpl.js" 
				renderTo=null 
				pager=null
				q=null
				pager="`$pager`_`$i.id_filtre_optima`"
				pager_parent="`$pager`"
				id=$id
				name="grid_`$i.id_filtre_optima`"
				fromTab=tab
				closable=true
				id_filtre=$i.id_filtre_optima
				title=ATF::filtre_optima()->select($i.id_filtre_optima,filtre_optima)}
				
				var tabHeader = tab.items.get(nb).tabEl;	
				Ext.get(tabHeader).down('a.x-tab-strip-close').toggleClass('x-hidden');
				nb++;
		{/foreach}

		{foreach from=$current_class->filtre_ob key=k item=i}
			{$id=$k}
			{include file="generic-gridpanel.tpl.js" 
				renderTo=null 
				pager=null
				q=null
				pager="`$pager`_`$k`"
				pager_parent="`$pager`"
				id="`$k`"
				name="grid_`$k`"
				fromTab=tab
				function="`$i.function`"
				closable=false
				id_filtre="`$k`"
				title="`$i.titre`"}
		{/foreach}
		
		{if ATF::$usr->privilege($current_class->name(),'filter_select')}
		tab.add({
			xtype:'panel',
			title: 'Filtre',
			id:'{$pager}tabFiltre',
			autoHeight:true,
			listeners:{
				render:function(){
					ATF.loadMask.show();	
					Ext.Ajax.request({
					   url: '{$current_class->name()},loadFilters.ajax',
					   params:{
						   'id_tab':'{$pager}tabFiltre',
						   'id_tab_panel':'{$pager}TabPanel',
						   'table':'{$current_class->table}',
						   'parent_table':'{$parent_class->table}',
						   'onglet':'{$onglet}',
						   'pager':'{$pager}',
						   'search':'{$search}'
						   {foreach from=$fk key=key item=item}
							,'fk[{$key}]':'{$item}'
						   {/foreach}
					   },
					   success: function (response, opts) {
						    ATF.loadMask.hide();
							eval(response.responseText);
					   },
					   failure: function() {
							ATF.loadMask.hide();
							alert('erreur_inconnue'); 
					   }
					});
				}
			}
		});
		{/if}
	{/if}
	var filtre = tab.items.get(activeTab).id;

	tab.setActiveTab(activeTab); 

{/strip}