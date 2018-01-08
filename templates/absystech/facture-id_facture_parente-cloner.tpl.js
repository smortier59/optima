{$id_affaire = ATF::facture()->select(ATF::_r("id_facture") , "id_affaire")}

{if $value!==false}
  {$value=$requests[$current_class->name()][$key]|default:ATF::_r($key)|default:$smarty.session.requests[$current_class->table][$key]|default:$value|default:$current_class->default_value($key,$smarty.session,$smarty.request)|default:$item.default}
{/if}
{
  xtype: 'superboxselect',
  fieldLabel: '{ATF::$usr->trans($key,$current_class->table)|escape:javascript}',
  name: '{$alternateName|default:$name}[]',
  id: '{$alternateId|default:$id}',
  anchor:"98%",
  size: 'small',

  store: [
    {ATF::facture()->q->reset()->where('facture.id_societe', '1930')->where('type','facture')->end()}
    {foreach from=ATF::facture()->autocomplete(false,false) key=k item=i}
      ['{$i[0]}', '{$i[1]|escape:javascript}']
      {if !$i@last},{/if}
    {/foreach}
  ]
}
