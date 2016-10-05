{if $value!==false}
	{$value=$requests[$current_class->name()][$key]|default:ATF::_r($key)|default:$smarty.session.requests[$current_class->table][$key]|default:$value|default:$current_class->default_value($key,$smarty.session,$smarty.request)|default:$item.default}
{/if}	
{$xtype=$xtype|default:$item.xtype|default:'textfield'}
{$anchor=$anchor|default:98}
{if $item.autocomplete}
	{$function=$function|default:$item.autocomplete.function|default:autocomplete}
	{$pageSize=$pageSize|default:$item.autocomplete.pageSize}
{else}
	{$function=$function|default:autocomplete}
{/if}

{* gestion des champs obligatoires *}
{if !$est_nul}
	{include file="champ_obligatoire.tpl.htm" assign="img_ob"}
{/if}
{
		xtype:'{$xtype}'
		,typeAhead:true
		,triggerAction:'all'
		,editable:false
		,lazyRender:true
		,mode:'local'
		,preventMark:true
		,hiddenName:'{$name}'{$alternateName="combo`$name`"}{$alternateId="combo`$id`"}
		,store: new Ext.data.ArrayStore({
			fields: [
				'myId',
				'displayText'
			],			
			data: [
				{if $est_nul}
					['', '{$item.libNull|default:"-"}'],
				{/if}
				{foreach from=$item.data item=i}
					['{$i}', '{ATF::$usr->trans($i,"`$current_class->table`_`$key`",false,true)|default:ATF::$usr->trans($i,$current_class->table)|escape:javascript}']
					{if !$i@last},{/if}
				{/foreach}
			]
		})
		,valueField: 'myId'
		,displayField: 'displayText'
		,fieldLabel: '{$fieldLabel|escape:javascript} {$img_ob|escape:javascript}'
		,name: '{$alternateName|default:$name}'
		,id: '{$alternateId|default:$id}'
		,listeners :{
			'select':function(f,n,o){				
					if(n.data.myId=='vente'){
						Ext.getCmp('panel_loyer_lignes').hide();
						Ext.getCmp('panel_loyer_uniques').hide();
						Ext.getCmp('combodevis[loyer_unique]').setValue('non');
						Ext.getCmp('panel_vente').show();
						Ext.getCmp('devis[prix_vente]').enable();
						Ext.getCmp('panel_avenant_lignes').hide();
						Ext.getCmp('panel_AR').hide();
					}else{
						Ext.getCmp('panel_vente').hide();
						Ext.getCmp('panel_loyer_lignes').show();
						Ext.getCmp('panel_avenant_lignes').show();
						Ext.getCmp('panel_AR').show();
					}
				
			},
		}
		{if $emptyText},emptyText:'{$emptyText|escape:javascript}'{/if}
		{if $labelStyle  || $item.labelStyle},labelStyle: '{$labelStyle|default:$item.labelStyle}'{/if}
		{if $originalValue},originalValue:'{$originalValue|escape:javascript}'{/if}
		{if $value || $value==='0' || $alternateValue || $alternateValue==='0'}
			{if $item.formatNumeric}
				{$value_def=$current_class->formatNumeric($value)}
			{else}
				{$value_def=$alternateValue|default:$value|escape:javascript}
			{/if}
			,value:'{$value_def}'
		{/if}
		{if $disabled || $item.disabled},disabled:true{/if}
		{if $readonly || $item.readonly},readOnly:true{/if}
		{if $renderTo  || $item.hidden},renderTo:'{$renderTo|escape:javascript}'{/if}
		{if $anchor || $item.anchor},anchor:'{$anchor|default:$item.anchor}%'{/if}
		{if $style || $item.style},style:'{$style|default:$item.style}'{/if}
		{if $width || $item.width},width:{$width|default:$item.width}{/if}
	}