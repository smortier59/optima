{strip}ATF.speedInsertForm{$table} = new Ext.FormPanel({
	labelWidth: 75,
	renderTo:'divSpeedInsertForm{$table}',
	frame:true,
	bodyStyle:'padding:400 400 0',
	items: [
		{* Panel primaire *}

		{include file="generic-panel.tpl.js" 
			colonnes=$current_class->colonnes.speed_insert 
			title="Données d'insertion rapide" 
			requests=$requests 
			forceVisible=true
			event=speedInsert
			colspan=$current_class->panels.speed_insert.nbCols|default:2 
			panel_key=speed_insert}
		
		
		{* Pour les fichiers *}
		{foreach from=$current_class->files key=key item=item}
			{if $item.obligatoire==1}
				{include file="champ_obligatoire.tpl.htm" assign="img_ob"}
			{/if}
			{if !$item.no_upload}
				,{
					xtype:'hidden'
					,name: '{$current_class->table}[filestoattach][{$key}]'
					,id: '{$current_class->table}[{$key}]'
					{if $event==update && ATF::getClass($current_class->table)->file_exists($identifiant,$key)}
					,value: 'true'
					{/if}
				},{
					xtype:'fieldset',
					title: '{ATF::$usr->trans($key,$current_class->table)} {$img_ob|escape:javascript}',
					collapsible:{if $item.collapsible===false}false{else}true{/if},
					collapsed: {if $item.collapsed==1}true{else}false{/if},
					animCollapse:false,
					autoHeight:true,
					autoWidth:true,
					id:'{$key}',
					defaults: { anchor: '95%' },
					items: {if ATF::$html->template_exists("`$current_class->table`-`$key`-upload_fichier.tpl.js")}
								{include file="`$current_class->table`-`$key`-upload_fichier.tpl.js" table=$current_class->table}									
							{else}
								{include file="generic-upload_fichier.tpl.js" table=$current_class->table}
							{/if}
				}
				{$img_ob=false}
			{/if}
		{/foreach}
		
	]
	,buttons: [
		{
		text: 'Insérer un nouvel enregistrement',
		handler: function(){
			ATF.speedInsertForm{$table}.getForm().submit({
				method  : 'post',
				waitMsg : 'Insertion de l\'élément en cours...',
				waitTitle : 'Chargement',
				url     : 'extjs.ajax',
				params: {
					'extAction':'{$current_class->table}'
					,'extMethod':'speed_insert'
					,
				}
				{if ATF::$html->template_exists("`$parent_class`-`$current_class->table`-speed_insert_success.tpl.js")}
					{include file="`$parent_class`-`$current_class->table`-speed_insert_success.tpl.js"}
				{else}
					,success:function(form, action) {
						if(action.result.result){
							Ext.ComponentMgr.get('{$id}').setValue(action.result.result.id);
							Ext.ComponentMgr.get('label_{$id}').setValue(action.result.result.nom);
							Ext.ComponentMgr.get('speed_insert{$id}').close();
						}else{
							ATF.extRefresh(action); 
						}
					}
				{/if}
				,failure:function(form, action) {
					ATF.extRefresh(action); 
				}
				,timeout:3600
			});
		}
	}
	{if ATF::$html->template_exists("`$current_class->table`-files.tpl.js")}
			{include file="`$current_class->table`-files.tpl.js"}
	{else}
		{foreach from=$current_class->files key=key item=item}
			{if $item.preview}
				,{
					text: 'Prévisualiser {ATF::$usr->trans($key)}',
					handler: function(){
						{if $event==update && ATF::getClass($current_class->table)->file_exists($identifiant,$key) && !$item.force_generate}
							ATF.speedInsertForm{$table}.getForm().submit({
								method  : 'post',
								waitMsg : 'Génération du PDF...',
								waitTitle : 'Chargement',
								url     : 'extjs.ajax',
								params: {
									'{$current_class->table}[id_{$current_class->table}]':'{$identifiant}'
									,'extAction':'{$current_class->table}'
									,'extMethod':"{if $event=='cloner'}cloner{else}update{/if}"
									,'preview':'true'
								}
								,success:function(form, action) {
									if(action.result.result){
										window.location='{$current_class->table}-select-{$key}-'+action.result.result+'.temp'; 
									}else if(action.result.cadre_refreshed){
										ATF.ajax_refresh(action.result,true);
									}else {
										ATF.extRefresh(action); 
									}
								}
								,failure:function(form, action) {
									ATF.extRefresh(action); 
								}
								,timeout:3600
							});
						{else}
							ATF.speedInsertForm{$table}.getForm().submit({
								method  : 'post',
								waitMsg : 'Génération du PDF...',
								waitTitle : 'Chargement',
								url     : 'extjs.ajax',
								params: {
									'extAction':'{$current_class->table}'
									,'extMethod':'insert'
									,'preview':'true'
								}
								,success:function(form, action) {
									if(action.result.result){
										window.location='{$current_class->table}-select-{$key}-'+action.result.result+'.temp'; 
									}else if(action.result.cadre_refreshed){
										ATF.ajax_refresh(action.result,true);
									}else{
										ATF.extRefresh(action); 
									}
								}
								,failure:function(form, action) {
									ATF.extRefresh(action); 
								}
								,timeout:3600
							});
						{/if}
					}
				}
			{/if}
		{/foreach}
	{/if}
	]
});
{/strip}
