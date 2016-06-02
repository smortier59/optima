{strip} {* Condition : ne parcourir que les contacts de la société et mettre le contact signataire par défaut *}
{$id_societe=$requests.id_societe|default:$requests.societe.id_societe}
{if $id_societe}
	{include file="generic-field-textfield.tpl.js" condition_field=id_societe condition_value=$id_societe}
{/if}
{/strip}