
{if ATF::_r(id_commande) || $requests.facture.id_comande}
	{include file="generic-field-textfield.tpl.js"}
{else}
	{}
{/if}