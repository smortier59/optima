ATF.quickMailForm{$table} = new Ext.FormPanel({
	labelWidth: 75,
	renderTo:'divQuickMailForm{$table}',
	frame:true,
	bodyStyle:'padding:400 400 0',
	items: [
		{* Panel primaire *}	
		{include file="generic-panel.tpl.js" 
			colonnes=$current_class->quick_mail_template_field()
			title="Envoi rapide de mail" 
			requests=$requests colspan=1 
			panel_key=quick_mail}

		,{
			xtype:'hidden'
			,name: 'id'
			,id: 'id'
			,value: {$identifiant}
		}

		{* Pour les fichiers *}
		{foreach from=$current_class->files key=key item=item}
			{if $item.quickMail}
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
					items: 
					{if $item.multiUpload}
						{include file="generic-multiple_upload_file.tpl.js" table=$current_class->table}
					{else if ATF::$html->template_exists("`$current_class->table`-`$key`-upload_fichier.tpl.js")}
						{include file="`$current_class->table`-`$key`-upload_fichier.tpl.js" table=$current_class->table}									
					{else}
						[{include file="generic-upload_fichier.tpl.js" table=$current_class->table},{include file="generic-uploadXHR_fichier.tpl.js" table=$current_class->table}]
					{/if}
				}
				{$img_ob=false}
			{/if}
		{/foreach}

	]
	,buttons: [
		{
		text: 'Envoi du mail',
		handler: function(){
			ATF.quickMailForm{$table}.getForm().submit({
				method  : 'post',
				waitMsg : 'Envoi du mail en cours...',
				waitTitle : 'Chargement',
				url     : 'extjs.ajax',
				params: {
					'extAction':'{$current_class->table}'
					,'extMethod':'quick_mail'
					,
				}
				{if ATF::$html->template_exists("`$parent_class`-`$current_class->table`-quick_mail_success.tpl.js")}
					{include file="`$parent_class`-`$current_class->table`-quick_mail_success.tpl.js"}
				{else}
					,success:function(form, action) {
						if(action.result.result){
							Ext.ComponentMgr.get('quick_mail{$id}').close();
						}
						ATF.extRefresh(action); 
					}
				{/if}
				,failure:function(form, action) {
					ATF.extRefresh(action); 
				}
				,timeout:3600
			});
		}
	}
	]
});
