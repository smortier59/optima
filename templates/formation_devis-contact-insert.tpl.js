{if $value!==false}
	{$value=$requests[$current_class->name()][$key]|default:ATF::_r($key)|default:$smarty.session.requests[$current_class->table][$key]|default:$value|default:$current_class->default_value($key,$smarty.session,$smarty.request)|default:$item.default}
{/if}	
{$xtype=$xtype|default:$item.xtype|default:'textfield'}
{* gestion des champs obligatoires *}
{include file="champ_obligatoire.tpl.htm" assign="img_ob"}

{if ATF::_r("id_societe")}
	{$id_societe = ATF::_r("id_societe")}
{else}
	{$id_societe = ATF::formation_devis()->select(ATF::_r("id_formation_devis") , "id_societe")}
{/if}
{
	xtype: 'superboxselect',
	fieldLabel: '{ATF::$usr->trans($key,$current_class->table)|escape:javascript}',
	name: '{$alternateName|default:$name}[]',
	id: '{$alternateId|default:$id}',
	anchor:"98%",
	store: [
		{foreach from=ATF::contact()->getContactFromSociete($id_societe) key=k item=i}
			 {$value[] = {$i[0]}}
			['{$i[0]}', '{$i[1]|escape:javascript}']
			{if !$i@last},{/if}

		{/foreach}
	],
	value : [
	{$esp = 0}
	{foreach $value as $k=>$v}		
		{if $esp === 0}
			'{$v}'
		{else}
			,'{$v}'
		{/if}
		{$esp = $esp+1}
	{/foreach}
	]
}