{strip}
{*
@string $xtype Type extJS
*}
{if $value!==false}
	{$value=$requests[$current_class->name()][$key]|default:$smarty.request[$key]|default:$smarty.session.requests[$current_class->table][$key]|default:$value|default:$current_class->default_value($key,$smarty.session,$smarty.request)|default:$item.default}
{/if}	
{$xtype=$xtype|default:$item.xtype|default:'textfield'}

	{
		xtype:'combo',
		typeAhead:true,
		editable:false,
		triggerAction:'all',
		lazyRender:true,
		mode:'local',
		preventMark:true,
		hiddenName:'{$name}',{$alternateName="combo`$name`"}{$alternateId="combo`$id`"}
		store: new Ext.data.ArrayStore({
			fields: [
				'myId',
				'displayText'
			],			
			data: [
				{foreach from=['',dev,system,telecom] item=i}
					['{$i}', '{ATF::$usr->trans($i,"`$current_class->table`_`$key`",false,true)|default:ATF::$usr->trans($i,$current_class->table)|escape:javascript}']
					{if !$i@last},{/if}
				{/foreach}
			]
		}),
		valueField: 'myId',
		displayField: 'displayText',
		fieldLabel: '{$fieldLabel|escape:javascript}',
		name: '{$alternateName|default:$name}',
		id: '{$alternateId|default:$id}',
		{if $originalValue}originalValue:'{$originalValue|escape:javascript}',{/if}
		{if $value || $alternateValue}value:'{$alternateValue|default:$value|escape:javascript}',{/if}
		{if $disabled}disabled:true,{/if}
		{if $renderTo}renderTo:'{$renderTo|escape:javascript}',{/if}
		anchor:'95%'
		{if $width},width:{$width}{/if}
	}
{/strip}