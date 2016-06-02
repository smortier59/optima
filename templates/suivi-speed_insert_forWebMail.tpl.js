{strip}
ATF.speedInsertForm{$table} = new Ext.FormPanel({
	labelWidth: 75,
	frame:true,
	bodyStyle:'padding:400 400 0',
	items: [
		{* Panel primaire *}	
		{include file="generic-panel.tpl.js" 
			colonnes=$current_class->colonnes.speed_insert 
			title="Données d'insertion rapide" 
			readingDirection=true
			requests=$requests 
			colspan=$current_class->panels.speed_insert.nbCols|default:1
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
				}
				{if ATF::$html->template_exists("`$parent_class`-`$current_class->table`-speed_insert_success.tpl.js")}
					{include file="`$parent_class`-`$current_class->table`-speed_insert_success.tpl.js"}
				{else}
					,success:function(form, action) {
						if(action.result.result){
							ATF.currentWindow.close();
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
	{/if}
	]
});
if(ATF.currentWindow){
	ATF.currentWindow.close();
}
ATF.currentWindow = new Ext.Window({
	title: '{ATF::$usr->trans("suiviAssocie")}',
	id:'mywindow',
	width: 900,
	buttonAlign:'center',
	closable:true,
	items: ATF.speedInsertForm{$table}
}).show();



{/strip}
