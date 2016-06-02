{if $id!==false}
	{$id=$requests[$current_class->name()][$key]|default:ATF::_r($key)|default:$smarty.session.requests[$current_class->table][$key]|default:$value|default:$current_class->default_value($key,$smarty.session,$smarty.request)|default:$item.default}
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
		{foreach from=ATF::societe()->getOpca() key=k item=i}			
			{if ATF::formation_devis()->opcaIn($id, $i)}
				{$value[] = {ATF::societe()->cryptId($i["id_societe"])}}
			{/if}			
			['{ATF::societe()->cryptId($i["id_societe"])}', '{$i["societe"]|escape:javascript}']
			{if !$i@last},{/if}
		{/foreach}
	],
	value : [ 
		{if $value}		
			{foreach $value as $k=>$v}		
				{if $esp === 0}
					'{$v}'
				{else}
					,'{$v}'
				{/if}
				{$esp = $esp+1}		
			{/foreach}
		{/if}
	]
}