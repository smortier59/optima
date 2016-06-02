{strip}

{if $current_class->selectExtjs}
	var {$formName} = {
		{if $renderTo}
			renderTo:'{$renderTo}',
		{/if}
		layout:'form',
		/*frame:true,*/
		border:false,
		autoHeight:true,
		style: {
			padding : '0 25px 0 10px',
		},
		id: "{$idForm}",

		{if !$notitle}
			title: '{ATF::$usr->trans($current_class->table,module)|escape:javascript} | {if $identifiant}{$current_class->nom($identifiant)|escape:javascript}{elseif $event=="update"}Modification{else}Création{/if}',
		{/if}
		items: [
			{* Panel primaire *}	
			{include file="generic-panel.tpl.js" 
				colonnes=$current_class->colonnes(primary,$event) 
				colsWidth=$current_class->panels.primary.columnsWidth
				title=ATF::$usr->trans(cadre_primary,$current_class->table)
				collapsed=false
				requests=$requests 
				colspan=$current_class->panels.primary.nbCols|default:2 
				collapsible=$current_class->panels.primary.collapsible|default:true
				border=$current_class->panels[primary].border|default:true
				panel_key=primary}
			{* Pour les champs placé dans un template suffix *}
			{if ATF::$html->template_exists("`$current_class->name()`-update_suffix.tpl.js")}
				,{include file="`$current_class->name()`-update_suffix.tpl.js"}
			{/if}
			
			{* Pour les éventuels panels supplémentaire*}
			{foreach from=$current_class->colonnes.panel key=k item=i}
				{if !$current_class->panels[$k].isSubPanel}
					,{include file="generic-panel.tpl.js" 
						colonnes=$current_class->colonnes($k,"update",'true') 
						title=ATF::$usr->trans("cadre_{$k}",$current_class->table) 
						requests=$requests 
						colspan=$current_class->panels[$k].nbCols|default:2 
						collapsed=(!$current_class->panels[$k].visible)
						checkable=$current_class->panels[$k].checkboxToggle
						hidden=true
						collapsible=$current_class->panels[$k].collapsible|default:true
						border=$current_class->panels[$k].border|default:true
						panel_key=$k}
				{/if}
			{/foreach}
			 
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
							title: '{ATF::$usr->trans($key,$current_class->name())} {$img_ob|escape:javascript}',
							collapsible:{if $item.collapsible===false}false{else}true{/if},
							collapsed: {if $item.collapsed===false || ATF::$usr->custom["user"]["show_all"]=='oui'}false{else}true{/if},
							animCollapse:false,
							autoHeight:true,
							autoWidth:true,
							id:'{$key}',
							{if $event!="insert"}hidden:true,{/if}
							defaults: { anchor: '100%' },
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
			
			{* Panel secondaire *}
			,{include file="generic-panel.tpl.js" 
				colonnes=$current_class->colonnes(secondary,$event)
				title=ATF::$usr->trans(cadre_secondary,$current_class->table)
				requests=$requests 
				colspan=$current_class->panels.secondary.nbCols|default:2 
				collapsed=(ATF::$usr->custom["user"]["show_all"]!='oui')
				border=$current_class->panels.secondary.border|default:true
				hidden=true
				collapsible=$current_class->panels.secondary.collapsible|default:true}
			
			{* Pour les champs cachés servant lorsque l on provient par exemple d une fiche select *}
			{include file="generic-champs_cache.tpl.js"}
			
				
		]

	}; 



	{/strip}
	{if $current_class->table!=importer && !$current_class->noUnsetFormIsActive}
		ATF.setFormIsActive();	
	{/if}

{else}
	var {$formName} = {
		{if $renderTo}
			renderTo:'{$renderTo}',
		{/if}
		frame:true,
		autoHeight:true,
		{if !$notitle}
			title: '{ATF::$usr->trans($current_class->table,module)|escape:javascript} | {if $identifiant}{$current_class->nom($identifiant)|escape:javascript}{elseif $event=="update"}Modification{else}Création{/if}',
		{/if}
		bodyStyle:'padding:5px 5px 0',
		items: [
			{* Pour les champs placé dans un template prefix *}
			{if ATF::$html->template_exists("`$current_class->name()`-update_prefix.tpl.js")}
				{include file="`$current_class->name()`-update_prefix.tpl.js"},
			{/if}
			
			{* Panel primaire *}	
			{include file="generic-panel.tpl.js" 
				colonnes=$current_class->colonnes(primary,$event) 
				title=ATF::$usr->trans(cadre_primary,$current_class->table)
				collapsed=false
				requests=$requests 
				colspan=$current_class->panels.primary.nbCols|default:2 
				readingDirection=$current_class->panels[primary].readingDirection|default:false
				collapsible=$current_class->panels.primary.collapsible|default:true
				border=$current_class->panels[primary].border|default:true
				panel_key=primary}
			
			{* Pour les champs placé dans un template suffix *}
			{if ATF::$html->template_exists("`$current_class->name()`-update_suffix.tpl.js")}
				,{include file="`$current_class->name()`-update_suffix.tpl.js"}
			{/if}
			
			{* Pour les éventuels panels supplémentaire *}
			{foreach from=$current_class->colonnes.panel key=k item=i}
				{if !$current_class->panels[$k].isSubPanel}
					,{include file="generic-panel.tpl.js" 
						colonnes=$current_class->colonnes($k,$event,'true') 
						title=ATF::$usr->trans("cadre_{$k}",$current_class->table) 
						requests=$requests 
						colspan=$current_class->panels[$k].nbCols|default:2 
						collapsed=(!$current_class->panels[$k].visible)
						checkable=$current_class->panels[$k].checkboxToggle
						hidden=$current_class->panels[$k].hidden
						readingDirection=$current_class->panels[$k].readingDirection|default:false
						collapsible=$current_class->panels[$k].collapsible|default:true
						border=$current_class->panels[$k].border|default:true
						panel_key=$k}
				{/if}
			{/foreach}
			
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
							title: '{ATF::$usr->trans($key,$current_class->name())|escape:javascript} {$img_ob|escape:javascript}',
							collapsible:{if $item.collapsible===false}false{else}true{/if},
							collapsed: {if $item.collapsed===false || ATF::$usr->custom["user"]["show_all"]=='oui'}false{else}true{/if},
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
			
			{* Panel secondaire *}
			,{include file="generic-panel.tpl.js" 
				colonnes=$current_class->colonnes(secondary,$event)
				title=ATF::$usr->trans(cadre_secondary,$current_class->table)
				requests=$requests 
				colspan=$current_class->panels.secondary.nbCols|default:2 
				collapsed=(ATF::$usr->custom["user"]["show_all"]!='oui')
				border=$current_class->panels.secondary.border|default:true
				readingDirection=$current_class->panels[secondary].readingDirection|default:false
				collapsible=$current_class->panels.secondary.collapsible|default:true}
			
			{* Pour les champs cachés servant lorsque l on provient par exemple d une fiche select *}
			{include file="generic-champs_cache.tpl.js"}
		]
		,buttons: [
			{* 1e : on va voir si le module comprends un champs import a true
			   2e : dans le cas d un update on ne met pas le bouton d import
			   3e : on vérifie qu on a le droit d effectuer l action *}
			{if ATF::module()->from_nom($current_class->table)}
				{if ATF::module()->select(ATF::module()->from_nom($current_class->table),'import')==1 && !$identifiant && ATF::$usr->privilege($current_class->table,'import')}
				{
					text: 'Importer massivement',
					handler: function(){
						ATF.basicInfo.getForm().submit({
							submitEmptyText:false,
							method  : 'post',
							waitMsg : '{ATF::$usr->trans(loading_new_page)|escape:javascript}',
							waitTitle :'{ATF::$usr->trans(loading)|escape:javascript}',
							url     : 'extjs.ajax',
							params: {
								'extAction':'importer'
								,'extMethod':'recupDonnees'
								,'provenance':'{$current_class->table}'
							}
							,success:function(form, action) {
								ATF.unsetFormIsActive();
								ATF.ajax_refresh(action.result,true);
							}
							,timeout:3600
						});
					}
				},
				{/if}
			{/if}
			{* extra Button sur les formulaires *}
			{if ATF::$html->template_exists("`$current_class->name()`-extraButton.tpl.js")}
				{include file="`$current_class->name()`-extraButton.tpl.js"}
			{/if}
			{
				{if $current_class->table==importer}
					text: 'Importer ce fichier',
				{elseif $identifiant || $current_class->table=="preferences"}
					text: '{ATF::$usr->trans(save_pref,preferences)|escape:javascript}',
				{else}
					text: '{ATF::$usr->trans(insert_new_record)|escape:javascript}',
				{/if}
				id:'validFormButton_{$current_class->table}',
				handler: function(){
					var redirect=false;
					{if ATF::$html->template_exists("`$current_class->name()`-onSubmitUpdate.tpl.js")}
						{include file="`$current_class->name()`-onSubmitUpdate.tpl.js"}
					{/if}
					{if ATF::$html->template_exists("`$current_class->name()`-submitFormRequest.tpl.js")}
						{include file="`$current_class->name()`-submitFormRequest.tpl.js"}
					{else}
						{include file="generic-submitFormRequest.tpl.js"}
					{/if}
				}
			}
			{if $current_class->table==importer}
				{* Bouton de simulation import *}
				,{
					text: 'Simuler de l\'import',
					handler: function(){
						ATF.basicInfo.getForm().submit({
							submitEmptyText:false,
							method  : 'post',
							waitMsg : '{ATF::$usr->trans(generating_pdf)|escape:javascript}',
							waitTitle : '{ATF::$usr->trans(loading)|escape:javascript}',
							url     : 'extjs.ajax',
							params: {
								'extAction':'{$current_class->table}'
								,'extMethod':'insert'
								,'file':'{$key}'
								,'preview':'true'
							}
							,success:function(form, action) {
								if(action.result.result){
									ATF.unsetFormIsActive();
									window.location='{$current_class->table}-select-{$key}.temp';
									Ext.defer(function () {
										ATF.setFormIsActive();
									},300);
								}else if(action.result.cadre_refreshed){
									ATF.ajax_refresh(action.result,true);
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
					}
				}
			{/if}

		{if ATF::$html->template_exists("`$current_class->name()`-files.tpl.js")}
				{include file="`$current_class->name()`-files.tpl.js"}
		{else}
			{foreach from=$current_class->files key=key item=item}
				{if $item.preview}
					,{
						text: '{ATF::$usr->trans(preview)|escape:javascript} {ATF::$usr->trans($key,$current_class->table)}',
						handler: function(){
							{if $identifiant}
								ATF.basicInfo.getForm().submit({
									submitEmptyText:false,
									method  : 'post',
									waitMsg : '{ATF::$usr->trans(generating_pdf)|escape:javascript}',
									waitTitle : '{ATF::$usr->trans(loading)|escape:javascript}',
									url     : 'extjs.ajax',
									params: {
										'{$current_class->table}[id_{$current_class->table}]':'{$identifiant}'
										,'extAction':'{$current_class->table}'
										,'extMethod':"{if $event=='cloner'}cloner{else}update{/if}"
										,'preview':'true'
									}
									,success:function(form, action) {
										if(action.result.result){
											ATF.unsetFormIsActive();
											window.location='{$current_class->table}-select-{$key}-'+action.result.result+'.temp'; 
											ATF.setFormIsActive();
										}else if(action.result.cadre_refreshed){
											ATF.ajax_refresh(action.result,true);
										}else {
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
									waitMsg : '{ATF::$usr->trans(generating_pdf)|escape:javascript}',
									waitTitle : '{ATF::$usr->trans(loading)|escape:javascript}',
									url     : 'extjs.ajax',
									params: {
										'extAction':'{$current_class->table}'
										,'extMethod':'insert'
										,'file':'{$key}'
										,'preview':'true'
									}
									,success:function(form, action) {
										if(action.result.result){
											ATF.unsetFormIsActive();
											window.location='{$current_class->table}-select-{$key}-'+action.result.result+'.temp'; 
											Ext.defer(function () {
												ATF.setFormIsActive();
											},300);
										}else if(action.result.cadre_refreshed){
											ATF.ajax_refresh(action.result,true);
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
							{/if}
						}
					}
				{/if}
			{/foreach}
		{/if}
		]
	};
{/if}
