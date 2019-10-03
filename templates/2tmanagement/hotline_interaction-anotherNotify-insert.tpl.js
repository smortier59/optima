{if $value!==false}
	{$value=$requests[$current_class->name()][$key]|default:ATF::_r($key)|default:$smarty.session.requests[$current_class->table][$key]|default:$value|default:$current_class->default_value($key,$smarty.session,$smarty.request)|default:$item.default}
{/if}	
{$xtype=$xtype|default:$item.xtype|default:'textfield'}
{$id_societe = ATF::hotline()->select($requests['id_hotline'],'id_societe')}
{$id_contact = ATF::hotline()->select($requests['id_hotline'],'id_contact')}
{* gestion des champs obligatoires *}
{include file="champ_obligatoire.tpl.htm" assign="img_ob"}

{
	xtype: 'superboxselect',
	fieldLabel: '{ATF::$usr->trans($key,$current_class->table)|escape:javascript}',
	name: '{$alternateName|default:$name}[]',
	id: '{$alternateId|default:$id}',
	anchor:"98%",
	store: [
		{ATF::contact()->q->reset()->whereIsNotNull('contact.email')->where('contact.id_societe',$id_societe)->end()}
		{if $id_contact}
			{ATF::contact()->q->where('contact.id_contact',$id_contact,'OR',false,'!=')->end()}
		{/if}
		{foreach from=ATF::contact()->autocomplete(false,false) key=k item=i}

			['{$i[0]}', '{$i[1]|escape:javascript}']
			{if !$i@last},{/if}
		{/foreach}
	]
}