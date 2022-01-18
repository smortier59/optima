{strip} {* Condition : ne parcourir que les contacts de la société de la hotline *}


{$id_societe=$requests[$current_class->name()].id_societe|default:ATF::_r(id_societe)|default:$smarty.session.requests[$current_class->table].id_societe|default:$current_class->default_value(id_societe,$smarty.session,$smarty.request)}
{include file="generic-field-textfield.tpl.js" condition_field=id_societe condition_value=$id_societe}
{/strip}