{strip} {* Condition : ne parcourir que les contacts de la soci�t� et mettre le contact signataire par d�faut *}
{$id_societe=$requests.id_societe|default:$requests.devis.id_societe}
{$id_contact_signataire=ATF::societe()->select($id_societe,"id_contact_signataire")}
{if $id_contact_signataire}
	{include file="generic-field-textfield.tpl.js" condition_field=id_societe condition_value=$id_societe value=$id_contact_signataire}
{else}
	{include file="generic-field-textfield.tpl.js" condition_field=id_societe condition_value=$id_societe}
{/if}
{/strip}