{strip} {* Condition : ne parcourir que les contacts de la société de la hotline *}
{$id_societe=$requests.id_societe}
{include file="generic-field-textfield.tpl.js" condition_field=id_societe condition_value=$id_societe}
{/strip}