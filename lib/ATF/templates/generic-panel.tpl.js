{* 
@param string $title
@param array $colonnes
@param array $requests
@param int $colspan
@param bool $readingDirection Sens de lecture des champs : True colonne par colonne, false de gauche à droite.
@param string $panel_key Clé du panel
*}
{strip}

{if $current_class->selectExtjs}
	{if $event=='insert' || $event=="speedInsert"}
		{$readonly = false} 
	{else}
		{$readonly = true}
	{/if}
{/if}
{if $colonnes}{
	xtype:'fieldset',
	title: '{$title|escape:javascript}',
	{if $checkable}
		checkboxToggle:true,
	{else}
		collapsible:{if $collapsible}true{else}false{/if},
		animCollapse:false,
	{/if}
	collapsed:{if $collapsed && ATF::$usr->custom["user"]["show_all"]!='oui'}true{else}false{/if},
	defaultType: 'textfield',
	defaults: { labelWidth: {$labelWidth|default:150} },
	hidden:{if $hidden}true{else}false{/if},
	{if $border===false}border:false,{/if}
	id: 'panel_{$panel_key}',
	{if ATF::$html->template_exists("{$current_class->table}-{$panel_key}-panelListeners.tpl.js")}
		listeners: {include file="{$current_class->table}-{$panel_key}-panelListeners.tpl.js"},
	{/if} 
	items :[{
		xtype: 'panel',
		layout:'column',
		border:false,
		items:[ 
			{for $colNumber=0; $colNumber<$colspan; $colNumber++}  
				{if $colNumber},{/if}
				{
					{if $colsWidth[$colNumber]}
						columnWidth:{$colsWidth[$colNumber]},
					{else}
						columnWidth:{1/$colspan|replace:",":"."},
					{/if}
					layout: 'form',
					border:false,
					items: [ 
						{foreach from=$colonnes key=key item=item}
							{if (($item@index%$colspan)===$colNumber && !$item.targetCols) || $item.targetCols==$colNumber+1}
								{if $item@index>=$colspan}
								,
								{/if}
								{* Afin de pouvoir gérer un template particulier pour un champ *}
								{if ATF::$html->template_exists("`$current_class->table`-`$key`-`$event`.tpl.js")} 
									{$nom_fichier="`$current_class->table`-`$key`-`$event`.tpl.js"}
								{elseif $event=='cloner' && ATF::$html->template_exists("`$current_class->table`-`$key`-insert.tpl.js")} 
									{$nom_fichier="`$current_class->table`-`$key`-insert.tpl.js"}
								{elseif $event!==update && ATF::$html->template_exists("`$current_class->table`-`$key`-update.tpl.js")}
									{$nom_fichier="`$current_class->table`-`$key`-update.tpl.js"}
								{elseif ATF::$html->template_exists("`$current_class->table`-`$key`.tpl.js")}
									{$nom_fichier="`$current_class->table`-`$key`.tpl.js"}
								{else}   
									{$nom_fichier="generic-field-textfield.tpl.js"}	
								{/if}

								{include file=$nom_fichier
									fieldLabel=ATF::$usr->trans($key,$current_class->table)
									name="`$current_class->table`[`$key`]"
									id="`$current_class->table`[`$key`]"
									disabled=in_array($key,$current_class->colonnes.bloquees[$event])
									forceHiddenIfValue=true
									value=$requests["{$current_class->table}"][$key]|default:$requests[$key]

									est_nul=$item["null"]
								}
							{/if}
						{/foreach}
					 ]
				}
			{/for}
		]
	}]
}
{else}
[]
{/if}

{/strip}
