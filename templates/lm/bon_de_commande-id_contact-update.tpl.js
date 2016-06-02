{strip} {* Condition : ne parcourir que les contacts de la société et mettre le contact signataire par défaut *}
{$id_commande=$requests.id_commande|default:$requests.commande.id_commande}
{if $id_commande}
	{$id_societe =ATF::commande()->select($id_commande,'id_societe')}
	{include file="generic-field-textfield.tpl.js" condition_field=id_societe condition_value=$id_societe}
{/if}
{/strip}