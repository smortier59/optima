{$id_affaire=ATF::commande()->select($smarty.request.id_commande,"id_affaire")}
{if ATF::affaire()->select($id_affaire,"nature")!="vente"}
	{include file="generic-field-textfield.tpl.js"}
{/if}