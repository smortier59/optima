{strip} {* Condition : ne parcourir que les contacts de la société de la hotline *}
{$id_hotline=$requests[$current_class->name()].id_hotline|default:ATF::_r(id_hotline)|default:$smarty.session.requests[$current_class->table].id_hotline|default:$current_class->default_value(id_hotline,$smarty.session,$smarty.request)}
{include file="generic-field-textfield.tpl.js" condition_field=id_societe condition_value=ATF::hotline()->select($id_hotline,id_societe)}
{/strip}