{strip}
{*
@string $xtype Type extJS
*}
{if $value!==false}
	{$value=$requests[$current_class->name()][$key]|default:$smarty.request[$key]|default:$smarty.session.requests[$current_class->table][$key]|default:$value|default:$current_class->default_value($key,$smarty.session,$smarty.request)|default:$item.default}
{/if}	
{$xtype=$xtype|default:$item.xtype|default:'textfield'}
{
	xtype:'sliderfield',
	value: '{ATF::hotline()->select($requests.id_hotline,avancement)|escape:javascript}',
	tipText: function(thumb){
		return String(thumb.value) + '%';
	},
	typeAhead:true,
	triggerAction:'all',
	editable:false,
	lazyRender:true,
	mode:'local',
	preventMark:true,
	hiddenName:'{$name}',{$alternateName="`$name`"}{$alternateId="`$id`"}
	valueField: 'myId',
	displayField: 'displayText',
	fieldLabel: '{$fieldLabel|escape:javascript} {$img_ob|escape:javascript}',
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