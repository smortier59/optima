{if $value!==false}
	{$value=$requests[$current_class->name()][$key]|default:ATF::_r($key)|default:$smarty.session.requests[$current_class->table][$key]|default:$value|default:$current_class->default_value($key,$smarty.session,$smarty.request)|default:$item.default}
{/if}	
{$xtype=$xtype|default:$item.xtype|default:'textfield'}
{* gestion des champs obligatoires *}
{include file="champ_obligatoire.tpl.htm" assign="img_ob"}



{
	xtype: 'superboxselect',
	fieldLabel: '{ATF::$usr->trans($key,$current_class->table)|escape:javascript}',
	name: '{$alternateName|default:$name}[]',
	id: '{$alternateId|default:$id}',
	anchor:"98%",
	store: [
		{foreach from=ATF::user()->autocomplete(false,true) key=k item=i}
			{if ATF::hotline_interaction()->isIntervenant($i[0], ATF::_r("id_hotline"))} {$value[] = {$i[0]}} {/if}
			['{$i[0]}', '{$i[1]|escape:javascript}']
			{if !$i@last},{/if}

		{/foreach}
	],
	value : [
	{$esp = 0}
	{foreach $value as $k=>$v}
		{if $v !== ATF::user()->cryptId(ATF::$usr->getID())}
			{if $esp === 0}
				'{$v}'
			{else}
				,'{$v}'
			{/if}
			{$esp = $esp+1}
		{/if}		
	{/foreach}
	]
}
